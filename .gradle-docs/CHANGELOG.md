# Gradle Build System Changelog

## 2025 - Migration Complete

### Major Changes

#### Ant Build System Removed
- **Removed**: `build.xml` - Legacy Ant build file
- **Status**: Gradle is now the only build system
- **Migration**: Phase 3 complete

#### Documentation Reorganization
- **Moved**: `.docs-gradle/` → `.gradle-docs/`
- **Reason**: Better naming convention (hidden directory with clear purpose)
- **Updated**: All documentation references and links

#### Build System Fixes
- **Fixed**: All `exec()` method calls replaced with `ProcessBuilder`
- **Reason**: Gradle 9.x compatibility and deprecation warnings
- **Impact**: Build system now fully compatible with modern Gradle versions

### Documentation Updates

#### README.md (Root)
- Added "Building from Source" section
- Added links to all Gradle documentation
- Added quick build examples
- Improved discoverability of build system

#### MIGRATION_GUIDE.md
- Updated to reflect build.xml removal
- Marked as historical reference
- Updated migration status to Phase 3 (Complete)
- Maintained for understanding differences between Ant and Gradle

#### All Documentation Files
- Maintained in `.gradle-docs/` directory:
  - `README.md` - Overview and quick links
  - `QUICKSTART.md` - Quick start guide
  - `BUILD_GUIDE.md` - Comprehensive build documentation
  - `GRADLE_README.md` - Gradle-specific information
  - `MIGRATION_GUIDE.md` - Historical Ant to Gradle migration
  - `IMPLEMENTATION_STATUS.md` - Current implementation status
  - `CHANGELOG.md` - This file

### Technical Improvements

#### ProcessBuilder Implementation
All external process executions now use Java's `ProcessBuilder` API:

**Before (Deprecated):**
```groovy
project.exec {
    commandLine 'cmd', '/c', 'command'
}
```

**After (Modern):**
```groovy
def process = new ProcessBuilder('cmd', '/c', 'command')
    .directory(workingDir)
    .redirectErrorStream(true)
    .start()
process.waitFor()
```

**Benefits:**
- ✅ No deprecation warnings
- ✅ Compatible with Gradle 9.x and future versions
- ✅ Better error handling
- ✅ More control over process execution

#### Affected Components
- `prepareBase` task - rcedit-x64.exe and fix-core-icon.bat
- `downloadModule` helper - 7-Zip extraction
- `compressArchive` helper - 7-Zip compression
- `sync` task - rcedit-x64.exe and ResourceHacker.exe
- `launch` task - bearsampp.exe execution

### File Changes Summary

#### Deleted Files
- `build.xml` - Ant build file (no longer needed)

#### Moved Directories
- `.docs-gradle/` → `.gradle-docs/`

#### Modified Files
- `README.md` - Added build documentation section
- `build.gradle` - Fixed all exec() calls
- `.gradle-docs/MIGRATION_GUIDE.md` - Updated migration status
- `.gradle-docs/CHANGELOG.md` - Created this file

### Migration Status

| Phase                        | Status       | Date      | Notes                    |
|------------------------------|--------------|-----------|--------------------------|
| Phase 1: Parallel Operation  | ✅ Complete  | 2024      | Both systems coexisted   |
| Phase 2: Transition          | ✅ Complete  | 2024-2025 | Gradle became primary    |
| Phase 3: Complete Migration  | ✅ Complete  | 2025      | Ant build.xml removed    |

### Breaking Changes

#### For Developers
- **Ant commands no longer work** - Must use Gradle
- **build.xml removed** - No fallback to Ant
- **Documentation moved** - Check `.gradle-docs/` instead of `.docs-gradle/`

#### Migration Path
```bash
# Old (Ant) - NO LONGER WORKS
ant build-full

# New (Gradle) - USE THIS
.\gradlew buildFull
```

### Compatibility

#### Gradle Version
- **Minimum**: Gradle 8.5 (via wrapper)
- **Tested**: Gradle 8.5, 9.0, 9.2
- **Recommended**: Use included wrapper (`gradlew`)

#### Java Version
- **Minimum**: JDK 8
- **Recommended**: JDK 11 or higher
- **Tested**: JDK 8, 11, 17, 21

### Known Issues

None. All known issues from the Ant migration have been resolved.

### Future Plans

#### Short Term
- Monitor build performance
- Gather user feedback
- Optimize build times further

#### Long Term
- Explore Gradle configuration cache
- Consider Gradle build cache server
- Investigate parallel module downloads
- Add more build variants if needed

### Documentation

All documentation is now located in `.gradle-docs/`:

- **Quick Start**: [QUICKSTART.md](.gradle-docs/QUICKSTART.md)
- **Build Guide**: [BUILD_GUIDE.md](.gradle-docs/BUILD_GUIDE.md)
- **Gradle Info**: [GRADLE_README.md](.gradle-docs/GRADLE_README.md)
- **Migration**: [MIGRATION_GUIDE.md](.gradle-docs/MIGRATION_GUIDE.md)
- **Status**: [IMPLEMENTATION_STATUS.md](.gradle-docs/IMPLEMENTATION_STATUS.md)

### Support

For questions or issues:
1. Check documentation in `.gradle-docs/`
2. Run with `--stacktrace` for debugging
3. Report issues on GitHub
4. Check Gradle forums for Gradle-specific questions

### Acknowledgments

Thanks to all contributors who helped with:
- Testing the Gradle build system
- Providing feedback during migration
- Reporting and fixing issues
- Improving documentation

---

**Last Updated**: 2025  
**Gradle Version**: 8.5+  
**Status**: Production Ready ✅  
**Migration**: Complete ✅
