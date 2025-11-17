# Build Script API Reference

Complete API reference for the Bearsampp Gradle build system.

---

## Table of Contents

- [Build Script API](#build-script-api)
- [Helper Functions](#helper-functions)
- [Extension Points](#extension-points)
- [Properties API](#properties-api)
- [Task API](#task-api)
- [File Operations API](#file-operations-api)
- [Exec API](#exec-api)
- [Logger API](#logger-api)
- [Exception Handling](#exception-handling)
- [API Examples](#api-examples)

---

## Build Script API

### Overview

The Bearsampp build script (`build.gradle`) provides a comprehensive API for building, packaging, and distributing Bearsampp releases.

### Core Components

| Component          | Description                                  |
|--------------------|----------------------------------------------|
| **Properties**     | Configuration loaded from build.properties   |
| **Tasks**          | Build, verification, and application tasks   |
| **Helpers**        | Utility functions for common operations      |
| **Extensions**     | Custom extensions for build functionality    |

---

## Helper Functions

### downloadModule

Downloads and extracts a module from GitHub releases.

**Signature**:
```groovy
void downloadModule(String repoUrl, String version, File destination)
```

**Parameters**:
- `repoUrl` (String): GitHub repository URL (e.g., `https://github.com/Bearsampp/module-apache/releases`)
- `version` (String): Module version to download (e.g., `2.4.63`)
- `destination` (File): Destination directory for extracted module

**Behavior**:
1. Converts GitHub URL to raw.githubusercontent.com
2. Downloads `releases.properties` from repository
3. Looks up download URL for specified version
4. Downloads module archive to cache (`bin/tmp/getmodule/`)
5. Extracts archive to destination directory
6. On failure: Creates empty directory and logs warning

**Example**:
```groovy
downloadModule(
    'https://github.com/Bearsampp/module-apache/releases',
    '2.4.63',
    file("${releaseTarget}/bin/apache")
)
```

**Error Handling**:
- Catches all exceptions
- Logs error message
- Creates empty directory as fallback
- Continues build (doesn't fail)

---

### compressArchive

Compresses a directory into an archive using 7-Zip.

**Signature**:
```groovy
void compressArchive(File sourceDir, File outputFile, String format)
```

**Parameters**:
- `sourceDir` (File): Source directory to compress
- `outputFile` (File): Output archive file
- `format` (String): Archive format (`7z` or `zip`)

**Behavior**:
1. Validates 7-Zip executable exists
2. Builds compression command based on format
3. Executes 7-Zip with appropriate settings
4. Waits for compression to complete

**7z Settings**:
```bash
7za.exe a -t7z {output}.7z {source}/* -m0=LZMA2 -mx9 -mmt6
```
- `-t7z`: 7z format
- `-m0=LZMA2`: LZMA2 compression method
- `-mx9`: Maximum compression level
- `-mmt6`: Use 6 threads

**ZIP Settings**:
```bash
7za.exe a {output}.zip {source}/* -mm=Deflate -mfb=258 -mpass=15 -r
```
- `-mm=Deflate`: Deflate compression method
- `-mfb=258`: Fast bytes
- `-mpass=15`: Number of passes
- `-r`: Recursive

**Example**:
```groovy
compressArchive(
    file("${releaseTarget}/Bearsampp-${releaseVersion}"),
    file("${releaseDir}/Bearsampp-${releaseVersion}.7z"),
    '7z'
)
```

---

### generateChecksums

Generates checksum files for an archive.

**Signature**:
```groovy
void generateChecksums(File archiveFile)
```

**Parameters**:
- `archiveFile` (File): Archive file to generate checksums for

**Behavior**:
1. Calculates MD5, SHA-1, SHA-256, SHA-512 checksums
2. Creates separate files for each checksum
3. Format: `{hash}  {filename}`

**Output Files**:
- `{archive}.md5`
- `{archive}.sha1`
- `{archive}.sha256`
- `{archive}.sha512`

**Example**:
```groovy
generateChecksums(file("${releaseDir}/Bearsampp-${releaseVersion}.7z"))
```

**Output**:
```
Bearsampp-2025.5.6.7z.md5
Bearsampp-2025.5.6.7z.sha1
Bearsampp-2025.5.6.7z.sha256
Bearsampp-2025.5.6.7z.sha512
```

---

### downloadFile

Downloads a file from a URL.

**Signature**:
```groovy
void downloadFile(String url, File destination)
```

**Parameters**:
- `url` (String): URL to download from
- `destination` (File): Destination file

**Behavior**:
1. Opens connection to URL
2. Reads input stream
3. Writes to destination file
4. Closes streams

**Example**:
```groovy
downloadFile(
    'https://raw.githubusercontent.com/Bearsampp/module-apache/main/releases.properties',
    file('bin/tmp/releases.properties')
)
```

---

### extractArchive

Extracts an archive file.

**Signature**:
```groovy
void extractArchive(File archiveFile, File destination)
```

**Parameters**:
- `archiveFile` (File): Archive file to extract
- `destination` (File): Destination directory

**Behavior**:
1. Detects archive format (.7z or .zip)
2. Uses 7-Zip for .7z files
3. Uses Ant unzip for .zip files
4. Extracts to destination directory

**Example**:
```groovy
extractArchive(
    file('bin/tmp/getmodule/apache-2.4.63.7z'),
    file('bin/tmp/release/bin/apache')
)
```

---

### applyTokenFilter

Applies token replacement to files during copy.

**Signature**:
```groovy
void applyTokenFilter(CopySpec copySpec, Map<String, String> tokens)
```

**Parameters**:
- `copySpec` (CopySpec): Gradle copy specification
- `tokens` (Map<String, String>): Token replacement map

**Behavior**:
1. Applies filter to copy operation
2. Replaces `@TOKEN@` with corresponding value
3. Processes all files in copy operation

**Example**:
```groovy
copy {
    from 'base'
    into "${releaseTarget}/base"
    applyTokenFilter(it, [
        'BIN_APACHE_VERSION': '2.4.63',
        'BIN_PHP_VERSION': '8.4.6'
    ])
}
```

---

### getUserInput

Prompts user for input (console only).

**Signature**:
```groovy
String getUserInput(String prompt)
```

**Parameters**:
- `prompt` (String): Prompt message to display

**Returns**:
- User input as String
- Empty string if no console available

**Behavior**:
1. Checks if console is available
2. Displays prompt
3. Reads user input
4. Returns input or empty string

**Example**:
```groovy
def syncConfirm = getUserInput('Would you like to sync to sandbox? (y/yes or n/no): ')
if (syncConfirm.toLowerCase() in ['y', 'yes']) {
    // Perform sync
}
```

---

## Extension Points

### Custom Tasks

Add custom tasks to the build:

```groovy
tasks.register('myCustomTask') {
    group = 'custom'
    description = 'My custom task'
    
    doLast {
        println 'Executing custom task'
    }
}
```

---

### Custom Module Downloads

Add custom module downloads:

```groovy
tasks.register('downloadCustomModule') {
    doLast {
        downloadModule(
            'https://github.com/myorg/my-module/releases',
            '1.0.0',
            file("${releaseTarget}/custom")
        )
    }
}
```

---

### Custom Token Replacement

Add custom tokens:

```groovy
def customTokens = [
    'CUSTOM_TOKEN': 'custom_value',
    'ANOTHER_TOKEN': 'another_value'
]

copy {
    from 'source'
    into 'destination'
    filter { line ->
        customTokens.inject(line) { result, entry ->
            result.replace("@${entry.key}@", entry.value)
        }
    }
}
```

---

## Properties API

### Loading Properties

```groovy
// Load build.properties
def buildProps = new Properties()
file('build.properties').withInputStream { buildProps.load(it) }

// Access properties
def releaseVersion = buildProps.getProperty('release.default.version')
def apacheVersion = buildProps.getProperty('bin.apache.version')
```

---

### Setting Properties

```groovy
// Set project properties
project.ext.releaseVersion = '2025.5.6'
project.ext.apacheVersion = '2.4.63'

// Access project properties
println "Release version: ${project.releaseVersion}"
println "Apache version: ${project.apacheVersion}"
```

---

## Task API

### Registering Tasks

```groovy
tasks.register('myTask') {
    group = 'build'
    description = 'My custom task'
    
    doFirst {
        println 'Before task execution'
    }
    
    doLast {
        println 'After task execution'
    }
}
```

---

### Task Dependencies

```groovy
tasks.register('taskA') {
    doLast {
        println 'Task A'
    }
}

tasks.register('taskB') {
    dependsOn 'taskA'
    doLast {
        println 'Task B'
    }
}
```

---

### Task Configuration

```groovy
tasks.register('configuredTask') {
    // Task group
    group = 'build'
    
    // Task description
    description = 'A configured task'
    
    // Task dependencies
    dependsOn 'initBuild', 'prepareBase'
    
    // Task inputs
    inputs.files('build.properties')
    
    // Task outputs
    outputs.dir('bin/release')
    
    // Task action
    doLast {
        println 'Executing configured task'
    }
}
```

---

## File Operations API

### Copy Files

```groovy
copy {
    from 'source'
    into 'destination'
    include '**/*.txt'
    exclude '**/*.tmp'
}
```

---

### Delete Files

```groovy
delete {
    delete 'bin'
    delete fileTree('tmp') {
        include '**/*.tmp'
    }
}
```

---

### Create Directories

```groovy
mkdir 'bin/release'
mkdir 'bin/tmp/getmodule'
```

---

### File Tree Operations

```groovy
fileTree('source') {
    include '**/*.java'
    exclude '**/test/**'
}.each { file ->
    println file.name
}
```

---

## Exec API

### Execute Command

```groovy
def process = new ProcessBuilder('cmd', '/c', 'echo', 'Hello')
    .directory(file('.'))
    .redirectErrorStream(true)
    .start()

process.inputStream.eachLine { line ->
    println line
}

def exitCode = process.waitFor()
if (exitCode != 0) {
    throw new GradleException("Command failed with exit code ${exitCode}")
}
```

---

### Execute with Output Capture

```groovy
def output = new ByteArrayOutputStream()
def process = new ProcessBuilder('cmd', '/c', 'dir')
    .directory(file('.'))
    .redirectErrorStream(true)
    .start()

process.inputStream.eachLine { line ->
    output.write(line.bytes)
    output.write('\n'.bytes)
}

process.waitFor()
println output.toString()
```

---

## Logger API

### Log Levels

```groovy
// Error
logger.error('Error message')

// Warning
logger.warn('Warning message')

// Info
logger.info('Info message')

// Debug
logger.debug('Debug message')

// Lifecycle
logger.lifecycle('Lifecycle message')
```

---

### Conditional Logging

```groovy
if (logger.isDebugEnabled()) {
    logger.debug('Debug information')
}

if (logger.isInfoEnabled()) {
    logger.info('Info message')
}
```

---

## Exception Handling

### Try-Catch

```groovy
try {
    downloadModule(repoUrl, version, destination)
} catch (Exception e) {
    logger.error("Failed to download module: ${e.message}")
    // Create fallback
    destination.mkdirs()
}
```

---

### Gradle Exception

```groovy
if (!file('build.properties').exists()) {
    throw new GradleException('build.properties not found')
}
```

---

### Stop Execution

```groovy
if (someCondition) {
    throw new StopExecutionException('Stopping execution')
}
```

---

## API Examples

### Example 1: Custom Module Download Task

```groovy
tasks.register('downloadCustomModule') {
    group = 'build'
    description = 'Download custom module'
    
    doLast {
        def repoUrl = 'https://github.com/myorg/my-module/releases'
        def version = '1.0.0'
        def destination = file("${releaseTarget}/custom")
        
        try {
            downloadModule(repoUrl, version, destination)
            logger.lifecycle("Downloaded custom module ${version}")
        } catch (Exception e) {
            logger.error("Failed to download custom module: ${e.message}")
        }
    }
}
```

---

### Example 2: Custom Compression Task

```groovy
tasks.register('compressCustom') {
    group = 'build'
    description = 'Compress custom directory'
    
    doLast {
        def sourceDir = file('custom')
        def outputFile = file("bin/release/custom-${releaseVersion}.7z")
        
        compressArchive(sourceDir, outputFile, '7z')
        generateChecksums(outputFile)
        
        logger.lifecycle("Created archive: ${outputFile.name}")
    }
}
```

---

### Example 3: Custom Token Replacement

```groovy
tasks.register('processCustomConfig') {
    group = 'build'
    description = 'Process custom configuration'
    
    doLast {
        def tokens = [
            'CUSTOM_VERSION': '1.0.0',
            'CUSTOM_NAME': 'MyApp',
            'CUSTOM_AUTHOR': 'John Doe'
        ]
        
        copy {
            from 'config/template.conf'
            into 'bin/config'
            rename { 'app.conf' }
            filter { line ->
                tokens.inject(line) { result, entry ->
                    result.replace("@${entry.key}@", entry.value)
                }
            }
        }
        
        logger.lifecycle('Processed custom configuration')
    }
}
```

---

### Example 4: Custom Verification Task

```groovy
tasks.register('verifyCustom') {
    group = 'verification'
    description = 'Verify custom requirements'
    
    doLast {
        def requiredFiles = [
            'build.properties',
            'custom.properties',
            'config/app.conf'
        ]
        
        def missing = requiredFiles.findAll { !file(it).exists() }
        
        if (missing) {
            throw new GradleException("Missing required files: ${missing.join(', ')}")
        }
        
        logger.lifecycle('All required files present')
    }
}
```

---

### Example 5: Custom Build Variant

```groovy
tasks.register('buildCustom') {
    group = 'build'
    description = 'Build custom variant'
    
    dependsOn 'initBuild', 'prepareBase'
    
    doLast {
        def releaseTarget = "${buildDir}/tmp/release/Bearsampp-custom-${releaseVersion}"
        
        // Download custom modules
        downloadModule(
            'https://github.com/Bearsampp/module-apache/releases',
            apacheVersion,
            file("${releaseTarget}/bin/apache")
        )
        
        downloadModule(
            'https://github.com/Bearsampp/module-php/releases',
            phpVersion,
            file("${releaseTarget}/bin/php")
        )
        
        // Compress
        def outputFile = file("${releaseDir}/Bearsampp-custom-${releaseVersion}.7z")
        compressArchive(file(releaseTarget), outputFile, '7z')
        generateChecksums(outputFile)
        
        logger.lifecycle("Built custom variant: ${outputFile.name}")
    }
}
```

---

## Best Practices

### 1. Error Handling

Always wrap risky operations in try-catch:

```groovy
try {
    downloadModule(repoUrl, version, destination)
} catch (Exception e) {
    logger.error("Download failed: ${e.message}")
    destination.mkdirs() // Fallback
}
```

---

### 2. Logging

Use appropriate log levels:

```groovy
logger.error('Critical error')    // Errors
logger.warn('Warning message')    // Warnings
logger.lifecycle('User message')  // Important messages
logger.info('Info message')       // Informational
logger.debug('Debug details')     // Debug only
```

---

### 3. Task Dependencies

Declare dependencies explicitly:

```groovy
tasks.register('myTask') {
    dependsOn 'initBuild', 'prepareBase'
    // ...
}
```

---

### 4. File Operations

Use Gradle's file API:

```groovy
// Good
def file = file('path/to/file')
def dir = file('path/to/dir')

// Avoid
def file = new File('path/to/file')
```

---

### 5. Properties

Use project properties for configuration:

```groovy
// Set
project.ext.myProperty = 'value'

// Access
println project.myProperty
```

---

## Additional Resources

- [Gradle DSL Reference](https://docs.gradle.org/current/dsl/)
- [Gradle API Documentation](https://docs.gradle.org/current/javadoc/)
- [Build Guide](BUILD_GUIDE.md) - Comprehensive build documentation
- [Task Reference](TASKS.md) - All available tasks
- [Configuration Guide](CONFIGURATION.md) - Configuration reference

---

**Last Updated**: 2025  
**Gradle Version**: 8.5  
**Status**: Production Ready ✅
