# Admin Architecture Audit (تقرير تدقيق معمارية مكوّنات لوحة الإدارة)

**تاريخ التدقيق**: 2026-10-04  
**النطاق المفحوص**: `packages/Webkul/Admin`  
**الملفات المفحوصة والمحللة**:
- `packages/Webkul/Admin/src/Providers/AdminServiceProvider.php`
- `packages/Webkul/Admin/src/Resources/views/components/layouts/index.blade.php`
- `packages/Webkul/Admin/src/Resources/assets/js/app.js`
- `packages/Webkul/Admin/src/Resources/assets/js/plugins/emitter.js`
- `packages/Webkul/Admin/src/Resources/assets/js/plugins/admin.js`
- `packages/Webkul/Admin/src/Resources/views/components/accordion/index.blade.php`
- `packages/Webkul/Admin/src/Resources/views/components/shimmer/accordion/index.blade.php`
- `packages/Webkul/Admin/src/Resources/views/components/modal/index.blade.php`
- `packages/Webkul/Admin/src/Resources/views/components/modal/confirm.blade.php`
- `packages/Webkul/Admin/src/Resources/views/components/dropdown/index.blade.php`
- `packages/Webkul/Admin/src/Resources/views/components/tabs/index.blade.php`
- `packages/Webkul/Admin/src/Resources/views/components/tabs/item.blade.php`
- `packages/Webkul/Admin/src/Resources/views/components/flash-group/index.blade.php`
- `packages/Webkul/Admin/src/Resources/views/components/flash-group/item.blade.php`
- `packages/Webkul/Admin/vite.config.js`
- `packages/Webkul/Admin/package.json`

---

## 1. آلية تسجيل المكوّنات ومساحات العرض في Admin

في `AdminServiceProvider.php` (السطور 49-53):
```php
$this->loadTranslationsFrom(__DIR__.'/../Resources/lang', 'admin');
$this->loadViewsFrom(__DIR__.'/../Resources/views', 'admin');
Blade::anonymousComponentPath(__DIR__.'/../Resources/views/components', 'admin');
```
تُسجّل المكوّنات كـ Anonymous Blade Components ضمن مساحة الأسماء `admin`، مما يتيح استدعاءها في Blade بالصيغة:
`<x-admin::accordion>`, `<x-admin::modal>`, `<x-admin::dropdown>`, إلخ.

---

## 2. النمط المرجعي للمكوّنات التفاعلية (The Blade + Vue Hybrid Pattern)

تعتمد حزمة `Admin` نمطاً معمارياً هجيناً فائق القوة يجمع بين مزايا Blade في التوليد من جانب الخادم (SSR) ومزايا Vue 3 في إدارة الحالة التفاعلية من جانب العميل:

### عناصر النمط:
1. **غلاف Blade الخارجي**:
   - تعريف الخصائص عبر `@props` (مثل `'isActive' => true`).
   - دمج وتمرير سمات HTML عبر `$attributes->merge(['class' => '...'])`.
2. **عنصر Vue المخصص (Custom Element)**:
   - وضع وسم مخصص مثل `<v-accordion is-active="{{ $isActive }}" {{ $attributes }}>`.
3. **عنصر التحميل المؤقت (Shimmer Placeholder)**:
   - تضمين مكوّن Shimmer فرعي مثل `<x-admin::shimmer.accordion />` داخل وسم Vue ليظهر للمستخدم ريثما يكتمل تحميل Vue وتركيب القالب.
4. **ربط الفتحات المسماة (Blade Named Slots -> Vue Scoped Slots)**:
   - استقبال فتحات Blade (مثل `@isset($header)`) وربطها بفتحات Vue مع تمرير متغيرات الحالة والدوال:
     `<template v-slot:header="{ toggle, isOpen }"> ... </template>`
5. **منع تكرار السكربتات (`@pushOnce('scripts')`)**:
   - تغليف تعريف القالب وتسجيل مكوّن Vue داخل `@pushOnce('scripts')` بحيث يُحقن القالب والسكربت مرة واحدة فقط في الصفحة مهما تكرر استدعاء المكوّن.
6. **قالب `text/x-template`**:
   - وضع قالب Vue داخل `<script type="text/x-template" id="v-*-template">` مع فتحات Vue (`<slot name="header" :toggle="toggle" :isOpen="isOpen">`).
7. **تسجيل مكوّن Vue العالمي**:
   - استدعاء `app.component('v-*', { template: '#v-*-template', props: [...], data() {...}, methods: {...} })` على تطبيق Vue العام المعرف في `window.app`.

---

## 3. دورة حياة التطبيق وتهيئة Vue في Layout

1. **إنشاء مثيل Vue**: في `packages/Webkul/Admin/src/Resources/assets/js/app.js`:
   ```javascript
   import { createApp } from "vue/dist/vue.esm-bundler";
   window.app = createApp({ ... });
   ```
   وتثبيت الإضافات العالمية (`mitt` للـ `$emitter`، `admin` للـ `$admin`، وغيرها).
2. **الحاوية الجذرية في القالب**: في `components/layouts/index.blade.php`:
   ```blade
   <div id="app" class="h-full">
       <x-admin::flash-group />
       <x-admin::modal.confirm />
       ...
       {{ $slot }}
   </div>
   ```
3. **تجميع السكربتات وتركيب التطبيق في نهاية الصفحة**:
   ```blade
   @stack('scripts')
   <script>
       window.addEventListener("load", function(event) {
           app.mount("#app");
       });
   </script>
   ```
   هذا يضمن أن جميع المكوّنات المسجلة في ملفات Blade عبر `@pushOnce('scripts')` قد تم تعريفها في `app.component(...)` قبل استدعاء `app.mount("#app")`.

---

## 4. المكوّنات التفاعلية المفحوصة في Admin

1. **الأكورديون (`x-admin::accordion`)**:
   - إدارة حالة الفتح والإغلاق `isOpen` وتوليد حدث `toggle`.
   - وجود Shimmer فرعي `<x-admin::shimmer.accordion />`.
2. **النافذة المنبثقة (`x-admin::modal`) ونافذة التأكيد (`x-admin::modal.confirm`)**:
   - إدارة العرض والمواضع والأحجام وقفل تمرير الصفحة (`document.body.style.overflow = 'hidden'`).
   - الاستماع لأحداث Emitter العالمية (`$emitter.on('open-confirm-modal', ...)`).
3. **القائمة المنسدلة (`x-admin::dropdown`)**:
   - حساب مواضع القائمة ديناميكياً وإغلاقها عند النقر بالخارج (`handleFocusOut`).
4. **التبويبات (`x-admin::tabs` و`x-admin::tabs.item`)**:
   - تسجيل المكوّن الفرعي تلقائياً في بيانات المكوّن الأب عبر `this.$parent.$data.tabs.push(this)`.
5. **رسائل التنبيه الفورية (`x-admin::flash-group` و`x-admin::flash-group.item`)**:
   - عرض التنبيهات مع شريط تقدم زمني دائري وإمكانية الإيقاف المؤقت عند تمرير الفأرة والاستماع لحدث `add-flash`.
