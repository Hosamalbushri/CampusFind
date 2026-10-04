# Web Current State Audit (تقرير تدقيق حالة حزمة Web قبل إعادة البناء)

**تاريخ التدقيق**: 2026-10-04  
**النطاق المفحوص**: `packages/CampusFind/Web`  
**الملفات المفحوصة والمحللة**:
- `packages/CampusFind/Web/src/Web/Providers/WebServiceProvider.php`
- `packages/CampusFind/Web/src/Providers/WebServiceProvider.php`
- `packages/CampusFind/Web/src/Web/Resources/views/components/layouts/index.blade.php`
- `packages/CampusFind/Web/src/Web/Resources/assets/js/app.js`
- `packages/CampusFind/Web/src/Web/Resources/views/components/accordion/index.blade.php`
- `packages/CampusFind/Web/src/Web/Resources/views/components/modal/index.blade.php`
- `packages/CampusFind/Web/src/Web/Resources/views/components/modal/confirm.blade.php`
- `packages/CampusFind/Web/src/Web/Resources/views/components/dropdown/index.blade.php`
- `packages/CampusFind/Web/src/Web/Resources/views/components/tabs/index.blade.php`
- `packages/CampusFind/Web/src/Web/Resources/views/components/flash-group/index.blade.php`
- `packages/CampusFind/Web/vite.config.js`
- `packages/CampusFind/Web/package.json`

---

## 1. الأسباب الجذرية لاختلاف معمارية Web السابقة عن Admin

أظهر التدقيق التفصيلي لمصدر حزمة `Web` الأسباب الجذرية التالية:

1. **غياب بيئة تشغيل Vue (No Vue Runtime in Web)**:
   - كان `app.js` في Web يعتمد على فئة JavaScript تقليدية (`WebStarterKernel`) تقوم بتفويض الأحداث عبر استماع عام للنقرات والبحث عن سمات مثل `data-component` و`data-action`.
   - لم يكن `window.app` معرفاً، ولم تكن حزمة `vue` مثبتة في `package.json` الخاصة بـ Web.
2. **المكوّنات التفاعلية كانت مجرد Static Blade Partials**:
   - كان مكوّن الأكورديون في `components/accordion/index.blade.php` مجرد عناصر HTML ثابتة مع سمات `data-action="toggle-accordion"`.
   - لم يكن يحتوي على وسم `<v-accordion>` ولا فتحات `v-slot` مع scoped properties، ولم تكن توجد قوالب `text/x-template` أو تسجيل عبر `app.component()`.
3. **غياب مكوّنات الـ Shimmer التفاعلية**:
   - لم تكن المكوّنات التفاعلية (الأكورديون، التبويبات) توفر مكوّنات shimmer فرعية تظهر كحالة تحميل مؤقتة أثناء استقرار بيئة تشغيل الواجهة.
4. **تسجيل مساحة الأسماء المحدودة**:
   - كان `WebServiceProvider` يسجل فقط `campusfind_web_web` ولم يكن مسار `web` مسجلاً، مما يمنع استدعاء الصيغة القياسية `<x-web::...>`.
5. **عدم اكتمال دورة حياة التركيب في Layout**:
   - لم يكن `layouts/index.blade.php` يربط معالج تحميل النافذة `window.addEventListener("load", ...)` لتركيب تطبيق Vue، ولم تكن رسائل الفلاش ومودال التأكيد مهيأة كـ Global Reactive Vue components.

---

## 2. جدول مقارنة الحالة السابقة مقابل الحالة المرجعية في Admin

| المعيار المعماري | Admin Package | Web Package (الحالة السابقة) |
|---|---|---|
| **بيئة التشغيل (Runtime)** | Vue 3 ESM Bundler (`createApp`) عالمي | Vanilla JS (`WebStarterKernel`) بلا Vue |
| **مساحة المكوّنات** | `x-admin::*` | `x-campusfind_web_web::*` فقط |
| **بنية الأكورديون** | Blade + `<v-accordion>` + scoped slots + shimmer | Static Blade + `data-action="toggle-accordion"` |
| **قوالب Vue** | `<script type="text/x-template">` | غير موجودة |
| **تسجيل المكوّنات** | `app.component(...)` داخل `@pushOnce` | مستمعات DOM عامة داخل Class |
| **النوافذ والقوائم** | مكوّنات Vue مع animations وإدارة Escape | تفويض نقرات DOM يدوي |
| **شريط الفلاش والتأكيد** | قائمة تفاعلية مرتبطة بـ `$emitter` مع عداد زمني | قوالب Blade ثابتة مكررة |
