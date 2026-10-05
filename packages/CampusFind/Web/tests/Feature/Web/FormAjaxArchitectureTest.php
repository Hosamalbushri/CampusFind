<?php

namespace CampusFind\Web\Tests\Feature\Web;

use CampusFind\LostAndFound\Enums\ItemStatus;
use CampusFind\LostAndFound\Enums\ReportStatus;
use CampusFind\LostAndFound\Models\FoundItem;
use CampusFind\LostAndFound\Models\FoundReportResponse;
use CampusFind\LostAndFound\Models\LostFoundCategory;
use CampusFind\LostAndFound\Models\LostReport;
use CampusFind\LostAndFound\Services\PublicReference;
use CampusFind\Student\Models\Student;
use CampusFind\Web\Tests\TestCase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;

class FormAjaxArchitectureTest extends TestCase
{
    private Student $student;
    private LostFoundCategory $category;

    protected function setUp(): void
    {
        parent::setUp();

        Storage::fake('lost_found_private');
        Storage::fake('public');

        $this->student = Student::create([
            'university_card_number' => '441209999',
            'name'                   => 'سارة الأحمدي',
            'password'               => Hash::make('secret123'),
            'registration_number'    => 'REG-9999',
            'major'                  => 'Computer Science',
            'academic_level'         => 'Senior',
        ]);

        $this->category = LostFoundCategory::create([
            'code'        => 'electronics',
            'name'        => 'إلكترونيات',
            'is_active'   => true,
            'sort_order'  => 1,
        ]);
    }

    public function test_ajax_login_returns_json_response_with_redirect_url_on_success(): void
    {
        $response = $this->postJson(route('campusfind_web.web.login.store'), [
            'university_card_number' => '441209999',
            'password'               => 'secret123',
        ]);

        $response->assertStatus(200)
            ->assertJsonStructure([
                'message',
                'redirect_url',
            ]);

        $this->assertAuthenticatedAs($this->student, 'student');
    }

    public function test_ajax_login_returns_422_with_field_errors_on_invalid_credentials(): void
    {
        $response = $this->postJson(route('campusfind_web.web.login.store'), [
            'university_card_number' => '441209999',
            'password'               => 'wrong-password',
        ]);

        $response->assertStatus(422)
            ->assertJsonStructure([
                'message',
                'errors' => [
                    'university_card_number',
                ],
            ]);

        $this->assertGuest('student');
    }

    public function test_ajax_lost_report_creation_requires_authentication(): void
    {
        $response = $this->postJson(route('campusfind_web.web.reports.lost.store'), [
            'category_id' => $this->category->id,
            'title'       => 'سماعات بلوتوث مفقودة',
        ]);

        $response->assertStatus(401)
            ->assertJsonStructure([
                'message',
                'redirect_url',
            ]);
    }

