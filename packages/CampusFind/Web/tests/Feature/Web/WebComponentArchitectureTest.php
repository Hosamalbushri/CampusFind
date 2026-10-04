<?php

namespace CampusFind\Web\Tests\Feature\Web;

use CampusFind\Student\Models\Student;
use CampusFind\Web\Tests\TestCase;
use Illuminate\Support\Facades\Blade;

class WebComponentArchitectureTest extends TestCase
{
    public function test_shared_components_render_variants_and_forward_html_attributes(): void
    {
        $html = Blade::render(<<<'BLADE'
            <x-web::button variant="danger" size="sm" data-contract="button">Remove</x-web::button>
            <x-web::badge variant="success" size="lg" data-contract="badge">Ready</x-web::badge>
            <x-web::card variant="mint" padding="sm" data-contract="card">Content</x-web::card>
            <x-campusfind_web_web::button variant="primary" size="md">Submit</x-campusfind_web_web::button>
        BLADE);

        $this->assertStringContainsString('data-contract="button"', $html);
        $this->assertStringContainsString('bg-rose-600', $html);
        $this->assertStringContainsString('data-contract="badge"', $html);
        $this->assertStringContainsString('bg-emerald-50', $html);
        $this->assertStringContainsString('data-contract="card"', $html);
        $this->assertStringContainsString('bg-[#e6f4ee]', $html);
        $this->assertStringContainsString('Submit', $html);
    }

    public function test_content_primitives_have_semantic_accessibility_contracts(): void
    {
        $html = Blade::render(<<<'BLADE'
            <x-web::alert variant="warning">Check input</x-web::alert>
            <x-web::page-header title="Browse" description="Find an item" />
            <x-web::empty-state title="Nothing here" description="Try again" />
        BLADE);

        $this->assertStringContainsString('role="alert"', $html);
        $this->assertStringContainsString('<h1', $html);
        $this->assertStringContainsString('Browse', $html);
        $this->assertStringContainsString('Nothing here', $html);
    }

    public function test_reference_accordion_renders_vue_architecture_and_shimmer(): void
    {
        $html = Blade::render(<<<'BLADE'
            <x-web::accordion :isActive="true" class="custom-accordion-class">
                <x-slot:header>
                    <span>Custom FAQ Question</span>
                </x-slot:header>

                <x-slot:content>
                    <p>Detailed answer to the question.</p>
                </x-slot:content>
            </x-web::accordion>
            @stack('scripts')
        BLADE);

        // Assert outer Blade markup and attribute merging
        $this->assertStringContainsString('custom-accordion-class', $html);
        $this->assertStringContainsString('rounded-2xl border', $html);

        // Assert Vue component tag and attributes
        $this->assertStringContainsString('<v-accordion', $html);
        $this->assertStringContainsString('is-active="true"', $html);

        // Assert shimmer placeholder inclusion
        $this->assertStringContainsString('shimmer', $html);

        // Assert slot templates with scoped bindings
        $this->assertStringContainsString('v-slot:header="{ toggle, isOpen }"', $html);
        $this->assertStringContainsString('Custom FAQ Question', $html);
        $this->assertStringContainsString('v-slot:content="{ isOpen }"', $html);
        $this->assertStringContainsString('Detailed answer to the question.', $html);

        // Assert x-template and Vue component registration script
        $this->assertStringContainsString('type="text/x-template"', $html);
        $this->assertStringContainsString('id="v-accordion-template"', $html);
        $this->assertStringContainsString("app.component('v-accordion'", $html);
        $this->assertStringContainsString('props:', $html);
        $this->assertStringContainsString("this.\$emit('toggle'", $html);
    }

    public function test_multiple_accordions_do_not_duplicate_scripts_due_to_pushonce(): void
    {
        $html = Blade::render(<<<'BLADE'
            <x-web::accordion :isActive="false" title="Question 1">
                Content 1
            </x-web::accordion>

            <x-web::accordion :isActive="true" title="Question 2">
                Content 2
            </x-web::accordion>

            <x-web::accordion :isActive="false" title="Question 3">
                Content 3
            </x-web::accordion>

            @stack('scripts')
        BLADE);

        // Three v-accordion elements rendered in template
        $this->assertSame(3, substr_count($html, '<v-accordion'));

        // But exactly ONE script template and registration in the scripts stack
        $this->assertSame(1, substr_count($html, 'id="v-accordion-template"'));
        $this->assertSame(1, substr_count($html, "app.component('v-accordion'"));
    }

