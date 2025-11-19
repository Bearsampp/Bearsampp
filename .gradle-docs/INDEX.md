# Documentation Index

Complete index of all Gradle build documentation for Bearsampp.

---

## Quick Links

| Document              | Description                                      | Link                          |
|-----------------------|--------------------------------------------------|-------------------------------|
| **Main Documentation**| Complete build system guide                      | [README.md](README.md)        |
| **Quick Start**       | Get started quickly                              | [QUICKSTART.md](QUICKSTART.md)|
| **Build Guide**       | Comprehensive build documentation                | [BUILD_GUIDE.md](BUILD_GUIDE.md)|
| **Task Reference**    | All available Gradle tasks                       | [TASKS.md](TASKS.md)          |
| **Configuration**     | Configuration files and properties               | [CONFIGURATION.md](CONFIGURATION.md) |
| **API Reference**     | Build script API and helper functions            | [API.md](API.md)              |
| **Migration Guide**   | Ant to Gradle migration guide                    | [MIGRATION_GUIDE.md](MIGRATION_GUIDE.md)|
| **Implementation**    | Current implementation status                    | [IMPLEMENTATION_STATUS.md](IMPLEMENTATION_STATUS.md)|

---

## Documentation Structure

```
.gradle-docs/
├── INDEX.md                      # This file - Documentation index
├── README.md                     # Main documentation and overview
├── QUICKSTART.md                 # Quick start guide
├── BUILD_GUIDE.md                # Comprehensive build guide
├── TASKS.md                      # Complete task reference
├── CONFIGURATION.md              # Configuration guide
├── API.md                        # API reference for build scripts
├── MIGRATION_GUIDE.md            # Ant to Gradle migration guide
└── IMPLEMENTATION_STATUS.md      # Implementation status and roadmap
```

---

## Getting Started

### New Users

1. **Start Here**: [README.md](README.md) - Overview and quick start
2. **Quick Start**: [QUICKSTART.md](QUICKSTART.md) - Get building quickly
3. **List Tasks**: Run `.\gradlew tasks` to see available tasks
4. **Build Release**: Run `.\gradlew buildLite` for fastest build

### Migrating from Ant

1. **Migration Guide**: [MIGRATION_GUIDE.md](MIGRATION_GUIDE.md) - Complete migration guide
2. **Command Mapping**: See command equivalents in migration guide
3. **File Changes**: Understand what changed from Ant to Gradle
4. **Troubleshooting**: Common migration issues and solutions

### Advanced Users

1. **Task Reference**: [TASKS.md](TASKS.md) - All tasks with examples
2. **Configuration**: [CONFIGURATION.md](CONFIGURATION.md) - Advanced configuration
3. **API Reference**: [API.md](API.md) - Build script API and extensions
4. **Build Guide**: [BUILD_GUIDE.md](BUILD_GUIDE.md) - Deep dive into build system

---

## Documentation by Topic

### Build System

| Topic                 | Document              | Section                          |
|-----------------------|-----------------------|----------------------------------|
| Overview              | README.md             | Overview                         |
| Quick Start           | QUICKSTART.md         | Quick Start                      |
| Installation          | README.md             | Installation                     |
| Architecture          | BUILD_GUIDE.md        | Architecture                     |
| Build Process         | BUILD_GUIDE.md        | Build Variants                   |

### Tasks

| Topic                 | Document              | Section                          |
|-----------------------|-----------------------|----------------------------------|
| Build Tasks           | TASKS.md              | Build Tasks                      |
| Verification Tasks    | TASKS.md              | Verification Tasks               |
| Information Tasks     | TASKS.md              | Information Tasks                |
| Task Examples         | TASKS.md              | Task Examples                    |
| Task Reference        | BUILD_GUIDE.md        | Available Tasks                  |

### Configuration

| Topic                 | Document              | Section                          |
|-----------------------|-----------------------|----------------------------------|
| Build Properties      | CONFIGURATION.md      | Build Properties                 |
| Gradle Properties     | CONFIGURATION.md      | Gradle Properties                |
| Module Versions       | CONFIGURATION.md      | Module Configuration             |
| Token Replacement     | CONFIGURATION.md      | Token Replacement                |
| Environment Variables | CONFIGURATION.md      | Environment Variables            |

### API

