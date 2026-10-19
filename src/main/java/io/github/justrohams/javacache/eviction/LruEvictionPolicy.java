package io.github.justrohams.javacache.eviction;

import java.util.Iterator;
import java.util.LinkedHashMap;

public final class LruEvictionPolicy<K> implements EvictionPolicy<K> {

    private final LinkedHashMap<K, Boolean> order = new LinkedHashMap<>(64, 0.75f, true);

    @Override
    public void recordInsertion(K key) {
        order.put(key, Boolean.TRUE);
    }

    @Override
    public void recordAccess(K key) {
        order.get(key);
    }

    @Override
    public void recordRemoval(K key) {
        order.remove(key);
    }

    @Override
    public K selectEvictionCandidate() {
        Iterator<K> it = order.keySet().iterator();
        return it.hasNext() ? it.next() : null;
    }

    @Override
    public void clear() {
        order.clear();
    }

    @Override
    public int size() {
        return order.size();
    }
}