    public function test_modal_and_confirm_components_render_vue_architecture(): void
    {
        $html = Blade::render(<<<'BLADE'
            <x-web::modal id="test-modal" size="lg" :isActive="false">
                <x-slot:toggle>
                    <button type="button">Open Modal</button>
                </x-slot:toggle>

                <x-slot:header>
                    <h2>Modal Header</h2>
                </x-slot:header>

                <x-slot:content>
                    <p>Modal body text.</p>
                </x-slot:content>

                <x-slot:footer>
                    <button type="button">Save</button>
                </x-slot:footer>
            </x-web::modal>

            <x-web::modal.confirm />

            @stack('scripts')
        BLADE);

        $this->assertStringContainsString('<v-modal', $html);
        $this->assertStringContainsString('id="test-modal"', $html);
        $this->assertStringContainsString('size="lg"', $html);
        $this->assertStringContainsString('v-slot:toggle', $html);
        $this->assertStringContainsString('v-slot:header', $html);
        $this->assertStringContainsString('v-slot:content', $html);
        $this->assertStringContainsString('v-slot:footer', $html);

        $this->assertStringContainsString('<v-modal-confirm', $html);

        // Templates & Vue scripts
        $this->assertStringContainsString('id="v-modal-template"', $html);
        $this->assertStringContainsString("app.component('v-modal'", $html);
        $this->assertStringContainsString('id="v-modal-confirm-template"', $html);
        $this->assertStringContainsString("app.component('v-modal-confirm'", $html);
        $this->assertStringContainsString("this.\$emitter.on('open-confirm-modal'", $html);
    }

    public function test_dropdown_component_renders_vue_architecture(): void
    {
        $html = Blade::render(<<<'BLADE'
            <x-web::dropdown position="bottom-right">
                <x-slot:toggle>
                    <button type="button">Options</button>
                </x-slot:toggle>

                <x-slot:menu>
                    <x-web::dropdown.menu.item href="/profile">Profile</x-web::dropdown.menu.item>
                    <x-web::dropdown.menu.item href="/settings">Settings</x-web::dropdown.menu.item>
                </x-slot:menu>
            </x-web::dropdown>

            @stack('scripts')
        BLADE);

        $this->assertStringContainsString('<v-dropdown', $html);
        $this->assertStringContainsString('position="bottom-right"', $html);
        $this->assertStringContainsString('v-slot:toggle', $html);
        $this->assertStringContainsString('#menu="{ isActive, positionStyles }"', $html);
        $this->assertStringContainsString('Profile', $html);

        // Templates & Vue script
        $this->assertStringContainsString('id="v-dropdown-template"', $html);
        $this->assertStringContainsString("app.component('v-dropdown'", $html);
    }

    public function test_tabs_component_renders_vue_architecture_and_shimmer(): void
    {
        $html = Blade::render(<<<'BLADE'
            <x-web::tabs position="center">
                <x-web::tabs.item title="Tab One" :isSelected="true">
                    <p>First tab content</p>
                </x-web::tabs.item>

                <x-web::tabs.item title="Tab Two" :isSelected="false">
                    <p>Second tab content</p>
                </x-web::tabs.item>
            </x-web::tabs>

            @stack('scripts')
        BLADE);

        $this->assertStringContainsString('<v-tabs', $html);
        $this->assertStringContainsString('position="center"', $html);
        $this->assertStringContainsString('shimmer', $html);

        $this->assertStringContainsString('<v-tab-item', $html);
        $this->assertStringContainsString('title="Tab One"', $html);
        $this->assertStringContainsString('is-selected="true"', $html);

        // Templates & Vue scripts
        $this->assertStringContainsString('id="v-tabs-template"', $html);
        $this->assertStringContainsString("app.component('v-tabs'", $html);
        $this->assertStringContainsString('id="v-tab-item-template"', $html);
        $this->assertStringContainsString("app.component('v-tab-item'", $html);
    }

    public function test_flash_group_renders_vue_architecture(): void
    {
        session()->flash('success', 'Test success message');
        session()->flash('warning', 'Test warning message');

        $html = Blade::render(<<<'BLADE'
            <x-web::flash-group />

            @stack('scripts')
        BLADE);

        $this->assertStringContainsString('<v-flash-group', $html);
        $this->assertStringContainsString('id="v-flash-group-template"', $html);
        $this->assertStringContainsString("app.component('v-flash-group'", $html);
        $this->assertStringContainsString('id="v-flash-item-template"', $html);
        $this->assertStringContainsString("app.component('v-flash-item'", $html);
        $this->assertStringContainsString('Test success message', $html);
        $this->assertStringContainsString('Test warning message', $html);
    }

