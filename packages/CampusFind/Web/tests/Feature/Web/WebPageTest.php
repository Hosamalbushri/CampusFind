<?php

namespace CampusFind\Web\Tests\Feature\Web;

use CampusFind\LostAndFound\Enums\FoundItemSubmissionChannel;
use CampusFind\LostAndFound\Enums\ItemStatus;
use CampusFind\LostAndFound\Models\FoundItem;
use CampusFind\LostAndFound\Models\LostFoundCategory;
use CampusFind\Student\Models\Student;
use CampusFind\Web\Tests\TestCase;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class WebPageTest extends TestCase
{
    private int $userId;

    protected function setUp(): void
    {
        parent::setUp();

        if (! DB::table('roles')->where('id', 1)->exists()) {
            DB::table('roles')->insert([
                'id' => 1,
                'name' => 'Administrator',
                'permission_type' => 'all',
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }

        $this->userId = DB::table('users')->insertGetId([
            'name' => 'Test Staff',
            'email' => 'staff@example.com',
            'password' => bcrypt('secret'),
            'role_id' => 1,
            'status' => 1,
            'created_at' => now(),
            'updated_at' => now(),
        ]);
    }

    public function test_web_home_page_renders_successfully(): void
    {
        $response = $this->get(route('campusfind_web.web.home'));
        $response->assertStatus(200);
        $response->assertSee(trans('campusfind_web_web::app.web.title'));
    }

    public function test_web_items_catalog_renders_successfully(): void
    {
        $category = LostFoundCategory::create([
            'code' => 'electronics',
            'is_active' => true,
            'sort_order' => 1,
        ]);

        $item = FoundItem::create([
            'public_reference' => 'LF-2026-TEST01',
            'category_id' => $category->id,
            'logged_by_user_id' => $this->userId,
            'title' => 'Blue Backpack',
            'public_description' => 'A dark blue backpack found in the library.',
            'found_location' => 'Main Library, 2nd Floor',
            'found_at' => Carbon::now()->subDay(),
            'status' => ItemStatus::IN_CUSTODY,
            'custody_state' => 'received',
            'custody_location' => 'Storage Room A',
        ]);

        $response = $this->get(route('campusfind_web.web.items.index'));
        $response->assertStatus(200);
        $response->assertSee('Blue Backpack');
        $response->assertSee('LF-2026-TEST01');

        // Test filtering by query
        $searchResponse = $this->get(route('campusfind_web.web.items.index', ['query' => 'Backpack']));
        $searchResponse->assertStatus(200);
        $searchResponse->assertSee('Blue Backpack');

        // Test filtering with no match
        $emptyResponse = $this->get(route('campusfind_web.web.items.index', ['query' => 'NonExistentItemXYZ']));
        $emptyResponse->assertStatus(200);
        $emptyResponse->assertDontSee('Blue Backpack');
    }

    public function test_web_item_show_page_renders_with_valid_reference(): void
    {
        $category = LostFoundCategory::create([
            'code' => 'keys',
            'is_active' => true,
            'sort_order' => 2,
        ]);

        $item = FoundItem::create([
            'public_reference' => 'LF-2026-KEY001',
            'category_id' => $category->id,
            'logged_by_user_id' => $this->userId,
            'title' => 'Set of House Keys',
            'public_description' => 'Keys with a red lanyard.',
            'found_location' => 'Cafeteria',
            'found_at' => Carbon::now()->subHours(5),
            'status' => ItemStatus::REPORTED,
            'custody_state' => 'reported',
            'custody_location' => 'Front Desk',
        ]);

        $response = $this->get(route('campusfind_web.web.items.show', ['reference' => 'LF-2026-KEY001']));
        $response->assertStatus(200);
        $response->assertSee('Set of House Keys');
        $response->assertSee('LF-2026-KEY001');
    }

    public function test_web_item_show_page_aborts_404_for_unknown_reference(): void
    {
        $response = $this->get(route('campusfind_web.web.items.show', ['reference' => 'LF-NONEXISTENT']));
        $response->assertStatus(404);
    }

    public function test_web_login_page_renders_successfully(): void
    {
        $response = $this->get(route('campusfind_web.web.login'));
        $response->assertStatus(200);
        $response->assertSee(trans('campusfind_web_web::app.web.auth.card_number'));
    }

    public function test_student_login_with_local_credentials_succeeds(): void
    {
        $student = Student::create([
            'university_card_number' => '4410998877',
            'password' => 'secretPass123',
            'name' => 'Test Student User',
            'registration_number' => 'REG-9988',
            'major' => 'Software Engineering',
            'academic_level' => '4',
        ]);

        $response = $this->post(route('campusfind_web.web.login.store'), [
            'university_card_number' => '4410998877',
            'password' => 'secretPass123',
        ]);

        $response->assertRedirect(route('campusfind_web.web.account.dashboard'));
        $this->assertTrue(Auth::guard('student')->check());
        $this->assertSame($student->id, Auth::guard('student')->id());
    }

    public function test_student_login_never_redirects_to_admin_routes(): void
    {
        $student = Student::create([
            'university_card_number' => '4410998899',
            'password' => 'secretPass123',
            'name' => 'No Admin Student',
        ]);

        // Simulate stale or injected admin intended URL
        session(['url.intended' => 'http://localhost/admin/dashboard']);

        $response = $this->post(route('campusfind_web.web.login.store'), [
            'university_card_number' => '4410998899',
            'password' => 'secretPass123',
        ]);

        // Must redirect to student dashboard, NOT to admin
        $response->assertRedirect(route('campusfind_web.web.account.dashboard'));
        $this->assertTrue(Auth::guard('student')->check());
    }

    public function test_student_login_with_invalid_credentials_fails(): void
    {
        $response = $this->from(route('campusfind_web.web.login'))->post(route('campusfind_web.web.login.store'), [
            'university_card_number' => 'INVALID_CARD',
            'password' => 'wrongpass',
        ]);

        $response->assertRedirect(route('campusfind_web.web.login'));
        $this->assertFalse(Auth::guard('student')->check());
    }

    public function test_student_logout_clears_session_and_redirects_home(): void
    {
        $student = Student::create([
            'university_card_number' => '4410112233',
            'password' => 'secretPass123',
            'name' => 'Logout Student',
        ]);

        Auth::guard('student')->login($student);
        $this->assertTrue(Auth::guard('student')->check());

        $response = $this->post(route('campusfind_web.web.logout'));
        $response->assertRedirect(route('campusfind_web.web.home'));
        $this->assertFalse(Auth::guard('student')->check());
    }

    public function test_student_dashboard_requires_authentication(): void
    {
        $response = $this->get(route('campusfind_web.web.account.dashboard'));
        $response->assertRedirect(route('campusfind_web.web.login'));
    }

    public function test_authenticated_student_can_access_dashboard(): void
    {
        $student = Student::create([
            'university_card_number' => '4410554433',
            'password' => 'secretPass123',
            'name' => 'Dashboard Student',
            'major' => 'Computer Science',
        ]);

        $response = $this->actingAs($student, 'student')->get(route('campusfind_web.web.account.dashboard'));
        $response->assertStatus(200);
        $response->assertSee('Dashboard Student');
        $response->assertSee('Computer Science');
    }

    public function test_web_static_pages_render_successfully(): void
    {
        $pages = ['about', 'how-it-works', 'faq', 'contact', 'features'];

        foreach ($pages as $page) {
            $response = $this->get(route('campusfind_web.web.pages.show', ['page' => $page]));
            $response->assertStatus(200);
        }
    }

    public function test_web_unknown_static_page_returns_404(): void
    {
        $response = $this->get(route('campusfind_web.web.pages.show', ['page' => 'non-existent-page-xyz']));
        $response->assertStatus(404);
    }

    public function test_locale_switcher_works_via_query_parameter(): void
    {
        $response = $this->get(route('campusfind_web.web.home', ['locale' => 'ar']));
        $response->assertStatus(200);
        $this->assertSame('ar', app()->getLocale());

        $enResponse = $this->get(route('campusfind_web.web.home', ['locale' => 'en']));
        $enResponse->assertStatus(200);
        $this->assertSame('en', app()->getLocale());
    }

    public function test_seven_locales_translation_parity(): void
    {
        $locales = ['ar', 'en', 'es', 'fa', 'pt_BR', 'tr', 'vi'];
        $baseEn = require __DIR__.'/../../../src/Web/Resources/lang/en/app.php';
        $enKeys = array_keys($baseEn['web']);
        $authKeys = array_keys($baseEn['web']['auth']);
        $reportsKeys = array_keys($baseEn['web']['reports']);
        $browseKeys = array_keys($baseEn['web']['browse']);
        $foundResponseKeys = array_keys($baseEn['web']['found_response']);
        $heroKeys = array_keys($baseEn['web']['hero']);
        $highlightKeys = array_keys($baseEn['web']['highlights']);
        $footerKeys = array_keys($baseEn['web']['footer']);
        $statsKeys = array_keys($baseEn['web']['stats']);

        foreach ($locales as $locale) {
            $filePath = __DIR__."/../../../src/Web/Resources/lang/{$locale}/app.php";
            $this->assertFileExists($filePath, "Translation file missing for locale: {$locale}");

            $data = require $filePath;
            $this->assertIsArray($data);
            $this->assertArrayHasKey('web', $data);

            $this->assertSame($enKeys, array_keys($data['web']), "Mismatch in top-level web keys for locale: {$locale}");
            $this->assertSame($authKeys, array_keys($data['web']['auth']), "Mismatch in auth keys for locale: {$locale}");
            $this->assertSame($reportsKeys, array_keys($data['web']['reports']), "Mismatch in reports keys for locale: {$locale}");
            $this->assertSame($browseKeys, array_keys($data['web']['browse']), "Mismatch in browse keys for locale: {$locale}");
            $this->assertSame($foundResponseKeys, array_keys($data['web']['found_response']), "Mismatch in found response keys for locale: {$locale}");
            $this->assertSame($heroKeys, array_keys($data['web']['hero']), "Mismatch in hero keys for locale: {$locale}");
            $this->assertSame($highlightKeys, array_keys($data['web']['highlights']), "Mismatch in highlight keys for locale: {$locale}");
            $this->assertSame($footerKeys, array_keys($data['web']['footer']), "Mismatch in footer keys for locale: {$locale}");
            $this->assertSame($statsKeys, array_keys($data['web']['stats']), "Mismatch in stats keys for locale: {$locale}");
        }
    }

    public function test_lost_report_page_renders_for_guest_with_login_cta(): void
    {
        $response = $this->get(route('campusfind_web.web.reports.lost'));
        $response->assertStatus(200);
        $response->assertSee(trans('campusfind_web_web::app.web.reports.report_lost_title'));
        $response->assertSee(trans('campusfind_web_web::app.web.reports.login_required_title'));
        $response->assertSee(route('campusfind_web.web.login'));
    }

    public function test_guest_submitting_lost_report_redirects_to_login(): void
    {
        $category = LostFoundCategory::create([
            'code' => 'books',
            'is_active' => true,
            'sort_order' => 10,
        ]);

        $response = $this->post(route('campusfind_web.web.reports.lost.store'), [
            'category_id' => $category->id,
            'title' => 'Organic Chemistry Textbook',
        ]);

        $response->assertRedirect(route('campusfind_web.web.login'));
    }

    public function test_authenticated_student_can_submit_lost_report(): void
    {
        $student = Student::create([
            'university_card_number' => '4410776655',
            'password' => 'studentSecret',
            'name' => 'Reporting Student',
        ]);

        $category = LostFoundCategory::create([
            'code' => 'laptops',
            'is_active' => true,
            'sort_order' => 5,
        ]);

        $response = $this->actingAs($student, 'student')
            ->get(route('campusfind_web.web.reports.lost'));
        $response->assertStatus(200);
        $response->assertDontSee(trans('campusfind_web_web::app.web.reports.login_required_title'));

        $postResponse = $this->actingAs($student, 'student')
            ->post(route('campusfind_web.web.reports.lost.store'), [
                'category_id' => $category->id,
                'title' => 'ThinkPad X1 Carbon',
                'lost_location' => 'Engineering Lab 3',
                'public_description' => 'Matte black with sticker on lid',
                'private_description' => 'Serial number: TP-998811',
            ]);

        $postResponse->assertRedirect(route('campusfind_web.web.account.dashboard'));

        $this->assertDatabaseHas('lost_found_reports', [
            'student_id' => $student->id,
            'title' => 'ThinkPad X1 Carbon',
            'status' => 'draft',
        ]);

        $dashboardResponse = $this->actingAs($student, 'student')
            ->get(route('campusfind_web.web.account.dashboard'));
        $dashboardResponse->assertStatus(200);
        $dashboardResponse->assertSee('ThinkPad X1 Carbon');
    }

    public function test_found_report_page_renders_successfully(): void
    {
        $response = $this->get(route('campusfind_web.web.reports.found'));
        $response->assertStatus(200);
        $response->assertSee(trans('campusfind_web_web::app.web.reports.report_found_title'));
        $response->assertSee(trans('campusfind_web_web::app.web.reports.handover_title'));
    }

    public function test_found_report_submission_succeeds_and_flashes_success(): void
    {
        $category = LostFoundCategory::create([
            'code' => 'accessories',
            'is_active' => true,
            'sort_order' => 8,
        ]);

        $response = $this->from(route('campusfind_web.web.reports.found'))
            ->post(route('campusfind_web.web.reports.found.store'), [
                'category_id' => $category->id,
                'title' => 'Prescription Glasses',
                'found_location' => 'Cafeteria Table 4',
                'description' => 'Black frame rectangular glasses.',
                'dropoff_location' => 'Main Security Office',
            ]);

        $response->assertRedirect(route('campusfind_web.web.reports.found'));
        $response->assertSessionHas('success');

        $this->assertDatabaseHas('lost_found_items', [
            'category_id' => $category->id,
            'title' => 'Prescription Glasses',
            'found_location' => 'Cafeteria Table 4',
            'public_description' => 'Black frame rectangular glasses.',
            'status' => 'draft',
            'logged_by_user_id' => null,
            'submission_channel' => FoundItemSubmissionChannel::PUBLIC_ANONYMOUS->value,
        ]);
        $this->assertDatabaseMissing('users', ['email' => 'intake@campusfind.system']);
    }

    public function test_lost_report_accepts_category_code_string_and_resolves_to_integer_id(): void
    {
        $student = Student::create([
            'university_card_number' => '4410991122',
            'password' => 'studentSecret',
            'name' => 'String Category Student',
        ]);

        $category = LostFoundCategory::create([
            'code' => 'smartphones',
            'is_active' => true,
            'sort_order' => 12,
        ]);

        $postResponse = $this->actingAs($student, 'student')
            ->post(route('campusfind_web.web.reports.lost.store'), [
                'category_id' => 'smartphones',
                'title' => 'iPhone 15 Pro Max',
            ]);

        $postResponse->assertRedirect(route('campusfind_web.web.account.dashboard'));

        $dashboardResponse = $this->actingAs($student, 'student')
            ->get(route('campusfind_web.web.account.dashboard'));
        $dashboardResponse->assertStatus(200);
        $dashboardResponse->assertSee('iPhone 15 Pro Max');
    }

    public function test_guest_claiming_item_redirects_to_login(): void
    {
        $response = $this->post(route('campusfind_web.web.items.claim', 'LF-NONEXISTENT'), [
            'statement' => 'This is my item',
        ]);

        $response->assertRedirect(route('campusfind_web.web.login'));
    }

    public function test_authenticated_student_can_claim_found_item_and_view_it_in_dashboard(): void
    {
        $student = Student::create([
            'university_card_number' => '4410555666',
            'password' => 'studentSecret',
            'name' => 'Claimant Student',
        ]);

        $category = LostFoundCategory::create([
            'code' => 'tablets',
            'is_active' => true,
            'sort_order' => 15,
        ]);

        $foundItem = FoundItem::create([
            'public_reference' => 'LF-2026-TAB99',
            'category_id' => $category->id,
            'logged_by_user_id' => $this->userId,
            'title' => 'iPad Pro 11-inch',
            'public_description' => 'Silver tablet in blue case.',
            'found_location' => 'Library 3rd Floor',
            'found_at' => Carbon::now()->subHours(2),
            'status' => ItemStatus::IN_CUSTODY,
            'custody_state' => 'received',
            'custody_location' => 'Vault 2',
        ]);

        // Student visits item show page
        $showResponse = $this->actingAs($student, 'student')
            ->get(route('campusfind_web.web.items.show', 'LF-2026-TAB99'));
        $showResponse->assertStatus(200);
        $showResponse->assertSee('iPad Pro 11-inch');

        // Student submits claim
        $claimResponse = $this->actingAs($student, 'student')
            ->post(route('campusfind_web.web.items.claim', 'LF-2026-TAB99'), [
                'statement' => 'The iPad has a sticker of Linux Tux on the bottom right and the serial ends in 9982.',
            ]);

        $claimResponse->assertRedirect(route('campusfind_web.web.account.dashboard'));
        $claimResponse->assertSessionHas('success');

        // Verify in database
        $this->assertDatabaseHas('lost_found_claims', [
            'found_item_id' => $foundItem->id,
            'claimant_student_id' => $student->id,
        ]);

        // Verify student sees claim in dashboard
        $dashboardResponse = $this->actingAs($student, 'student')
            ->get(route('campusfind_web.web.account.dashboard'));
        $dashboardResponse->assertStatus(200);
        $dashboardResponse->assertSee('iPad Pro 11-inch');
        $dashboardResponse->assertSee('LF-2026-TAB99');

        // Verify duplicate claim is rejected
        $dupResponse = $this->actingAs($student, 'student')
            ->post(route('campusfind_web.web.items.claim', 'LF-2026-TAB99'), [
                'statement' => 'Attempting duplicate claim.',
            ]);
        $dupResponse->assertSessionHas('error');
    }
}
