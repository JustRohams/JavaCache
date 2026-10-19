package io.github.justrohams.javacache.eviction;

import java.util.HashMap;
import java.util.Map;

public final class LfuEvictionPolicy<K> implements EvictionPolicy<K> {

    private final Map<K, Long> frequencies = new HashMap<>();

    @Override
    public void recordInsertion(K key) {
        frequencies.putIfAbsent(key, 0L);
    }

    @Override
    public void recordAccess(K key) {
        frequencies.merge(key, 1L, Long::sum);
    }

    @Override
    public void recordRemoval(K key) {
        frequencies.remove(key);
    }

    @Override
    public K selectEvictionCandidate() {
        K minKey = null;
        long minFreq = Long.MAX_VALUE;
        for (Map.Entry<K, Long> e : frequencies.entrySet()) {
            if (e.getValue() < minFreq) {
                minFreq = e.getValue();
                minKey = e.getKey();
            }
        }
        return minKey;
    }

    @Override
    public void clear() {
        frequencies.clear();
    }

    @Override
    public int size() {
        return frequencies.size();
    }
}