    public function test_web_views_never_depend_directly_on_admin_presentation(): void
    {
        $viewFiles = glob(__DIR__ . '/../../../src/Web/Resources/views/**/*.blade.php') ?: [];
        $viewFiles = array_merge($viewFiles, glob(__DIR__ . '/../../../src/Web/Resources/views/**/**/*.blade.php') ?: []);
        $viewFiles = array_merge($viewFiles, glob(__DIR__ . '/../../../src/Web/Resources/views/*.blade.php') ?: []);
        $viewFiles = array_unique($viewFiles);

        $this->assertNotEmpty($viewFiles);

        foreach ($viewFiles as $file) {
            $content = file_get_contents($file);
            $this->assertStringNotContainsString('x-admin::', $content, "View file {$file} directly references Admin component namespace");
            $this->assertStringNotContainsString('admin/build', $content, "View file {$file} references Admin build directory");
        }
    }

    public function test_validation_state_links_control_to_announced_error(): void
    {
        Student::create([
            'university_card_number' => 'COMPONENT-ERROR-01',
            'password' => 'correct-password',
            'name' => 'Validation Student',
        ]);

        $this->from(route('campusfind_web.web.login'))->post(route('campusfind_web.web.login.store'), [
            'university_card_number' => 'COMPONENT-ERROR-01',
            'password' => 'wrong-password',
        ])->assertRedirect(route('campusfind_web.web.login'));

        $response = $this->get(route('campusfind_web.web.login'));

        $response->assertOk()
            ->assertSee('aria-invalid="true"', false)
            ->assertSee('aria-describedby="university_card_number-error"', false)
            ->assertSee('id="university_card_number-error"', false)
            ->assertSee('role="alert"', false);
    }

    public function test_public_and_authenticated_layouts_keep_navigation_and_direction_contracts(): void
    {
        $public = $this->get(route('campusfind_web.web.home', ['locale' => 'en']));
        $public->assertOk()
            ->assertSee('lang="en"', false)
            ->assertSee('dir="ltr"', false)
            ->assertSee('aria-controls="mobile-menu"', false)
            ->assertSee(trans('campusfind_web_web::app.web.accessibility.toggle_navigation'))
            ->assertSee('id="app"', false)
            ->assertSee('v-flash-group', false)
            ->assertSee('v-modal-confirm', false)
            ->assertSee('app.mount("#app")', false);

        $student = Student::create([
            'university_card_number' => 'COMPONENT-PORTAL-01',
            'password' => 'secret-password',
            'name' => 'Component Student',
        ]);

        $authenticated = $this->actingAs($student, 'student')->get(route('campusfind_web.web.account.dashboard', ['locale' => 'ar']));
        $authenticated->assertOk()
            ->assertSee('lang="ar"', false)
            ->assertSee('dir="rtl"', false)
            ->assertSee('Component Student');
    }

    public function test_media_images_and_drawer_components_render_vue_architecture(): void
    {
        $html = Blade::render(<<<'BLADE'
            <x-web::media.images name="evidence" :allowMultiple="true" />
            <x-web::drawer id="filter-drawer" position="left" width="350px">
                <x-slot:toggle>
                    <button type="button">Open Drawer</button>
                </x-slot:toggle>
                <x-slot:header>
                    <h3>Drawer Title</h3>
                </x-slot:header>
                <x-slot:content>
                    <p>Drawer Body</p>
                </x-slot:content>
            </x-web::drawer>
            @stack('scripts')
        BLADE);

        $this->assertStringContainsString('<v-media-images', $html);
        $this->assertStringContainsString('name="evidence"', $html);
        $this->assertStringContainsString('id="v-media-images-template"', $html);
        $this->assertStringContainsString("app.component('v-media-images'", $html);

        $this->assertStringContainsString('<v-drawer', $html);
        $this->assertStringContainsString('position="left"', $html);
        $this->assertStringContainsString('width="350px"', $html);
        $this->assertStringContainsString('id="v-drawer-template"', $html);
        $this->assertStringContainsString("app.component('v-drawer'", $html);
    }

