<x-web::layouts>
    <x-web::container class="py-10 sm:py-12">
        <!-- Breadcrumbs Navigation -->
        <x-web::breadcrumbs
            :items="[
                (trans('campusfind_web_web::app.web.' . str_replace('-', '_', $page)) !== 'campusfind_web_web::app.web.' . str_replace('-', '_', $page)
                    ? trans('campusfind_web_web::app.web.' . str_replace('-', '_', $page))
                    : ucfirst(str_replace('-', ' ', $page))) => ''
            ]"
            class="mb-8"
        />

        <!-- Page Content Card -->
        <x-web::card variant="elevated" padding="lg">
            <div class="border-b border-slate-100 dark:border-slate-800 pb-6 mb-8">
                <h1 class="text-3xl font-black text-slate-900 dark:text-white tracking-tight">
                    {{ trans('campusfind_web_web::app.web.' . str_replace('-', '_', $page)) !== 'campusfind_web_web::app.web.' . str_replace('-', '_', $page) ? trans('campusfind_web_web::app.web.' . str_replace('-', '_', $page)) : ucfirst(str_replace('-', ' ', $page)) }}
                </h1>
            </div>

            <div class="prose dark:prose-invert max-w-none text-slate-600 dark:text-slate-300 leading-relaxed">
                @if ($page === 'how-it-works')
                    <div class="grid grid-cols-1 gap-5 not-prose">
                        <x-web::card variant="flat" padding="md" class="flex gap-4 items-start">
                            <div class="shrink-0 h-12 w-12 rounded-2xl bg-[#e6f4ee] text-[#185c54] dark:bg-[#185c54]/30 dark:text-[#a3e4c8] ring-4 ring-[#e6f4ee]/60 dark:ring-[#185c54]/10 flex items-center justify-center font-black text-lg">
                                1
                            </div>
                            <div class="flex-1">
                                <h3 class="font-bold text-lg text-slate-900 dark:text-white">@lang('campusfind_web_web::app.web.pages.how_it_works.step1_title')</h3>
                                <p class="text-sm text-slate-600 dark:text-slate-400 mt-1 leading-relaxed">
                                    @lang('campusfind_web_web::app.web.pages.how_it_works.step1_desc')
                                </p>
                            </div>
                        </x-web::card>

                        <x-web::card variant="flat" padding="md" class="flex gap-4 items-start">
                            <div class="shrink-0 h-12 w-12 rounded-2xl bg-[#e6f4ee] text-[#185c54] dark:bg-[#185c54]/30 dark:text-[#a3e4c8] ring-4 ring-[#e6f4ee]/60 dark:ring-[#185c54]/10 flex items-center justify-center font-black text-lg">
                                2
                            </div>
                            <div class="flex-1">
                                <h3 class="font-bold text-lg text-slate-900 dark:text-white">@lang('campusfind_web_web::app.web.pages.how_it_works.step2_title')</h3>
                                <p class="text-sm text-slate-600 dark:text-slate-400 mt-1 leading-relaxed">
                                    @lang('campusfind_web_web::app.web.pages.how_it_works.step2_desc')
                                </p>
                            </div>
                        </x-web::card>

                        <x-web::card variant="flat" padding="md" class="flex gap-4 items-start">
                            <div class="shrink-0 h-12 w-12 rounded-2xl bg-[#e6f4ee] text-[#185c54] dark:bg-[#185c54]/30 dark:text-[#a3e4c8] ring-4 ring-[#e6f4ee]/60 dark:ring-[#185c54]/10 flex items-center justify-center font-black text-lg">
                                3
                            </div>
                            <div class="flex-1">
                                <h3 class="font-bold text-lg text-slate-900 dark:text-white">@lang('campusfind_web_web::app.web.pages.how_it_works.step3_title')</h3>
                                <p class="text-sm text-slate-600 dark:text-slate-400 mt-1 leading-relaxed">
                                    @lang('campusfind_web_web::app.web.pages.how_it_works.step3_desc')
                                </p>
                            </div>
                        </x-web::card>
                    </div>
                @elseif ($page === 'about')
                    <div class="space-y-4 text-base">
                        <p>
                            @lang('campusfind_web_web::app.web.pages.about.description1')
                        </p>
                        <p>
                            @lang('campusfind_web_web::app.web.pages.about.description2')
                        </p>
                    </div>
                @elseif ($page === 'contact')
                    <div class="space-y-6">
                        <p class="text-base">
                            @lang('campusfind_web_web::app.web.pages.contact.intro')
                        </p>
                        <x-web::card variant="flat" padding="md" class="not-prose">
                            <h3 class="font-bold text-slate-900 dark:text-white text-base">@lang('campusfind_web_web::app.web.pages.contact.office_title')</h3>
                            <p class="text-sm text-slate-600 dark:text-slate-400 mt-2">@lang('campusfind_web_web::app.web.pages.contact.office_location')</p>
                            <p class="text-sm text-slate-600 dark:text-slate-400 mt-1">@lang('campusfind_web_web::app.web.pages.contact.email_label'): <span class="font-bold text-[#185c54] dark:text-[#a3e4c8]">lostandfound@university.edu</span></p>
                        </x-web::card>
                    </div>
                @elseif ($page === 'faq')
                    <div class="space-y-4 not-prose">
                        <x-web::accordion :isActive="true">
                            <x-slot:header>
                                <h3 class="text-base font-bold text-slate-900 dark:text-white">
                                    @lang('campusfind_web_web::app.web.how_it_works')
                                </h3>
                            </x-slot:header>

                            <x-slot:content>
                                <p class="text-sm text-slate-600 dark:text-slate-400 leading-relaxed">
                                    @lang('campusfind_web_web::app.web.hero.subheadline')
                                </p>
                            </x-slot:content>
                        </x-web::accordion>

                        <x-web::accordion :isActive="false">
                            <x-slot:header>
                                <h3 class="text-base font-bold text-slate-900 dark:text-white">
                                    @lang('campusfind_web_web::app.web.browse_items')
                                </h3>
                            </x-slot:header>

                            <x-slot:content>
                                <p class="text-sm text-slate-600 dark:text-slate-400 leading-relaxed">
                                    @lang('campusfind_web_web::app.web.browse.subtitle')
                                </p>
                            </x-slot:content>
                        </x-web::accordion>

                        <x-web::accordion :isActive="false">
                            <x-slot:header>
                                <h3 class="text-base font-bold text-slate-900 dark:text-white">
                                    @lang('campusfind_web_web::app.web.hero.claim_prompt')
                                </h3>
                            </x-slot:header>

                            <x-slot:content>
                                <p class="text-sm text-slate-600 dark:text-slate-400 leading-relaxed">
                                    @lang('campusfind_web_web::app.web.hero.claim_hint')
                                </p>
                            </x-slot:content>
                        </x-web::accordion>
                    </div>
                @else
                    <p>
                        @lang('campusfind_web_web::app.web.hero.subheadline')
                    </p>
                @endif
            </div>
        </x-web::card>
    </x-web::container>
</x-web::layouts>
