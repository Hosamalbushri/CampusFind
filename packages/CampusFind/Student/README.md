# CampusFind Student Package

The **CampusFind Student Package** provides university authentication, student identity persistence, and administrative management capabilities for Laraseed V4.

## Features
- **University API Authentication**: Authenticates students directly against university endpoints on first login with SSL enforcement and SSRF protection.
- **Local Persistence & Sessions**: Subsequent logins use hashed credentials, full session lifecycle protection, and rate limiting.
- **Admin Management**: Full CRUD capabilities, profile image management with automatic storage cleanup, and mass deletion within database transactions.
- **DataGrid & MegaSearch**: Interactive table with sorting, filtering, row/mass actions, and search contributions.
- **Multilingual Support**: Fully localized across 7 locales (`ar`, `en`, `es`, `fa`, `pt_BR`, `tr`, `vi`) with 100% key parity.
- **Strict Isolation**: Complies with Laraseed Foundation architecture rules (Rules 06, 07, 08, 09, 11).

## Documentation
- [Installation Guide](docs/INSTALLATION.md)
- [Configuration Reference](docs/CONFIGURATION.md)
- [Authentication Architecture](docs/AUTHENTICATION.md)
- [Admin Integration](docs/ADMIN_INTEGRATION.md)
- [Testing & Quality Assurance](docs/TESTING.md)
- [Architecture Manifest](ARCHITECTURE.md)