    public function test_ajax_lost_report_creation_validates_required_fields_and_returns_422(): void
    {
        $response = $this->actingAs($this->student, 'student')
            ->postJson(route('campusfind_web.web.reports.lost.store'), []);

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['category_id', 'title']);
    }

    public function test_ajax_lost_report_creation_succeeds_and_returns_201(): void
    {
        $response = $this->actingAs($this->student, 'student')
            ->postJson(route('campusfind_web.web.reports.lost.store'), [
                'category_id'         => $this->category->id,
                'title'               => 'محفظة سوداء ضائعة',
                'lost_location'       => 'مبنى 5 - القاعة 201',
                'lost_at'             => '2026-10-04 10:00:00',
                'public_description'  => 'محفظة جلدية سوداء',
                'private_description' => 'تحتوي على بطاقة الصراف باسم سارة',
            ]);

        $response->assertStatus(201)
            ->assertJsonStructure([
                'message',
                'redirect_url',
                'data' => [
                    'id',
                    'reference',
                ],
            ]);

        $this->assertDatabaseHas('lost_found_reports', [
            'student_id'  => $this->student->id,
            'category_id' => $this->category->id,
            'title'       => 'محفظة سوداء ضائعة',
        ]);
    }

    public function test_ajax_lost_report_creation_with_image_stores_image_file_and_database_record(): void
    {
        $file = \Illuminate\Http\UploadedFile::fake()->image('lost-item.jpg', 600, 600);

        $response = $this->actingAs($this->student, 'student')
            ->postJson(route('campusfind_web.web.reports.lost.store'), [
                'category_id'         => $this->category->id,
                'title'               => 'حقيبة ظهر زرقاء',
                'lost_location'       => 'المكتبة المركزية',
                'lost_at'             => '2026-10-04 12:00:00',
                'public_description'  => 'حقيبة تحتوي على كتب دراسية',
                'private_description' => 'علامة مميزة على السحاب',
                'image'               => $file,
            ]);

        $response->assertStatus(201);

        $reportId = $response->json('data.id');
        $reference = $response->json('data.reference');
        $this->assertNotNull($reportId);
        $this->assertNotNull($reference);

        $this->assertDatabaseHas('lost_found_report_images', [
            'lost_report_id' => $reportId,
        ]);

        $imageResponse = $this->get(route('campusfind_web.web.reports.lost.image', ['reference' => $reference]));
        $imageResponse->assertStatus(200);
    }

    public function test_ajax_found_report_creation_returns_201_on_success(): void
    {
        $response = $this->actingAs($this->student, 'student')
            ->postJson(route('campusfind_web.web.reports.found.store'), [
                'category_id'      => $this->category->id,
                'title'            => 'نظارة شمسية سوداء',
                'found_location'   => 'المكتبة المركزية - الدور الثاني',
                'found_at'         => '2026-10-04 11:30:00',
                'dropoff_location' => 'library_desk',
                'description'      => 'نظارة شمسية ماركة رايبان',
            ]);

        $response->assertStatus(201)
            ->assertJsonStructure([
                'message',
                'redirect_url',
            ]);

        $this->assertDatabaseHas('lost_found_items', [
            'category_id'    => $this->category->id,
            'title'          => 'نظارة شمسية سوداء',
            'found_location' => 'المكتبة المركزية - الدور الثاني',
        ]);
    }

    public function test_ajax_claim_submission_succeeds_and_prevents_duplicate(): void
    {
        $foundItem = FoundItem::create([
            'category_id'      => $this->category->id,
            'title'            => 'حقيبة كمبيوتر رمادية',
            'found_location'   => 'كلية العلوم',
            'found_at'         => now(),
            'public_reference' => 'FI-2026-BAG01',
            'status'           => ItemStatus::IN_CUSTODY,
        ]);

        $response = $this->actingAs($this->student, 'student')
            ->postJson(route('campusfind_web.web.items.claim', $foundItem->public_reference), [
                'statement' => 'هذه حقيبتي وبها علامة زرقاء على المقبض وتحتوي على حاسوب لينوفو.',
            ]);

        $response->assertStatus(201)
            ->assertJsonStructure([
                'message',
                'redirect_url',
            ]);

        $this->assertDatabaseHas('lost_found_claims', [
            'found_item_id'       => $foundItem->id,
            'claimant_student_id' => $this->student->id,
        ]);

        // Second attempt returns 422 duplicate error
        $duplicateResponse = $this->actingAs($this->student, 'student')
            ->postJson(route('campusfind_web.web.items.claim', $foundItem->public_reference), [
                'statement' => 'محاولة مطالبة مكررة',
            ]);

        $duplicateResponse->assertStatus(422)
            ->assertJsonStructure([
                'message',
                'errors' => [
                    'statement',
                ],
            ]);
    }

    public function test_ajax_lost_report_response_flow_and_cancellation(): void
    {
        $owner = Student::create([
            'university_card_number' => '441208888',
            'name'                   => 'أحمد المنصور',
            'password'               => Hash::make('password'),
            'registration_number'    => 'REG-8888',
            'major'                  => 'Engineering',
            'academic_level'         => 'Junior',
        ]);

        $lostReport = LostReport::create([
            'student_id'           => $owner->id,
            'category_id'          => $this->category->id,
            'title'                => 'خاتم فضي ضائع',
            'lost_location'        => 'المسجد الجامعي',
            'lost_at'              => now()->subDay(),
            'public_reference'     => 'LR-2026-RING01',
            'public_reference_key' => PublicReference::normalize('LR-2026-RING01'),
            'status'               => ReportStatus::ACTIVE,
        ]);

        // Submit response via AJAX
        $response = $this->actingAs($this->student, 'student')
            ->postJson(route('campusfind_web.web.lost-reports.responses.store', $lostReport->public_reference), [
                'found_location'   => 'بوابة المسجد الشرقية',
                'found_at'         => now()->subHours(2)->format('Y-m-d H:i:s'),
                'dropoff_location' => 'main_security',
                'message'          => 'وجدت الخاتم وسلمته لمكتب الأمن الرئيسي',
            ]);

        $response->assertStatus(200)
            ->assertJsonStructure([
                'message',
                'redirect_url',
            ]);

        $this->assertDatabaseHas('lost_found_report_responses', [
            'lost_report_id'       => $lostReport->id,
            'responder_student_id' => $this->student->id,
            'found_location'       => 'بوابة المسجد الشرقية',
        ]);

        // Cancel response via AJAX
        $cancelResponse = $this->actingAs($this->student, 'student')
            ->postJson(route('campusfind_web.web.lost-reports.responses.cancel', $lostReport->public_reference));

        $cancelResponse->assertStatus(200)
            ->assertJsonStructure([
                'message',
            ]);
    }
}
