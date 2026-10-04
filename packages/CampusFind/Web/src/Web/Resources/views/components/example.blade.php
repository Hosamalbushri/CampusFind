<div class="space-y-10 font-cairo p-6 max-w-5xl mx-auto">
    <!-- Component 1: Accordion -->
    <x-web::card title="Accordion Component (<x-web::accordion>)" variant="elevated" padding="lg">
        <div class="space-y-4">
            <x-web::accordion :isActive="true">
                <x-slot:header>
                    <span class="font-bold">What is CampusFind?</span>
                </x-slot:header>
                <x-slot:content>
                    <p class="text-sm text-slate-600 dark:text-slate-400">CampusFind is the centralized campus lost & found platform.</p>
                </x-slot:content>
            </x-web::accordion>

            <x-web::accordion :isActive="false">
                <x-slot:header>
                    <span class="font-bold">How do I submit a claim?</span>
                </x-slot:header>
                <x-slot:content>
                    <p class="text-sm text-slate-600 dark:text-slate-400">Sign in with your student credentials and click 'Claim Item'.</p>
                </x-slot:content>
            </x-web::accordion>
        </div>
    </x-web::card>

    <!-- Component 2: Tabs -->
    <x-web::card title="Tabs Component (<x-web::tabs>)" variant="elevated" padding="lg">
        <x-web::tabs position="start">
            <x-web::tabs.item title="Overview" :isSelected="true">
                <p class="text-sm text-slate-600 dark:text-slate-400 py-2">This is the Overview tab content.</p>
            </x-web::tabs.item>
            <x-web::tabs.item title="Specifications" :isSelected="false">
                <p class="text-sm text-slate-600 dark:text-slate-400 py-2">This is the Specifications tab content.</p>
            </x-web::tabs.item>
            <x-web::tabs.item title="Activity Log" :isSelected="false">
                <p class="text-sm text-slate-600 dark:text-slate-400 py-2">This is the Activity Log tab content.</p>
            </x-web::tabs.item>
        </x-web::tabs>
    </x-web::card>

    <!-- Component 3: Modal & Confirm Modal -->
    <x-web::card title="Modal Component (<x-web::modal> & <x-web::modal.confirm>)" variant="elevated" padding="lg">
        <div class="flex flex-wrap gap-4">
            <x-web::modal id="example-modal" size="md">
                <x-slot:toggle>
                    <x-web::button variant="primary" size="md">Open Modal</x-web::button>
                </x-slot:toggle>
                <x-slot:header>
                    <h3 class="font-bold text-lg">Example Modal Header</h3>
                </x-slot:header>
                <x-slot:content>
                    <p class="text-sm text-slate-600 dark:text-slate-400">Modal content goes here with complete accessibility and keyboard traps.</p>
                </x-slot:content>
                <x-slot:footer>
                    <x-web::button variant="secondary" size="sm">Cancel</x-web::button>
                    <x-web::button variant="primary" size="sm">Confirm</x-web::button>
                </x-slot:footer>
            </x-web::modal>

            <x-web::button
                variant="danger"
                size="md"
                onclick="window.app.config.globalProperties.$emitter.emit('open-confirm-modal', {
                    title: 'Confirm Action',
                    message: 'Are you sure you want to proceed?',
                    agree: () => alert('Agreed!')
                })"
            >
                Trigger Confirm Modal
            </x-web::button>
        </div>
    </x-web::card>

    <!-- Component 4: Drawer -->
    <x-web::card title="Drawer Component (<x-web::drawer>)" variant="elevated" padding="lg">
        <x-web::drawer id="example-drawer" position="right" width="400px">
            <x-slot:toggle>
                <x-web::button variant="secondary" size="md">Open Slide-over Drawer</x-web::button>
            </x-slot:toggle>
            <x-slot:header>
                <h3 class="font-bold text-lg">Filter Drawer</h3>
            </x-slot:header>
            <x-slot:content>
                <p class="text-sm text-slate-600 dark:text-slate-400">Drawer content sliding in from the right or left with backdrop overlay.</p>
            </x-slot:content>
            <x-slot:footer>
                <x-web::button variant="primary" size="sm" class="w-full">Apply Filters</x-web::button>
            </x-slot:footer>
        </x-web::drawer>
    </x-web::card>

    <!-- Component 5: Media Images Upload -->
    <x-web::card title="Media Images Component (<x-web::media.images>)" variant="elevated" padding="lg">
        <x-web::media.images name="demo_images" :allowMultiple="true" />
    </x-web::card>

    <!-- Component 6: Tags -->
    <x-web::card title="Tags Component (<x-web::tags>)" variant="elevated" padding="lg">
        <x-web::tags name="demo_tags" :value="['Electronics', 'Campus', 'Urgent']" />
    </x-web::card>

    <!-- Component 7: Dropdown -->
    <x-web::card title="Dropdown Component (<x-web::dropdown>)" variant="elevated" padding="lg">
        <x-web::dropdown position="bottom-right">
            <x-slot:toggle>
                <x-web::button variant="secondary" size="md">Options Menu ▼</x-web::button>
            </x-slot:toggle>
            <x-slot:menu>
                <x-web::dropdown.menu.item href="#">View Profile</x-web::dropdown.menu.item>
                <x-web::dropdown.menu.item href="#">Account Settings</x-web::dropdown.menu.item>
                <x-web::dropdown.menu.item href="#">Help & Support</x-web::dropdown.menu.item>
            </x-slot:menu>
        </x-web::dropdown>
    </x-web::card>

    <!-- Component 8: Form Controls Matching Admin Architecture -->
    <x-web::card title="Form Components (<x-web::form>, <x-web::form.control-group>, <v-field>, <v-error-message>)" variant="elevated" padding="lg">
        <x-web::form class="space-y-4">
            <x-web::form.control-group>
                <x-web::form.control-group.label for="sample_text" :required="true">
                    Full Name
                </x-web::form.control-group.label>

                <x-web::form.control-group.control
                    type="text"
                    name="sample_text"
                    id="sample_text"
                    rules="required"
                    label="Full Name"
                    placeholder="John Doe"
                />

                <x-web::form.control-group.error name="sample_text" />
            </x-web::form.control-group>

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <x-web::form.control-group>
                    <x-web::form.control-group.label for="sample_date">
                        Date Picker
                    </x-web::form.control-group.label>

                    <x-web::form.control-group.control
                        type="date"
                        name="sample_date"
                        id="sample_date"
                    />

                    <x-web::form.control-group.error name="sample_date" />
                </x-web::form.control-group>

                <x-web::form.control-group>
                    <x-web::form.control-group.label for="sample_datetime">
                        DateTime Picker
                    </x-web::form.control-group.label>

                    <x-web::form.control-group.control
                        type="datetime"
                        name="sample_datetime"
                        id="sample_datetime"
                    />

                    <x-web::form.control-group.error name="sample_datetime" />
                </x-web::form.control-group>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
                <x-web::form.control-group>
                    <x-web::form.control-group.label for="sample_checkbox">
                        Checkbox
                    </x-web::form.control-group.label>

                    <x-web::form.control-group.control
                        type="checkbox"
                        name="sample_checkbox"
                        id="sample_checkbox"
                        value="1"
                    >
                        Accept Terms
                    </x-web::form.control-group.control>
                </x-web::form.control-group>

                <x-web::form.control-group>
                    <x-web::form.control-group.label for="sample_switch">
                        Switch Toggle
                    </x-web::form.control-group.label>

                    <x-web::form.control-group.control
                        type="switch"
                        name="sample_switch"
                        id="sample_switch"
                        value="1"
                    >
                        Enable Notifications
                    </x-web::form.control-group.control>
                </x-web::form.control-group>

                <x-web::form.control-group>
                    <x-web::form.control-group.label for="sample_price">
                        Price Input
                    </x-web::form.control-group.label>

                    <x-web::form.control-group.control
                        type="price"
                        name="sample_price"
                        id="sample_price"
                        placeholder="0.00"
                    />
                </x-web::form.control-group>
            </div>
        </x-web::form>
    </x-web::card>

    <!-- Component 9: Badges, Buttons, Shimmer -->
    <x-web::card title="Badges and Buttons" variant="elevated" padding="lg">
        <div class="space-y-6">
            <div class="flex flex-wrap gap-2">
                <x-web::badge variant="mint">Mint Badge</x-web::badge>
                <x-web::badge variant="success" :dot="true">Active</x-web::badge>
                <x-web::badge variant="warning" :dot="true">Pending</x-web::badge>
                <x-web::badge variant="danger">High Priority</x-web::badge>
                <x-web::badge variant="neutral">REF-10928</x-web::badge>
            </div>

            <div class="flex flex-wrap gap-2">
                <x-web::button variant="primary" size="md">Primary</x-web::button>
                <x-web::button variant="secondary" size="md">Secondary</x-web::button>
                <x-web::button variant="mint" size="md">Mint Action</x-web::button>
                <x-web::button variant="ghost" size="md">Ghost Action</x-web::button>
                <x-web::button variant="danger" size="md">Danger</x-web::button>
            </div>

            <div class="pt-4 border-t border-slate-100 dark:border-slate-800">
                <h4 class="font-bold text-sm mb-3">Shimmer Loading States</h4>
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <x-web::shimmer.card />
                    <x-web::shimmer.accordion />
                </div>
            </div>
        </div>
    </x-web::card>

    <!-- Component 10: Icon Library (Admin Parity) -->
    <x-web::card title="Icon Library (Admin Icomoon Font Parity)" variant="elevated" padding="lg">
        <div class="grid grid-cols-2 sm:grid-cols-4 md:grid-cols-6 gap-4 text-center">
            <div class="p-3 rounded-xl border border-slate-100 dark:border-slate-800 flex flex-col items-center gap-2 hover:bg-slate-50 dark:hover:bg-slate-800/50 transition-colors">
                <span class="icon-search text-2xl text-[#185c54] dark:text-[#a3e4c8]"></span>
                <span class="text-xs text-slate-500">icon-search</span>
            </div>
            <div class="p-3 rounded-xl border border-slate-100 dark:border-slate-800 flex flex-col items-center gap-2 hover:bg-slate-50 dark:hover:bg-slate-800/50 transition-colors">
                <span class="icon-filter text-2xl text-[#185c54] dark:text-[#a3e4c8]"></span>
                <span class="text-xs text-slate-500">icon-filter</span>
            </div>
            <div class="p-3 rounded-xl border border-slate-100 dark:border-slate-800 flex flex-col items-center gap-2 hover:bg-slate-50 dark:hover:bg-slate-800/50 transition-colors">
                <span class="icon-calendar text-2xl text-[#185c54] dark:text-[#a3e4c8]"></span>
                <span class="text-xs text-slate-500">icon-calendar</span>
            </div>
            <div class="p-3 rounded-xl border border-slate-100 dark:border-slate-800 flex flex-col items-center gap-2 hover:bg-slate-50 dark:hover:bg-slate-800/50 transition-colors">
                <span class="icon-location text-2xl text-[#185c54] dark:text-[#a3e4c8]"></span>
                <span class="text-xs text-slate-500">icon-location</span>
            </div>
            <div class="p-3 rounded-xl border border-slate-100 dark:border-slate-800 flex flex-col items-center gap-2 hover:bg-slate-50 dark:hover:bg-slate-800/50 transition-colors">
                <span class="icon-image text-2xl text-[#185c54] dark:text-[#a3e4c8]"></span>
                <span class="text-xs text-slate-500">icon-image</span>
            </div>
            <div class="p-3 rounded-xl border border-slate-100 dark:border-slate-800 flex flex-col items-center gap-2 hover:bg-slate-50 dark:hover:bg-slate-800/50 transition-colors">
                <span class="icon-attached-file text-2xl text-[#185c54] dark:text-[#a3e4c8]"></span>
                <span class="text-xs text-slate-500">icon-attached-file</span>
            </div>
            <div class="p-3 rounded-xl border border-slate-100 dark:border-slate-800 flex flex-col items-center gap-2 hover:bg-slate-50 dark:hover:bg-slate-800/50 transition-colors">
                <span class="icon-tag text-2xl text-[#185c54] dark:text-[#a3e4c8]"></span>
                <span class="text-xs text-slate-500">icon-tag</span>
            </div>
            <div class="p-3 rounded-xl border border-slate-100 dark:border-slate-800 flex flex-col items-center gap-2 hover:bg-slate-50 dark:hover:bg-slate-800/50 transition-colors">
                <span class="icon-settings-tag text-2xl text-[#185c54] dark:text-[#a3e4c8]"></span>
                <span class="text-xs text-slate-500">icon-settings-tag</span>
            </div>
            <div class="p-3 rounded-xl border border-slate-100 dark:border-slate-800 flex flex-col items-center gap-2 hover:bg-slate-50 dark:hover:bg-slate-800/50 transition-colors">
                <span class="icon-notification text-2xl text-[#185c54] dark:text-[#a3e4c8]"></span>
                <span class="text-xs text-slate-500">icon-notification</span>
            </div>
            <div class="p-3 rounded-xl border border-slate-100 dark:border-slate-800 flex flex-col items-center gap-2 hover:bg-slate-50 dark:hover:bg-slate-800/50 transition-colors">
                <span class="icon-dashboard text-2xl text-[#185c54] dark:text-[#a3e4c8]"></span>
                <span class="text-xs text-slate-500">icon-dashboard</span>
            </div>
            <div class="p-3 rounded-xl border border-slate-100 dark:border-slate-800 flex flex-col items-center gap-2 hover:bg-slate-50 dark:hover:bg-slate-800/50 transition-colors">
                <span class="icon-user text-2xl text-[#185c54] dark:text-[#a3e4c8]"></span>
                <span class="text-xs text-slate-500">icon-user</span>
            </div>
            <div class="p-3 rounded-xl border border-slate-100 dark:border-slate-800 flex flex-col items-center gap-2 hover:bg-slate-50 dark:hover:bg-slate-800/50 transition-colors">
                <span class="icon-edit text-2xl text-[#185c54] dark:text-[#a3e4c8]"></span>
                <span class="text-xs text-slate-500">icon-edit</span>
            </div>
            <div class="p-3 rounded-xl border border-slate-100 dark:border-slate-800 flex flex-col items-center gap-2 hover:bg-slate-50 dark:hover:bg-slate-800/50 transition-colors">
                <span class="icon-delete text-2xl text-rose-500"></span>
                <span class="text-xs text-slate-500">icon-delete</span>
            </div>
            <div class="p-3 rounded-xl border border-slate-100 dark:border-slate-800 flex flex-col items-center gap-2 hover:bg-slate-50 dark:hover:bg-slate-800/50 transition-colors">
                <span class="icon-cross-large text-2xl text-slate-500"></span>
                <span class="text-xs text-slate-500">icon-cross-large</span>
            </div>
            <div class="p-3 rounded-xl border border-slate-100 dark:border-slate-800 flex flex-col items-center gap-2 hover:bg-slate-50 dark:hover:bg-slate-800/50 transition-colors">
                <span class="icon-success text-2xl text-emerald-500"></span>
                <span class="text-xs text-slate-500">icon-success</span>
            </div>
            <div class="p-3 rounded-xl border border-slate-100 dark:border-slate-800 flex flex-col items-center gap-2 hover:bg-slate-50 dark:hover:bg-slate-800/50 transition-colors">
                <span class="icon-warning text-2xl text-amber-500"></span>
                <span class="text-xs text-slate-500">icon-warning</span>
            </div>
            <div class="p-3 rounded-xl border border-slate-100 dark:border-slate-800 flex flex-col items-center gap-2 hover:bg-slate-50 dark:hover:bg-slate-800/50 transition-colors">
                <span class="icon-error text-2xl text-rose-500"></span>
                <span class="text-xs text-slate-500">icon-error</span>
            </div>
            <div class="p-3 rounded-xl border border-slate-100 dark:border-slate-800 flex flex-col items-center gap-2 hover:bg-slate-50 dark:hover:bg-slate-800/50 transition-colors">
                <span class="icon-info text-2xl text-sky-500"></span>
                <span class="text-xs text-slate-500">icon-info</span>
            </div>
            <div class="p-3 rounded-xl border border-slate-100 dark:border-slate-800 flex flex-col items-center gap-2 hover:bg-slate-50 dark:hover:bg-slate-800/50 transition-colors">
                <span class="icon-dark text-2xl text-slate-600 dark:text-slate-300"></span>
                <span class="text-xs text-slate-500">icon-dark</span>
            </div>
            <div class="p-3 rounded-xl border border-slate-100 dark:border-slate-800 flex flex-col items-center gap-2 hover:bg-slate-50 dark:hover:bg-slate-800/50 transition-colors">
                <span class="icon-light text-2xl text-amber-400"></span>
                <span class="text-xs text-slate-500">icon-light</span>
            </div>
            <div class="p-3 rounded-xl border border-slate-100 dark:border-slate-800 flex flex-col items-center gap-2 hover:bg-slate-50 dark:hover:bg-slate-800/50 transition-colors">
                <span class="icon-menu text-2xl text-slate-600 dark:text-slate-300"></span>
                <span class="text-xs text-slate-500">icon-menu</span>
            </div>
            <div class="p-3 rounded-xl border border-slate-100 dark:border-slate-800 flex flex-col items-center gap-2 hover:bg-slate-50 dark:hover:bg-slate-800/50 transition-colors">
                <span class="icon-up-arrow text-2xl text-slate-500"></span>
                <span class="text-xs text-slate-500">icon-up-arrow</span>
            </div>
            <div class="p-3 rounded-xl border border-slate-100 dark:border-slate-800 flex flex-col items-center gap-2 hover:bg-slate-50 dark:hover:bg-slate-800/50 transition-colors">
                <span class="icon-down-arrow text-2xl text-slate-500"></span>
                <span class="text-xs text-slate-500">icon-down-arrow</span>
            </div>
            <div class="p-3 rounded-xl border border-slate-100 dark:border-slate-800 flex flex-col items-center gap-2 hover:bg-slate-50 dark:hover:bg-slate-800/50 transition-colors">
                <span class="icon-toast-done text-2xl text-emerald-500"></span>
                <span class="text-xs text-slate-500">icon-toast-done</span>
            </div>
        </div>
    </x-web::card>
</div>
