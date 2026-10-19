package io.github.justrohams.javacache;

import java.util.concurrent.atomic.LongAdder;

public final class CacheStats {

    private final LongAdder hits = new LongAdder();
    private final LongAdder misses = new LongAdder();
    private final LongAdder evictions = new LongAdder();
    private final LongAdder expirations = new LongAdder();
    private final LongAdder loads = new LongAdder();
    private final LongAdder loadSuccesses = new LongAdder();
    private final LongAdder loadFailures = new LongAdder();
    private final LongAdder totalLoadTimeNanos = new LongAdder();

    void recordHit()            { hits.increment(); }
    void recordMiss()           { misses.increment(); }
    void recordEviction()       { evictions.increment(); }
    void recordExpiration()     { expirations.increment(); }

    void recordLoadSuccess(long nanos) {
        loadSuccesses.increment();
        loads.increment();
        totalLoadTimeNanos.add(nanos);
    }

    void recordLoadFailure(long nanos) {
        loadFailures.increment();
        loads.increment();
        totalLoadTimeNanos.add(nanos);
    }

    public long hitCount()              { return hits.sum(); }
    public long missCount()             { return misses.sum(); }
    public long requestCount()          { return hitCount() + missCount(); }
    public long evictionCount()         { return evictions.sum(); }
    public long expirationCount()       { return expirations.sum(); }
    public long loadCount()             { return loads.sum(); }
    public long loadSuccessCount()      { return loadSuccesses.sum(); }
    public long loadFailureCount()      { return loadFailures.sum(); }
    public long totalLoadTimeNanos()    { return totalLoadTimeNanos.sum(); }

    public double hitRate() {
        long total = requestCount();
        return total == 0 ? 0.0 : (double) hitCount() / total;
    }

    public double missRate() {
        long total = requestCount();
        return total == 0 ? 0.0 : (double) missCount() / total;
    }

    public double averageLoadPenaltyNanos() {
        long total = loadCount();
        return total == 0 ? 0.0 : (double) totalLoadTimeNanos() / total;
    }

    @Override
    public String toString() {
        return "CacheStats{" +
                "hits=" + hitCount() +
                ", misses=" + missCount() +
                ", evictions=" + evictionCount() +
                ", expirations=" + expirationCount() +
                ", loads=" + loadCount() +
                ", loadSuccesses=" + loadSuccessCount() +
                ", loadFailures=" + loadFailureCount() +
                ", hitRate=" + String.format("%.2f", hitRate()) +
                '}';
    }
}