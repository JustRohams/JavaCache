package io.github.justrohams.javacache.eviction;

import java.util.Iterator;
import java.util.LinkedHashSet;

public final class FifoEvictionPolicy<K> implements EvictionPolicy<K> {

    private final LinkedHashSet<K> order = new LinkedHashSet<>();

    @Override
    public void recordInsertion(K key) {
        order.add(key);
    }

    @Override
    public void recordAccess(K key) {
    }

    @Override
    public void recordRemoval(K key) {
        order.remove(key);
    }

    @Override
    public K selectEvictionCandidate() {
        Iterator<K> it = order.iterator();
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