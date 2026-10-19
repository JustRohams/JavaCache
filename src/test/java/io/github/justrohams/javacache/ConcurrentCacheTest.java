package io.github.justrohams.javacache;

import org.junit.jupiter.api.Test;

import java.time.Duration;
import java.util.ArrayList;
import java.util.List;
import java.util.concurrent.CountDownLatch;
import java.util.concurrent.ExecutorService;
import java.util.concurrent.Executors;
import java.util.concurrent.TimeUnit;
import java.util.concurrent.atomic.AtomicInteger;

import static org.junit.jupiter.api.Assertions.*;

class ConcurrentCacheTest {

    @Test
    void putAndGetRoundTrip() {
        Cache<String, String> cache = CacheBuilder.<String, String>newBuilder().build();
        cache.put("k", "v");
        assertEquals("v", cache.get("k"));
        assertTrue(cache.getIfPresent("k").isPresent());
    }

    @Test
    void missingKeyReturnsNullAndEmpty() {
        Cache<String, String> cache = CacheBuilder.<String, String>newBuilder().build();
        assertNull(cache.get("nope"));
        assertFalse(cache.getIfPresent("nope").isPresent());
    }

    @Test
    void nullKeysOrValuesRejected() {
        Cache<String, String> cache = CacheBuilder.<String, String>newBuilder().build();
        assertThrows(NullPointerException.class, () -> cache.put(null, "x"));
        assertThrows(NullPointerException.class, () -> cache.put("x", null));
        assertThrows(NullPointerException.class, () -> cache.get(null));
    }

    @Test
    void ttlExpiresEntries() throws InterruptedException {
        Cache<String, String> cache = CacheBuilder.<String, String>newBuilder()
                .enableBackgroundCleanup(false)
                .build();
        cache.put("k", "v", Duration.ofMillis(50));
        assertEquals("v", cache.get("k"));
        Thread.sleep(120);
        assertNull(cache.get("k"));
    }

    @Test
    void explicitRemovalFiresListener() {
        List<RemovalNotification<String, String>> events = new ArrayList<>();
        Cache<String, String> cache = CacheBuilder.<String, String>newBuilder()
                .listener(events::add)
                .build();
        cache.put("k", "v");
        cache.remove("k");
        assertEquals(1, events.size());
        assertEquals(RemovalCause.EXPLICIT, events.get(0).cause());
    }

    @Test
    void maximumSizeEvicts() {
        Cache<Integer, Integer> cache = CacheBuilder.<Integer, Integer>newBuilder()
                .maximumSize(3)
                .enableBackgroundCleanup(false)
                .build();
        for (int i = 0; i < 10; i++) {
            cache.put(i, i);
        }
        assertTrue(cache.size() <= 3, "size should be capped");
    }

    @Test
    void statsTrackHitsAndMisses() {
        Cache<String, String> cache = CacheBuilder.<String, String>newBuilder().build();
        cache.put("a", "1");
        cache.get("a");        // hit
        cache.get("missing");  // miss
        CacheStats stats = cache.stats();
        assertEquals(1, stats.hitCount());
        assertEquals(1, stats.missCount());
        assertEquals(0.5, stats.hitRate(), 1e-9);
    }

    @Test
    void singleFlightLoadsOnlyOnce() throws Exception {
        AtomicInteger loads = new AtomicInteger();
        Cache<String, String> cache = CacheBuilder.<String, String>newBuilder().build();

        int threads = 20;
        ExecutorService pool = Executors.newFixedThreadPool(threads);
        CountDownLatch start = new CountDownLatch(1);
        List<java.util.concurrent.Future<String>> futures = new ArrayList<>();

        for (int i = 0; i < threads; i++) {
            futures.add(pool.submit(() -> {
                start.await();
                return cache.get("k", key -> {
                    loads.incrementAndGet();
                    Thread.sleep(100);
                    return "value";
                });
            }));
        }
        start.countDown();
        for (var f : futures) {
            assertEquals("value", f.get(5, TimeUnit.SECONDS));
        }
        pool.shutdownNow();
        assertEquals(1, loads.get(), "loader should be invoked only once");
    }

    @Test
    void loaderNullResultIsNotCached() {
        Cache<String, String> cache = CacheBuilder.<String, String>newBuilder().build();
        String v = cache.get("k", key -> null);
        assertNull(v);
        assertFalse(cache.containsKey("k"));
    }

    @Test
    void asMapSnapshotExcludesExpired() throws InterruptedException {
        Cache<String, String> cache = CacheBuilder.<String, String>newBuilder()
                .enableBackgroundCleanup(false)
                .build();
        cache.put("a", "1");
        cache.put("b", "2", Duration.ofMillis(30));
        Thread.sleep(80);
        var map = cache.asMap();
        assertTrue(map.containsKey("a"));
        assertFalse(map.containsKey("b"));
    }
}