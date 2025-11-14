# Bearsampp Gradle Build System Documentation

This directory contains comprehensive documentation for the Bearsampp Gradle build system.

## Documentation Files

1. **[QUICKSTART.md](QUICKSTART.md)** - Quick start guide for common tasks
2. **[BUILD_GUIDE.md](BUILD_GUIDE.md)** - Comprehensive build system documentation  
3. **[MIGRATION_GUIDE.md](MIGRATION_GUIDE.md)** - Migration from Ant to Gradle
4. **[IMPLEMENTATION_STATUS.md](IMPLEMENTATION_STATUS.md)** - Current implementation status

## Quick Links

### Getting Started
- [Installation](BUILD_GUIDE.md#installation)
- [First Build](QUICKSTART.md#quick-start)
- [Available Tasks](BUILD_GUIDE.md#available-tasks)

### Common Tasks
- [Build Full Release](QUICKSTART.md#build-full-release)
- [Build Lite Release](QUICKSTART.md#build-lite-release)
- [Build All Variants](QUICKSTART.md#build-all-variants)

### Advanced Topics
- [Module Downloads](BUILD_GUIDE.md#module-download-system)
- [Compression & Checksums](BUILD_GUIDE.md#compression-and-checksums)
- [Sync Functionality](BUILD_GUIDE.md#sync-task)

## Overview

The Gradle build system is a modern replacement for the Ant-based build system, providing:

- ✅ **Complete feature parity** with Ant build
- ✅ **Module download system** from GitHub releases
- ✅ **7-Zip compression** with multiple formats
- ✅ **Checksum generation** (MD5, SHA-1, SHA-256, SHA-512)
- ✅ **Sync functionality** with user prompts
- ✅ **Incremental builds** for faster rebuilds
- ✅ **Better IDE integration** (IntelliJ, Eclipse, VS Code)

## System Requirements

- **Java**: JDK 8 or higher
- **Gradle**: 8.5 (included via wrapper)
- **7-Zip**: Required for compression (in dev/tools/7zip/)
- **rcedit-x64.exe**: Required for version updates
- **ResourceHacker.exe**: Required for icon updates

## Quick Start

```bash
# List all available tasks
.\gradlew tasks

# Build lite release (fastest)
.\gradlew buildLite

# Build full release (all modules)
.\gradlew buildFull

# Build all variants
.\gradlew release
```

## Build Variants

| Variant  | Command      | Components                                      | Use Case                   |
|----------|--------------|------------------------------------------------|----------------------------|
| **Lite** | `buildLite`  | Apache, PHP, MySQL, Mailpit, phpMyAdmin        | Development, testing       |
| **Basic** | `buildBasic` | + MariaDB, Node.js, Xlight                     | Standard development       |
| **Full** | `buildFull`  | + PostgreSQL, Memcached, all tools             | Production, complete setup |

## Key Features

### 1. Automatic Module Downloads
Downloads modules from GitHub releases using `releases.properties` files in each module repository.

### 2. Smart Caching
- Downloads are cached in `bin/tmp/getmodule/`
- Reuses cached files on subsequent builds
- Significantly faster rebuilds

### 3. Resilient Error Handling
- Continues build if module download fails
- Creates empty directories as fallback
- Logs warnings for missing modules

### 4. Compression Support
- 7-Zip (.7z) with maximum compression
- ZIP format support
- Configurable via `build.properties`

### 5. Checksum Generation
- Automatic generation for all archives
- MD5, SHA-1, SHA-256, SHA-512
- Separate files for each algorithm

## Configuration

All configuration is in `build.properties`:

```properties
# Release version
release.default.version=2025.5.6

# Archive format (7z, zip, or all)
release.format=7z

# Module versions
bin.apache.version=2.4.63
bin.php.version=8.4.6
bin.mysql.version=9.3.0
# ... and more
```

## Support

- **Issues**: Report on GitHub
- **Questions**: Check documentation files
- **Debug**: Run with `--stacktrace` or `--debug` flags

## License

Same as Bearsampp project license.

---

**Last Updated**: 2025
**Gradle Version**: 8.5
**Status**: Production Ready ✅
