# MANDATORY WEB COMPONENT ARCHITECTURE RULES

## 1. Scope & Authority

These rules govern the architecture, construction, registration, extension, and consumption of all Blade and Vue components in `packages/CampusFind/Web`.

All developers, automated agents, and code generators modifying the Web package MUST strictly adhere to these rules without exception.

---

## 2. Component Namespace & Discovery

1. **Namespace Isolation**:
   - All Web components must be registered under the `web` namespace (`<x-web::...>`) with backward-compatible alias `campusfind_web_web` (`<x-campusfind_web_web::...>`).
   - Direct consumption of Admin presentation components (`<x-admin::...>`) inside Web views is **strictly prohibited**.
2. **Component Discovery**:
   - Registered via `Blade::anonymousComponentPath(__DIR__ . '/../Resources/views/components', 'web')` in `WebServiceProvider`.

---

## 3. Component Construction & Vue 3 Contract

Every interactive component in `packages/CampusFind/Web` must follow the verified reference pattern:

1. **Blade Scaffolding**:
   - Declare properties explicitly via `@props([...])`.
   - Merge attributes cleanly via `$attributes->merge([...])`.
   - Render a custom Vue element tag (e.g. `<v-accordion>`, `<v-modal>`, `<v-tabs>`, `<v-flash-group>`, `<v-button>`).
2. **Template Shimmer Placeholders**:
   - Include a matching shimmer component (e.g. `<x-web::shimmer.accordion />`) to provide immediate skeleton feedback before Vue mounts.
3. **Scoped Slot Projection**:
   - Map Blade slots into Vue template slots with proper scope bindings (e.g. `<template v-slot:header="{ toggle, isOpen }">`).
4. **Script Stack Deduplication**:
   - Wrap Vue templates (`<script type="text/x-template" id="v-*-template">`) and component definitions (`app.component('v-*', {...})`) inside `@pushOnce('scripts')`.
   - Never register components outside `@pushOnce('scripts')` to prevent duplicate script execution when multiple component instances exist on a single page.
5. **Unified Vue Lifecycle**:
   - `window.app = createApp({...})` is initialized once in `src/Web/Resources/assets/js/app.js`.
   - Vue mounts on `#app` strictly upon the window `load` event:
     ```javascript
     window.addEventListener("load", function() {
         if (window.app && typeof window.app.mount === 'function') {
             window.app.mount("#app");
         }
     });
     ```
   - **Never create secondary or conflicting Vue instances**.

---

## 4. Form System, Validation & AJAX Pipeline

1. **Form Container**:
   - Standard forms use `<x-web::form method="POST" action="...">` with automatic `@csrf`, `@method(...)`, `:initial-errors`, and `@invalid-submit="onInvalidSubmit"`.
   - AJAX forms use `<x-web::form as="div" v-slot="{ meta, errors, handleSubmit }">` with `<form @submit="handleSubmit($event, submitHandler)" ref="formRef">`.
2. **Form Controls**:
   - Every input field must be structured using `<x-web::form.control-group>`, `<x-web::form.control-group.label>`, `<x-web::form.control-group.control>`, and `<x-web::form.control-group.error>`.
   - `<x-web::form.control-group.control>` must be used for all field types (`text`, `email`, `password`, `select`, `multiselect`, `textarea`, `checkbox`, `radio`, `switch`, `file`, `price`, `date`, `datetime`, `tags`, `image`, `custom`).
3. **Client-Side Validation (VeeValidate 4)**:
   - Rules are declared via `rules` (e.g. `rules="required|email|max:160"`).
   - Custom rules: `phone`, `address`, `postcode`, `decimal`, `required_if`, `date_format`, `after`.
   - Multi-locale error strings for 7 locales: `ar`, `en`, `es`, `fa`, `pt_BR`, `tr`, `vi`.
4. **AJAX Submission & State Management**:
   - Use `FormData` with `this.$axios.post(...)`.
   - Button loading and disabled states must use `<x-web::button ::loading="isProcessing" ::disabled="isProcessing">` (which delegates to `<v-button>` with centered spinner and `aria-busy="true"`).
   - HTTP 422 server validation errors must be passed to `setErrors(error.response.data.errors)`.
   - Global flash toast feedback dispatched via `this.$emitter.emit('add-flash', { type: 'success'|'error', message: '...' })`.
5. **Dual Controller Response Contract**:
   - Controllers must support `$request->expectsJson()` returning JSON `{ message, redirect_url, data }` with standard redirect fallback for non-AJAX requests.

---

## 5. Design Tokens, Theming & Accessibility

1. **Brand Colors & Typography**:
   - Primary: Emerald `#185c54` / `#134942`.
   - Accent/Mint: `#e6f4ee` / `#a3e4c8`.
   - Neutrals: Slate shades (`slate-50` through `slate-950`).
   - Font: Cairo font across Arabic and English interfaces.
2. **Dark Mode**:
   - Dark mode classes must be applied via Tailwind `dark:*` variants.
   - Dark mode state is persisted in a secure `dark_mode` cookie.
3. **Bi-Directional Support (RTL / LTR)**:
   - Arabic (`dir="rtl"`) and English (`dir="ltr"`) layout directions must use directional utility classes (`ltr:*` and `rtl:*`).
4. **Accessibility (a11y)**:
   - Modals and Drawers must manage focus traps and close on `Escape`.
   - Disclosures must support keyboard activation (`Enter`, `Space`).
   - Interactive elements must maintain proper ARIA attributes (`aria-expanded`, `aria-controls`, `aria-hidden`, `aria-invalid`, `role="dialog"`, `role="tabpanel"`).

---

## 6. Testing & Quality Enforcement

Before committing any changes to the Web package:
1. Run the Web component test suite:
   ```bash
   LARASEED_OPTIONAL_PACKAGES="student,lost_and_found,web" ./vendor/bin/pest packages/CampusFind/Web/tests
   ```
2. Build frontend assets for production:
   ```bash
   npm --prefix packages/CampusFind/Web run build
   ```
3. Verify that all 66+ tests pass with zero regressions.
