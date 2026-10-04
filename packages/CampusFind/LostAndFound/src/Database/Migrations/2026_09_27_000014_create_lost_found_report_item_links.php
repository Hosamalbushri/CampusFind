<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('lost_found_potential_matches', function (Blueprint $table): void {
            $table->id();
            $table->unsignedBigInteger('lost_report_id');
            $table->unsignedBigInteger('found_item_id');
            $table->unsignedInteger('proposed_by_user_id');
            $table->dateTime('proposed_at');
            $table->timestamps();

            $table->foreign('lost_report_id')->references('id')->on('lost_found_reports')->restrictOnDelete();
            $table->foreign('found_item_id')->references('id')->on('lost_found_items')->restrictOnDelete();
            $table->foreign('proposed_by_user_id')->references('id')->on('users')->restrictOnDelete();

            $table->unique(['lost_report_id', 'found_item_id'], 'lf_potential_report_item_unique');
            $table->unique(['id', 'lost_report_id', 'found_item_id'], 'lf_potential_identity_unique');
            $table->index(['found_item_id', 'proposed_at'], 'lf_potential_item_time_index');
        });

        Schema::create('lost_found_verified_links', function (Blueprint $table): void {
            $table->id();
            $table->unsignedBigInteger('potential_match_id');
            $table->unsignedBigInteger('lost_report_id');
            $table->unsignedBigInteger('found_item_id');
            $table->unsignedInteger('verified_by_user_id');
            $table->text('verification_evidence');
            $table->dateTime('verified_at');
            $table->timestamps();

            $table->foreign(
                ['potential_match_id', 'lost_report_id', 'found_item_id'],
                'lf_verified_potential_foreign',
            )->references(['id', 'lost_report_id', 'found_item_id'])
                ->on('lost_found_potential_matches')
                ->restrictOnDelete();
            $table->foreign('verified_by_user_id')->references('id')->on('users')->restrictOnDelete();

            $table->unique('potential_match_id', 'lf_verified_potential_unique');
            $table->unique('lost_report_id', 'lf_verified_report_unique');
            $table->unique('found_item_id', 'lf_verified_item_unique');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('lost_found_verified_links');
        Schema::dropIfExists('lost_found_potential_matches');
    }
};
