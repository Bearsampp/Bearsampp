# Gradle Build Quick Start Guide

## Prerequisites

- Java JDK 8 or higher installed
- Gradle wrapper included (no separate Gradle installation needed)
- 7-Zip available in `dev/tools/7zip/`

## Quick Start

### 1. List Available Tasks

```bash
.\gradlew tasks
```

### 2. Build Lite Release (Fastest)

```bash
.\gradlew buildLite
```

### 3. Build Full Release

```bash
.\gradlew buildFull
```

### 4. Build All Variants

```bash
.\gradlew release
```

## Common Commands

| Command | Description | Time |
|---------|-------------|------|
| `.\gradlew initBuild` | Initialize build directories | ~1s |
| `.\gradlew prepareBase` | Prepare base environment | ~30s |
| `.\gradlew buildLite` | Build lite release | ~5-10min |
| `.\gradlew buildBasic` | Build basic release | ~10-15min |
| `.\gradlew buildFull` | Build full release | ~15-25min |
| `.\gradlew release` | Build all variants | ~30-45min |
| `.\gradlew checkLang` | Verify language files | ~1s |
| `.\gradlew launch` | Build and launch Bearsampp | ~10min |
| `.\gradlew clean` | Clean build directory | ~5s |

## Platform-Specific Commands

### Windows (PowerShell)
```powershell
.\gradlew buildLite
```

### Windows (CMD)
```cmd
gradlew.bat buildLite
```

### Linux/Mac
```bash
./gradlew buildLite
```

## Build Variants Explained

### Lite Build (Recommended for Development)
```bash
.\gradlew buildLite
```

**Includes:**
- Apache 2.4.63
- PHP 8.4.6
- MySQL 9.3.0
- Mailpit 1.24.1
- phpMyAdmin 5.2.2
- Bruno, Composer, ConsoleZ

**Excludes:**
- MariaDB, PostgreSQL
- Node.js, Xlight, Memcached
- phpPgAdmin
- Git, Ngrok, Perl, Python, Ruby, Ghostscript

**Use Case:** Fast builds for development and testing

---

### Basic Build
```bash
.\gradlew buildBasic
```

**Adds to Lite:**
- MariaDB
- Node.js
- Xlight
- Git, Ngrok, Perl

**Use Case:** Standard development environment

---

### Full Build (Production)
```bash
.\gradlew buildFull
```

**Adds to Basic:**
- PostgreSQL
- Memcached
- phpPgAdmin
- Python, Ruby, Ghostscript

**Use Case:** Complete production setup with all tools

## Configuration

### Change Version

Edit `build.properties`:

```properties
release.default.version=2025.5.6
```

### Change Module Versions

```properties
bin.apache.version=2.4.63
bin.php.version=8.4.6
bin.mysql.version=9.3.0
```

### Change Archive Format

```properties
# Options: 7z, zip, all
release.format=7z
```

## Output Locations

After building, find your files here:

```
bin/
├── release/                    # Final archives
│   ├── Bearsampp-2025.5.6.7z
│   ├── Bearsampp-2025.5.6.7z.md5
│   ├── Bearsampp-2025.5.6.7z.sha1
│   ├── Bearsampp-2025.5.6.7z.sha256
│   └── Bearsampp-2025.5.6.7z.sha512
├── tmp/                        # Temporary files
│   ├── getmodule/             # Downloaded modules (cached)
│   └── release/               # Build staging
└── launch/                     # Launch directory
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

### Java Version Issues

Check Java version:

```bash
java -version
```

Requires Java 8 or higher.

## Advanced Usage

### Build Specific Task Only

```bash
# Only prepare base (no module downloads)
.\gradlew prepareBase

# Only initialize
.\gradlew initBuild
```

### Skip Sync Prompt

The `release` task includes sync, which prompts for user input. To skip:

```bash
# Build all variants without sync
.\gradlew buildFull buildBasic buildLite
```

### Parallel Builds

Gradle can run tasks in parallel:

```bash
.\gradlew buildLite --parallel
```

### Offline Mode

Use cached dependencies only:

```bash
.\gradlew buildLite --offline
```

## Performance Tips

1. **Use Lite Build for Development**
   - Fastest build time
   - Includes essential components

2. **Enable Gradle Daemon**
   - Automatically enabled
   - Speeds up subsequent builds

3. **Use Cached Downloads**
   - Modules are cached in `bin/tmp/getmodule/`
   - Reused on subsequent builds

4. **Incremental Builds**
   - Gradle only rebuilds changed parts
   - Much faster than full rebuilds

## Comparison with Ant

| Feature | Ant (build.xml) | Gradle (build.gradle) |
|---------|----------------|----------------------|
| Build Time (Lite) | ~10min | ~5-10min |
| Incremental Builds | ❌ No | ✅ Yes |
| Caching | ❌ No | ✅ Yes |
| IDE Integration | ⚠️ Basic | ✅ Excellent |
| Error Messages | ⚠️ Basic | ✅ Detailed |
| Parallel Execution | ❌ No | ✅ Yes |

## Next Steps

1. **Try a Build**: Start with `.\gradlew buildLite`
2. **Review Output**: Check `bin/release/` for archives
3. **Read Full Docs**: See [BUILD_GUIDE.md](BUILD_GUIDE.md)
4. **Customize**: Edit `build.properties` for your needs

## Getting Help

- **Detailed Documentation**: [BUILD_GUIDE.md](BUILD_GUIDE.md)
- **Migration Guide**: [MIGRATION_GUIDE.md](MIGRATION_GUIDE.md)
- **Implementation Status**: [IMPLEMENTATION_STATUS.md](IMPLEMENTATION_STATUS.md)
- **Gradle Docs**: https://docs.gradle.org

## License

Same as Bearsampp project license.

---

**Quick Reference Card**

```bash
# Most Common Commands
.\gradlew tasks          # List all tasks
.\gradlew buildLite      # Fast build
.\gradlew buildFull      # Complete build
.\gradlew release        # All variants
.\gradlew clean          # Clean up

# Troubleshooting
.\gradlew buildLite --stacktrace  # Show errors
.\gradlew buildLite --debug       # Verbose output
.\gradlew clean buildLite         # Clean rebuild
```
