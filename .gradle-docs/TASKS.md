# Gradle Tasks Reference

Complete reference for all Gradle tasks in the Bearsampp build system.

---

## Table of Contents

- [Task Groups](#task-groups)
- [Build Setup Tasks](#build-setup-tasks)
- [Build Tasks](#build-tasks)
- [Verification Tasks](#verification-tasks)
- [Application Tasks](#application-tasks)
- [Task Dependencies](#task-dependencies)
- [Task Examples](#task-examples)

---

## Task Groups

| Group            | Purpose                                          |
|------------------|--------------------------------------------------|
| **build**        | Build and package tasks                          |
| **verification** | Verification and validation tasks                |
| **application**  | Application launch and sync tasks                |
| **help**         | Help and information tasks                       |

---

## Build Setup Tasks

### initBuild

**Description**: Initializes the build environment

**Command**:
```bash
.\gradlew initBuild
```

**Actions**:
- Creates build directories (`bin/`, `bin/release/`, `bin/tmp/`)
- Verifies dev directory exists
- Sets up build environment

**Dependencies**: None

**Output**: Build directory structure

**Example**:
```bash
# Initialize build environment
.\gradlew initBuild

# Output:
# > Task :initBuild
# BUILD SUCCESSFUL in 1s
```

---

### prepareBase

**Description**: Prepares the base Bearsampp environment

**Command**:
```bash
.\gradlew prepareBase
```

**Actions**:
1. Downloads Bearsampp iconography
2. Updates executable version with rcedit-x64.exe
3. Updates executable icon with ResourceHacker.exe
4. Copies core files (excluding build files, .gradle, etc.)
5. Downloads openssl.cfg if missing
6. Copies base directory structure
7. Applies token filters to configuration files
8. Generates version.dat files

**Dependencies**: `initBuild`

**Output**: `bin/tmp/release/base/`

**Example**:
```bash
# Prepare base environment
.\gradlew prepareBase

# Output:
# > Task :initBuild
# > Task :prepareBase
# BUILD SUCCESSFUL in 30s
```

---

## Build Tasks

### buildLite

**Description**: Builds the lite release with minimal modules

**Command**:
```bash
.\gradlew buildLite
```

**Includes**:
- **Binaries**: Apache, PHP, MySQL, Mailpit
- **Applications**: phpMyAdmin
- **Tools**: Bruno, Composer, ConsoleZ

**Excludes**:
- MariaDB, PostgreSQL, Node.js, Xlight, Memcached
- phpPgAdmin
- Git, Ngrok, Perl, Python, Ruby, Ghostscript

**Dependencies**: `initBuild`, `prepareBase`

**Output**: 
- `bin/release/Bearsampp-lite-{version}.7z`
- `bin/release/Bearsampp-lite-{version}.7z.md5`
- `bin/release/Bearsampp-lite-{version}.7z.sha1`
- `bin/release/Bearsampp-lite-{version}.7z.sha256`
- `bin/release/Bearsampp-lite-{version}.7z.sha512`

**Build Time**: ~5-10 minutes (first build), ~2 minutes (incremental)

**Example**:
```bash
# Build lite release
.\gradlew buildLite

# Output:
# > Task :initBuild
# > Task :prepareBase
# > Task :buildLite
# BUILD SUCCESSFUL in 8m 32s
```

---

### buildBasic

**Description**: Builds the basic release with essential modules

**Command**:
```bash
.\gradlew buildBasic
```

**Includes**:
- **Binaries**: Apache, PHP, MySQL, MariaDB, Node.js, Xlight, Mailpit
- **Applications**: phpMyAdmin
- **Tools**: Bruno, Composer, ConsoleZ, Git, Ngrok, Perl

**Excludes**:
- PostgreSQL, Memcached
- phpPgAdmin
- Python, Ruby, Ghostscript

**Dependencies**: `initBuild`, `prepareBase`

**Output**: 
- `bin/release/Bearsampp-basic-{version}.7z`
- `bin/release/Bearsampp-basic-{version}.7z.md5`
- `bin/release/Bearsampp-basic-{version}.7z.sha1`
- `bin/release/Bearsampp-basic-{version}.7z.sha256`
- `bin/release/Bearsampp-basic-{version}.7z.sha512`

**Build Time**: ~10-15 minutes (first build), ~3 minutes (incremental)

**Example**:
```bash
# Build basic release
.\gradlew buildBasic

# Output:
# > Task :initBuild
# > Task :prepareBase
# > Task :buildBasic
# BUILD SUCCESSFUL in 12m 45s
```

---

### buildFull

**Description**: Builds the full release with all modules

**Command**:
```bash
.\gradlew buildFull
```

**Includes**:
- **Binaries**: Apache, PHP, MySQL, MariaDB, PostgreSQL, Node.js, Xlight, Mailpit, Memcached
- **Applications**: phpMyAdmin, phpPgAdmin
- **Tools**: Bruno, Composer, ConsoleZ, Ghostscript, Git, Ngrok, Perl, Python, Ruby

**Dependencies**: `initBuild`, `prepareBase`

**Output**: 
- `bin/release/Bearsampp-{version}.7z`
- `bin/release/Bearsampp-{version}.7z.md5`
- `bin/release/Bearsampp-{version}.7z.sha1`
- `bin/release/Bearsampp-{version}.7z.sha256`
- `bin/release/Bearsampp-{version}.7z.sha512`

**Build Time**: ~15-25 minutes (first build), ~5 minutes (incremental)

**Example**:
```bash
# Build full release
.\gradlew buildFull

# Output:
# > Task :initBuild
# > Task :prepareBase
# > Task :buildFull
# BUILD SUCCESSFUL in 18m 23s
```

---

### release

**Description**: Builds all release variants

**Command**:
```bash
.\gradlew release
```

**Actions**:
- Runs `buildFull`, `buildBasic`, `buildLite`
- Runs `sync` task (with user prompts)

**Dependencies**: `initBuild`, `prepareBase`, `buildFull`, `buildBasic`, `buildLite`, `sync`

**Output**: All three release archives with checksums

**Build Time**: ~30-45 minutes (first build)

**Interactive**: Yes (sync task prompts for user input)

**Example**:
```bash
# Build all variants
.\gradlew release

# Output:
# > Task :initBuild
# > Task :prepareBase
# > Task :buildFull
# > Task :buildBasic
# > Task :buildLite
# > Task :sync
# Would you like to sync to sandbox? (y/yes or n/no): n
# BUILD SUCCESSFUL in 35m 12s
```

---

### clean

**Description**: Cleans build artifacts and temporary files

**Command**:
```bash
.\gradlew clean
```

**Actions**:
- Deletes `bin/` directory
- Removes all build artifacts
- Clears temporary files

**Dependencies**: None

**Example**:
```bash
# Clean build artifacts
.\gradlew clean

# Output:
# > Task :clean
# BUILD SUCCESSFUL in 2s
```

---

## Verification Tasks

### checkLang

**Description**: Verifies language files

**Command**:
```bash
.\gradlew checkLang
```

**Actions**:
- Scans `core/langs/` directory
- Lists all `.lang` files found
- Verifies language file structure

**Dependencies**: `initBuild`

**Example**:
```bash
# Check language files
.\gradlew checkLang

# Output:
# > Task :initBuild
# > Task :checkLang
# Found language files:
#   - english.lang
#   - french.lang
#   - german.lang
#   - spanish.lang
#   - swedish.lang
#   - hungarian.lang
# BUILD SUCCESSFUL in 1s
```

---

## Application Tasks

### launch

**Description**: Builds and launches Bearsampp

**Command**:
```bash
.\gradlew launch
```

**Actions**:
1. Builds full release
2. Copies to `bin/launch/`
3. Executes `bearsampp.exe`

**Dependencies**: `buildFull`

**Example**:
```bash
# Build and launch Bearsampp
.\gradlew launch

# Output:
# > Task :initBuild
# > Task :prepareBase
# > Task :buildFull
# > Task :launch
# Launching Bearsampp...
# BUILD SUCCESSFUL in 20m 15s
```

---

### sync

**Description**: Syncs build to sandbox with interactive prompts

**Command**:
```bash
.\gradlew sync
```

**Actions**:
1. Prompts user for sync confirmation
2. Prompts for sync path (default: `bearsampp-destination/`)
3. Downloads Sandbox iconography
4. Copies base core files
5. Updates executable version
6. Updates executable icon
7. Copies to sync path

**Dependencies**: `buildFull`

**Interactive**: Yes (requires user input)

**Prompts**:
1. **Sync Confirmation**:
   ```
   Would you like to sync to sandbox? (y/yes or n/no):
   ```

2. **Path Confirmation**:
   ```
   Current sync path is: E:\Bearsampp-development\Bearsampp\bearsampp-destination
   Is this the path you want to sync to? (y/yes or n/no):
   ```

3. **Custom Path** (if no to #2):
   ```
   Please enter the new path to sync to:
   ```

**Example**:
```bash
# Sync to sandbox
.\gradlew sync

# Output:
# > Task :sync
# Would you like to sync to sandbox? (y/yes or n/no): y
# Current sync path is: E:\Bearsampp-development\Bearsampp\bearsampp-destination
# Is this the path you want to sync to? (y/yes or n/no): y
# Syncing to sandbox...
# BUILD SUCCESSFUL in 2m 30s
```

**Non-Interactive Mode**:
If running in non-interactive environment (no console):
- Skips sync automatically
- Logs message: "Running in non-interactive mode. Skipping sync."

---

## Task Dependencies

### Dependency Graph

```
release
├── buildFull
│   ├── prepareBase
│   │   └── initBuild
│   └── initBuild
├── buildBasic
│   ├── prepareBase
│   │   └── initBuild
│   └── initBuild
├── buildLite
│   ├── prepareBase
│   │   └── initBuild
│   └── initBuild
└── sync
    └── buildFull
        ├── prepareBase
        │   └── initBuild
        └── initBuild

launch
└── buildFull
    ├── prepareBase
    │   └── initBuild
    └��─ initBuild

checkLang
└── initBuild
```

### Execution Order

When running `.\gradlew release`, tasks execute in this order:

1. `initBuild`
2. `prepareBase`
3. `buildFull`
4. `buildBasic`
5. `buildLite`
6. `sync`

---

## Task Examples

### Build Specific Variant

```bash
# Build only lite variant
.\gradlew buildLite

# Build only basic variant
.\gradlew buildBasic

# Build only full variant
.\gradlew buildFull
```

### Build Multiple Variants

```bash
# Build full and basic (skip lite)
.\gradlew buildFull buildBasic

# Build all variants without sync
.\gradlew buildFull buildBasic buildLite
```

### Clean and Rebuild

```bash
# Clean and rebuild lite
.\gradlew clean buildLite

# Clean and rebuild all
.\gradlew clean release
```

### Debug Build

```bash
# Build with stack trace
.\gradlew buildLite --stacktrace

# Build with debug output
.\gradlew buildLite --debug

# Build with info output
.\gradlew buildLite --info
```

### Parallel Build

```bash
# Build with parallel execution
.\gradlew buildLite --parallel

# Build all variants in parallel
.\gradlew release --parallel
```

### Offline Build

```bash
# Build using cached dependencies only
.\gradlew buildLite --offline
```

### Dry Run

```bash
# See what would be executed without running
.\gradlew buildLite --dry-run
```

### Continuous Build

```bash
# Watch for changes and rebuild automatically
.\gradlew buildLite --continuous
```

### Build with Cache

```bash
# Build with build cache enabled
.\gradlew buildLite --build-cache
```

---

## Task Options

### Global Options

| Option              | Description                                  | Example                          |
|---------------------|----------------------------------------------|----------------------------------|
| `--stacktrace`      | Show stack trace on error                    | `.\gradlew buildLite --stacktrace` |
| `--debug`           | Show debug output                            | `.\gradlew buildLite --debug`    |
| `--info`            | Show info output                             | `.\gradlew buildLite --info`     |
| `--parallel`        | Execute tasks in parallel                    | `.\gradlew buildLite --parallel` |
| `--offline`         | Use cached dependencies only                 | `.\gradlew buildLite --offline`  |
| `--dry-run`         | Show what would be executed                  | `.\gradlew buildLite --dry-run`  |
| `--continuous`      | Watch for changes and rebuild                | `.\gradlew buildLite --continuous` |
| `--build-cache`     | Enable build cache                           | `.\gradlew buildLite --build-cache` |
| `--scan`            | Create build scan                            | `.\gradlew buildLite --scan`     |

---

## Task Properties

### Build Properties

Configure tasks via `build.properties`:

```properties
# Release version
release.default.version=2025.5.6

# Archive format (7z, zip, or all)
release.format=7z

# Module versions
bin.apache.version=2.4.63
bin.php.version=8.4.6
bin.mysql.version=9.3.0
```

### Gradle Properties

Configure Gradle via `gradle.properties`:

```properties
# Gradle daemon
org.gradle.daemon=true

# Parallel execution
org.gradle.parallel=true

# Build cache
org.gradle.caching=true

# JVM settings
org.gradle.jvmargs=-Xmx4g -XX:MaxMetaspaceSize=512m
```

---

## Performance Tips

### Faster Builds

1. **Use Lite Build for Development**
   ```bash
   .\gradlew buildLite
   ```

2. **Enable Parallel Execution**
   ```bash
   .\gradlew buildLite --parallel
   ```

3. **Enable Build Cache**
   ```bash
   .\gradlew buildLite --build-cache
   ```

4. **Use Gradle Daemon** (enabled by default)
   - Keeps Gradle in memory
   - Faster subsequent builds

5. **Increase Heap Size**
   
   Create `gradle.properties`:
   ```properties
   org.gradle.jvmargs=-Xmx4g -XX:MaxMetaspaceSize=512m
   ```

### Build Time Comparison

| Build Type | First Build | Incremental | No Changes |
|------------|-------------|-------------|------------|
| Lite       | ~10 min     | ~2 min      | ~5 sec     |
| Basic      | ~15 min     | ~3 min      | ~5 sec     |
| Full       | ~25 min     | ~5 min      | ~5 sec     |
| Release    | ~45 min     | ~10 min     | ~5 sec     |

---

## Troubleshooting

### Common Issues

#### Task Not Found

**Symptom**:
```
Task 'buildLite' not found in root project 'Bearsampp'.
```

**Solution**:
```bash
# List available tasks
.\gradlew tasks

# Verify task name
.\gradlew tasks --all
```

---

#### Build Fails

**Symptom**:
```
FAILURE: Build failed with an exception.
```

**Solution**:
```bash
# Run with stack trace
.\gradlew buildLite --stacktrace

# Run with debug output
.\gradlew buildLite --debug
```

---

#### Module Download Fails

**Symptom**:
```
ERROR: Failed to download module
WARNING: Created empty directory as fallback
```

**Solution**:
- Check internet connection
- Verify GitHub access
- Build continues with empty directories (expected behavior)

---

#### Permission Denied

**Symptom**:
```
Permission denied: ./gradlew
```

**Solution** (Linux/Mac):
```bash
chmod +x gradlew
```

---

#### Lock Error

**Symptom**:
```
Could not copy file ... another process has locked a portion of the file
```

**Solution**:
- Close IDEs (IntelliJ, Eclipse, VS Code)
- Stop Gradle daemon: `.\gradlew --stop`
- Retry build

---

## Additional Resources

- [Build Guide](BUILD_GUIDE.md) - Comprehensive build documentation
- [Configuration Guide](CONFIGURATION.md) - Configuration reference
- [API Reference](API.md) - Build script API
- [Quick Start](QUICKSTART.md) - Quick start guide

---

**Last Updated**: 2025  
**Gradle Version**: 8.5  
**Status**: Production Ready ✅
