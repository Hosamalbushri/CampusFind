# Configuration Reference

The package configuration is merged from `src/Config/lost_found.php` and `src/Config/filesystems.php`.

---

## 1. Filesystem Configuration (`filesystems.php`)

```php
'lost_found_private' => [
    'driver' => 'local',
    'root' => storage_path('app/lost-found-private'),
    'throw' => true,
],
```

---

## 2. Image Processing & Limits (`lost_found.php`)

| Config Key | Environment Variable | Default Value | Description |
|---|---|---|---|
| `claim_evidence_images.disk` | N/A | `lost_found_private` | Target storage disk for private claim evidence |
| `claim_evidence_images.max_bytes` | `LOST_FOUND_EVIDENCE_IMAGE_MAX_BYTES` | `2097152` (2 MB) | Maximum upload file size |
| `claim_evidence_images.max_width` | `LOST_FOUND_EVIDENCE_IMAGE_MAX_WIDTH` | `4096` | Maximum image width in pixels |
| `claim_evidence_images.max_height` | `LOST_FOUND_EVIDENCE_IMAGE_MAX_HEIGHT` | `4096` | Maximum image height in pixels |
| `claim_evidence_images.max_pixels` | `LOST_FOUND_EVIDENCE_IMAGE_MAX_PIXELS` | `12000000` | Maximum total pixels limit |
| `claim_evidence_images.jpeg_quality` | `LOST_FOUND_EVIDENCE_JPEG_QUALITY` | `90` | Re-encoding JPEG quality |
| `claim_evidence_images.png_compression` | `LOST_FOUND_EVIDENCE_PNG_COMPRESSION` | `6` | PNG compression level (0-9) |
| `claim_evidence_images.webp_quality` | `LOST_FOUND_EVIDENCE_WEBP_QUALITY` | `90` | WebP quality factor (0-100) |

---

## 3. Role-Based Access Control (`acl.php`)

The package registers the following granular ACL nodes under `lost_found`:
- `lost_found`: Lost & Found Module Root
- `lost_found.items.view`: View found items and DataGrid
- `lost_found.items.create`: Register new found items
- `lost_found.items.edit`: Edit found items and upload item images
- `lost_found.claims.view`: View claims and evidence details
- `lost_found.claims.review`: Submit claim reviews and messages
- `lost_found.claims.approve`: Approve claims and revoke approvals
- `lost_found.claims.reject`: Reject claims
- `lost_found.custody.manage`: Log, transfer, and relocate custody
- `lost_found.handover.complete`: Execute physical handover and verify recipient
- `lost_found.settings.categories`: Manage item categories
