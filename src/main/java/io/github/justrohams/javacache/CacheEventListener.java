package io.github.justrohams.javacache;

@FunctionalInterface
public interface CacheEventListener<K, V> {
    void onRemoval(RemovalNotification<K, V> notification);
}