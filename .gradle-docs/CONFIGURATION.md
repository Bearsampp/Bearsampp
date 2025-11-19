# Configuration Guide

Complete configuration reference for the Bearsampp Gradle build system.

---

## Table of Contents

- [Configuration Files](#configuration-files)
- [Build Properties](#build-properties)
- [Gradle Properties](#gradle-properties)
- [Module Configuration](#module-configuration)
- [Token Replacement](#token-replacement)
- [Environment Variables](#environment-variables)
- [Configuration Examples](#configuration-examples)
- [Best Practices](#best-practices)

---

## Configuration Files

### Overview

| File                  | Purpose                                  | Location          |
|-----------------------|------------------------------------------|-------------------|
| `build.properties`    | Build configuration and module versions  | Project root      |
| `gradle.properties`   | Gradle-specific settings                 | Project root      |
| `settings.gradle`     | Gradle project settings                  | Project root      |
| `build.gradle`        | Main Gradle build script                 | Project root      |

---

## Build Properties

### File: `build.properties`

Main configuration file for the Bearsampp build system.

### Application Configuration

```properties
# Language
appconf.lang=english

# Timezone
appconf.timezone=Europe/Paris

# Notepad editor
appconf.notepad=notepad.exe

# Maximum log archives to keep
appconf.maxLogsArchives=5

# Verbose logging (0=off, 1=on)
appconf.logsVerbose=0

# Scripts timeout in seconds
appconf.scriptsTimeout=60

# Download ID (optional)
appconf.downloadId=

# Include pull requests in version check
appconf.IncludePR=false
```

| Property                  | Type    | Default         | Description                          |
|---------------------------|---------|-----------------|--------------------------------------|
| `appconf.lang`            | String  | `english`       | Default language                     |
| `appconf.timezone`        | String  | `Europe/Paris`  | Default timezone                     |
| `appconf.notepad`         | String  | `notepad.exe`   | Text editor command                  |
| `appconf.maxLogsArchives` | Integer | `5`             | Maximum log archives to keep         |
| `appconf.logsVerbose`     | Integer | `0`             | Verbose logging (0=off, 1=on)        |
| `appconf.scriptsTimeout`  | Integer | `60`            | Scripts timeout in seconds           |
| `appconf.downloadId`      | String  | (empty)         | Download ID for tracking             |
| `appconf.IncludePR`       | Boolean | `false`         | Include PRs in version check         |

---

### Release Configuration

```properties
# Release version
release.default.version=2025.5.6

# Archive format: 7z, zip, or all
release.format=7z

# Release type: release, beta, alpha
release.release.type=release
```

| Property                  | Type    | Default         | Description                          |
|---------------------------|---------|-----------------|--------------------------------------|
| `release.default.version` | String  | `2025.5.6`      | Release version number               |
| `release.format`          | String  | `7z`            | Archive format (7z, zip, or all)     |
| `release.release.type`    | String  | `release`       | Release type (release, beta, alpha)  |

---

### Binary Versions

```properties
# Apache HTTP Server
bin.apache.version=2.4.63

# PHP
bin.php.version=8.4.6

# MySQL
bin.mysql.version=9.3.0

# MariaDB
bin.maria.version=11.7.2

# PostgreSQL
bin.postgresql.version=17.4.1

# Node.js
bin.nodejs.version=23.11.0

# Xlight FTP Server
bin.xlight.version=3.9.8.3

# Mailpit
bin.mailpit.version=1.24.1

# Memcached
bin.memcached.version=1.6.33
```

| Property                  | Type    | Description                          |
|---------------------------|---------|--------------------------------------|
| `bin.apache.version`      | String  | Apache HTTP Server version           |
| `bin.php.version`         | String  | PHP version                          |
| `bin.mysql.version`       | String  | MySQL version                        |
| `bin.maria.version`       | String  | MariaDB version                      |
| `bin.postgresql.version`  | String  | PostgreSQL version                   |
| `bin.nodejs.version`      | String  | Node.js version                      |
| `bin.xlight.version`      | String  | Xlight FTP Server version            |
| `bin.mailpit.version`     | String  | Mailpit version                      |
| `bin.memcached.version`   | String  | Memcached version                    |

---

### Application Versions

```properties
# phpMyAdmin
app.phpmyadmin.version=5.2.2

# phpPgAdmin
app.phppgadmin.version=7.14.7
```

| Property                  | Type    | Description                          |
|---------------------------|---------|--------------------------------------|
| `app.phpmyadmin.version`  | String  | phpMyAdmin version                   |
| `app.phppgadmin.version`  | String  | phpPgAdmin version                   |

---

### Tool Versions

```properties
# Bruno API Client
tool.bruno.version=2.1.0

# Composer
tool.composer.version=2.8.8

# ConsoleZ
tool.consolez.version=1.19.0.19104

# Ghostscript
tool.ghostscript.version=10.05.0

# Git
tool.git.version=2.48.1

# Ngrok
tool.ngrok.version=3.20.1

# Perl
tool.perl.version=5.40.0.1

# Python
tool.python.version=3.13.1

# Ruby
tool.ruby.version=3.4.1.1
```

| Property                  | Type    | Description                          |
|---------------------------|---------|--------------------------------------|
| `tool.bruno.version`      | String  | Bruno API Client version             |
| `tool.composer.version`   | String  | Composer version                     |
| `tool.consolez.version`   | String  | ConsoleZ version                     |
| `tool.ghostscript.version`| String  | Ghostscript version                  |
| `tool.git.version`        | String  | Git version                          |
| `tool.ngrok.version`      | String  | Ngrok version                        |
| `tool.perl.version`       | String  | Perl version                         |
| `tool.python.version`     | String  | Python version                       |
| `tool.ruby.version`       | String  | Ruby version                         |

---

### Directory Configuration

```properties
# Development directory (relative to project root)
dev.dir=../dev

# Build output directory
build.dir=bin
```

| Property      | Type    | Default   | Description                          |
|---------------|---------|-----------|--------------------------------------|
| `dev.dir`     | String  | `../dev`  | Development directory path           |
| `build.dir`   | String  | `bin`     | Build output directory               |

---

## Gradle Properties

### File: `gradle.properties`

Gradle-specific configuration (optional, create if needed).

### Recommended Configuration

```properties
# Gradle daemon (keeps Gradle in memory for faster builds)
org.gradle.daemon=true

# Parallel execution (run tasks in parallel)
org.gradle.parallel=true

# Build cache (cache task outputs for faster rebuilds)
org.gradle.caching=true

# JVM settings (increase heap size for large builds)
org.gradle.jvmargs=-Xmx4g -XX:MaxMetaspaceSize=512m -XX:+HeapDumpOnOutOfMemoryError

# File encoding
org.gradle.jvmargs=-Dfile.encoding=UTF-8

# Console output (plain, auto, rich, verbose)
org.gradle.console=auto

# Warning mode (all, fail, summary, none)
org.gradle.warning.mode=all
```

### Property Reference

| Property                  | Type    | Default | Description                          |
|---------------------------|---------|---------|--------------------------------------|
| `org.gradle.daemon`       | Boolean | `true`  | Enable Gradle daemon                 |
| `org.gradle.parallel`     | Boolean | `false` | Enable parallel execution            |
| `org.gradle.caching`      | Boolean | `false` | Enable build cache                   |
| `org.gradle.jvmargs`      | String  | (varies)| JVM arguments                        |
| `org.gradle.console`      | String  | `auto`  | Console output mode                  |
| `org.gradle.warning.mode` | String  | `summary`| Warning display mode                |

---

## Module Configuration

### Binary Modules

#### Apache

```properties
bin.apache.version=2.4.63
```

**Download Source**: https://github.com/Bearsampp/module-apache

**Configuration Tokens**:
- `@BIN_APACHE_VERSION@` → `2.4.63`
- `@BIN_APACHE_ENABLE@` → `1` (full/basic) or `0` (lite)

---

#### PHP

```properties
bin.php.version=8.4.6
```

**Download Source**: https://github.com/Bearsampp/module-php

**Configuration Tokens**:
- `@BIN_PHP_VERSION@` → `8.4.6`
- `@BIN_PHP_ENABLE@` → `1` (all variants)

---

#### MySQL

```properties
bin.mysql.version=9.3.0
```

**Download Source**: https://github.com/Bearsampp/module-mysql

**Configuration Tokens**:
- `@BIN_MYSQL_VERSION@` → `9.3.0`
- `@BIN_MYSQL_ENABLE@` → `1` (all variants)

---

#### MariaDB

```properties
bin.maria.version=11.7.2
```

**Download Source**: https://github.com/Bearsampp/module-mariadb

**Configuration Tokens**:
- `@BIN_MARIADB_VERSION@` → `11.7.2`
- `@BIN_MARIADB_ENABLE@` → `1` (full/basic) or `0` (lite)

---

#### PostgreSQL

```properties
bin.postgresql.version=17.4.1
```

**Download Source**: https://github.com/Bearsampp/module-postgresql

**Configuration Tokens**:
- `@BIN_POSTGRESQL_VERSION@` → `17.4.1`
- `@BIN_POSTGRESQL_ENABLE@` → `1` (full) or `0` (basic/lite)

---

#### Node.js

```properties
bin.nodejs.version=23.11.0
```

**Download Source**: https://github.com/Bearsampp/module-nodejs

**Configuration Tokens**:
- `@BIN_NODEJS_VERSION@` → `23.11.0`
- `@BIN_NODEJS_ENABLE@` → `1` (full/basic) or `0` (lite)

---

#### Xlight

```properties
bin.xlight.version=3.9.8.3
```

**Download Source**: https://github.com/Bearsampp/module-xlight

**Configuration Tokens**:
- `@BIN_XLIGHT_VERSION@` → `3.9.8.3`
- `@BIN_XLIGHT_ENABLE@` → `1` (full/basic) or `0` (lite)

---

#### Mailpit

```properties
bin.mailpit.version=1.24.1
```

**Download Source**: https://github.com/Bearsampp/module-mailpit

**Configuration Tokens**:
- `@BIN_MAILPIT_VERSION@` → `1.24.1`
- `@BIN_MAILPIT_ENABLE@` → `1` (all variants)

---

#### Memcached

```properties
bin.memcached.version=1.6.33
```

**Download Source**: https://github.com/Bearsampp/module-memcached

**Configuration Tokens**:
- `@BIN_MEMCACHED_VERSION@` → `1.6.33`
- `@BIN_MEMCACHED_ENABLE@` → `1` (full) or `0` (basic/lite)

---

### Application Modules

#### phpMyAdmin

```properties
app.phpmyadmin.version=5.2.2
```

**Download Source**: https://github.com/Bearsampp/app-phpmyadmin

**Configuration Tokens**:
- `@APP_PHPMYADMIN_VERSION@` → `5.2.2`

---

#### phpPgAdmin

```properties
app.phppgadmin.version=7.14.7
```

**Download Source**: https://github.com/Bearsampp/app-phppgadmin

**Configuration Tokens**:
- `@APP_PHPPGADMIN_VERSION@` → `7.14.7`

---

### Tool Modules

Tool versions are configured similarly to binary and application modules.

---

## Token Replacement

### Overview

The build system uses token replacement to inject configuration values into files during the build process.

### Token Format

```
@TOKEN_NAME@
```

### Available Tokens

#### Application Configuration Tokens

| Token                          | Source Property              | Example Value      |
|--------------------------------|------------------------------|--------------------|
| `@APPCONF_LANG@`               | `appconf.lang`               | `english`          |
| `@APPCONF_TIMEZONE@`           | `appconf.timezone`           | `Europe/Paris`     |
| `@APPCONF_NOTEPAD@`            | `appconf.notepad`            | `notepad.exe`      |
| `@APPCONF_MAX_LOGS_ARCHIVES@`  | `appconf.maxLogsArchives`    | `5`                |
| `@APPCONF_LOGS_VERBOSE@`       | `appconf.logsVerbose`        | `0`                |
| `@APPCONF_SCRIPTS_TIMEOUT@`    | `appconf.scriptsTimeout`     | `60`               |

#### Release Configuration Tokens

| Token                          | Source Property              | Example Value      |
|--------------------------------|------------------------------|--------------------|
| `@RELEASE_VERSION@`            | `release.default.version`    | `2025.5.6`         |
| `@RELEASE_FORMAT@`             | `release.format`             | `7z`               |
| `@RELEASE_TYPE@`               | `release.release.type`       | `release`          |

#### Binary Version Tokens

| Token                          | Source Property              | Example Value      |
|--------------------------------|------------------------------|--------------------|
| `@BIN_APACHE_VERSION@`         | `bin.apache.version`         | `2.4.63`           |
| `@BIN_PHP_VERSION@`            | `bin.php.version`            | `8.4.6`            |
| `@BIN_MYSQL_VERSION@`          | `bin.mysql.version`          | `9.3.0`            |
| `@BIN_MARIADB_VERSION@`        | `bin.maria.version`          | `11.7.2`           |
| `@BIN_POSTGRESQL_VERSION@`     | `bin.postgresql.version`     | `17.4.1`           |
| `@BIN_NODEJS_VERSION@`         | `bin.nodejs.version`         | `23.11.0`          |
| `@BIN_XLIGHT_VERSION@`         | `bin.xlight.version`         | `3.9.8.3`          |
| `@BIN_MAILPIT_VERSION@`        | `bin.mailpit.version`        | `1.24.1`           |
| `@BIN_MEMCACHED_VERSION@`      | `bin.memcached.version`      | `1.6.33`           |

#### Application Version Tokens

| Token                          | Source Property              | Example Value      |
|--------------------------------|------------------------------|--------------------|
| `@APP_PHPMYADMIN_VERSION@`     | `app.phpmyadmin.version`     | `5.2.2`            |
| `@APP_PHPPGADMIN_VERSION@`     | `app.phppgadmin.version`     | `7.14.7`           |

#### Tool Version Tokens

| Token                          | Source Property              | Example Value      |
|--------------------------------|------------------------------|--------------------|
| `@TOOL_BRUNO_VERSION@`         | `tool.bruno.version`         | `2.1.0`            |
| `@TOOL_COMPOSER_VERSION@`      | `tool.composer.version`      | `2.8.8`            |
| `@TOOL_CONSOLEZ_VERSION@`      | `tool.consolez.version`      | `1.19.0.19104`     |
| `@TOOL_GHOSTSCRIPT_VERSION@`   | `tool.ghostscript.version`   | `10.05.0`          |
| `@TOOL_GIT_VERSION@`           | `tool.git.version`           | `2.48.1`           |
| `@TOOL_NGROK_VERSION@`         | `tool.ngrok.version`         | `3.20.1`           |
| `@TOOL_PERL_VERSION@`          | `tool.perl.version`          | `5.40.0.1`         |
| `@TOOL_PYTHON_VERSION@`        | `tool.python.version`        | `3.13.1`           |
| `@TOOL_RUBY_VERSION@`          | `tool.ruby.version`          | `3.4.1.1`          |

#### Enable/Disable Tokens

| Token                          | Value (Full) | Value (Basic) | Value (Lite) |
|--------------------------------|--------------|---------------|--------------|
| `@BIN_APACHE_ENABLE@`          | `1`          | `1`           | `1`          |
| `@BIN_PHP_ENABLE@`             | `1`          | `1`           | `1`          |
| `@BIN_MYSQL_ENABLE@`           | `1`          | `1`           | `1`          |
| `@BIN_MARIADB_ENABLE@`         | `1`          | `1`           | `0`          |
| `@BIN_POSTGRESQL_ENABLE@`      | `1`          | `0`           | `0`          |
| `@BIN_NODEJS_ENABLE@`          | `1`          | `1`           | `0`          |
| `@BIN_XLIGHT_ENABLE@`          | `1`          | `1`           | `0`          |
| `@BIN_MAILPIT_ENABLE@`         | `1`          | `1`           | `1`          |
| `@BIN_MEMCACHED_ENABLE@`       | `1`          | `0`           | `0`          |

### Token Replacement Example

**Template File** (`base/bearsampp.ini`):
```ini
[apache]
version=@BIN_APACHE_VERSION@

[php]
version=@BIN_PHP_VERSION@

[mysql]
version=@BIN_MYSQL_VERSION@
```

**After Token Replacement**:
```ini
[apache]
version=2.4.63

[php]
version=8.4.6

[mysql]
version=9.3.0
```

---

## Environment Variables

### Build Environment

| Variable          | Description                          | Example Value                    |
|-------------------|--------------------------------------|----------------------------------|
| `JAVA_HOME`       | Java installation directory          | `C:\Program Files\Java\jdk-11`   |
| `GRADLE_HOME`     | Gradle installation directory        | `C:\Gradle\gradle-8.5`           |
| `PATH`            | System path (includes Java, Gradle)  | (varies)                         |

### Gradle Environment

| Variable                  | Description                          | Example Value      |
|---------------------------|--------------------------------------|--------------------|
| `GRADLE_USER_HOME`        | Gradle user home directory           | `C:\Users\user\.gradle` |
| `GRADLE_OPTS`             | Gradle JVM options                   | `-Xmx4g`           |

---

## Configuration Examples

### Example 1: Change Release Version

**Edit `build.properties`**:
```properties
release.default.version=2025.6.0
```

**Build**:
```bash
.\gradlew buildFull
```

**Output**:
```
bin/release/Bearsampp-2025.6.0.7z
```

---

### Example 2: Change Archive Format to ZIP

**Edit `build.properties`**:
```properties
release.format=zip
```

**Build**:
```bash
.\gradlew buildFull
```

**Output**:
```
bin/release/Bearsampp-2025.5.6.zip
```

---

### Example 3: Build All Archive Formats

**Edit `build.properties`**:
```properties
release.format=all
```

**Build**:
```bash
.\gradlew buildFull
```

**Output**:
```
bin/release/Bearsampp-2025.5.6.7z
bin/release/Bearsampp-2025.5.6.zip
```

---

### Example 4: Update PHP Version

**Edit `build.properties`**:
```properties
bin.php.version=8.4.7
```

**Build**:
```bash
.\gradlew buildFull
```

**Result**: Downloads and includes PHP 8.4.7

---

### Example 5: Optimize Gradle Performance

**Create `gradle.properties`**:
```properties
org.gradle.daemon=true
org.gradle.parallel=true
org.gradle.caching=true
org.gradle.jvmargs=-Xmx4g -XX:MaxMetaspaceSize=512m
```

**Build**:
```bash
.\gradlew buildFull
```

**Result**: Faster builds with parallel execution and caching

---

## Best Practices

### 1. Version Management

- **Use Semantic Versioning**: `MAJOR.MINOR.PATCH`
- **Update Regularly**: Keep module versions up to date
- **Test Before Release**: Test new versions before updating

### 2. Archive Format

- **Use 7z for Distribution**: Better compression ratio
- **Use ZIP for Compatibility**: Better compatibility with older systems
- **Use All for Both**: Generate both formats if needed

### 3. Gradle Configuration

- **Enable Daemon**: Faster subsequent builds
- **Enable Parallel**: Faster builds on multi-core systems
- **Enable Caching**: Reuse task outputs
- **Increase Heap Size**: For large builds (4GB recommended)

### 4. Token Replacement

- **Use Consistent Naming**: Follow existing token naming conventions
- **Document Custom Tokens**: Document any custom tokens added
- **Test Token Replacement**: Verify tokens are replaced correctly

### 5. Module Versions

- **Check Compatibility**: Ensure module versions are compatible
- **Update Dependencies**: Update dependent modules together
- **Test Combinations**: Test different module combinations

---

## Additional Resources

- [Build Guide](BUILD_GUIDE.md) - Comprehensive build documentation
- [Task Reference](TASKS.md) - All available tasks
- [API Reference](API.md) - Build script API
- [Quick Start](QUICKSTART.md) - Quick start guide

---

**Last Updated**: 2025  
**Gradle Version**: 8.5  
**Status**: Production Ready ✅
