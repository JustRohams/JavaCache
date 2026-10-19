package io.github.justrohams.javacache;

final class CacheEntry<V> {

    private final V value;
    private final long expiresAtNanos;

    CacheEntry(V value, long expiresAtNanos) {
        this.value = value;
        this.expiresAtNanos = expiresAtNanos;
    }

    V value() {
        return value;
    }

    long expiresAtNanos() {
        return expiresAtNanos;
    }

    boolean isExpired() {
        return expiresAtNanos > 0 && System.nanoTime() >= expiresAtNanos;
    }
}