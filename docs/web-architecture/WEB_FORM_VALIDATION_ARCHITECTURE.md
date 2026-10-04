# Web Form Validation Architecture

## Overview & Architecture Parity with Webkul Admin

The CampusFind Web package reproduces the **Bagisto Webkul Admin & Shop form validation architecture**, integrating **Vue 3**, **VeeValidate 4**, and **Laravel Server-Side Form Validation** into a cohesive, declarative, and accessible experience.

```
+-----------------------------------------------------------------------+
|                             Blade View                                |
|  <x-web::form v-slot="{ meta, errors, handleSubmit }" as="div">       |
|    <form @submit="handleSubmit($event, onSubmit)" ref="myForm">       |
|      <x-web::form.control-group>                                      |
|        <x-web::form.control-group.label :required="true" />           |
|        <x-web::form.control-group.control rules="required|..." />     |
|        <x-web::form.control-group.error name="..." />                 |
|      </x-web::form.control-group>                                     |
|      <x-web::button ::loading="isProcessing" ::disabled="isProcessing"|
|    </form>                                                            |
|  </x-web::form>                                                       |
+-----------------------------------------------------------------------+
                                  |
                                  v
+-----------------------------------------------------------------------+
|                     Client-Side Validation (Vue 3)                    |
|  - VeeValidate 4 Plugin (Form, Field, ErrorMessage)                   |
|  - Real-time & On-Submit rule evaluation                              |
|  - Localized messages (ar, en, es, fa, pt_BR, tr, vi)                 |
|  - Auto-scroll & focus to first invalid field (onInvalidSubmit)       |
+-----------------------------------------------------------------------+
                                  |
                        Valid Form Submission
                                  |
                                  v
+-----------------------------------------------------------------------+
|                    AJAX Client ($axios / Axios)                       |
|  - Multipart FormData serialization                                   |
|  - X-Requested-With: XMLHttpRequest & CSRF Token hygiene              |
|  - Reactive isProcessing spinner state                                |
+-----------------------------------------------------------------------+
                                  |
                           HTTP Request
                                  |
                                  v
+-----------------------------------------------------------------------+
|                   Laravel Server-Side Validation                      |
|  - FormRequest / $request->validate([...])                            |
|  - Invariant validation, RBAC, domain rules                           |
|  - HTTP 422: JSON { message, errors: { field: ['...'] } }             |
|  - HTTP 200/201: JSON { message, redirect_url, data }                 |
+-----------------------------------------------------------------------+
                                  |
                                  v
+-----------------------------------------------------------------------+
|                        Unified UI Feedback                            |
|  - 422 errors mapped back to fields via setErrors(errors)             |
|  - Global flash toasts via $emitter.emit('add-flash', {...})          |
|  - Automatic redirection or form reset                                |
+-----------------------------------------------------------------------+
```

---

## 1. VeeValidate 4 Plugin (`vee-validate.js`)

Located at `packages/CampusFind/Web/src/Web/Resources/assets/js/plugins/vee-validate.js`.

### Registered Components
- `VForm` (`Form` from VeeValidate)
- `VField` (`Field` from VeeValidate)
- `VErrorMessage` (`ErrorMessage` from VeeValidate)

### Validation Rules Library
- **Standard rules**: all rules imported from `@vee-validate/rules` (`required`, `email`, `min`, `max`, `numeric`, `regex`, etc.).
- **Custom domain rules**:
  - `phone`: regex-based international and local telephone validation (`/^\+?\d+$/`).
  - `address`: unicode-safe address character validation with multilingual support.
  - `postcode`: alphanumeric postal code pattern validation.
  - `decimal`: customizable precision floating point decimal validation.
  - `required_if`: conditional required constraint based on boolean logic.
  - `date_format`: strict `YYYY-MM-DD` date validation.
  - `after`: future or current date boundary validation.

### Multilingual Error Messages (7 Locales)
Configured using `@vee-validate/i18n` with support for:
1. `ar` (العربية - Primary)
2. `en` (English)
3. `es` (Español)
4. `fa` (فارسی)
5. `pt_BR` (Português do Brasil)
6. `tr` (Türkçe)
7. `vi` (Tiếng Việt)

---

## 2. Blade Component System Integration

### `<x-web::form>`
- **Slot Form / AJAX Mode**: `<x-web::form as="div" v-slot="{ meta, errors, handleSubmit }">`
- **Standard Traditional Form Mode**: `<x-web::form method="POST" action="...">` renders `<v-form>` with automatic `@csrf`, `@method`, and `@invalid-submit="onInvalidSubmit"`.

### `<x-web::form.control-group>`
Container providing label, input control, helper hint, and automatic field-level error announcer.

### `<x-web::form.control-group.control>`
Wraps `<v-field>` around:
- `text`, `email`, `password`, `number`, `search`
- `textarea`
- `select`, `multiselect`
- `date`, `datetime`, `flatpickr-date`, `flatpickr-datetime`
- `checkbox`, `radio`, `switch`
- `file`, `image` (`<x-web::media.images>`)
- `tags` (`<x-web::tags>`)

### `<x-web::form.control-group.error>`
Renders `<v-error-message>` with `role="alert"`, `aria-live="polite"`, connecting to the input field's `aria-invalid` and `aria-describedby` attributes for WCAG AA compliance.

### `<x-web::button>`
Integrates `<v-button>` with `:loading="isProcessing"` spinner state, `aria-busy="true"`, preventing duplicate submissions while retaining button dimensions and slot contents.
