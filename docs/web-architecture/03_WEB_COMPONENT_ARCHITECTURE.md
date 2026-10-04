# Web Component Architecture (المعمارية الموحدة لمكوّنات حزمة Web)

**تاريخ الاعتماد والتنفيذ**: 2026-10-04  
**الحزمة**: `packages/CampusFind/Web`  

---

## 1. الهيكل العام لمعمارية المكوّنات

تم بناء معمارية مكوّنات `Web` لتطابق النموذج المرجعي في `Admin` مع الاحتفاظ الكامل باستقلالية الحزمة، وتصميم وهوية `CampusFind` البصرية، ونظام الألوان (الزمردي/النعناع/Cairo).

```
packages/CampusFind/Web/
├── src/
│   ├── Providers/
│   │   ├── WebServiceProvider.php        # تسجيل مساحات web و campusfind_web_web
│   │   └── ModuleServiceProvider.php
│   └── Web/
│       ├── Providers/
│       │   └── WebServiceProvider.php    # تسجيل Anonymous Components لـ web و campusfind_web_web
│       ├── Resources/
│       │   ├── assets/
│       │   │   ├── js/
│       │   │   │   ├── app.js            # Vue 3 Runtime + WebStarterKernel
│       │   │   │   └── plugins/
│       │   │   │       ├── emitter.js    # Mitt Event Emitter Plugin ($emitter)
│       │   │   │       └── web.js        # Web Utility Plugin ($web)
│       │   │   └── css/
│       │   │       └── app.css
│       │   └── views/
│       │       └── components/
│       │           ├── accordion/        # <x-web::accordion> (Blade + Vue + Shimmer)
│       │           ├── modal/            # <x-web::modal>, <x-web::modal.confirm>
│       │           ├── dropdown/         # <x-web::dropdown>, <x-web::dropdown.menu.item>
│       │           ├── tabs/             # <x-web::tabs>, <x-web::tabs.item>
│       │           ├── flash-group/      # <x-web::flash-group>, <x-web::flash-group.item>
│       │           ├── shimmer/          # <x-web::shimmer.accordion>, <x-web::shimmer.tabs>, etc.
│       │           ├── button/           # <x-web::button> (Pure Blade Primitive)
│       │           ├── badge/            # <x-web::badge> (Pure Blade Primitive)
│       │           ├── card/             # <x-web::card> (Pure Blade Primitive)
│       │           ├── alert/            # <x-web::alert> (Pure Blade Primitive)
│       │           └── layouts/          # <x-web::layouts>, header, footer
```

---

## 2. معمارية وقت التشغيل (Runtime Integration)

### 1. تهيئة تطبيق Vue
في `packages/CampusFind/Web/src/Web/Resources/assets/js/app.js`:
```javascript
import { createApp } from "vue/dist/vue.esm-bundler";
import Emitter from "./plugins/emitter";
import Web from "./plugins/web";

window.app = createApp({ ... });
[Emitter, Web].forEach(plugin => app.use(plugin));
```

### 2. تركيب التطبيق في القالب العام
في `packages/CampusFind/Web/src/Web/Resources/views/components/layouts/index.blade.php`:
```blade
<div id="app" class="flex min-h-screen flex-col">
    <x-web::flash-group />
    <x-web::modal.confirm />
    <x-web::layouts.header />
    <main class="flex-1">{{ $slot }}</main>
    <x-web::layouts.footer />
</div>

@stack('scripts')

<script>
    window.addEventListener("load", function() {
        if (window.app && typeof window.app.mount === 'function') {
            window.app.mount("#app");
        }
    });
</script>
```

---

## 3. العقد المعماري للأكورديون المرجعي (`<x-web::accordion>`)

```blade
@props([
    'isActive' => false,
    'title'    => null,
])

<div {{ $attributes->merge(['class' => 'rounded-2xl border border-slate-200/80 dark:border-slate-800 bg-white dark:bg-slate-900 overflow-hidden font-cairo shadow-xs transition-all']) }}>
    <v-accordion
        is-active="{{ filter_var($isActive, FILTER_VALIDATE_BOOLEAN) ? 'true' : 'false' }}"
        {{ $attributes }}
    >
        <x-web::shimmer.accordion />

        @isset($header)
            <template v-slot:header="{ toggle, isOpen }">
                <div
                    {{ $header->attributes->merge(['class' => 'flex w-full items-center justify-between p-5 text-start cursor-pointer select-none hover:bg-slate-50/70 dark:hover:bg-slate-800/50 transition-colors focus:outline-none focus:ring-4 focus:ring-inset focus:ring-[#185c54]/20']) }}
                    @click="toggle"
                    role="button"
                    tabindex="0"
                    :aria-expanded="isOpen ? 'true' : 'false'"
                    @keydown.enter.prevent="toggle"
                    @keydown.space.prevent="toggle"
                >
                    <div class="flex-1">{{ $header }}</div>
                    <span :class="`ltr:ml-4 rtl:mr-4 shrink-0 text-slate-400 transition-transform duration-200 ${isOpen ? 'rotate-180' : ''}`">
                        <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.2" d="M19 9l-7 7-7-7" />
                        </svg>
                    </span>
                </div>
            </template>
        @endisset

        @isset($content)
            <template v-slot:content="{ isOpen }">
                <div {{ $content->attributes->merge(['class' => 'border-t border-slate-100 dark:border-slate-800/80 p-5 text-sm text-slate-600 dark:text-slate-400 leading-relaxed']) }} v-show="isOpen">
                    {{ $content }}
                </div>
            </template>
        @endisset
    </v-accordion>
</div>

@pushOnce('scripts')
    <script type="text/x-template" id="v-accordion-template">
        <div>
            <slot name="header" :toggle="toggle" :is-open="isOpen" :isOpen="isOpen"></slot>
            <slot name="content" :is-open="isOpen" :isOpen="isOpen"></slot>
        </div>
    </script>

    <script type="module">
        app.component('v-accordion', {
            template: '#v-accordion-template',
            props: {
                isActive: { type: [Boolean, String], default: false },
            },
            emits: ['toggle'],
            data() {
                return {
                    isOpen: String(this.isActive) === 'true' || this.isActive === true,
                };
            },
            methods: {
                toggle() {
                    this.isOpen = ! this.isOpen;
                    this.$emit('toggle', { isActive: this.isOpen });
                },
            },
        });
    </script>
@endPushOnce
```