    public function test_tags_attachments_and_flat_picker_components_render_vue_architecture(): void
    {
        $html = Blade::render(<<<'BLADE'
            <x-web::tags name="categories" :value="['Tag1', 'Tag2']" />
            <x-web::attachments name="files" :allowMultiple="true" />
            <x-web::flat-picker.date name="event_date" />
            <x-web::flat-picker.datetime name="event_datetime" />
            @stack('scripts')
        BLADE);

        $this->assertStringContainsString('<v-tags', $html);
        $this->assertStringContainsString('id="v-tags-template"', $html);
        $this->assertStringContainsString("app.component('v-tags'", $html);

        $this->assertStringContainsString('<v-attachments', $html);
        $this->assertStringContainsString('id="v-attachments-template"', $html);
        $this->assertStringContainsString("app.component('v-attachments'", $html);

        $this->assertStringContainsString('<v-date-picker', $html);
        $this->assertStringContainsString('id="v-date-picker-template"', $html);
        $this->assertStringContainsString("app.component('v-date-picker'", $html);

        $this->assertStringContainsString('<v-datetime-picker', $html);
        $this->assertStringContainsString('id="v-datetime-picker-template"', $html);
        $this->assertStringContainsString("app.component('v-datetime-picker'", $html);
    }

    public function test_components_example_view_renders_successfully(): void
    {
        $html = Blade::render(<<<'BLADE'
            <x-web::example />
            @stack('scripts')
        BLADE);

        $this->assertStringContainsString('Accordion Component', $html);
        $this->assertStringContainsString('Tabs Component', $html);
        $this->assertStringContainsString('Modal Component', $html);
        $this->assertStringContainsString('Drawer Component', $html);
        $this->assertStringContainsString('Media Images Component', $html);
        $this->assertStringContainsString('Tags Component', $html);
        $this->assertStringContainsString('Dropdown Component', $html);
        $this->assertStringContainsString('Form Components', $html);
        $this->assertStringContainsString('Badges and Buttons', $html);
    }

    public function test_anonymous_layout_renders_vue_architecture_and_event_hooks(): void
    {
        $html = Blade::render(<<<'BLADE'
            <x-web::layouts.anonymous>
                <x-slot:title>Anonymous Login Title</x-slot>
                <div class="auth-card">Login Form Content</div>
            </x-web::layouts.anonymous>
        BLADE);

        $this->assertStringContainsString('Anonymous Login Title', $html);
        $this->assertStringContainsString('id="app"', $html);
        $this->assertStringContainsString('v-flash-group', $html);
        $this->assertStringContainsString('v-modal-confirm', $html);
        $this->assertStringContainsString('Login Form Content', $html);
        $this->assertStringContainsString('app.mount("#app")', $html);
    }

    public function test_layout_tabs_component_renders_tab_links(): void
    {
        $html = Blade::render(<<<'BLADE'
            <x-web::layouts.tabs :tabs="[
                ['title' => 'Tab 1', 'url' => '/tab-1', 'active' => true, 'badge' => '5'],
                ['title' => 'Tab 2', 'url' => '/tab-2', 'active' => false],
            ]" />
        BLADE);

