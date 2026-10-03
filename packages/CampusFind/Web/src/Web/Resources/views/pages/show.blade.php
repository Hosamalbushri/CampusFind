<x-campusfind_web_web::layouts>
    <x-campusfind_web_web::container class="max-w-4xl py-12">
        <!-- Breadcrumbs Navigation -->
        <nav class="flex items-center gap-2 text-sm text-gray-500 dark:text-gray-400 mb-6">
            <a href="{{ route('campusfind_web.web.home') }}" class="hover:text-[var(--brand-color)] text-decoration-none">
                @lang('campusfind_web_web::app.web.home')
            </a>
            <span>/</span>
            <span class="font-medium text-gray-800 dark:text-gray-200 capitalize">
                {{ trans('campusfind_web_web::app.web.' . str_replace('-', '_', $page)) !== 'campusfind_web_web::app.web.' . str_replace('-', '_', $page) ? trans('campusfind_web_web::app.web.' . str_replace('-', '_', $page)) : ucfirst(str_replace('-', ' ', $page)) }}
            </span>
        </nav>

        <!-- Page Content Card -->
        <x-campusfind_web_web::card>
            <h1 class="text-3xl font-extrabold text-gray-900 dark:text-white mb-6">
                {{ trans('campusfind_web_web::app.web.' . str_replace('-', '_', $page)) !== 'campusfind_web_web::app.web.' . str_replace('-', '_', $page) ? trans('campusfind_web_web::app.web.' . str_replace('-', '_', $page)) : ucfirst(str_replace('-', ' ', $page)) }}
            </h1>

            <div class="prose dark:prose-invert max-w-none text-gray-600 dark:text-gray-300 leading-relaxed space-y-4">
                @if ($page === 'how-it-works')
                    <div class="space-y-6">
                        <div class="flex gap-4 items-start">
                            <div class="flex-shrink-0 h-10 w-10 rounded-xl bg-blue-100 text-[var(--brand-color)] dark:bg-blue-950 dark:text-blue-400 flex items-center justify-center font-bold text-lg">
                                1
                            </div>
                            <div>
                                <h3 class="font-bold text-lg text-gray-900 dark:text-white">@lang('campusfind_web_web::app.web.browse_items')</h3>
                                <p class="text-sm mt-1">Search the public catalog of found items using keywords, category filters, and campus location.</p>
                            </div>
                        </div>

                        <div class="flex gap-4 items-start">
                            <div class="flex-shrink-0 h-10 w-10 rounded-xl bg-blue-100 text-[var(--brand-color)] dark:bg-blue-950 dark:text-blue-400 flex items-center justify-center font-bold text-lg">
                                2
                            </div>
                            <div>
                                <h3 class="font-bold text-lg text-gray-900 dark:text-white">@lang('campusfind_web_web::app.web.claim_item')</h3>
                                <p class="text-sm mt-1">Sign in with your student credentials and submit a claim with specific proof or distinctive identifying marks.</p>
                            </div>
                        </div>

                        <div class="flex gap-4 items-start">
                            <div class="flex-shrink-0 h-10 w-10 rounded-xl bg-blue-100 text-[var(--brand-color)] dark:bg-blue-950 dark:text-blue-400 flex items-center justify-center font-bold text-lg">
                                3
                            </div>
                            <div>
                                <h3 class="font-bold text-lg text-gray-900 dark:text-white">Review & Handover</h3>
                                <p class="text-sm mt-1">Campus security and staff verify your claim, approve the request, and complete the physical handover securely.</p>
                            </div>
                        </div>
                    </div>
                @elseif ($page === 'about')
                    <p>
                        CampusFind is the centralized lost and found management platform designed for university campuses. It streamlines the lifecycle of lost belongings from reporting to secure verification and custody return.
                    </p>
                    <p>
                        Built with Laraseed modular architecture, it ensures complete security, privacy of sensitive claimant information, and high availability.
                    </p>
                @elseif ($page === 'contact')
                    <p>
                        Need help recovering a lost item or have questions about campus custody procedures?
                    </p>
                    <div class="mt-4 p-4 rounded-xl bg-gray-50 dark:bg-gray-800 border border-gray-200 dark:border-gray-700">
                        <p class="font-semibold text-gray-900 dark:text-white">Campus Security & Lost Property Office</p>
                        <p class="text-sm text-gray-500 dark:text-gray-400 mt-1">Main Administration Building, Ground Floor</p>
                        <p class="text-sm text-gray-500 dark:text-gray-400">Email: lostandfound@university.edu</p>
                    </div>
                @else
                    <p>
                        @lang('campusfind_web_web::app.web.hero.subheadline')
                    </p>
                @endif
            </div>
        </x-campusfind_web_web::card>
    </x-campusfind_web_web::container>
</x-campusfind_web_web::layouts>
