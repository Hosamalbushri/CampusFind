# ADMIN COMPONENT INVENTORY — FORENSIC ARCHITECTURAL AUDIT

## 1. Overview & Forensic Discovery

The `Webkul\Admin` package acts as the enterprise reference implementation for component construction, Blade/Vue integration, form handling, and reactive UI architecture in the Laraseed/Bagisto platform.

Component Namespace: `<x-admin::...>`
Component View Root: `packages/Webkul/Admin/src/Resources/views/components/`
Assets Entry Point: `packages/Webkul/Admin/src/Resources/assets/js/app.js`

---

## 2. Complete Component Taxonomy & Inventory

### A. Layout & Navigation Components

| Component Name | Source Path | Props / Inputs | Slots | Vue / JS Integration | Classification |
|---|---|---|---|---|---|
| `<x-admin::layouts>` | `layouts/index.blade.php` | `title`, `hasFeature` | `slot`, `meta`, `styles`, `scripts` | `window.app.mount("#app")` on window load event; manages sidebar collapse state via ref | Layout |
| `<x-admin::layouts.anonymous>` | `layouts/anonymous.blade.php` | `title` | `slot`, `meta`, `styles`, `scripts` | `window.app.mount("#app")` on window load; standalone viewport | Layout |
| `<x-admin::layouts.header>` | `layouts/header/index.blade.php` | None | None | Emits search/mega-search queries via `$emitter` | Header / Nav |
| `<x-admin::layouts.sidebar.desktop>` | `layouts/sidebar/desktop/index.blade.php` | None | None | Tracks hover state, active menus, flyout navigation | Navigation |
| `<x-admin::layouts.sidebar.mobile>` | `layouts/sidebar/mobile/index.blade.php` | None | None | Responsive drawer and touch disclosure | Navigation |
| `<x-admin::layouts.tabs>` | `layouts/tabs.blade.php` | `tabs` (array with title, url, active, badge) | None | Pure Blade navigation bar for sub-settings | Navigation |
| `<x-admin::breadcrumbs>` | `breadcrumbs/index.blade.php` | `name`, `entity` | None | Laravel Breadcrumbs integration | Navigation |

---

### B. Form System Primitives & Control Groups

| Component Name | Source Path | Props / Inputs | Slots | Vue / JS Integration | Form Behavior |
|---|---|---|---|---|---|
| `<x-admin::form>` | `form/index.blade.php` | `method`, `action`, `as` | `default` | `<v-form>`, `onInvalidSubmit` scrolls smoothly to first error field | Handles CSRF (`@csrf`), method spoofing (`@method`), `@invalid-submit`, `:initial-errors` |
| `<x-admin::form.control-group>` | `form/control-group/index.blade.php` | `$attributes` | `default` | None (Blade container `mb-4`) | Standardized spacing container |
| `<x-admin::form.control-group.label>` | `form/control-group/label.blade.php` | `$attributes` | `default` | None | Label styling with `.required` asterisk support |
| `<x-admin::form.control-group.control>` | `form/control-group/control.blade.php` | `type`, `name`, `value`, `rules`, `label` | `default` (for select/custom) | `<v-field>`, `v-bind="field"`, `v-checked-handler` | Two-way binding, client & server validation border triggers |
| `<x-admin::form.control-group.error>` | `form/control-group/error.blade.php` | `name`, `controlName` | None | `<v-error-message>` | Displays validation error messages reactively |
| `<x-admin::form.control-group.controls.tags>` | `form/control-group/controls/tags.blade.php` | `name`, `data` | None | `v-tags` | Multi-tag input with enter/comma tokenizer |
| `<x-admin::flat-picker.date>` | `flat-picker/date.blade.php` | `name`, `value`, `allowInput`, `disable`, `minDate`, `maxDate` | `default` (input) | `v-date-picker` wrapping Flatpickr JS library | Date picker with dynamic locale and theme support |
| `<x-admin::flat-picker.datetime>` | `flat-picker/datetime.blade.php` | `name`, `value`, `allowInput`, `disable`, `minDate`, `maxDate` | `default` (input) | `v-datetime-picker` wrapping Flatpickr JS library | Datetime picker with 24-hr format and time picker |

---

### C. Feedback, Alerts & Overlay Primitives

