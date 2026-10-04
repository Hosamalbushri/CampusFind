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

        <!-- Page Anonymous / Standalone Content -->
        <main class="flex-1 flex flex-col justify-center">
            {{ $slot }}
        </main>

        {!! view_render_event('campusfind_web.web.layout.content.after') !!}
    </div>

    {!! view_render_event('campusfind_web.web.layout.body.after') !!}

    @stack('scripts')

    {!! view_render_event('campusfind_web.web.layout.vue-app-mount.before') !!}

    <script>
        /**
         * Mount the Vue application after all components registered in Blade files
         * have been pushed into the scripts stack.
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
