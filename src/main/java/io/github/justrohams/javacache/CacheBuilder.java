package io.github.justrohams.javacache;

import io.github.justrohams.javacache.eviction.EvictionPolicy;
import io.github.justrohams.javacache.eviction.LruEvictionPolicy;

import java.time.Duration;
import java.util.Objects;

public final class CacheBuilder<K, V> {

    private static final long DEFAULT_MAX_SIZE = 10_000L;
    private static final Duration DEFAULT_CLEANUP_INTERVAL = Duration.ofMinutes(1);

    private long maximumSize = DEFAULT_MAX_SIZE;
    private Duration defaultTtl;
    private EvictionPolicy<K> evictionPolicy;
    private CacheEventListener<K, V> listener;
    private boolean backgroundCleanup = true;
    private Duration cleanupInterval = DEFAULT_CLEANUP_INTERVAL;

    private CacheBuilder() { }

    public static <K, V> CacheBuilder<K, V> newBuilder() {
        return new CacheBuilder<>();
    }

    public CacheBuilder<K, V> maximumSize(long size) {
        if (size <= 0) {
            throw new IllegalArgumentException("maximumSize must be > 0");
        }
        this.maximumSize = size;
        return this;
    }

    public CacheBuilder<K, V> expireAfterWrite(Duration ttl) {
        this.defaultTtl = ttl;
        return this;
    }

    public CacheBuilder<K, V> evictionPolicy(EvictionPolicy<K> policy) {
        this.evictionPolicy = Objects.requireNonNull(policy, "policy");
        return this;
    }

    public CacheBuilder<K, V> listener(CacheEventListener<K, V> listener) {
        this.listener = listener;
        return this;
    }

    public CacheBuilder<K, V> enableBackgroundCleanup(boolean enable) {
        this.backgroundCleanup = enable;
        return this;
    }

    public CacheBuilder<K, V> cleanupInterval(Duration interval) {
        this.cleanupInterval = Objects.requireNonNull(interval, "interval");
        return this;
    }

    public Cache<K, V> build() {
        EvictionPolicy<K> policy = evictionPolicy != null
                ? evictionPolicy
                : new LruEvictionPolicy<>();
        return new ConcurrentCache<>(this, policy);
    }


    long maximumSize() { return maximumSize; }
    Duration defaultTtl() { return defaultTtl; }
    CacheEventListener<K, V> listener() { return listener; }
    boolean backgroundCleanup() { return backgroundCleanup; }
    Duration cleanupInterval() { return cleanupInterval; }
}