| Component Name | Source Path | Props / Inputs | Slots | Vue / JS Integration | Classification |
|---|---|---|---|---|---|
| `<x-admin::flash-group>` | `flash-group/index.blade.php` | None | None | `v-flash-group`, listens to `$emitter.on('add-flash')` and parses session flash | Feedback |
| `<x-admin::flash-group.item>` | `flash-group/item.blade.php` | `flash` | None | `v-flash-item`, animated timer ring, pause on hover | Feedback |
| `<x-admin::modal>` | `modal/index.blade.php` | `isActive`, `position`, `size` | `toggle`, `header`, `content`, `footer` | `v-modal`, backdrop overlay, escape key, body scroll lock | Overlay |
| `<x-admin::modal.confirm>` | `modal/confirm.blade.php` | None | None | `v-modal-confirm`, listens to `$emitter.on('open-confirm-modal')` | Overlay / Dialog |
| `<x-admin::drawer>` | `drawer/index.blade.php` | `isActive`, `position`, `width` | `toggle`, `header`, `content`, `footer` | `v-drawer`, slide-out panel with overlay | Overlay |
| `<x-admin::dropdown>` | `dropdown/index.blade.php` | `position` | `toggle`, `content`, `menu` | `v-dropdown`, click-outside handler, keyboard escape | Overlay |
| `<x-admin::spinner>` | `spinner/index.blade.php` | None | None | SVG animate-spin element | Feedback |

---

### D. Data Display, Tables & Media

| Component Name | Source Path | Props / Inputs | Slots | Vue / JS Integration | Classification |
|---|---|---|---|---|---|
| `<x-admin::accordion>` | `accordion/index.blade.php` | `isActive` | `header`, `content` | `v-accordion`, collapse/expand state, emits toggle | Interactive |
| `<x-admin::tabs>` | `tabs/index.blade.php` | `position` | `default` | `v-tabs`, child tab registration, tab switching | Interactive |
| `<x-admin::tabs.item>` | `tabs/item.blade.php` | `title`, `isSelected` | `default` | `v-tab-item`, registers with parent `$parent.tabs` | Interactive |
| `<x-admin::tags>` | `tags/index.blade.php` | `name`, `data` | None | `v-tags`, tag management and chip rendering | Interactive |
| `<x-admin::media.images>` | `media/images.blade.php` | `name`, `allowMultiple`, `showPlaceholders`, `uploadedImages`, `width`, `height` | None | `v-media-images`, `v-media-image-item`, `draggable` integration | Form / Media |
| `<x-admin::table>` | `table/index.blade.php` | `$attributes` | `default` | Pure Blade semantic table wrapper | Data Display |
| `<x-admin::table.thead>` | `table/thead/index.blade.php` | `$attributes` | `default` | Pure Blade `<thead>` | Data Display |
| `<x-admin::table.tbody>` | `table/tbody/index.blade.php` | `$attributes` | `default` | Pure Blade `<tbody>` | Data Display |
| `<x-admin::table.th>` | `table/th.blade.php` | `$attributes` | `default` | Pure Blade `<th>` | Data Display |
| `<x-admin::table.td>` | `table/td.blade.php` | `$attributes` | `default` | Pure Blade `<td>` | Data Display |

---

### E. Domain & Admin-Specific Exclusions (Documented Justification)

| Admin Component | Reason for Exclusion in Public Web Package |
|---|---|
| `<x-admin::shimmer.dashboard.*>` | Specific to Admin CRM/Lead metrics dashboard charts |
| `<x-admin::shimmer.leads.*>` | Specific to Admin CRM Leads and Kanban pipeline stages |
| `<x-admin::shimmer.mail.*>` | Specific to Admin Mailbox and IMAP integration |
| `<x-admin::shimmer.quotes.*>` | Specific to Admin Quote generator |
| `<x-admin::tree.view>` | Specific to Admin ACL permission hierarchy tree |
| `<x-admin::tinymce>` | Heavy WYSIWYG editor for Admin CMS/Email templating |
| `<x-admin::activities.*>` | Specific to Admin CRM activity stream |

---

## 3. Verified Script & State Flow Pattern

Every interactive Admin component adheres strictly to the following 5-point contract:
1. **Blade Component Scaffolding**: Declares props via `@props` and outputs custom Vue tag (e.g. `<v-accordion>`, `<v-modal>`).
2. **Template Projection**: Uses `<template v-slot:...>` to project Blade slot markup into Vue slots.
3. **Lazy-Registration Stack**: Employs `@pushOnce('scripts')` with `<script type="text/x-template" id="...">` and `app.component(...)`.
4. **Shimmer Loading State**: Renders an initial skeleton placeholder that is replaced seamlessly upon Vue mounting.
5. **Unified Mounting Point**: The layout invokes `app.mount("#app")` inside `window.addEventListener("load")`, preventing mount-time race conditions.
