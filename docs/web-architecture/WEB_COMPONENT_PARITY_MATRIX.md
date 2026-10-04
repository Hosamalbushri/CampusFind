# WEB COMPONENT PARITY MATRIX — SOURCE-TO-TARGET REPLICATION

## 1. Overview & Verification Status

Every reusable component from the Admin reference architecture has been mapped to its Web equivalent under the `x-web::` namespace. All public UI primitives preserve CampusFind branding (`#185c54`, `#e6f4ee`, Cairo typography, dark mode, responsive styling, and accessibility contracts).

---

## 2. Complete Source-to-Target Mapping

| Admin Source Component | Web Equivalent Component | Status | Props / Parameters | Vue Integration & Architectural Behavior | Styling & Brand Adaptations |
|---|---|---|---|---|---|
| `<x-admin::layouts>` | `<x-web::layouts>` | **Implemented & Verified** | `title` | Root `<div id="app">`, `app.mount("#app")` on load, includes flash-group & confirm modal | CampusFind public shell, Emerald navbar, brand footer |
| `<x-admin::layouts.anonymous>` | `<x-web::layouts.anonymous>` | **Implemented & Verified** | `title` | Standalone `<div id="app">`, `app.mount("#app")`, no global chrome | Centered container for student authentication |
| `<x-admin::layouts.tabs>` | `<x-web::layouts.tabs>` | **Implemented & Verified** | `tabs` | Pure Blade navigation tab bar with active route highlight | CampusFind tab pill styling |
| `<x-admin::breadcrumbs>` | `<x-web::breadcrumbs>` | **Implemented & Verified** | `items` (array) | Accessible `<nav aria-label="breadcrumb">` with RTL chevron icons | Slate-500 muted trail, text-sm, bold current page |
| `<x-admin::accordion>` | `<x-web::accordion>` | **Implemented & Verified** | `isActive`, `title` | `<v-accordion>`, `<v-accordion-template>`, `@pushOnce('scripts')`, emits `toggle` | Emerald focus ring, rounded-2xl cards, dark mode |
| `<x-admin::tabs>` | `<x-web::tabs>` | **Implemented & Verified** | `position` | `<v-tabs>`, `<v-tabs-template>`, `@pushOnce('scripts')`, child tab collection | Mint background pills (`#e6f4ee`), rounded-xl tabs |
| `<x-admin::tabs.item>` | `<x-web::tabs.item>` | **Implemented & Verified** | `title`, `isSelected` | `<v-tab-item>`, registers in parent `tabs[]`, emits active state | Smooth tab panel switching, role="tabpanel" |
| `<x-admin::modal>` | `<x-web::modal>` | **Implemented & Verified** | `isActive`, `position`, `size`, `id`, `title` | `<v-modal>`, backdrop click, Esc key listener, scroll locking | Rounded-3xl surface, backdrop-blur-sm, Cairo font |
| `<x-admin::modal.confirm>` | `<x-web::modal.confirm>` | **Implemented & Verified** | None (triggered via `$emitter`) | `<v-modal-confirm>`, listens to `$emitter.on('open-confirm-modal')` | Warning/Danger alertdialog, agree/disagree callbacks |
| `<x-admin::drawer>` | `<x-web::drawer>` | **Implemented & Verified** | `isActive`, `position`, `width`, `id`, `title` | `<v-drawer>`, slide-over animation (left/right with RTL support) | Cairo typography, responsive width, slide transitions |
| `<x-admin::dropdown>` | `<x-web::dropdown>` | **Implemented & Verified** | `position`, `closeOnClick` | `<v-dropdown>`, click-outside listener, keyboard escape handling | Rounded-2xl floating card, shadow-xl, RTL positioning |
| `<x-admin::dropdown.menu.item>` | `<x-web::dropdown.menu.item>` | **Implemented & Verified** | `href`, `icon` | Pure Blade semantic menu item (`<a>` or `<button>`) | Rounded-xl hover styling, text-sm font-semibold |
| `<x-admin::form>` | `<x-web::form>` | **Implemented & Verified** | `action`, `method`, `as` | `<v-form>`, `:initial-errors`, `@invalid-submit="onInvalidSubmit"` | Seamless CSRF & method spoofing integration |
| `<x-admin::form.control-group>` | `<x-web::form.control-group>` | **Implemented & Verified** | `name`, `label`, `required`, `hint` | Blade container with optional inline label & error linking | `mb-4` standardized spacing, accessible hint text |
| `<x-admin::form.control-group.label>` | `<x-web::form.control-group.label>` | **Implemented & Verified** | `for`, `required` | Pure Blade semantic label | `font-semibold text-slate-700`, red required asterisk |
| `<x-admin::form.control-group.control>` | `<x-web::form.control-group.control>` | **Implemented & Verified** | `type`, `name`, `value`, `rules`, `label`, `id` | `<v-field>`, `v-bind="field"`, `v-checked-handler` for checks/radios | Rounded-xl inputs, focus:ring-[#185c54]/15, red error border |
| `<x-admin::form.control-group.error>` | `<x-web::form.control-group.error>` | **Implemented & Verified** | `name`, `controlName`, `id` | `<v-error-message>`, role="alert", aria-live="polite" | `text-xs font-semibold text-rose-600` |
| `<x-admin::flat-picker.date>` | `<x-web::flat-picker.date>` | **Implemented & Verified** | `name`, `value`, `placeholder` | `<v-date-picker>`, Flatpickr JS integration, dynamic calendar icon | Calendar picker with Cairo font and localized date strings |
| `<x-admin::flat-picker.datetime>` | `<x-web::flat-picker.datetime>` | **Implemented & Verified** | `name`, `value`, `placeholder` | `<v-datetime-picker>`, Flatpickr JS datetime integration | Datetime picker with 24-hr format and time picker |
| `<x-admin::flash-group>` | `<x-web::flash-group>` | **Implemented & Verified** | None | `<v-flash-group>`, parses session alerts and `$emitter` events | Bottom-center floating notification group |
| `<x-admin::flash-group.item>` | `<x-web::flash-group.item>` | **Implemented & Verified** | `flash` | `<v-flash-item>`, circular SVG timer ring, pause on hover | Success/Error/Warning/Info styles with dark mode |
| `<x-admin::media.images>` | `<x-web::media.images>` | **Implemented & Verified** | `name`, `allowMultiple`, `uploadedImages`, `width`, `height` | `<v-media-images>`, multi-file reader, FileReader preview, remove action | Dashed border upload card, image thumbnail list |
| `<x-admin::tags>` | `<x-web::tags>` | **Implemented & Verified** | `name`, `value`, `placeholder` | `<v-tags>`, comma/enter tokenizer, backspace chip deletion | Mint tag chips with remove cross icon |
| `<x-admin::table>` | `<x-web::table>` | **Implemented & Verified** | `$attributes` | Pure Blade semantic table wrapper with horizontal scroll container | Rounded-2xl bordered table container |
| `<x-admin::table.thead>` | `<x-web::table.thead>` | **Implemented & Verified** | `$attributes` | Pure Blade `<thead>` | Slate-50 dark:bg-slate-900 border-b header |
| `<x-admin::table.tbody>` | `<x-web::table.tbody>` | **Implemented & Verified** | `$attributes` | Pure Blade `<tbody>` | Divide-y divide-slate-100 dark:divide-slate-800 |
| `<x-admin::table.th>` | `<x-web::table.th>` | **Implemented & Verified** | `$attributes` | Pure Blade `<th>` | `text-xs font-bold uppercase text-slate-500` |
| `<x-admin::table.td>` | `<x-web::table.td>` | **Implemented & Verified** | `$attributes` | Pure Blade `<td>` | `text-sm font-medium text-slate-900 dark:text-slate-100` |
| `<x-admin::spinner>` | `<x-web::spinner>` | **Implemented & Verified** | `size`, `color` | Animated SVG circular spinner | `#185c54` brand color, sm/md/lg variants |
| `<x-admin::shimmer.accordion>` | `<x-web::shimmer.accordion>` | **Implemented & Verified** | None | Pure Blade shimmer skeleton placeholder | Shimmer pulse animation |
| `<x-admin::shimmer.tabs>` | `<x-web::shimmer.tabs>` | **Implemented & Verified** | None | Pure Blade shimmer skeleton placeholder | Shimmer pulse animation |
| `<x-admin::shimmer.image>` | `<x-web::shimmer.image>` | **Implemented & Verified** | None | Pure Blade shimmer skeleton placeholder | Shimmer pulse animation |
| `<x-admin::shimmer>` | `<x-web::shimmer>` | **Implemented & Verified** | `$attributes` | Generic pulse skeleton | Shimmer pulse animation |

---

## 3. Web-Specific Additional Primitives

To support the public university portal and student interactions, the following high-level primitives were added while maintaining the same architectural patterns:

| Web Component | Purpose & Description |
|---|---|
| `<x-web::container>` | Standardized max-width responsive container (`sm`, `md`, `lg`, `xl`, `full`). |
| `<x-web::section>` | Landing page section container with optional badge, title, and subtitle slots. |
| `<x-web::card>` | Card primitive with variants (`flat`, `elevated`, `interactive`, `mint`) and padding options. |
| `<x-web::button>` | Primary action component supporting `primary`, `secondary`, `mint`, `ghost`, `danger`, and `outline` styles for both `<button>` and `<a>` elements. |
| `<x-web::badge>` | Status tag primitive with `mint`, `success`, `warning`, `danger`, `neutral`, and `dark-blur` variants, with optional status dot. |
| `<x-web::avatar>` | User avatar displaying initials or profile image with customizable size. |
| `<x-web::alert>` | Accessible feedback banner with `info`, `success`, `warning`, `danger` variants. |
| `<x-web::page-header>` | Consistent page title and description banner with breadcrumb support. |
| `<x-web::pagination>` | Accessible pagination controls with page numbers, next/prev links, and ARIA labels. |
| `<x-web::empty-state>` | Empty catalog/table placeholder illustration with title and call-to-action slot. |
| `<x-web::campus.report-card>` | Standardized LOST/FOUND unified record summary card. |
| `<x-web::campus.report-filters>` | Reusable multi-field search and filter bar for unified item browsing. |