---

## 4. مصفوفة قرار: متى يُستخدم Vue ومتى يُكتفى بـ Blade النقي؟

| المكوّن | النموذج المعماري | التبرير التقني |
|---|---|---|
| **الأكورديون (`accordion`)** | Blade + Vue + Shimmer | إدارة حالة الطي والفتح، إطلاق أحداث `toggle`، تفاعل لوحة المفاتيح والـ ARIA |
| **النافذة المنبثقة (`modal`)** | Blade + Vue + Animations | إدارة قفل التمرير، زر Escape، الانتقالات الحركية، أحداث الفتح والإغلاق |
| **نافذة التأكيد (`modal.confirm`)** | Blade + Vue + Emitter | استقبال طلبات التأكيد العامة من أي مكان عبر `$emitter.on('open-confirm-modal')` |
| **القائمة المنسدلة (`dropdown`)** | Blade + Vue + Floating | حساب الموضع ديناميكياً حسب اتجاه الصفحة (RTL/LTR)، إغلاق عند النقر بالخارج |
| **التبويبات (`tabs`)** | Blade + Vue + Shimmer | تسجيل العناصر الفرعية ديناميكياً مع الأب والتنقل السلس بين اللوحات |
| **رسائل الفلاش (`flash-group`)** | Blade + Vue + Progress Timer | شريط زمني تفاعلي للرسائل مع إيقاف مؤقت عند تمرير الفأرة والاستماع لأحداث Emitter |
| **منظومة النماذج والحقول (`form`, `control-group`)** | Blade + VeeValidate + Dynamic Error Mapping | معمارية VeeValidate كاملة (`v-form`, `v-field`, `v-error-message`) تدعم التحقق الفوري في المتصفح والتزامن مع أخطاء Laravel Server، مع دعم كافة أنواع الحقول |
| **الأزرار والبطاقات والشارات والتنبيهات** | Pure Blade Primitives | مكوّنات عرض نقية؛ استخدام Blade أسرع وأخف ولا يتطلب تعقيد Vue |

---

## 5. معمارية منظومة النماذج والحقول (Form & Field Architecture)

تعتمد حزمة `Web` على نفس النموذج المعماري المعتمد في `Admin`:

1. **مكوّن النموذج الأساسي (`<x-web::form>`)**:
   - يدعم الوضعين: وضع النموذج العادي (Traditional HTTP Form) المزود تلقائياً بـ `@csrf` و`@method` وأخطاء الخادم الأولية `:initial-errors` مع تمريرها لـ VeeValidate عبر `v-slot="{ meta, errors, setValues }"` و`@invalid-submit="onInvalidSubmit"`.
   - ووضع النماذج غير المتزامنة والمخصصة عند تمرير السمة `as`.

2. **مجموعة التحكم (`<x-web::form.control-group>`)**:
   - مكوّن حاوٍ ينسق المسافات ويضم عناصر `label` و`control` و`error`.
   - يدعم التمرير الصريح للمكوّنات الفرعية أو التمرير المختصر عبر الـ props (`name`, `label`, `required`, `hint`).

3. **حقول الإدخال وعناصر التحكم (`<x-web::form.control-group.control>`)**:
   - يغلف الحقول بـ `<v-field>` من VeeValidate لتوفير التحقق من صحة المدخلات وإدارة الأخطاء.
   - يدعم كافة الأنواع: `text`, `email`, `password`, `number`, `time`, `datetime-local`, `date` (عبر `<x-web::flat-picker.date>`), `price`, `file`, `color`, `textarea`, `select`, `multiselect`, `checkbox`, `radio`, `switch`, `image` (عبر `<x-web::media.images>`), `tags` (عبر `<x-web::tags>`), و`custom`.
   - يربط حالة الخطأ بصرياً (`:class="[errors.length ? 'border !border-rose-500' : '']"`) ودلالياً (`aria-invalid="true"`, `aria-describedby="{$name}-error"`).

4. **رسائل الخطأ (`<x-web::form.control-group.error>`)**:
   - يعتمد على `<v-error-message>` من VeeValidate مع تحديد `role="alert"` و`aria-live="polite"` لدعم قارئات الشاشة والتحقق المباشر.
