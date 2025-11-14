# Bearsampp Gradle Build System - Complete Guide

## Table of Contents

1. [Overview](#overview)
2. [Installation](#installation)
3. [Available Tasks](#available-tasks)
4. [Build Variants](#build-variants)
5. [Module Download System](#module-download-system)
6. [Compression and Checksums](#compression-and-checksums)
7. [Sync Task](#sync-task)
8. [Configuration](#configuration)
9. [Advanced Features](#advanced-features)
10. [Troubleshooting](#troubleshooting)

## Overview

The Gradle build system for Bearsampp provides a modern, efficient alternative to the Ant-based build system. It offers complete feature parity with the original build.xml while adding improvements like incremental builds, better caching, and enhanced error handling.

### Key Features

- ✅ **Automatic Module Downloads** from GitHub releases
- ✅ **7-Zip Compression** with maximum compression settings
- ✅ **Checksum Generation** (MD5, SHA-1, SHA-256, SHA-512)
- ✅ **Sync Functionality** with interactive prompts
- ✅ **Incremental Builds** for faster rebuilds
- ✅ **Smart Caching** of downloaded modules
- ✅ **Resilient Error Handling** continues on failures
- ✅ **Token Replacement** for configuration files
- ✅ **Version Management** via build.properties

## Installation

### Prerequisites

- **Java JDK 8+**: Required for Gradle
- **7-Zip**: Must be available in `dev/tools/7zip/7za.exe`
- **rcedit-x64.exe**: For executable version updates
- **ResourceHacker.exe**: For icon updates

### Verify Installation

```bash
# Check Java version
java -version

# Check Gradle wrapper
.\gradlew --version

# List available tasks
.\gradlew tasks
```

## Available Tasks

### Build Setup Tasks

#### `initBuild`
Initializes the build environment.

```bash
.\gradlew initBuild
```

**Actions:**
- Creates build directories (`bin/`, `bin/release/`, `bin/tmp/`)
- Verifies dev directory exists
- Sets up build environment

**Dependencies:** None

---

#### `prepareBase`
Prepares the base Bearsampp environment.

```bash
.\gradlew prepareBase
```

**Actions:**
- Downloads Bearsampp iconography
- Updates executable version with rcedit-x64.exe
- Updates executable icon with ResourceHacker.exe
- Copies core files (excluding build files, .gradle, etc.)
- Downloads openssl.cfg if missing
- Copies base directory structure
- Applies token filters to configuration files
- Generates version.dat files

**Dependencies:** `initBuild`

**Output:** `bin/tmp/release/base/`

---

### Build Tasks

#### `buildFull`
Builds the full release with all modules.

```bash
.\gradlew buildFull
```

**Includes:**
- **Binaries:** Apache, PHP, MySQL, MariaDB, PostgreSQL, Node.js, Xlight, Mailpit, Memcached
- **Applications:** phpMyAdmin, phpPgAdmin
- **Tools:** Bruno, Composer, ConsoleZ, Ghostscript, Git, Ngrok, Perl, Python, Ruby

**Dependencies:** `initBuild`, `prepareBase`

**Output:** `bin/release/Bearsampp-{version}.7z` (+ checksums)

---

#### `buildBasic`
Builds the basic release with essential modules.

```bash
.\gradlew buildBasic
```

**Includes:**
- **Binaries:** Apache, PHP, MySQL, MariaDB, Node.js, Xlight, Mailpit
- **Applications:** phpMyAdmin
- **Tools:** Bruno, Composer, ConsoleZ, Git, Ngrok, Perl

**Excludes:** PostgreSQL, Memcached, phpPgAdmin, Ghostscript, Python, Ruby

**Dependencies:** `initBuild`, `prepareBase`

**Output:** `bin/release/Bearsampp-basic-{version}.7z` (+ checksums)

---

#### `buildLite`
Builds the lite release with minimal modules.

```bash
.\gradlew buildLite
```

**Includes:**
- **Binaries:** Apache, PHP, MySQL, Node.js, Mailpit
- **Applications:** phpMyAdmin
- **Tools:** Bruno, Composer, ConsoleZ

**Excludes:** MariaDB, PostgreSQL, Xlight, Memcached, phpPgAdmin, most tools

**Dependencies:** `initBuild`, `prepareBase`

**Output:** `bin/release/Bearsampp-lite-{version}.7z` (+ checksums)

---

#### `release`
Builds all release variants.

```bash
.\gradlew release
```

**Actions:**
- Runs `buildFull`, `buildBasic`, `buildLite`
- Runs `sync` task (with user prompts)

**Dependencies:** `initBuild`, `prepareBase`, `buildFull`, `buildBasic`, `buildLite`, `sync`

**Output:** All three release archives with checksums

---

### Verification Tasks

#### `checkLang`
Verifies language files.

```bash
.\gradlew checkLang
```

**Actions:**
- Scans `core/langs/` directory
- Lists all `.lang` files found

**Dependencies:** `initBuild`

---

### Application Tasks

#### `launch`
Builds and launches Bearsampp.

```bash
.\gradlew launch
```

**Actions:**
- Builds full release
- Copies to `bin/launch/`
- Executes `bearsampp.exe`

**Dependencies:** `buildFull`

---

#### `sync`
Syncs build to sandbox with interactive prompts.

```bash
.\gradlew sync
```

**Actions:**
1. Prompts user for sync confirmation
2. Prompts for sync path (default: `bearsampp-destination/`)
3. Downloads Sandbox iconography
4. Copies base core files
5. Updates executable version
6. Updates executable icon
7. Copies to sync path

**Dependencies:** `buildFull`

**Interactive:** Yes (requires user input)

---

## Build Variants

### Comparison Table

| Component        | Lite | Basic | Full |
|------------------|------|-------|------|
| **Binaries**     |      |       |      |
| Apache           | ✅   | ✅    | ✅   |
| PHP              | ✅   | ✅    | ✅   |
| MySQL            | ✅   | ✅    | ✅   |
| MariaDB          | ❌   | ✅    | ✅   |
| PostgreSQL       | ❌   | ❌    | ✅   |
| Node.js          | ✅   | ✅    | ✅   |
| Xlight           | ❌   | ✅    | ✅   |
| Mailpit          | ✅   | ✅    | ✅   |
| Memcached        | ❌   | ❌    | ✅   |
| **Applications** |      |       |      |
| phpMyAdmin       | ✅   | ✅    | ✅   |
| phpPgAdmin       | ❌   | ❌    | ✅   |
| **Tools**        |      |       |      |
| Bruno            | ✅   | ✅    | ✅   |
| Composer         | ✅   | ✅    | ✅   |
| ConsoleZ         | ✅   | ✅    | ✅   |
| Git              | ❌   | ✅    | ✅   |
| Ngrok            | ❌   | ✅    | ✅   |
| Perl             | ❌   | ✅    | ✅   |
| Python           | ❌   | ❌    | ✅   |
| Ruby             | ❌   | ❌    | ✅   |
| Ghostscript      | ❌   | ❌    | ✅   |

### Configuration Tokens

Each variant applies different configuration tokens:

**Full Build:**
```groovy
BIN_APACHE_ENABLE = 1
BIN_PHP_ENABLE = 1
BIN_MYSQL_ENABLE = 1
BIN_MARIADB_ENABLE = 1
BIN_POSTGRESQL_ENABLE = 1
BIN_NODEJS_ENABLE = 1
BIN_XLIGHT_ENABLE = 1
BIN_MAILPIT_ENABLE = 1
BIN_MEMCACHED_ENABLE = 1
```

**Basic Build:**
```groovy
BIN_POSTGRESQL_ENABLE = 0
BIN_MEMCACHED_ENABLE = 0
```

**Lite Build:**
```groovy
BIN_MARIADB_ENABLE = 0
BIN_POSTGRESQL_ENABLE = 0
BIN_NODEJS_ENABLE = 0
BIN_XLIGHT_ENABLE = 0
BIN_MEMCACHED_ENABLE = 0
```

## Module Download System

### How It Works

1. **Fetch releases.properties**
   - Downloads from repository root (main branch)
   - URL: `https://raw.githubusercontent.com/{org}/{repo}/main/releases.properties`

2. **Parse Version**
   - Looks up version in properties file
   - Gets download URL for that version

3. **Download Module**
   - Downloads archive (.7z or .zip)
   - Caches in `bin/tmp/getmodule/`

4. **Extract Module**
   - Extracts to destination directory
   - Uses 7-Zip for .7z files
   - Uses Ant unzip for .zip files

### Example

```groovy
downloadModule(
    'https://github.com/Bearsampp/module-apache/releases',
    '2.4.63',
    file("${releaseTarget}/bin/apache")
)
```

**Process:**
1. Converts URL: `github.com` → `raw.githubusercontent.com`
2. Fetches: `https://raw.githubusercontent.com/Bearsampp/module-apache/main/releases.properties`
3. Reads property: `2.4.63=https://github.com/.../apache-2.4.63.7z`
4. Downloads to: `bin/tmp/getmodule/apache-2.4.63.7z`
5. Extracts to: `bin/tmp/release/Bearsampp-{version}/bin/apache/`

### Caching

- Downloaded files are cached in `bin/tmp/getmodule/`
- Subsequent builds reuse cached files
- Significantly faster rebuilds

### Error Handling

If download fails:
- Logs error message
- Creates empty directory as fallback
- Continues build (doesn't fail)

## Compression and Checksums

### Compression

Uses 7-Zip with maximum compression:

```groovy
7za.exe a -t7z {output}.7z {source}/* -m0=LZMA2 -mx9 -mmt6
```

**Settings:**
- `-t7z`: 7z format
- `-m0=LZMA2`: LZMA2 compression method
- `-mx9`: Maximum compression level
- `-mmt6`: Use 6 threads

**ZIP Format:**
```groovy
7za.exe a {output}.zip {source}/* -mm=Deflate -mfb=258 -mpass=15 -r
```

### Checksums

Generates 4 checksum files for each archive:

- **MD5**: `Bearsampp-{version}.7z.md5`
- **SHA-1**: `Bearsampp-{version}.7z.sha1`
- **SHA-256**: `Bearsampp-{version}.7z.sha256`
- **SHA-512**: `Bearsampp-{version}.7z.sha512`

**Format:**
```
{hash}  {filename}
```

**Example:**
```
a1b2c3d4e5f6...  Bearsampp-2025.5.6.7z
```

## Sync Task

### Interactive Prompts

1. **Sync Confirmation**
   ```
   Would you like to sync to sandbox? (y/yes or n/no):
   ```

2. **Path Confirmation**
   ```
   Current sync path is: E:\Bearsampp-development\Bearsampp\bearsampp-destination
   Is this the path you want to sync to? (y/yes or n/no):
   ```

3. **Custom Path** (if no to #2)
   ```
   Please enter the new path to sync to:
   ```

### Sync Process

1. Downloads Sandbox iconography
2. Copies base core files
3. Prepares bearsampp.exe
4. Updates version with rcedit-x64.exe
5. Updates icon with ResourceHacker.exe
6. Copies icons to sync path
7. Copies updated executable to sync path

### Non-Interactive Mode

If running in non-interactive environment (no console):
- Skips sync automatically
- Logs message: "Running in non-interactive mode. Skipping sync."

## Configuration

### build.properties

All configuration is loaded from `build.properties`:

#### Application Configuration
```properties
appconf.lang=english
appconf.timezone=Europe/Paris
appconf.notepad=notepad.exe
appconf.maxLogsArchives=5
appconf.logsVerbose=0
appconf.scriptsTimeout=60
appconf.downloadId=
appconf.IncludePR=false
```

#### Release Configuration
```properties
release.default.version=2025.5.6
release.format=7z
release.release.type=release
```

#### Binary Versions
```properties
bin.apache.version=2.4.63
bin.php.version=8.4.6
bin.mysql.version=9.3.0
bin.maria.version=11.7.2
bin.postgresql.version=17.4.1
bin.nodejs.version=23.11.0
bin.xlight.version=3.9.8.3
bin.mailpit.version=1.24.1
bin.memcached.version=1.6.33
```

#### Application Versions
```properties
app.phpmyadmin.version=5.2.2
app.phppgadmin.version=7.14.7
```

#### Tool Versions
```properties
tool.bruno.version=2.1.0
tool.composer.version=2.8.8
tool.consolez.version=1.19.0.19104
tool.ghostscript.version=10.05.0
tool.git.version=2.48.1
tool.ngrok.version=3.20.1
tool.perl.version=5.40.0.1
tool.python.version=3.13.1
tool.ruby.version=3.4.1.1
```

#### Directories
```properties
dev.dir=../dev
build.dir=bin
```

### Token Replacement

Configuration files use token replacement:

**Template:**
```ini
[apache]
version=@BIN_APACHE_VERSION@
```

**Result:**
```ini
[apache]
version=2.4.63
```

**Available Tokens:**
- `@APPCONF_*@` - Application configuration
- `@BIN_*_VERSION@` - Binary versions
- `@APP_*_VERSION@` - Application versions
- `@TOOL_*_VERSION@` - Tool versions
- `@RELEASE_VERSION@` - Release version

## Advanced Features

### Incremental Builds

Gradle tracks file changes and only rebuilds what's necessary:

```bash
# First build (full)
.\gradlew buildLite  # ~10 minutes

# No changes
.\gradlew buildLite  # ~5 seconds (UP-TO-DATE)

# Change build.properties
.\gradlew buildLite  # ~2 minutes (only affected tasks)
```

### Parallel Execution

Run tasks in parallel:

```bash
.\gradlew buildLite --parallel
```

### Build Cache

Enable build cache for even faster builds:

```bash
.\gradlew buildLite --build-cache
```

### Offline Mode

Use cached dependencies only:

```bash
.\gradlew buildLite --offline
```

### Continuous Build

Watch for changes and rebuild automatically:

```bash
.\gradlew buildLite --continuous
```

### Dry Run

See what would be executed without running:

```bash
.\gradlew buildLite --dry-run
```

## Troubleshooting

### Common Issues

#### 1. Module Download Fails

**Symptom:**
```
ERROR: Failed to download module: Can't get https://...
WARNING: Created empty directory as fallback
```

**Solutions:**
- Check internet connection
- Verify GitHub access
- Check if releases.properties exists in module repo
- Build continues with empty directories (expected behavior)

---

#### 2. 7-Zip Not Found

**Symptom:**
```
7-Zip not found at E:\Bearsampp-development\dev\tools\7zip\7za.exe
```

**Solutions:**
- Verify dev directory path in build.properties
- Ensure 7-Zip is installed in dev/tools/7zip/
- Check file permissions

---

#### 3. Java Version Issues

**Symptom:**
```
Unsupported class file major version
```

**Solutions:**
```bash
# Check Java version
java -version

# Requires Java 8+
# Update JAVA_HOME if needed
```

---

#### 4. Permission Denied (Linux/Mac)

**Symptom:**
```
Permission denied: ./gradlew
```

**Solution:**
```bash
chmod +x gradlew
```

---

#### 5. Build Fails with Lock Error

**Symptom:**
```
Could not copy file ... another process has locked a portion of the file
```

**Solution:**
- Close IDEs (IntelliJ, Eclipse, VS Code)
- Stop Gradle daemon: `.\gradlew --stop`
- Retry build

---

### Debug Commands

```bash
# Show stack trace
.\gradlew buildLite --stacktrace

# Show debug output
.\gradlew buildLite --debug

# Show info output
.\gradlew buildLite --info

# Scan for performance issues
.\gradlew buildLite --scan
```

### Clean Build

```bash
# Clean all build artifacts
.\gradlew clean

# Clean and rebuild
.\gradlew clean buildLite
```

### Stop Gradle Daemon

```bash
# Stop all Gradle daemons
.\gradlew --stop
```

## Performance Optimization

### Tips for Faster Builds

1. **Use Lite Build for Development**
   - Fastest build time (~5-10 min)
   - Includes essential components

2. **Enable Gradle Daemon** (default)
   - Keeps Gradle in memory
   - Faster subsequent builds

3. **Use Build Cache**
   ```bash
   .\gradlew buildLite --build-cache
   ```

4. **Parallel Execution**
   ```bash
   .\gradlew buildLite --parallel
   ```

5. **Increase Heap Size**
   
   Create `gradle.properties`:
   ```properties
   org.gradle.jvmargs=-Xmx4g -XX:MaxMetaspaceSize=512m
   org.gradle.parallel=true
   org.gradle.caching=true
   ```

### Build Time Comparison

| Build Type | First Build | Incremental | No Changes |
|------------|-------------|-------------|------------|
| Lite | ~10 min | ~2 min | ~5 sec |
| Basic | ~15 min | ~3 min | ~5 sec |
| Full | ~25 min | ~5 min | ~5 sec |

## License

Same as Bearsampp project license.

---

**Last Updated**: 2025  
**Gradle Version**: 8.5  
**Status**: Production Ready ✅