| Topic                 | Document              | Section                          |
|-----------------------|-----------------------|----------------------------------|
| Build Script API      | API.md                | Build Script API                 |
| Helper Functions      | API.md                | Helper Functions                 |
| Extension Points      | API.md                | Extension Points                 |
| Properties API        | API.md                | Properties API                   |
| Task API              | API.md                | Task API                         |

### Migration

| Topic                 | Document              | Section                          |
|-----------------------|-----------------------|----------------------------------|
| Overview              | MIGRATION_GUIDE.md    | Overview                         |
| What Changed          | MIGRATION_GUIDE.md    | What Changed                     |
| Command Mapping       | MIGRATION_GUIDE.md    | Command Mapping                  |
| File Changes          | MIGRATION_GUIDE.md    | File Changes                     |
| Troubleshooting       | MIGRATION_GUIDE.md    | Troubleshooting                  |

---

## Common Tasks

### Building

| Task                                      | Document      | Reference                        |
|-------------------------------------------|---------------|----------------------------------|
| Build lite release                        | QUICKSTART.md | buildLite                        |
| Build basic release                       | QUICKSTART.md | buildBasic                       |
| Build full release                        | QUICKSTART.md | buildFull                        |
| Build all variants                        | QUICKSTART.md | release                          |
| Clean build artifacts                     | TASKS.md      | clean task                       |

### Configuration

| Task                                      | Document      | Reference                        |
|-------------------------------------------|---------------|----------------------------------|
| Configure build properties                | CONFIGURATION.md | Build Properties              |
| Configure module versions                 | CONFIGURATION.md | Module Configuration          |
| Configure archive format                  | CONFIGURATION.md | Archive Configuration         |
| Configure token replacement               | CONFIGURATION.md | Token Replacement             |

### Verification

| Task                                      | Document      | Reference                        |
|-------------------------------------------|---------------|----------------------------------|
| Verify language files                     | TASKS.md      | checkLang task                   |
| List available tasks                      | QUICKSTART.md | Common Commands                  |

### Application

| Task                                      | Document      | Reference                        |
|-------------------------------------------|---------------|----------------------------------|
| Launch Bearsampp                          | TASKS.md      | launch task                      |
| Sync to sandbox                           | TASKS.md      | sync task                        |

---

## Quick Reference

### Essential Commands

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

### Essential Files

| File                  | Purpose                                  |
|-----------------------|------------------------------------------|
| `build.gradle`        | Main Gradle build script                 |
| `settings.gradle`     | Gradle project settings                  |
| `build.properties`    | Build configuration                      |
| `gradle.properties`   | Gradle-specific settings (optional)      |

### Essential Directories

| Directory             | Purpose                                  |
|-----------------------|------------------------------------------|
| `base/`               | Base Bearsampp files                     |
| `core/`               | Core PHP classes and scripts             |
| `bin/`                | Build output directory                   |
| `bin/release/`        | Final release archives                   |
| `bin/tmp/`            | Temporary build files                    |
| `.gradle-docs/`       | Gradle documentation                     |

---

## Search by Keyword

### A-C

| Keyword               | Document              | Section                          |
|-----------------------|-----------------------|----------------------------------|
| API                   | API.md                | All sections                     |
| Apache                | CONFIGURATION.md      | Module Configuration             |
| Archive               | CONFIGURATION.md      | Archive Configuration            |
| Build                 | TASKS.md              | Build Tasks                      |
| Cache                 | BUILD_GUIDE.md        | Advanced Features                |
| Clean                 | TASKS.md              | clean task                       |
| Configuration         | CONFIGURATION.md      | All sections                     |
| Compression           | BUILD_GUIDE.md        | Compression and Checksums        |

### D-L

| Keyword               | Document              | Section                          |
|-----------------------|-----------------------|----------------------------------|
| Download              | BUILD_GUIDE.md        | Module Download System           |
| Gradle                | README.md             | All sections                     |
| Helper Functions      | API.md                | Helper Functions                 |
| Installation          | README.md             | Installation                     |
| Launch                | TASKS.md              | launch task                      |

### M-R

| Keyword               | Document              | Section                          |
|-----------------------|-----------------------|----------------------------------|
| Migration             | MIGRATION_GUIDE.md    | All sections                     |
| Modules               | BUILD_GUIDE.md        | Module Download System           |
| MySQL                 | CONFIGURATION.md      | Module Configuration             |
| PHP                   | CONFIGURATION.MD      | Module Configuration             |
| Properties            | CONFIGURATION.md      | Build Properties                 |
| Release               | TASKS.md              | release task                     |

