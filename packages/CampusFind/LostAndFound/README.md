# CampusFind Lost and Found Package (`campus-find/lost-and-found`)

An enterprise-grade, optional domain package for campus lost-and-found inventory, student claims, custody lifecycle tracking, and physical item handover, architected for **Laraseed V4** on top of Krayin CRM.

---

## Key Features

- **Decoupled Architecture**: Clean domain boundaries respecting Laraseed V4 package design rules.
- **Student Integration**: Seamlessly integrates with `CampusFind\Student` for student identity verification and self-service.
- **Complete Custody Lifecycle**: Append-only custody logs (`LOGGED`, `TRANSFERRED`, `STORAGE_LOCATION_CHANGED`, `HANDED_OVER`).
- **Claim State Machine**: Robust claim transitions (`SUBMITTED`, `UNDER_REVIEW`, `NEEDS_INFORMATION`, `APPROVED`, `REJECTED`, `WITHDRAWN`).
- **Cryptographic & Privacy Protection**: AES-256 encrypted private descriptions, staff notes, and sensitive verification data.
- **Secure Image Ingestion**: GD raster re-encoding, EXIF/GPS stripping, byte/dimension limits, and disk compensation on transaction failure.
- **7-Locale Parity**: Full multilingual support across Arabic (`ar`), English (`en`), Spanish (`es`), Persian (`fa`), Portuguese (`pt_BR`), Turkish (`tr`), and Vietnamese (`vi`).
- **100% Test Coverage**: 309 unit and feature tests covering HTTP endpoints, authorization bouncers, state machines, image sanitation, and DB rollbacks.

---

## Documentation

- [Architecture & Domain Design](ARCHITECTURE.md)
- [Installation Guide](docs/INSTALLATION.md)
- [Configuration Reference](docs/CONFIGURATION.md)
- [Security Model & Invariants](docs/SECURITY.md)
- [Testing & Verification](docs/TESTING.md)

---

## License

MIT License.
