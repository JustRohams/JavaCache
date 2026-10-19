# Changelog

All notable changes to this project will be documented in this file.

The format is based on [Keep a Changelog](https://keepachangelog.com/en/1.1.0/),
and this project adheres to [Semantic Versioning](https://semver.org/spec/v2.0.0.html).

## [1.0.0] - 2026-10-19

### Added
- Initial public release.
- `Cache<K, V>` interface and `CacheBuilder` fluent API.
- `ConcurrentCache` implementation backed by `ConcurrentHashMap`.
- Per-entry and default TTL support.
- Eviction policies: LRU, LFU, FIFO (extensible `EvictionPolicy` SPI).
- Single-flight loading via `get(K, CacheLoader)`.
- `CacheStats` with hit/miss/eviction/load-penalty metrics.
- `CacheEventListener` with `RemovalCause` notifications.
- Optional background expiration cleanup on a shared daemon scheduler.
- Full JUnit 5 test suite and GitHub Actions CI.