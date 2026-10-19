package io.github.justrohams.javacache.eviction;

public interface EvictionPolicy<K> {

    void recordInsertion(K key);

    void recordAccess(K key);

    void recordRemoval(K key);

    K selectEvictionCandidate();

    void clear();

    int size();
}