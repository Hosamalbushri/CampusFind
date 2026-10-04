<?php

use CampusFind\LostAndFound\Enums\FoundItemSubmissionChannel;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('lost_found_items', function (Blueprint $table): void {
            $table->unsignedInteger('logged_by_user_id')->nullable()->change();
            $table->string('submission_channel', 32)
                ->default(FoundItemSubmissionChannel::LEGACY_UNCERTAIN->value)
                ->after('logged_by_user_id');
            $table->unsignedBigInteger('reporter_student_id')->nullable()->after('submission_channel');
            $table->unsignedBigInteger('submitted_by_student_id')->nullable()->after('reporter_student_id');
            $table->unsignedInteger('intake_employee_user_id')->nullable()->after('submitted_by_student_id');

            $table->foreign('reporter_student_id')->references('id')->on('students')->restrictOnDelete();
            $table->foreign('submitted_by_student_id')->references('id')->on('students')->restrictOnDelete();
            $table->foreign('intake_employee_user_id')->references('id')->on('users')->restrictOnDelete();
            $table->index(['submission_channel', 'created_at'], 'lf_items_submission_channel_index');
            $table->index(['submitted_by_student_id', 'created_at'], 'lf_items_student_submissions_index');
        });

        Schema::table('lost_found_item_images', function (Blueprint $table): void {
            $table->unsignedInteger('created_by_user_id')->nullable()->change();
        });
    }

    public function down(): void
    {
        if (DB::table('lost_found_items')->whereNull('logged_by_user_id')->exists()
            || DB::table('lost_found_item_images')->whereNull('created_by_user_id')->exists()) {
            throw new RuntimeException('Phase 05 identity rollback would destroy anonymous or student intake attribution.');
        }

        Schema::table('lost_found_item_images', function (Blueprint $table): void {
            $table->unsignedInteger('created_by_user_id')->nullable(false)->change();
        });

        Schema::table('lost_found_items', function (Blueprint $table): void {
            $table->dropForeign(['reporter_student_id']);
            $table->dropForeign(['submitted_by_student_id']);
            $table->dropForeign(['intake_employee_user_id']);
            $table->dropIndex('lf_items_submission_channel_index');
            $table->dropIndex('lf_items_student_submissions_index');
            $table->dropColumn([
                'submission_channel',
                'reporter_student_id',
                'submitted_by_student_id',
                'intake_employee_user_id',
            ]);
            $table->unsignedInteger('logged_by_user_id')->nullable(false)->change();
        });
    }
};
