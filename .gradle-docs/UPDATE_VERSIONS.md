# Automatic Version Update Feature

## Overview

The build system now includes an `updateVersions` task that automatically fetches the latest version numbers from each module's GitHub repository and updates the `build.properties` file accordingly.

## How It Works

The `updateVersions` task:

1. **Auto-discovers modules** by scanning `build.properties` for any property matching the pattern:
   - `bin.{module}.version`
   - `app.{module}.version`
   - `tool.{module}.version`

2. **Fetches releases.properties files** from each module's GitHub repository (e.g., `https://raw.githubusercontent.com/Bearsampp/module-php/main/releases.properties`)

3. **Parses version numbers** from each releases.properties file and identifies the latest version using semantic versioning comparison

4. **Updates build.properties** with the latest versions for all discovered modules

5. **Runs early in the build process** - The task is automatically executed before `prepareBase`, ensuring all version numbers are up-to-date before the build begins

### Self-Adjusting Feature

**No Gradle file changes needed!** The task automatically discovers modules from `build.properties`, so when you add a new module:

1. Add the version property to `build.properties`:
   ```properties
   bin.newmodule.version = 1.0.0
   ```

2. The `updateVersions` task will automatically:
   - Detect the new module
   - Fetch its latest version from `https://github.com/Bearsampp/module-newmodule`
   - Update the version in `build.properties`

The repository name is automatically derived from the property name:
- `bin.apache.version` → `module-apache`
- `app.phpmyadmin.version` → `module-phpmyadmin`
- `tool.git.version` → `module-git`

**Special Cases:** If a repository name doesn't match the pattern (e.g., `bin.maria.version` → `module-mariadb`), it's handled via a mapping table in the Gradle file.

### Adding a New Module with Non-Standard Repository Name

If your new module's repository name doesn't follow the standard pattern, you'll need to add a mapping in `build.gradle`:

1. Add the version property to `build.properties`:
   ```properties
   bin.newmodule.version = 1.0.0
   ```

2. If the repository is named differently (e.g., `module-newmodule-special`), add a mapping in `build.gradle`:
   ```groovy
   def repoMappings = [
       'maria': 'module-mariadb',
       'powershell': 'module-powershell',
       'newmodule': 'module-newmodule-special'  // Add your mapping here
   ]
   ```

This is only needed for exceptions. Most modules follow the standard naming convention and work automatically.

## Usage

### Run Version Update Standalone

To update versions without building:

```bash
gradle updateVersions
```

This will:
- Fetch the latest versions from all module repositories
- Update the `build.properties` file
- Display a summary of updated versions

### Automatic Update During Build

The `updateVersions` task runs automatically when you execute any build task:

```bash
gradle release        # Updates versions, then builds all variants
gradle buildFull      # Updates versions, then builds full release
gradle buildBasic     # Updates versions, then builds basic release
gradle buildLite      # Updates versions, then builds lite release
gradle prepareBase    # Updates versions, then prepares base
```

## Example Output

```
*** Updating module versions from GitHub
* Fetching apache versions from https://raw.githubusercontent.com/Bearsampp/module-apache/main/releases.properties
  ✓ Latest version: 2.4.63
* Fetching php versions from https://raw.githubusercontent.com/Bearsampp/module-php/main/releases.properties
  ✓ Latest version: 8.4.6
* Fetching nodejs versions from https://raw.githubusercontent.com/Bearsampp/module-nodejs/main/releases.properties
  ✓ Latest version: 23.11.0
...

*** Updating build.properties
* Updated bin.apache.version: 2.4.63
* Updated bin.php.version: 8.4.6
* Updated bin.nodejs.version: 23.11.0
...

✓ build.properties updated successfully
```

## Module Repository Structure

Each module repository must have a `releases.properties` file in the main branch with the following format:

```properties
# releases.properties
23.11.0 = https://github.com/Bearsampp/module-nodejs/releases/download/v23.11.0/nodejs-23.11.0.7z
23.10.0 = https://github.com/Bearsampp/module-nodejs/releases/download/v23.10.0/nodejs-23.10.0.7z
23.9.0 = https://github.com/Bearsampp/module-nodejs/releases/download/v23.9.0/nodejs-23.9.0.7z
```

The task will automatically identify the highest version number (using semantic versioning) as the latest version.

## Error Handling

- If a module's releases.properties file cannot be fetched, the task will display an error message but continue processing other modules
- If no versions are found in a releases.properties file, a warning is displayed
- If a property is not found in build.properties, an error message is shown
- The build.properties file is only updated if at least one version was successfully fetched

## Integration with Build Process

The task dependency chain ensures versions are updated at the right time:

```
updateVersions → initBuild → prepareBase → buildFull/buildBasic/buildLite
```

This means:
1. Versions are fetched from GitHub first
2. Build environment is initialized
3. Base files are prepared with updated versions
4. Build proceeds with the latest module versions

## Benefits

- **Always uses latest versions**: No need to manually check and update version numbers
- **Reduces manual errors**: Eliminates typos and outdated version numbers
- **Saves time**: Automates a repetitive task
- **Consistent builds**: Ensures all modules use the latest compatible versions
- **Early execution**: Runs before any build operations, ensuring consistency throughout the build process
