# JavaCache

> A lightweight, high-performance, thread-safe in-memory cache library for Java.

[![CI](https://github.com/JustRohams/JavaCache/actions/workflows/ci.yml/badge.svg)](https://github.com/JustRohams/JavaCache/actions/workflows/ci.yml)
[![License: MIT](https://img.shields.io/badge/License-MIT-yellow.svg)](https://opensource.org/licenses/MIT)
[![Java](https://img.shields.io/badge/Java-17%2B-blue.svg)](https://www.oracle.com/java/)
[![Maven Central](https://img.shields.io/badge/Maven-1.0.0-orange.svg)](https://github.com/JustRohams/JavaCache)

**JavaCache** is a modern, dependency-free in-memory cache for Java, designed for simplicity and speed. It ships with TTL expiration, pluggable eviction policies (LRU / LFU / FIFO), single-flight loading, and built-in statistics.

---

## ✨ Features

- ⚡ **Fast** — backed by `ConcurrentHashMap`, lock-free reads.
- 🧵 **Thread-safe** — safe for concurrent use in highly parallel applications.
- ⏱ **TTL support** — per-entry and cache-wide time-to-live.
- 🗑 **Pluggable eviction** — LRU, LFU, and FIFO policies built-in (extensible SPI).
- 🎯 **Single-flight loading** — prevents cache stampedes on concurrent misses.
- 📊 **Statistics** — hit/miss rate, evictions, load penalties.
- 🔔 **Event listeners** — get notified on removals (explicit, expired, size, replaced).
- 🧹 **Background cleanup** — automatic daemon-based expiration sweeper.
- 🪶 **Zero dependencies** — pure JDK, no external jars.

---

## 📦 Installation

### Maven

```xml
<dependency>
    <groupId>io.github.justrohams</groupId>
    <artifactId>javacache</artifactId>
    <version>1.0.0</version>
</dependency>
```

### Gradle

```groovy
implementation 'io.github.justrohams:javacache:1.0.0'
```

### Manual

Clone the repo and build:

```bash
git clone https://github.com/JustRohams/JavaCache.git
cd JavaCache
mvn clean package
```

---

## 🚀 Quick Start

```java
import io.github.justrohams.javacache.Cache;
import io.github.justrohams.javacache.CacheBuilder;
import java.time.Duration;

Cache<String, User> cache = CacheBuilder.<String, User>newBuilder()
        .maximumSize(10_000)
        .expireAfterWrite(Duration.ofMinutes(10))
        .build();

cache.put("user:1", new User("Alice"));
User user = cache.get("user:1"); // or null
```

### With single-flight loading

```java
User user = cache.get("user:1", key -> userRepository.findById(key));
```

If two threads request `"user:1"` at the same time and it's not cached,
the loader runs **only once**; the second thread waits for the first result.

---

## ⚙️ Configuration

| Builder option                    | Default              | Description                                    |
|-----------------------------------|----------------------|------------------------------------------------|
| `maximumSize(long)`               | `10_000`             | Maximum number of cached entries.              |
| `expireAfterWrite(Duration)`      | none                 | Default TTL applied to entries without one.    |
| `evictionPolicy(EvictionPolicy)`  | `LruEvictionPolicy`  | Strategy used when maximumSize is exceeded.    |
| `listener(CacheEventListener)`    | none                 | Callback invoked on every removal.             |
| `enableBackgroundCleanup(boolean)`| `true`               | Background thread for expiration.              |
| `cleanupInterval(Duration)`       | `Duration.ofMinutes(1)` | Interval for background cleanup.            |

### Eviction policies

```java
CacheBuilder.<String, User>newBuilder()
        .evictionPolicy(new LfuEvictionPolicy<>())
        .maximumSize(1000)
        .build();
```

Available: `LruEvictionPolicy`, `LfuEvictionPolicy`, `FifoEvictionPolicy`.
Implement `EvictionPolicy<K>` to plug in your own.

---

## 📊 Statistics

```java
CacheStats stats = cache.stats();

System.out.println(stats.hitRate());          // 0.94
System.out.println(stats.missCount());        // 61
System.out.println(stats.evictionCount());    // 12
System.out.println(stats.averageLoadPenaltyNanos());
```

---

## 🔔 Event Listeners

```java
CacheBuilder.<String, User>newBuilder()
        .listener(notification -> {
            System.out.printf("Removed %s (%s)%n",
                notification.key(), notification.cause());
        })
        .build();
```

`RemovalCause` values: `EXPLICIT`, `REPLACED`, `EXPIRED`, `SIZE`.

---

## 🧪 Running Tests

```bash
mvn test
```

---

## 🤝 Contributing

Contributions are welcome! Please read [CONTRIBUTING.md](CONTRIBUTING.md) first.

---

## 📄 License

This project is licensed under the MIT License — see [LICENSE](LICENSE).

---

## 👤 Author

**RohaM**
- GitHub: [@JustRohams](https://github.com/JustRohams)

---

## 🌍 نسخه فارسی

**JavaCache** یک کتابخانه کش سبک، سریع و thread-safe برای زبان جاواست که بدون هیچ وابستگی خارجی طراحی شده. کافیه با Maven/Gradle به پروژه‌ت اضافه کنی و از قابلیت‌هایی مثل TTL، سیاست‌های LRU/LFU/FIFO، single-flight loading و آمار hit/miss استفاده کنی.

**مشارکت:** خوشحال می‌شیم اگه ایده، issue یا PR داری! 🚀