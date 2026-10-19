package io.github.justrohams.javacache;

import io.github.justrohams.javacache.eviction.EvictionPolicy;

import java.time.Duration;
import java.util.ArrayList;
import java.util.Collections;
import java.util.HashMap;
import java.util.LinkedHashMap;
import java.util.List;
import java.util.Map;
import java.util.Objects;
import java.util.Optional;
import java.util.concurrent.CompletableFuture;
import java.util.concurrent.ConcurrentHashMap;
import java.util.concurrent.ExecutionException;
import java.util.concurrent.Executors;
import java.util.concurrent.ScheduledExecutorService;
import java.util.concurrent.ScheduledFuture;
import java.util.concurrent.TimeUnit;
import java.util.concurrent.atomic.AtomicReference;
import java.lang.ref.WeakReference;

final class ConcurrentCache<K, V> implements Cache<K, V> {

    private static final ScheduledExecutorService CLEANUP_SCHEDULER =
            Executors.newSingleThreadScheduledExecutor(r -> {
                Thread t = new Thread(r, "javacache-cleanup");
                t.setDaemon(true);
                return t;
            });

    private final ConcurrentHashMap<K, CacheEntry<V>> store = new ConcurrentHashMap<>();
    private final ConcurrentHashMap<K, CompletableFuture<V>> loadingMap = new ConcurrentHashMap<>();
    private final EvictionPolicy<K> evictionPolicy;
    private final CacheStats stats = new CacheStats();
    private final CacheEventListener<K, V> listener;
    private final long maximumSize;
    private final long defaultTtlNanos;
    private final ScheduledFuture<?> cleanupFuture;

    private volatile boolean closed;

    ConcurrentCache(CacheBuilder<K, V> config, EvictionPolicy<K> policy) {
        this.evictionPolicy = policy;
        this.listener = config.listener();
        this.maximumSize = config.maximumSize();
        this.defaultTtlNanos = config.defaultTtl() == null
                ? -1L
                : config.defaultTtl().toNanos();

        if (config.backgroundCleanup()) {
            long intervalMs = Math.max(50L, config.cleanupInterval().toMillis());
            AtomicReference<ScheduledFuture<?>> ref = new AtomicReference<>();
            WeakReference<ConcurrentCache<K, V>> weakThis = new WeakReference<>(this);
            Runnable task = () -> {
                ConcurrentCache<K, V> cache = weakThis.get();
                if (cache == null) {
                    ScheduledFuture<?> f = ref.get();
                    if (f != null) f.cancel(false);
                    return;
                }
                try {
                    cache.cleanUp();
                } catch (Throwable ignored) {
                }
            };
            ScheduledFuture<?> future = CLEANUP_SCHEDULER.scheduleAtFixedRate(
                    task, intervalMs, intervalMs, TimeUnit.MILLISECONDS);
            ref.set(future);
            this.cleanupFuture = future;
        } else {
            this.cleanupFuture = null;
        }
    }

    @Override
    public Optional<V> getIfPresent(K key) {
        Objects.requireNonNull(key, "key");
        CacheEntry<V> entry = store.get(key);

        if (entry == null) {
            stats.recordMiss();
            return Optional.empty();
        }
        if (entry.isExpired()) {
            if (store.remove(key, entry)) {
                synchronized (evictionPolicy) {
                    evictionPolicy.recordRemoval(key);
                }
                stats.recordExpiration();
                dispatchRemoval(key, entry.value(), RemovalCause.EXPIRED);
            }
            stats.recordMiss();
            return Optional.empty();
        }
        stats.recordHit();
        synchronized (evictionPolicy) {
            evictionPolicy.recordAccess(key);
        }
        return Optional.of(entry.value());
    }

    @Override
    public V get(K key) {
        return getIfPresent(key).orElse(null);
    }

    @Override
    public V get(K key, CacheLoader<? super K, ? extends V> loader) {
        Objects.requireNonNull(key, "key");
        Objects.requireNonNull(loader, "loader");

        Optional<V> cached = getIfPresent(key);
        if (cached.isPresent()) {
            return cached.get();
        }

        CompletableFuture<V> myFuture = new CompletableFuture<>();
        CompletableFuture<V> existing = loadingMap.putIfAbsent(key, myFuture);
        if (existing != null) {
            try {
                return existing.get();
            } catch (InterruptedException e) {
                Thread.currentThread().interrupt();
                throw new CacheException("Interrupted while waiting for load of key: " + key, e);
            } catch (ExecutionException e) {
                throw new CacheException("Load failed for key: " + key, e.getCause());
            }
        }

        long start = System.nanoTime();
        try {
            V value = loader.load(key);
            if (value != null) {
                put(key, value);
            }
            stats.recordLoadSuccess(System.nanoTime() - start);
            myFuture.complete(value);
            return value;
        } catch (Throwable t) {
            stats.recordLoadFailure(System.nanoTime() - start);
            myFuture.completeExceptionally(t);
            throw new CacheException("Load failed for key: " + key, t);
        } finally {
            loadingMap.remove(key, myFuture);
        }
    }

