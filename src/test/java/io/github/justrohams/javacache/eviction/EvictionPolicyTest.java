package io.github.justrohams.javacache.eviction;

import org.junit.jupiter.api.Test;

import static org.junit.jupiter.api.Assertions.*;

class EvictionPolicyTest {

    @Test
    void lruEvictsLeastRecentlyUsed() {
        LruEvictionPolicy<String> p = new LruEvictionPolicy<>();
        p.recordInsertion("a");
        p.recordInsertion("b");
        p.recordInsertion("c");
        p.recordAccess("a");
        assertEquals("b", p.selectEvictionCandidate());
    }

    @Test
    void fifoEvictsOldest() {
        FifoEvictionPolicy<String> p = new FifoEvictionPolicy<>();
        p.recordInsertion("a");
        p.recordInsertion("b");
        p.recordAccess("a"); 
        assertEquals("a", p.selectEvictionCandidate());
    }

    @Test
    void lfuEvictsLeastFrequent() {
        LfuEvictionPolicy<String> p = new LfuEvictionPolicy<>();
        p.recordInsertion("a");
        p.recordInsertion("b");
        p.recordAccess("b");
        p.recordAccess("b");
        assertEquals("a", p.selectEvictionCandidate());
    }

    @Test
    void emptyPolicyReturnsNull() {
        assertNull(new LruEvictionPolicy<String>().selectEvictionCandidate());
        assertNull(new LfuEvictionPolicy<String>().selectEvictionCandidate());
        assertNull(new FifoEvictionPolicy<String>().selectEvictionCandidate());
    }
}