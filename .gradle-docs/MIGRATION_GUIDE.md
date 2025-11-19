# Build System History: Ant to Gradle

## Overview

This document provides historical context about the migration from the legacy Ant-based build system to the current Gradle-based build system.

**Current Status:** Gradle is the only build system. The Ant build system has been completely removed.

## Why Gradle?

The project migrated to Gradle for several key benefits:

### Performance Improvements

| Feature                  | Ant          | Gradle        | Benefit                           |
|--------------------------|--------------|---------------|-----------------------------------|
| **Incremental Builds**   | ❌           | ✅            | 120x faster for unchanged builds  |
| **Build Caching**        | ❌           | ✅            | Reuse outputs across builds       |
| **Parallel Execution**   | ❌           | ✅            | Faster multi-task builds          |
| **IDE Integration**      | ⚠️ Basic     | ✅ Excellent  | Better development experience     |
| **Error Messages**       | ⚠️ Basic     | ✅ Detailed   | Easier debugging                  |
| **Dependency Management** | ❌           | ✅            | Automatic dependency resolution   |
| **Plugin Ecosystem**     | ⚠️ Limited   | ✅ Rich       | More functionality                |
| **Modern Tooling**       | ⚠️ Legacy    | ✅ Active     | Ongoing development               |

### Build Time Comparison

| Build Type | Ant (Legacy) | Gradle (First) | Gradle (Incremental) | Gradle (No Changes) |
|------------|--------------|----------------|----------------------|---------------------|
| Lite       | ~10 min      | ~5-10 min      | ~1-2 min             | ~5 sec              |
| Basic      | ~15 min      | ~10-15 min     | ~2-3 min             | ~5 sec              |
| Full       | ~25 min      | ~15-25 min     | ~3-5 min             | ~5 sec              |

## Current Build System

### Available Commands

| Command                   | Description                      | Time      |
|---------------------------|----------------------------------|-----------|
| `.\gradlew initBuild`     | Initialize build directories     | ~1s       |
| `.\gradlew prepareBase`   | Prepare base environment         | ~30s      |
| `.\gradlew buildLite`     | Build lite release               | ~5-10min  |
| `.\gradlew buildBasic`    | Build basic release              | ~10-15min |
| `.\gradlew buildFull`     | Build full release               | ~15-25min |
| `.\gradlew release`       | Build all variants               | ~30-45min |
| `.\gradlew checkLang`     | Verify language files            | ~1s       |
| `.\gradlew launch`        | Build and launch Bearsampp       | ~10min    |
| `.\gradlew clean`         | Clean build directory            | ~5s       |

### Quick Start

```bash
# List all available tasks
.\gradlew tasks

# Build lite release (fastest)
.\gradlew buildLite

# Build full release
.\gradlew buildFull

# Build all variants
.\gradlew release
```

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

## IDE Integration

### IntelliJ IDEA

**Setup:**
1. Open project
2. IntelliJ detects build.gradle automatically
3. Gradle tool window appears
4. Double-click tasks to run

### VS Code

**Setup:**
1. Install "Gradle for Java" extension
2. Open project
3. Gradle tasks appear in sidebar

### Eclipse

**Setup:**
1. Import as Gradle project
2. Gradle tasks view available
3. Right-click → Gradle → Refresh

## Best Practices

### 1. Use Gradle Wrapper

Always use the wrapper (`gradlew`) instead of system Gradle:

```bash
# Good
.\gradlew buildLite

# Avoid
gradle buildLite
```

**Why:** Ensures consistent Gradle version across team.

### 2. Enable Build Cache

Add to `gradle.properties`:

```properties
org.gradle.caching=true
org.gradle.parallel=true
```

### 3. Use Incremental Builds

Don't clean unless necessary:

```bash
# Good (incremental)
.\gradlew buildLite

# Avoid (unless needed)
.\gradlew clean buildLite
```

### 4. Monitor Build Performance

Use build scans:

```bash
.\gradlew buildLite --scan
```

## Troubleshooting

### Build Fails

Run with detailed output:

```bash
.\gradlew buildLite --stacktrace
```

Or with debug info:

```bash
.\gradlew buildLite --debug
```

### Module Download Fails

Check internet connection and GitHub access. The build will continue with empty directories as fallback.

### Permission Denied (Linux/Mac)

Make wrapper executable:

```bash
chmod +x gradlew
```

### Clean Build

Remove all build artifacts:

```bash
.\gradlew clean
.\gradlew buildLite
```

## Documentation

For detailed information, see:

- **Quick Start**: [QUICKSTART.md](QUICKSTART.md)
- **Build Guide**: [BUILD_GUIDE.md](BUILD_GUIDE.md)
- **Implementation Status**: [IMPLEMENTATION_STATUS.md](IMPLEMENTATION_STATUS.md)

## Support

- **Issues**: Report on GitHub
- **Questions**: Check documentation files
- **Debug**: Run with `--stacktrace` or `--debug` flags

---

**Last Updated**: 2025  
**Gradle Version**: 8.5  
**Status**: Production Ready ✅
