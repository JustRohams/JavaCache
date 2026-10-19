package io.github.justrohams.javacache;

import java.time.Duration;
import java.util.Map;
import java.util.Optional;

public interface Cache<K, V> extends AutoCloseable {

    Optional<V> getIfPresent(K key);

    V get(K key);

    V get(K key, CacheLoader<? super K, ? extends V> loader);

    void put(K key, V value);

    void put(K key, V value, Duration ttl);

    Optional<V> remove(K key);

    void removeAll();

    boolean containsKey(K key);

    long size();

    void cleanUp();

    CacheStats stats();

    Map<K, V> asMap();

    @Override
    void close();
}