package io.github.justrohams.javacache;

@FunctionalInterface
public interface CacheLoader<K, V> {
    V load(K key) throws Exception;
}