    @Override
    public boolean containsKey(K key) {
        Objects.requireNonNull(key, "key");
        CacheEntry<V> entry = store.get(key);
        if (entry == null) {
            return false;
        }
        if (entry.isExpired()) {
            if (store.remove(key, entry)) {
                synchronized (evictionPolicy) {
                    evictionPolicy.recordRemoval(key);
                }
                stats.recordExpiration();
                dispatchRemoval(key, entry.value(), RemovalCause.EXPIRED);
            }
            return false;
        }
        return true;
    }

    @Override
    public void put(K key, V value) {
        put(key, value, defaultTtlNanos > 0 ? Duration.ofNanos(defaultTtlNanos) : null);
    }

    @Override
    public void put(K key, V value, Duration ttl) {
        Objects.requireNonNull(key, "key");
        Objects.requireNonNull(value, "value");

        long expiresAt = (ttl != null && !ttl.isNegative() && !ttl.isZero())
                ? System.nanoTime() + ttl.toNanos()
                : -1L;
        CacheEntry<V> newEntry = new CacheEntry<>(value, expiresAt);

        CacheEntry<V> old = store.put(key, newEntry);
        if (old != null) {
            dispatchRemoval(key, old.value(), RemovalCause.REPLACED);
            return;
        }

        synchronized (evictionPolicy) {
            if (store.containsKey(key)) {
                evictionPolicy.recordInsertion(key);
            } else {
                return;
            }
        }

        evictToMaximumSize();
    }

    @Override
    public Optional<V> remove(K key) {
        Objects.requireNonNull(key, "key");
        CacheEntry<V> removed;
        synchronized (evictionPolicy) {
            removed = store.remove(key);
            if (removed != null) {
                evictionPolicy.recordRemoval(key);
            }
        }
        if (removed == null) {
            return Optional.empty();
        }
        dispatchRemoval(key, removed.value(), RemovalCause.EXPLICIT);
        return Optional.of(removed.value());
    }

    @Override
    public void removeAll() {
        List<RemovalNotification<K, V>> toNotify = new ArrayList<>();
        synchronized (evictionPolicy) {
            for (K k : store.keySet()) {
                CacheEntry<V> removed = store.remove(k);
                if (removed != null) {
                    toNotify.add(new RemovalNotification<>(k, removed.value(), RemovalCause.EXPLICIT));
                }
            }
            evictionPolicy.clear();
        }
        for (RemovalNotification<K, V> n : toNotify) {
            dispatchRemoval(n);
        }
    }

    @Override
    public void cleanUp() {
        List<RemovalNotification<K, V>> toNotify = new ArrayList<>();
        synchronized (evictionPolicy) {
            for (Map.Entry<K, CacheEntry<V>> e : store.entrySet()) {
                if (e.getValue().isExpired() && store.remove(e.getKey(), e.getValue())) {
                    evictionPolicy.recordRemoval(e.getKey());
                    stats.recordExpiration();
                    toNotify.add(new RemovalNotification<>(
                            e.getKey(), e.getValue().value(), RemovalCause.EXPIRED));
                }
            }
        }
        for (RemovalNotification<K, V> n : toNotify) {
            dispatchRemoval(n);
        }
    }

    @Override
    public long size() {
        return store.size();
    }

    @Override
    public CacheStats stats() {
        return stats;
    }

    @Override
    public Map<K, V> asMap() {
        Map<K, V> snapshot = new LinkedHashMap<>();
        for (Map.Entry<K, CacheEntry<V>> e : store.entrySet()) {
            if (!e.getValue().isExpired()) {
                snapshot.put(e.getKey(), e.getValue().value());
            }
        }
        return Collections.unmodifiableMap(snapshot);
    }

    @Override
    public void close() {
        if (closed) return;
        closed = true;
        if (cleanupFuture != null) {
            cleanupFuture.cancel(false);
        }
        removeAll();
    }

    private void evictToMaximumSize() {
        List<RemovalNotification<K, V>> toNotify = new ArrayList<>();
        synchronized (evictionPolicy) {
            while (store.size() > maximumSize) {
                K victim = evictionPolicy.selectEvictionCandidate();
                if (victim == null) break;
                CacheEntry<V> removed = store.remove(victim);
                evictionPolicy.recordRemoval(victim);
                if (removed != null) {
                    stats.recordEviction();
                    toNotify.add(new RemovalNotification<>(
                            victim, removed.value(), RemovalCause.SIZE));
                }
            }
        }
        for (RemovalNotification<K, V> n : toNotify) {
            dispatchRemoval(n);
        }
    }

    private void dispatchRemoval(K key, V value, RemovalCause cause) {
        dispatchRemoval(new RemovalNotification<>(key, value, cause));
    }

    private void dispatchRemoval(RemovalNotification<K, V> n) {
        if (listener != null) {
            try {
                listener.onRemoval(n);
            } catch (Throwable ignored) {
            }
        }
    }
}