### S-Z

| Keyword               | Document              | Section                          |
|-----------------------|-----------------------|----------------------------------|
| Sync                  | TASKS.md              | sync task                        |
| Tasks                 | TASKS.md              | All sections                     |
| Token                 | CONFIGURATION.md      | Token Replacement                |
| Troubleshooting       | BUILD_GUIDE.md        | Troubleshooting                  |
| Variants              | BUILD_GUIDE.md        | Build Variants                   |
| Verification          | TASKS.md              | Verification Tasks               |

---

## Document Summaries

### README.md

**Purpose**: Main documentation and overview

**Contents**:
- Overview of the Gradle build system
- Quick start guide with basic commands
- System requirements
- Key features
- Build variants comparison
- Configuration overview
- Support information

**Target Audience**: All users, especially new users

---

### QUICKSTART.md

**Purpose**: Quick start guide for common tasks

**Contents**:
- Prerequisites
- Quick start commands
- Common commands reference
- Platform-specific commands
- Build variants explained
- Configuration basics
- Output locations
- Troubleshooting quick fixes
- Performance tips

**Target Audience**: New users and developers

---

### BUILD_GUIDE.md

**Purpose**: Comprehensive build system documentation

**Contents**:
- Complete overview
- Installation instructions
- All available tasks with details
- Build variants comparison
- Module download system
- Compression and checksums
- Sync task details
- Configuration reference
- Advanced features
- Troubleshooting guide

**Target Audience**: All users, comprehensive reference

---

### TASKS.md

**Purpose**: Complete reference for all Gradle tasks

**Contents**:
- Build tasks (buildLite, buildBasic, buildFull, release)
- Setup tasks (initBuild, prepareBase)
- Verification tasks (checkLang)
- Application tasks (launch, sync)
- Task dependencies and execution order
- Task examples and usage patterns
- Task options and properties

**Target Audience**: Developers and build engineers

---

### CONFIGURATION.md

**Purpose**: Configuration guide for build system

**Contents**:
- Configuration files overview
- Build properties reference
- Gradle properties reference
- Module version configuration
- Token replacement system
- Environment variables
- Configuration examples
- Best practices

**Target Audience**: Build engineers and advanced users

---

### API.md

**Purpose**: API reference for build scripts

**Contents**:
- Build script API
- Helper functions reference
- Extension points
- Properties API
- Task API
- File operations API
- Exec API
- Logger API
- Exception handling
- API examples

**Target Audience**: Advanced users and contributors

---

### MIGRATION_GUIDE.md

**Purpose**: Guide for migrating from Ant to Gradle

**Contents**:
- Migration overview
- What changed from Ant to Gradle
- Command mapping (Ant to Gradle)
- File changes
- Configuration changes
- Task equivalents
- Troubleshooting migration issues
- Benefits of migration
- Next steps

**Target Audience**: Users migrating from Ant build system

---

### IMPLEMENTATION_STATUS.md

**Purpose**: Current implementation status and roadmap

**Contents**:
- Implementation status
- Completed features
- Known issues
- Future enhancements
- Testing status
- Documentation status

**Target Audience**: Contributors and project maintainers

---

## Version History

| Version       | Date       | Changes                                  |
|---------------|------------|------------------------------------------|
| 2025.5.6      | 2025       | Initial Gradle documentation             |
|               |            | - Created comprehensive documentation    |
|               |            | - Migrated from Ant to Gradle            |
|               |            | - Added INDEX.md                         |

---

## Contributing

To contribute to the documentation:

1. **Fork Repository**: Fork the Bearsampp repository
2. **Edit Documentation**: Make changes to documentation files
3. **Follow Style**: Maintain consistent formatting and style
4. **Test Examples**: Verify all code examples work
5. **Submit PR**: Create pull request with changes

### Documentation Style Guide

- Use Markdown formatting
- Include code examples
- Use tables for structured data
- Add links to related sections
- Keep language clear and concise
- Include practical examples

---

## Support

For documentation issues or questions:

- **GitHub Issues**: https://github.com/bearsampp/bearsampp/issues
- **Documentation**: This directory (.gradle-docs/)
- **Website**: https://bearsampp.com

---

**Last Updated**: 2025  
**Version**: 2025.5.6  
**Total Documents**: 8