        $this->assertStringContainsString('class="tabs"', $html);
        $this->assertStringContainsString('Tab 1', $html);
        $this->assertStringContainsString('Tab 2', $html);
        $this->assertStringContainsString('/tab-1', $html);
        $this->assertStringContainsString('5', $html);
    }

    public function test_all_form_control_types_render_expected_markup_and_vue_fields(): void
    {
        $html = Blade::render(<<<'BLADE'
            <x-web::form action="/submit" method="PUT">
                <x-web::form.control-group>
                    <x-web::form.control-group.label :required="true">Text Input</x-web::form.control-group.label>
                    <x-web::form.control-group.control type="text" name="text_field" rules="required" />
                    <x-web::form.control-group.error name="text_field" />
                </x-web::form.control-group>

                <x-web::form.control-group>
                    <x-web::form.control-group.control type="email" name="email_field" rules="required|email" />
                    <x-web::form.control-group.control type="password" name="password_field" rules="required|min:8" />
                    <x-web::form.control-group.control type="number" name="number_field" />
                    <x-web::form.control-group.control type="time" name="time_field" />
                    <x-web::form.control-group.control type="price" name="price_field" />
                    <x-web::form.control-group.control type="file" name="file_field" />
                    <x-web::form.control-group.control type="textarea" name="textarea_field" />
                    <x-web::form.control-group.control type="checkbox" name="checkbox_field" value="agree">I Agree</x-web::form.control-group.control>
                    <x-web::form.control-group.control type="radio" name="radio_field" value="opt1">Option 1</x-web::form.control-group.control>
                    <x-web::form.control-group.control type="switch" name="switch_field" value="enabled">Toggle Feature</x-web::form.control-group.control>
                    <x-web::form.control-group.control type="select" name="select_field">
                        <option value="1">One</option>
                    </x-web::form.control-group.control>
                    <x-web::form.control-group.control type="multiselect" name="multi_field">
                        <option value="a">A</option>
                    </x-web::form.control-group.control>
                </x-web::form.control-group>
            </x-web::form>
            @stack('scripts')
        BLADE);

        $this->assertStringContainsString('method="POST"', $html);
        $this->assertStringContainsString('name="_method" value="PUT"', $html);
        $this->assertStringContainsString('name="_token"', $html);
        $this->assertStringContainsString(':initial-errors="{}"', $html);
        $this->assertStringContainsString('@invalid-submit="onInvalidSubmit"', $html);

        $this->assertStringContainsString('type="text"', $html);
        $this->assertStringContainsString('name="text_field"', $html);
        $this->assertStringContainsString('rules="required"', $html);

        $this->assertStringContainsString('type="email"', $html);
        $this->assertStringContainsString('type="password"', $html);
        $this->assertStringContainsString('type="number"', $html);
        $this->assertStringContainsString('type="time"', $html);
        $this->assertStringContainsString('type="file"', $html);
        $this->assertStringContainsString('<textarea', $html);
        $this->assertStringContainsString('type="checkbox"', $html);
        $this->assertStringContainsString('type="radio"', $html);
        $this->assertStringContainsString('I Agree', $html);
        $this->assertStringContainsString('Option 1', $html);
        $this->assertStringContainsString('Toggle Feature', $html);
        $this->assertStringContainsString('<select', $html);
        $this->assertStringContainsString('multiple', $html);

        $this->assertStringContainsString('v-error-message', $html);
        $this->assertStringContainsString('v-checked-handler', $html);
    }

    public function test_table_primitives_render_accessible_markup(): void
    {
        $html = Blade::render(<<<'BLADE'
            <x-web::table>
                <x-web::table.thead>
                    <x-web::table.thead.tr>
                        <x-web::table.th>ID</x-web::table.th>
                        <x-web::table.th>Title</x-web::table.th>
                    </x-web::table.thead.tr>
                </x-web::table.thead>
                <x-web::table.tbody>
                    <x-web::table.tbody.tr>
                        <x-web::table.td>1</x-web::table.td>
                        <x-web::table.td>Blue Wallet</x-web::table.td>
                    </x-web::table.tbody.tr>
                </x-web::table.tbody>
            </x-web::table>
        BLADE);

        $this->assertStringContainsString('<table', $html);
        $this->assertStringContainsString('<thead', $html);
        $this->assertStringContainsString('<tbody', $html);
        $this->assertStringContainsString('<tr', $html);
        $this->assertStringContainsString('<th', $html);
        $this->assertStringContainsString('<td', $html);
        $this->assertStringContainsString('Blue Wallet', $html);
    }

    public function test_navigation_and_layout_primitives_render_properly(): void
    {
        $html = Blade::render(<<<'BLADE'
            <x-web::breadcrumbs :items="[
                ['title' => 'Home', 'url' => '/'],
                ['title' => 'Items', 'url' => '/items'],
                ['title' => 'Item Details'],
            ]" />
            <x-web::container>
                <x-web::section title="Section Title" description="Section Subtitle">
                    <p>Section Content</p>
                </x-web::section>
            </x-web::container>
            <x-web::avatar name="Hosam Albushri" size="lg" />
        BLADE);

        $this->assertStringContainsString('aria-label="' . trans('campusfind_web_web::app.web.accessibility.breadcrumbs') . '"', $html);
        $this->assertStringContainsString('Home', $html);
        $this->assertStringContainsString('Item Details', $html);
        $this->assertStringContainsString('max-w-7xl', $html);
        $this->assertStringContainsString('Section Title', $html);
        $this->assertStringContainsString('Section Content', $html);
        $this->assertStringContainsString('HA', $html);
    }
}
