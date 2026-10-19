package io.github.justrohams.javacache;

import java.util.Objects;

public final class RemovalNotification<K, V> {

    private final K key;
    private final V value;
    private final RemovalCause cause;

    public RemovalNotification(K key, V value, RemovalCause cause) {
        this.key = Objects.requireNonNull(key, "key");
        this.value = Objects.requireNonNull(value, "value");
        this.cause = Objects.requireNonNull(cause, "cause");
    }

    public K key()             { return key; }
    public V value()           { return value; }
    public RemovalCause cause() { return cause; }

    @Override
    public String toString() {
        return "RemovalNotification{" +
                "key=" + key +
                ", cause=" + cause +
                '}';
    }
}