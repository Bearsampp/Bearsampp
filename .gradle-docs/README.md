# Bearsampp - Gradle Build Documentation

## Table of Contents

- [Overview](#overview)
- [Quick Start](#quick-start)
- [Installation](#installation)
- [Build Tasks](#build-tasks)
- [Configuration](#configuration)
- [Architecture](#architecture)
- [Troubleshooting](#troubleshooting)
- [Documentation Index](#documentation-index)

---

## Overview

The Bearsampp project uses **Gradle** as its build system, replacing the legacy Ant build configuration. This provides:

- **Modern Build System**     - Native Gradle tasks and conventions
- **Better Performance**       - Incremental builds and caching
- **Simplified Maintenance**   - Pure Groovy/Gradle DSL
- **Enhanced Tooling**         - IDE integration and dependency management
- **Cross-Platform Support**   - Works on Windows, Linux, and macOS

### Project Information

| Property          | Value                                    |
|-------------------|------------------------------------------|
| **Project Name**  | Bearsampp                                |
| **Type**          | WAMP Stack Builder                       |
| **Build Tool**    | Gradle 8.x+                              |
| **Language**      | Groovy (Gradle DSL)                      |

---

## Quick Start

### Prerequisites

| Requirement       | Version       | Purpose                                  |
|-------------------|---------------|------------------------------------------|
| **Java**          | 8+            | Required for Gradle execution            |
| **Gradle**        | 8.5+          | Build automation tool (included via wrapper) |
| **7-Zip**         | Latest        | Archive compression (in dev/tools/7zip/) |
| **rcedit-x64.exe**| Latest        | Executable version updates               |
| **ResourceHacker**| Latest        | Executable icon updates                  |

### Basic Commands

```bash
# List all available tasks
.\gradlew tasks

# Build lite release (fastest)
.\gradlew buildLite

# Build basic release
.\gradlew buildBasic

# Build full release
.\gradlew buildFull

# Build all variants
.\gradlew release

# Clean build artifacts
.\gradlew clean

# Launch Bearsampp
.\gradlew launch
```

---

## Installation

### 1. Verify Environment

```bash
# Check Java version
java -version

# Check Gradle wrapper
.\gradlew --version
```

### 2. List Available Tasks

```bash
.\gradlew tasks
```

### 3. Build Your First Release

```bash
# Build lite release (fastest, ~5-10 minutes)
.\gradlew buildLite

# Or build full release (~15-25 minutes)
.\gradlew buildFull
```

---

## Build Tasks

### Core Build Tasks

| Task                  | Description                                      | Build Time    |
|-----------------------|--------------------------------------------------|---------------|
| `buildLite`           | Build lite release (minimal modules)             | ~5-10 min     |
| `buildBasic`          | Build basic release (essential modules)          | ~10-15 min    |
| `buildFull`           | Build full release (all modules)                 | ~15-25 min    |
| `release`             | Build all variants                               | ~30-45 min    |
| `clean`               | Clean build artifacts and temporary files        | ~5 sec        |

### Setup Tasks

| Task                  | Description                                      |
|-----------------------|--------------------------------------------------|
| `initBuild`           | Initialize build directories                     |
| `prepareBase`         | Prepare base environment                         |

### Verification Tasks

| Task                  | Description                                      |
|-----------------------|--------------------------------------------------|
| `checkLang`           | Verify language files                            |

### Application Tasks

| Task                  | Description                                      |
|-----------------------|--------------------------------------------------|
| `launch`              | Build and launch Bearsampp                       |
| `sync`                | Sync build to sandbox (interactive)              |

For complete task reference, see [TASKS.md](TASKS.md)

---

## Configuration

### build.properties

The main configuration file for the build:

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

For complete configuration reference, see [CONFIGURATION.md](CONFIGURATION.md)

### gradle.properties

Gradle-specific configuration (optional, create if needed):

```properties
# Gradle daemon configuration
org.gradle.daemon=true
org.gradle.parallel=true
org.gradle.caching=true

# JVM settings
org.gradle.jvmargs=-Xmx4g -XX:MaxMetaspaceSize=512m
```

### Directory Structure

```
Bearsampp/
├── .gradle-docs/          # Gradle documentation
│   ├── README.md          # This file
│   ├── QUICKSTART.md      # Quick start guide
│   ├── BUILD_GUIDE.md     # Comprehensive build guide
│   ├── TASKS.md           # Task reference
│   ├── CONFIGURATION.md   # Configuration guide
│   ├── API.md             # API reference
│   ├── MIGRATION_GUIDE.md # Migration guide
│   ├── IMPLEMENTATION_STATUS.md # Implementation status
│   └── INDEX.md           # Documentation index
├── base/                  # Base Bearsampp files
├── core/                  # Core PHP classes and scripts
├── bin/                   # Build output directory
│   ├── release/           # Final release archives
│   └── tmp/               # Temporary build files
├── build.gradle           # Main Gradle build script
├── settings.gradle        # Gradle settings
└── build.properties       # Build configuration
```

---

## Architecture

### Build Process Flow

```
1. User runs: .\gradlew buildFull
                    ↓
2. Initialize build environment (initBuild)
                    ↓
3. Prepare base environment (prepareBase)
   - Download iconography
   - Update executable version/icon
   - Copy core files
   - Apply token replacement
                    ↓
4. Download modules from GitHub
   - Apache, PHP, MySQL, etc.
   - Cache in bin/tmp/getmodule/
                    ↓
5. Copy modules to release directory
                    ↓
6. Compress release directory
   - 7-Zip with maximum compression
   - Generate checksums (MD5, SHA-1, SHA-256, SHA-512)
                    ↓
7. Output final archives to bin/release/
```

For detailed architecture information, see [BUILD_GUIDE.md](BUILD_GUIDE.md)

---

## Troubleshooting

### Common Issues

#### Issue: "Module download fails"

**Symptom:**
```
ERROR: Failed to download module
WARNING: Created empty directory as fallback
```

**Solution:**
- Check internet connection
- Verify GitHub access
- Build continues with empty directories (expected behavior)

---

#### Issue: "7-Zip not found"

**Symptom:**
```
7-Zip not found at dev/tools/7zip/7za.exe
```

**Solution:**
- Verify dev directory path in build.properties
- Ensure 7-Zip is installed in dev/tools/7zip/

---

#### Issue: "Java version too old"

**Symptom:**
```
Unsupported class file major version
```

**Solution:**
```bash
# Check Java version
java -version

# Requires Java 8+
# Update JAVA_HOME if needed
```

---

### Debug Mode

Run Gradle with debug output:

```bash
.\gradlew buildLite --info
.\gradlew buildLite --debug
.\gradlew buildLite --stacktrace
```

### Clean Build

If you encounter issues, try a clean build:

```bash
.\gradlew clean
.\gradlew buildLite
```

For more troubleshooting information, see [BUILD_GUIDE.md](BUILD_GUIDE.md#troubleshooting)

---

## Documentation Index

### Complete Documentation

| Document              | Description                                      |
|-----------------------|--------------------------------------------------|
| **[INDEX.md](INDEX.md)** | Complete documentation index                  |
| **[QUICKSTART.md](QUICKSTART.md)** | Quick start guide                    |
| **[BUILD_GUIDE.md](BUILD_GUIDE.md)** | Comprehensive build guide          |
| **[TASKS.md](TASKS.md)** | Complete task reference                      |
| **[CONFIGURATION.md](CONFIGURATION.md)** | Configuration guide              |
| **[API.md](API.md)** | Build script API reference                       |
| **[MIGRATION_GUIDE.md](MIGRATION_GUIDE.md)** | Ant to Gradle migration    |
| **[IMPLEMENTATION_STATUS.md](IMPLEMENTATION_STATUS.md)** | Implementation status |

### Quick Links

#### Getting Started
- [Installation](#installation)
- [First Build](#quick-start)
- [Available Tasks](#build-tasks)

#### Common Tasks
- [Build Lite Release](QUICKSTART.md#build-lite-release)
- [Build Full Release](QUICKSTART.md#build-full-release)
- [Build All Variants](QUICKSTART.md#build-all-variants)

#### Advanced Topics
- [Module Downloads](BUILD_GUIDE.md#module-download-system)
- [Compression & Checksums](BUILD_GUIDE.md#compression-and-checksums)
- [Sync Functionality](BUILD_GUIDE.md#sync-task)
- [Token Replacement](CONFIGURATION.md#token-replacement)
- [API Reference](API.md)

---

## Support

For issues and questions:

- **GitHub Issues**: https://github.com/bearsampp/bearsampp/issues
- **Documentation**: This directory (.gradle-docs/)
- **Website**: https://bearsampp.com

## Additional Resources

- [Gradle Documentation](https://docs.gradle.org/)
- [Bearsampp Project](https://github.com/bearsampp/bearsampp)
- [Bearsampp Website](https://bearsampp.com)

---

**Last Updated**: 2025  
**Version**: 2025.5.6  
**Gradle Version**: 8.5  
**Status**: Production Ready ✅
