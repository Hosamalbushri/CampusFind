# Security Model & Invariants

## 1. Threat Mitigation & Invariants

### 1.1 Non-Secret Verification
- Verification methods for handover (e.g. government ID, student card inspection) **must never accept or store authentication secrets** such as passwords, OTPs, PINs, or raw cryptographic tokens.
- Enforced at both request validation and service level by `SecurityInvariants::assertNoAuthSecrets()`.

### 1.2 Encrypted Storage at Rest
- Sensitive domain fields are encrypted at rest via Eloquent `'encrypted'` casting and hidden from serialization:
  - `lost_found_item_private_details`: `identifying_details`, `serial_fragment`, `staff_notes`
  - `lost_found_reports`: `private_description`
  - `lost_found_claim_evidence`: `text_value`, `file_path`, `original_name`
  - `lost_found_claim_reviews`: `claimant_message`, `staff_notes`
  - `lost_found_custody_records`: `from_storage_location`, `to_storage_location`, `notes`
  - `lost_found_handovers`: `verification_method`, `verification_note`

### 1.3 Image Raster Sanitization
- `LostFoundRasterSanitizer` decodes uploaded images with PHP GD and re-encodes them to brand new binary streams.
- Strips all EXIF, GPS, IPTC, and thumbnail metadata.
- Prevents polyglot file uploads, embedded PHP scripts, and format desynchronization attacks.
- Enforces strict pixel, dimension, and byte bounds before writing to disk.

### 1.4 Append-Only Audit History
- `ClaimEvidence`, `ClaimReview`, `CustodyRecord`, `Handover`, and `LostReportImage` models disallow `updating` and `deleting` events via model boot hooks to guarantee immutable forensic logs.

### 1.5 Existence Hiding & Enumeration Protection
- Accessing claim or report resources belonging to other students yields `404 Not Found` rather than `403 Forbidden` to prevent user and reference enumeration.
