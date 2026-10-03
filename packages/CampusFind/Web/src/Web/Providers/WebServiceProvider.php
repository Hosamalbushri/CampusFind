<?php

namespace CampusFind\Web\Web\Providers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Blade;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\ServiceProvider;
use Webkul\Core\Contracts\AuthenticationRedirectResolver;

class WebServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     */
    public function register(): void
    {
        if (file_exists(__DIR__ . '/../Config/web.php')) {
            $this->mergeConfigFrom(__DIR__ . '/../Config/web.php', 'campusfind_web_web');
        }

        config([
            'krayin-vite.viters.campusfind_web_web' => [
                'hot_file'                 => 'campusfind_web-web-vite.hot',
                'build_directory'          => 'campus-find-web/web/build',
                'package_assets_directory' => 'src/Web/Resources/assets',
            ],
        ]);
    }

    /**
     * Bootstrap services.
     */
    public function boot(): void
    {
        Blade::anonymousComponentPath(__DIR__ . '/../Resources/views/components', 'campusfind_web_web');

        if (is_dir(__DIR__ . '/../Resources/views')) {
            $this->loadViewsFrom(__DIR__ . '/../Resources/views', 'campusfind_web_web');
        }

        if (is_dir(__DIR__ . '/../Resources/lang')) {
            $this->loadTranslationsFrom(__DIR__ . '/../Resources/lang', 'campusfind_web_web');
        }

        // Register package-owned auth middleware alias
        $router = $this->app['router'];
        $router->aliasMiddleware('campusfind_web_auth', \CampusFind\Web\Web\Http\Middleware\AuthenticateWeb::class);

        $authConfig = config('campusfind_web_web.auth', []);
        $authEnabled = (bool) ($authConfig['enabled'] ?? false);

        // When auth is enabled, validate configuration contract and register redirection resolver rule
        if ($authEnabled) {
            $guard = $authConfig['guard'] ?? null;
            if (! $guard || ! config("auth.guards.{$guard}")) {
                throw new \RuntimeException(sprintf(
                    'Authentication configuration error: Package [%s] specifies guard [%s], which is not defined in config/auth.php.',
                    'campusfind_web',
                    $guard ?? 'null'
                ));
            }

            $guardProvider = config("auth.guards.{$guard}.provider");
            if ($guardProvider && ! config("auth.providers.{$guardProvider}")) {
                throw new \RuntimeException(sprintf(
                    'Authentication configuration error: Package [%s] specifies guard [%s] with undefined user provider [%s] in config/auth.php.',
                    'campusfind_web',
                    $guard,
                    $guardProvider
                ));
            }

            $providerPackage = $authConfig['provider_package'] ?? null;
            if ($providerPackage) {
                $enabledPackages = config('laraseed.optional_packages.enabled', []);
                if (! in_array($providerPackage, $enabledPackages, true)) {
                    throw new \RuntimeException(sprintf(
                        'Authentication configuration error: Package [%s] requires authentication provider package [%s], which is not currently enabled in LARASEED_OPTIONAL_PACKAGES.',
                        'campusfind_web',
                        $providerPackage
                    ));
                }
            }

            $loginRoute = $authConfig['routes']['login'] ?? null;
            if (! $loginRoute) {
                throw new \RuntimeException(sprintf(
                    'Authentication configuration error: Package [%s] requires a configured login route when authentication is enabled.',
                    'campusfind_web'
                ));
            }

            if ($this->app->bound(AuthenticationRedirectResolver::class)) {
                $rawPrefix = config('campusfind_web_web.prefix');
                $defaultPkg = (string) (config('laraseed.default_web_package') ?? config('laraseed.web.default_package') ?? '');
                $isDefault = $defaultPkg !== '' && (
                    strtolower(str_replace('-', '_', $defaultPkg)) === 'campusfind_web'
                    || strtolower(str_replace('-', '_', $defaultPkg)) === 'web'
                );
                $prefix = $isDefault && ($rawPrefix === null || $rawPrefix === '' || $rawPrefix === 'campus-find-web')
                    ? ''
                    : (string) ($rawPrefix ?? 'campus-find-web');

                $this->app->make(AuthenticationRedirectResolver::class)->register(
                    'campusfind_web_web',
                    function (Request $request) use ($prefix): bool {
                        $route = $request->route();

                        if ($route) {
                            $name = (string) $route->getName();
                            if ($name !== '' && str_starts_with($name, 'campusfind_web.web.')) {
                                return true;
                            }

                            $middleware = is_array($route->middleware()) ? $route->middleware() : [];
                            if (in_array('campusfind_web_auth', $middleware, true)) {
                                return true;
                            }

                            // Resolved route does not belong to this package; do not intercept
                            return false;
                        }

                        $normalized = trim($prefix, '/');
                        if ($normalized !== '') {
                            return $request->is($normalized) || $request->is($normalized . '/*');
                        }

                        return false;
                    },
                    function () use ($loginRoute): string {
                        return route($loginRoute);
                    },
                    50
                );
            }

        }

        if (file_exists(__DIR__ . '/../Routes/web.php')) {
            $defaultPkg = (string) (config('laraseed.default_web_package') ?? config('laraseed.web.default_package') ?? '');
            $isDefault = $defaultPkg !== '' && (
                strtolower(str_replace('-', '_', $defaultPkg)) === 'campusfind_web'
                || strtolower(str_replace('-', '_', $defaultPkg)) === 'web'
            );

            $rawPrefix = config('campusfind_web_web.prefix');

            if ($isDefault && ($rawPrefix === null || $rawPrefix === '' || $rawPrefix === 'campus-find-web')) {
                $prefix = '';
            } else {
                $prefix = (string) ($rawPrefix ?? 'campus-find-web');
            }

            $middleware = config('campusfind_web_web.middleware', ['web']);
            $normalizedPrefix = trim($prefix, '/');

            // Guard against collision with core protected system route prefixes
            $reservedPrefixes = ['admin', 'install', 'api', 'up', 'sanctum', (string) config('app.admin_path', 'admin')];
            if ($normalizedPrefix !== '' && in_array($normalizedPrefix, array_filter($reservedPrefixes), true)) {
                throw new \RuntimeException(sprintf(
                    'Route conflict: Package [%s] specifies route prefix [%s], which conflicts with protected system route prefix.',
                    'campusfind_web',
                    $normalizedPrefix
                ));
            }

            if ($normalizedPrefix === '') {
                $rootOwner = config('laraseed.web.root_owner');
                if ($rootOwner && $rootOwner !== 'campusfind_web') {
                    throw new \RuntimeException(sprintf(
                        'Route conflict: Package [%s] attempted to claim the root route [/], but it is already owned by [%s]. Only one package may be root-mounted.',
                        'campusfind_web',
                        $rootOwner
                    ));
                }
                config(['laraseed.web.root_owner' => 'campusfind_web']);
            }

            Route::middleware($middleware)
                ->prefix($prefix)
                ->group(__DIR__ . '/../Routes/web.php');
        }
    }
}

