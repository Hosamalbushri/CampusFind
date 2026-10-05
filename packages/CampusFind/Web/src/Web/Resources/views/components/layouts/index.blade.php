@props([
    'title' => null,
])

<!DOCTYPE html>
<html
    class="{{ request()->cookie('dark_mode') ? 'dark' : '' }}"
    lang="{{ app()->getLocale() }}"
    dir="{{ in_array(app()->getLocale(), ['fa', 'ar']) ? 'rtl' : 'ltr' }}"
>
<head>
    {!! view_render_event('campusfind_web.web.layout.head.before') !!}

    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta http-equiv="content-language" content="{{ app()->getLocale() }}">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="base-url" content="{{ url()->to('/') }}">

    <title>{{ $title ?? trans('campusfind_web_web::app.web.title') }}</title>

    @stack('meta')

    <!-- Dynamic CSP-compliant Branding Stylesheet -->
    <link rel="stylesheet" href="{{ route('campusfind_web.web.branding.css') }}">

    @php
        $assetsBuilt = file_exists(public_path('campusfind_web-web-vite.hot')) || file_exists(public_path('campus-find-web/web/build/manifest.json'));
    @endphp

    @if ($assetsBuilt)
        {{
            vite()->set(['src/Web/Resources/assets/css/app.css', 'src/Web/Resources/assets/js/app.js'], 'campusfind_web_web')
        }}
    @endif

    @stack('styles')

    <style>
        :root,
        body {
            font-family: 'Cairo', ui-sans-serif, system-ui, -apple-system, 'Segoe UI', Roboto, Arial, 'Noto Sans', sans-serif;
        }
    </style>

    {!! view_render_event('campusfind_web.web.layout.head.after') !!}
</head>

<body class="min-h-screen bg-gray-50 text-gray-800 antialiased transition-colors duration-200 dark:bg-gray-950 dark:text-gray-100 flex flex-col font-cairo">
    {!! view_render_event('campusfind_web.web.layout.body.before') !!}

    @if (! $assetsBuilt)
        <aside class="bg-amber-600 text-white text-sm font-semibold px-4 py-2.5 text-center shadow-md flex items-center justify-center gap-2 relative z-50" role="alert">
            <svg class="h-5 w-5 inline-block align-middle" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
            </svg>
            <span>[Laraseed Diagnostic] Frontend assets for <strong>{{ 'Web' }}</strong> are not built. Run <code class="bg-black/25 px-1.5 py-0.5 rounded">npm run build</code> or <code class="bg-black/25 px-1.5 py-0.5 rounded">npm run dev</code> in <code class="bg-black/25 px-1.5 py-0.5 rounded">packages/{{ 'CampusFind' }}/{{ 'Web' }}</code>.</span>
        </aside>
    @endif

    <div id="app" class="flex min-h-screen flex-col">
        <!-- Flash Message Blade Component -->
        <x-web::flash-group />

        <!-- Confirm Modal Blade Component -->
        <x-web::modal.confirm />

        {!! view_render_event('campusfind_web.web.layout.content.before') !!}

        <!-- Page Header Blade Component -->
        <x-web::layouts.header />

        <!-- Page Content -->
        <main class="flex-1">
            {{ $slot }}
        </main>

        <!-- Page Footer Blade Component -->
        <x-web::layouts.footer />

        <!-- Scroll / Back To Top Button -->
        <v-back-to-top></v-back-to-top>

        {!! view_render_event('campusfind_web.web.layout.content.after') !!}
    </div>

    {!! view_render_event('campusfind_web.web.layout.body.after') !!}

    <script type="text/x-template" id="v-back-to-top-template">
        <transition
            enter-active-class="transition duration-200 ease-out"
            enter-from-class="opacity-0 translate-y-4 scale-90"
            enter-to-class="opacity-100 translate-y-0 scale-100"
            leave-active-class="transition duration-150 ease-in"
            leave-from-class="opacity-100 translate-y-0 scale-100"
            leave-to-class="opacity-0 translate-y-4 scale-90"
        >
            <button
                v-show="isVisible"
                type="button"
                @click="scrollToTop"
                class="fixed bottom-6 right-6 rtl:right-auto rtl:left-6 z-50 flex h-11 w-11 items-center justify-center rounded-2xl bg-[#185c54] text-white shadow-lg shadow-teal-950/20 border border-white/20 dark:border-slate-800 hover:bg-[#134942] hover:scale-110 active:scale-95 transition-all cursor-pointer focus:outline-none select-none"
                aria-label="{{ trans('campusfind_web_web::app.web.accessibility.back_to_top') }}"
                title="{{ trans('campusfind_web_web::app.web.accessibility.back_to_top') }}"
            >
                <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 15l7-7 7 7" />
                </svg>
            </button>
        </transition>
    </script>

    <script type="module">
        app.component('v-back-to-top', {
            template: '#v-back-to-top-template',

            data() {
                return {
                    isVisible: false,
                };
            },

            mounted() {
                window.addEventListener('scroll', this.handleScroll, { passive: true });
                this.handleScroll();
            },

            beforeUnmount() {
                window.removeEventListener('scroll', this.handleScroll);
            },

            methods: {
                handleScroll() {
                    const top = window.scrollY || document.documentElement.scrollTop || document.body.scrollTop || 0;
                    this.isVisible = top > 150;
                },

                scrollToTop() {
                    window.scrollTo({
                        top: 0,
                        behavior: 'smooth',
                    });
                },
            },
        });
    </script>

    @stack('scripts')

    {!! view_render_event('campusfind_web.web.layout.vue-app-mount.before') !!}

    <script>
        /**
         * Load event, the purpose of using the event is to mount the application
         * after all of our `Vue` components which is present in blade file have
         * been registered in the app. No matter what `app.mount()` should be
         * called in the last.
         */
        window.addEventListener("load", function() {
            if (window.app && typeof window.app.mount === 'function') {
                window.app.mount("#app");
            }
        });
    </script>

    {!! view_render_event('campusfind_web.web.layout.vue-app-mount.after') !!}
</body>
</html>
