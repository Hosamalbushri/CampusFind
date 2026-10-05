-- =============================================================================
-- CampusFind Database Schema (مخطط قاعدة بيانات منصة CampusFind)
-- Generated at: 2026-10-04 14:26:58
-- DBMS: MySQL / MariaDB (InnoDB, utf8mb4_unicode_ci)
-- Total Tables: 35
-- =============================================================================

SET FOREIGN_KEY_CHECKS = 0;

-- -----------------------------------------------------------------------------
-- SECTION: 1. نظام الطلاب (Student Module)
-- -----------------------------------------------------------------------------

-- Table: students
DROP TABLE IF EXISTS `students`;
CREATE TABLE `students` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `university_card_number` varchar(255) NOT NULL,
  `password` varchar(255) NOT NULL,
  `name` varchar(255) NOT NULL,
  `registration_number` varchar(255) DEFAULT NULL,
  `major` varchar(255) DEFAULT NULL,
  `academic_level` varchar(255) DEFAULT NULL,
  `profile_image` varchar(255) DEFAULT NULL,
  `remember_token` varchar(100) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `students_university_card_number_unique` (`university_card_number`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- -----------------------------------------------------------------------------
-- SECTION: 2. نظام المفقودات والموجودات (Lost & Found Core)
-- -----------------------------------------------------------------------------

-- Table: lost_found_categories
DROP TABLE IF EXISTS `lost_found_categories`;
CREATE TABLE `lost_found_categories` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `code` varchar(64) NOT NULL,
  `is_active` tinyint(1) NOT NULL DEFAULT 1,
  `sort_order` int(11) NOT NULL DEFAULT 0,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `lost_found_categories_code_unique` (`code`),
  KEY `lost_found_categories_is_active_sort_order_index` (`is_active`,`sort_order`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Table: lost_found_items
DROP TABLE IF EXISTS `lost_found_items`;
CREATE TABLE `lost_found_items` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `public_reference` varchar(64) NOT NULL,
  `public_reference_key` varchar(64) NOT NULL,
  `category_id` bigint(20) unsigned DEFAULT NULL,
  `logged_by_user_id` int(10) unsigned DEFAULT NULL,
  `submission_channel` varchar(32) NOT NULL DEFAULT 'legacy_uncertain',
  `reporter_student_id` bigint(20) unsigned DEFAULT NULL,
  `submitted_by_student_id` bigint(20) unsigned DEFAULT NULL,
  `intake_employee_user_id` int(10) unsigned DEFAULT NULL,
  `status` varchar(32) NOT NULL DEFAULT 'draft',
  `title` varchar(160) NOT NULL,
  `public_description` text DEFAULT NULL,
  `found_location` varchar(255) DEFAULT NULL,
  `found_at` datetime DEFAULT NULL,
  `reported_at` datetime DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `approved_claim_id` bigint(20) unsigned DEFAULT NULL,
  `current_custodian_user_id` int(10) unsigned DEFAULT NULL,
  `current_storage_location` varchar(255) DEFAULT NULL,
  `custody_started_at` datetime DEFAULT NULL,
  `custody_changed_at` datetime DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `lost_found_items_public_reference_key_unique` (`public_reference_key`),
  UNIQUE KEY `lost_found_items_approved_claim_id_unique` (`approved_claim_id`),
  KEY `lost_found_items_status_category_id_found_at_index` (`status`,`category_id`,`found_at`),
  KEY `lost_found_items_status_found_at_index` (`status`,`found_at`),
  KEY `lost_found_items_logged_by_user_id_index` (`logged_by_user_id`),
  KEY `lf_items_current_custodian_index` (`current_custodian_user_id`),
  KEY `lost_found_items_reporter_student_id_foreign` (`reporter_student_id`),
  KEY `lost_found_items_intake_employee_user_id_foreign` (`intake_employee_user_id`),
  KEY `lf_items_submission_channel_index` (`submission_channel`,`created_at`),
  KEY `lf_items_student_submissions_index` (`submitted_by_student_id`,`created_at`),
  KEY `lf_items_match_retrieval_index` (`category_id`,`status`,`found_at`,`id`),
  CONSTRAINT `lost_found_items_approved_claim_id_foreign` FOREIGN KEY (`approved_claim_id`) REFERENCES `lost_found_claims` (`id`),
  CONSTRAINT `lost_found_items_category_id_foreign` FOREIGN KEY (`category_id`) REFERENCES `lost_found_categories` (`id`),
  CONSTRAINT `lost_found_items_current_custodian_user_id_foreign` FOREIGN KEY (`current_custodian_user_id`) REFERENCES `users` (`id`),
  CONSTRAINT `lost_found_items_intake_employee_user_id_foreign` FOREIGN KEY (`intake_employee_user_id`) REFERENCES `users` (`id`),
  CONSTRAINT `lost_found_items_logged_by_user_id_foreign` FOREIGN KEY (`logged_by_user_id`) REFERENCES `users` (`id`),
  CONSTRAINT `lost_found_items_reporter_student_id_foreign` FOREIGN KEY (`reporter_student_id`) REFERENCES `students` (`id`),
  CONSTRAINT `lost_found_items_submitted_by_student_id_foreign` FOREIGN KEY (`submitted_by_student_id`) REFERENCES `students` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Table: lost_found_item_private_details
DROP TABLE IF EXISTS `lost_found_item_private_details`;
CREATE TABLE `lost_found_item_private_details` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `found_item_id` bigint(20) unsigned NOT NULL,
  `identifying_details` text DEFAULT NULL,
  `serial_fragment` text DEFAULT NULL,
  `staff_notes` text DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `lost_found_item_private_details_found_item_id_unique` (`found_item_id`),
  CONSTRAINT `lost_found_item_private_details_found_item_id_foreign` FOREIGN KEY (`found_item_id`) REFERENCES `lost_found_items` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Table: lost_found_item_images
DROP TABLE IF EXISTS `lost_found_item_images`;
CREATE TABLE `lost_found_item_images` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `found_item_id` bigint(20) unsigned NOT NULL,
  `created_by_user_id` int(10) unsigned DEFAULT NULL,
  `visibility` varchar(32) NOT NULL,
  `storage_key` varchar(255) NOT NULL,
  `mime_type` varchar(64) NOT NULL,
  `byte_size` bigint(20) unsigned NOT NULL,
  `sort_order` int(10) unsigned NOT NULL DEFAULT 0,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `lost_found_item_images_storage_key_unique` (`storage_key`),
  KEY `lf_item_images_lookup_index` (`found_item_id`,`visibility`,`sort_order`,`id`),
  KEY `lost_found_item_images_created_by_user_id_index` (`created_by_user_id`),
  CONSTRAINT `lost_found_item_images_created_by_user_id_foreign` FOREIGN KEY (`created_by_user_id`) REFERENCES `users` (`id`),
  CONSTRAINT `lost_found_item_images_found_item_id_foreign` FOREIGN KEY (`found_item_id`) REFERENCES `lost_found_items` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Table: lost_found_reports
DROP TABLE IF EXISTS `lost_found_reports`;
CREATE TABLE `lost_found_reports` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `public_reference` varchar(64) NOT NULL,
  `public_reference_key` varchar(64) NOT NULL,
  `student_id` bigint(20) unsigned NOT NULL,
  `category_id` bigint(20) unsigned DEFAULT NULL,
  `resolved_found_item_id` bigint(20) unsigned DEFAULT NULL,
  `status` varchar(32) NOT NULL DEFAULT 'draft',
  `title` varchar(160) NOT NULL,
  `public_description` text DEFAULT NULL,
  `private_description` text DEFAULT NULL,
  `lost_location` varchar(255) DEFAULT NULL,
  `lost_at` datetime DEFAULT NULL,
  `submitted_at` datetime DEFAULT NULL,
  `closed_at` datetime DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `lost_found_reports_public_reference_key_unique` (`public_reference_key`),
  KEY `lost_found_reports_student_id_status_index` (`student_id`,`status`),
  KEY `lost_found_reports_status_category_id_lost_at_index` (`status`,`category_id`,`lost_at`),
  KEY `lost_found_reports_resolved_found_item_id_index` (`resolved_found_item_id`),
  KEY `lf_reports_match_retrieval_index` (`category_id`,`status`,`lost_at`,`id`),
  CONSTRAINT `lost_found_reports_category_id_foreign` FOREIGN KEY (`category_id`) REFERENCES `lost_found_categories` (`id`),
  CONSTRAINT `lost_found_reports_resolved_found_item_id_foreign` FOREIGN KEY (`resolved_found_item_id`) REFERENCES `lost_found_items` (`id`),
  CONSTRAINT `lost_found_reports_student_id_foreign` FOREIGN KEY (`student_id`) REFERENCES `students` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Table: lost_found_report_images
DROP TABLE IF EXISTS `lost_found_report_images`;
CREATE TABLE `lost_found_report_images` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `lost_report_id` bigint(20) unsigned NOT NULL,
  `storage_key` varchar(255) NOT NULL,
  `mime_type` varchar(255) NOT NULL,
  `byte_size` int(10) unsigned NOT NULL,
  `sort_order` int(10) unsigned NOT NULL DEFAULT 0,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `lost_found_report_images_storage_key_unique` (`storage_key`),
  KEY `lf_report_images_lookup_index` (`lost_report_id`,`sort_order`,`id`),
  CONSTRAINT `lost_found_report_images_lost_report_id_foreign` FOREIGN KEY (`lost_report_id`) REFERENCES `lost_found_reports` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Table: lost_found_report_responses
DROP TABLE IF EXISTS `lost_found_report_responses`;
CREATE TABLE `lost_found_report_responses` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `public_reference` varchar(64) NOT NULL,
  `public_reference_key` varchar(64) NOT NULL,
  `lost_report_id` bigint(20) unsigned NOT NULL,
  `responder_student_id` bigint(20) unsigned NOT NULL,
  `reviewer_user_id` int(10) unsigned DEFAULT NULL,
  `resulting_found_item_id` bigint(20) unsigned DEFAULT NULL,
  `status` varchar(32) NOT NULL DEFAULT 'submitted',
  `found_location` varchar(255) NOT NULL,
  `found_at` datetime DEFAULT NULL,
  `dropoff_location` varchar(255) NOT NULL,
  `message` text DEFAULT NULL,
  `submitted_at` datetime NOT NULL,
  `review_started_at` datetime DEFAULT NULL,
  `verified_at` datetime DEFAULT NULL,
  `rejected_at` datetime DEFAULT NULL,
  `cancelled_at` datetime DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `lf_response_report_responder_unique` (`lost_report_id`,`responder_student_id`),
  UNIQUE KEY `lost_found_report_responses_public_reference_key_unique` (`public_reference_key`),
  UNIQUE KEY `lf_response_result_item_unique` (`resulting_found_item_id`),
  KEY `lost_found_report_responses_responder_student_id_foreign` (`responder_student_id`),
  KEY `lost_found_report_responses_reviewer_user_id_foreign` (`reviewer_user_id`),
  KEY `lf_response_status_time_index` (`status`,`submitted_at`),
  KEY `lf_response_report_status_index` (`lost_report_id`,`status`),
  CONSTRAINT `lost_found_report_responses_lost_report_id_foreign` FOREIGN KEY (`lost_report_id`) REFERENCES `lost_found_reports` (`id`),
  CONSTRAINT `lost_found_report_responses_responder_student_id_foreign` FOREIGN KEY (`responder_student_id`) REFERENCES `students` (`id`),
  CONSTRAINT `lost_found_report_responses_resulting_found_item_id_foreign` FOREIGN KEY (`resulting_found_item_id`) REFERENCES `lost_found_items` (`id`),
  CONSTRAINT `lost_found_report_responses_reviewer_user_id_foreign` FOREIGN KEY (`reviewer_user_id`) REFERENCES `users` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Table: lost_found_report_response_images
DROP TABLE IF EXISTS `lost_found_report_response_images`;
CREATE TABLE `lost_found_report_response_images` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `response_id` bigint(20) unsigned NOT NULL,
  `storage_key` text NOT NULL,
  `storage_key_hash` char(64) NOT NULL,
  `mime_type` varchar(64) NOT NULL,
  `byte_size` bigint(20) unsigned NOT NULL,
  `submitted_at` datetime NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `lost_found_report_response_images_storage_key_hash_unique` (`storage_key_hash`),
  KEY `lf_response_image_time_index` (`response_id`,`submitted_at`),
  CONSTRAINT `lost_found_report_response_images_response_id_foreign` FOREIGN KEY (`response_id`) REFERENCES `lost_found_report_responses` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Table: lost_found_report_response_reviews
DROP TABLE IF EXISTS `lost_found_report_response_reviews`;
CREATE TABLE `lost_found_report_response_reviews` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `response_id` bigint(20) unsigned NOT NULL,
  `reviewer_user_id` int(10) unsigned NOT NULL,
  `from_status` varchar(32) NOT NULL,
  `to_status` varchar(32) NOT NULL,
  `staff_notes` text DEFAULT NULL,
  `reviewed_at` datetime NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `lost_found_report_response_reviews_reviewer_user_id_foreign` (`reviewer_user_id`),
  KEY `lf_response_review_time_index` (`response_id`,`reviewed_at`),
  CONSTRAINT `lost_found_report_response_reviews_response_id_foreign` FOREIGN KEY (`response_id`) REFERENCES `lost_found_report_responses` (`id`),
  CONSTRAINT `lost_found_report_response_reviews_reviewer_user_id_foreign` FOREIGN KEY (`reviewer_user_id`) REFERENCES `users` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Table: lost_found_claims
DROP TABLE IF EXISTS `lost_found_claims`;
CREATE TABLE `lost_found_claims` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `found_item_id` bigint(20) unsigned NOT NULL,
  `claimant_student_id` bigint(20) unsigned NOT NULL,
  `status` varchar(32) NOT NULL DEFAULT 'submitted',
  `submitted_at` datetime NOT NULL,
  `withdrawn_at` datetime DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `lf_claims_item_claimant_unique` (`found_item_id`,`claimant_student_id`),
  KEY `lf_claims_item_status_index` (`found_item_id`,`status`),
  KEY `lf_claims_claimant_status_index` (`claimant_student_id`,`status`),
  KEY `lf_claims_status_submitted_index` (`status`,`submitted_at`),
  CONSTRAINT `lost_found_claims_claimant_student_id_foreign` FOREIGN KEY (`claimant_student_id`) REFERENCES `students` (`id`),
  CONSTRAINT `lost_found_claims_found_item_id_foreign` FOREIGN KEY (`found_item_id`) REFERENCES `lost_found_items` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Table: lost_found_claim_evidence
DROP TABLE IF EXISTS `lost_found_claim_evidence`;
CREATE TABLE `lost_found_claim_evidence` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `claim_id` bigint(20) unsigned NOT NULL,
  `evidence_type` varchar(32) NOT NULL,
  `text_value` text DEFAULT NULL,
  `file_path` text DEFAULT NULL,
  `storage_key_hash` char(64) DEFAULT NULL,
  `original_name` text DEFAULT NULL,
  `mime_type` varchar(100) DEFAULT NULL,
  `byte_size` bigint(20) unsigned DEFAULT NULL,
  `submitted_at` datetime NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `lost_found_claim_evidence_storage_key_hash_unique` (`storage_key_hash`),
  KEY `lf_claim_evidence_claim_type_index` (`claim_id`,`evidence_type`),
  CONSTRAINT `lost_found_claim_evidence_claim_id_foreign` FOREIGN KEY (`claim_id`) REFERENCES `lost_found_claims` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Table: lost_found_claim_reviews
DROP TABLE IF EXISTS `lost_found_claim_reviews`;
CREATE TABLE `lost_found_claim_reviews` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `claim_id` bigint(20) unsigned NOT NULL,
  `reviewer_user_id` int(10) unsigned NOT NULL,
  `from_status` varchar(32) NOT NULL,
  `to_status` varchar(32) NOT NULL,
  `claimant_message` text DEFAULT NULL,
  `staff_notes` text DEFAULT NULL,
  `reviewed_at` datetime NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `lf_claim_reviews_claim_time_index` (`claim_id`,`reviewed_at`,`id`),
  KEY `lf_claim_reviews_reviewer_index` (`reviewer_user_id`),
  CONSTRAINT `lost_found_claim_reviews_claim_id_foreign` FOREIGN KEY (`claim_id`) REFERENCES `lost_found_claims` (`id`),
  CONSTRAINT `lost_found_claim_reviews_reviewer_user_id_foreign` FOREIGN KEY (`reviewer_user_id`) REFERENCES `users` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Table: lost_found_custody_records
DROP TABLE IF EXISTS `lost_found_custody_records`;
CREATE TABLE `lost_found_custody_records` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `found_item_id` bigint(20) unsigned NOT NULL,
  `event_type` varchar(32) NOT NULL,
  `actor_user_id` int(10) unsigned NOT NULL,
  `from_custodian_user_id` int(10) unsigned DEFAULT NULL,
  `to_custodian_user_id` int(10) unsigned DEFAULT NULL,
  `from_storage_location` text DEFAULT NULL,
  `to_storage_location` text DEFAULT NULL,
  `notes` text DEFAULT NULL,
  `occurred_at` datetime NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `lost_found_custody_records_from_custodian_user_id_foreign` (`from_custodian_user_id`),
  KEY `lf_custody_item_occurred_index` (`found_item_id`,`occurred_at`,`id`),
  KEY `lf_custody_actor_index` (`actor_user_id`),
  KEY `lf_custody_to_custodian_index` (`to_custodian_user_id`),
  CONSTRAINT `lost_found_custody_records_actor_user_id_foreign` FOREIGN KEY (`actor_user_id`) REFERENCES `users` (`id`),
  CONSTRAINT `lost_found_custody_records_found_item_id_foreign` FOREIGN KEY (`found_item_id`) REFERENCES `lost_found_items` (`id`),
  CONSTRAINT `lost_found_custody_records_from_custodian_user_id_foreign` FOREIGN KEY (`from_custodian_user_id`) REFERENCES `users` (`id`),
  CONSTRAINT `lost_found_custody_records_to_custodian_user_id_foreign` FOREIGN KEY (`to_custodian_user_id`) REFERENCES `users` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Table: lost_found_handovers
DROP TABLE IF EXISTS `lost_found_handovers`;
CREATE TABLE `lost_found_handovers` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `found_item_id` bigint(20) unsigned NOT NULL,
  `claim_id` bigint(20) unsigned NOT NULL,
  `recipient_student_id` bigint(20) unsigned NOT NULL,
  `staff_user_id` int(10) unsigned NOT NULL,
  `verification_method` text NOT NULL,
  `verification_note` text DEFAULT NULL,
  `handed_over_at` datetime NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `lost_found_handovers_found_item_id_unique` (`found_item_id`),
  UNIQUE KEY `lost_found_handovers_claim_id_unique` (`claim_id`),
  KEY `lf_handovers_recipient_index` (`recipient_student_id`),
  KEY `lf_handovers_staff_time_index` (`staff_user_id`,`handed_over_at`),
  CONSTRAINT `lost_found_handovers_claim_id_foreign` FOREIGN KEY (`claim_id`) REFERENCES `lost_found_claims` (`id`),
  CONSTRAINT `lost_found_handovers_found_item_id_foreign` FOREIGN KEY (`found_item_id`) REFERENCES `lost_found_items` (`id`),
  CONSTRAINT `lost_found_handovers_recipient_student_id_foreign` FOREIGN KEY (`recipient_student_id`) REFERENCES `students` (`id`),
  CONSTRAINT `lost_found_handovers_staff_user_id_foreign` FOREIGN KEY (`staff_user_id`) REFERENCES `users` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Table: lost_found_potential_matches
DROP TABLE IF EXISTS `lost_found_potential_matches`;
CREATE TABLE `lost_found_potential_matches` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `lost_report_id` bigint(20) unsigned NOT NULL,
  `found_item_id` bigint(20) unsigned NOT NULL,
  `proposed_by_user_id` int(10) unsigned NOT NULL,
  `proposed_at` datetime NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `lf_potential_report_item_unique` (`lost_report_id`,`found_item_id`),
  UNIQUE KEY `lf_potential_identity_unique` (`id`,`lost_report_id`,`found_item_id`),
  KEY `lost_found_potential_matches_proposed_by_user_id_foreign` (`proposed_by_user_id`),
  KEY `lf_potential_item_time_index` (`found_item_id`,`proposed_at`),
  CONSTRAINT `lost_found_potential_matches_found_item_id_foreign` FOREIGN KEY (`found_item_id`) REFERENCES `lost_found_items` (`id`),
  CONSTRAINT `lost_found_potential_matches_lost_report_id_foreign` FOREIGN KEY (`lost_report_id`) REFERENCES `lost_found_reports` (`id`),
  CONSTRAINT `lost_found_potential_matches_proposed_by_user_id_foreign` FOREIGN KEY (`proposed_by_user_id`) REFERENCES `users` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Table: lost_found_match_reviews
DROP TABLE IF EXISTS `lost_found_match_reviews`;
CREATE TABLE `lost_found_match_reviews` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `potential_match_id` bigint(20) unsigned NOT NULL,
  `reviewer_user_id` int(10) unsigned NOT NULL,
  `decision` varchar(20) NOT NULL,
  `notes` text DEFAULT NULL,
  `reviewed_at` datetime NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `lost_found_match_reviews_reviewer_user_id_foreign` (`reviewer_user_id`),
  KEY `lf_match_review_time_index` (`potential_match_id`,`reviewed_at`,`id`),
  KEY `lf_match_review_decision_index` (`decision`,`reviewed_at`),
  CONSTRAINT `lost_found_match_reviews_potential_match_id_foreign` FOREIGN KEY (`potential_match_id`) REFERENCES `lost_found_potential_matches` (`id`),
  CONSTRAINT `lost_found_match_reviews_reviewer_user_id_foreign` FOREIGN KEY (`reviewer_user_id`) REFERENCES `users` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Table: lost_found_match_snapshots
DROP TABLE IF EXISTS `lost_found_match_snapshots`;
CREATE TABLE `lost_found_match_snapshots` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `potential_match_id` bigint(20) unsigned NOT NULL,
  `generated_by_user_id` int(10) unsigned NOT NULL,
  `algorithm_version` varchar(32) NOT NULL,
  `score_basis_points` int(10) unsigned NOT NULL,
  `signals` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin NOT NULL CHECK (json_valid(`signals`)),
  `input_fingerprint` char(64) NOT NULL,
  `generated_at` datetime NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `lf_match_snapshot_fingerprint_unique` (`potential_match_id`,`input_fingerprint`),
  KEY `lost_found_match_snapshots_generated_by_user_id_foreign` (`generated_by_user_id`),
  KEY `lf_match_snapshot_time_index` (`potential_match_id`,`generated_at`),
  KEY `lf_match_snapshot_score_index` (`score_basis_points`,`generated_at`),
  CONSTRAINT `lost_found_match_snapshots_generated_by_user_id_foreign` FOREIGN KEY (`generated_by_user_id`) REFERENCES `users` (`id`),
  CONSTRAINT `lost_found_match_snapshots_potential_match_id_foreign` FOREIGN KEY (`potential_match_id`) REFERENCES `lost_found_potential_matches` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Table: lost_found_verified_links
DROP TABLE IF EXISTS `lost_found_verified_links`;
CREATE TABLE `lost_found_verified_links` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `potential_match_id` bigint(20) unsigned NOT NULL,
  `lost_report_id` bigint(20) unsigned NOT NULL,
  `found_item_id` bigint(20) unsigned NOT NULL,
  `verified_by_user_id` int(10) unsigned NOT NULL,
  `verification_evidence` text NOT NULL,
  `verified_at` datetime NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `lf_verified_potential_unique` (`potential_match_id`),
  UNIQUE KEY `lf_verified_report_unique` (`lost_report_id`),
  UNIQUE KEY `lf_verified_item_unique` (`found_item_id`),
  KEY `lf_verified_potential_foreign` (`potential_match_id`,`lost_report_id`,`found_item_id`),
  KEY `lost_found_verified_links_verified_by_user_id_foreign` (`verified_by_user_id`),
  CONSTRAINT `lf_verified_potential_foreign` FOREIGN KEY (`potential_match_id`, `lost_report_id`, `found_item_id`) REFERENCES `lost_found_potential_matches` (`id`, `lost_report_id`, `found_item_id`),
  CONSTRAINT `lost_found_verified_links_verified_by_user_id_foreign` FOREIGN KEY (`verified_by_user_id`) REFERENCES `users` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- -----------------------------------------------------------------------------
-- SECTION: 3. نظام الإدارة والمستخدمين والصلاحيات (Admin & User System)
-- -----------------------------------------------------------------------------

-- Table: roles
DROP TABLE IF EXISTS `roles`;
CREATE TABLE `roles` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(255) NOT NULL,
  `description` varchar(255) DEFAULT NULL,
  `permission_type` varchar(255) NOT NULL,
  `permissions` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`permissions`)),
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Table: groups
DROP TABLE IF EXISTS `groups`;
CREATE TABLE `groups` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(255) NOT NULL,
  `description` varchar(255) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `groups_name_unique` (`name`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Table: users
DROP TABLE IF EXISTS `users`;
CREATE TABLE `users` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(255) NOT NULL,
  `email` varchar(255) NOT NULL,
  `password` varchar(255) DEFAULT NULL,
  `status` tinyint(1) NOT NULL DEFAULT 0,
  `view_permission` varchar(255) DEFAULT 'global',
  `role_id` int(10) unsigned NOT NULL,
  `remember_token` varchar(100) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `image` varchar(255) DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `users_email_unique` (`email`),
  KEY `users_role_id_foreign` (`role_id`),
  CONSTRAINT `users_role_id_foreign` FOREIGN KEY (`role_id`) REFERENCES `roles` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Table: user_groups
DROP TABLE IF EXISTS `user_groups`;
CREATE TABLE `user_groups` (
  `group_id` int(10) unsigned NOT NULL,
  `user_id` int(10) unsigned NOT NULL,
  KEY `user_groups_group_id_foreign` (`group_id`),
  KEY `user_groups_user_id_foreign` (`user_id`),
  CONSTRAINT `user_groups_group_id_foreign` FOREIGN KEY (`group_id`) REFERENCES `groups` (`id`) ON DELETE CASCADE,
  CONSTRAINT `user_groups_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Table: user_password_resets
DROP TABLE IF EXISTS `user_password_resets`;
CREATE TABLE `user_password_resets` (
  `email` varchar(255) NOT NULL,
  `token` varchar(255) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  KEY `user_password_resets_email_index` (`email`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- -----------------------------------------------------------------------------
-- SECTION: 4. إعدادات النظام واللغات (Core & Localization)
-- -----------------------------------------------------------------------------

-- Table: core_config
DROP TABLE IF EXISTS `core_config`;
CREATE TABLE `core_config` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `code` varchar(255) NOT NULL,
  `value` text NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Table: locales
DROP TABLE IF EXISTS `locales`;
CREATE TABLE `locales` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `code` varchar(16) NOT NULL,
  `name` varchar(120) NOT NULL,
  `direction` enum('ltr','rtl') NOT NULL,
  `is_active` tinyint(1) NOT NULL DEFAULT 1,
  `sort_order` int(10) unsigned NOT NULL DEFAULT 0,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `locales_code_unique` (`code`),
  KEY `locales_is_active_sort_order_code_index` (`is_active`,`sort_order`,`code`)
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Table: content_locale_settings
DROP TABLE IF EXISTS `content_locale_settings`;
CREATE TABLE `content_locale_settings` (
  `key` enum('primary') NOT NULL,
  `primary_locale_id` bigint(20) unsigned NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`key`),
  KEY `content_locale_settings_primary_locale_id_foreign` (`primary_locale_id`),
  CONSTRAINT `content_locale_settings_primary_locale_id_foreign` FOREIGN KEY (`primary_locale_id`) REFERENCES `locales` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Table: countries
DROP TABLE IF EXISTS `countries`;
CREATE TABLE `countries` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `code` varchar(255) NOT NULL,
  `name` varchar(255) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=256 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Table: country_states
DROP TABLE IF EXISTS `country_states`;
CREATE TABLE `country_states` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `country_code` varchar(255) NOT NULL,
  `code` varchar(255) NOT NULL,
  `name` varchar(255) NOT NULL,
  `country_id` int(10) unsigned NOT NULL,
  PRIMARY KEY (`id`),
  KEY `country_states_country_id_foreign` (`country_id`),
  CONSTRAINT `country_states_country_id_foreign` FOREIGN KEY (`country_id`) REFERENCES `countries` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=569 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- -----------------------------------------------------------------------------
-- SECTION: 5. جداول البيانات المحفوظة (DataGrid System)
-- -----------------------------------------------------------------------------

-- Table: datagrid_saved_filters
DROP TABLE IF EXISTS `datagrid_saved_filters`;
CREATE TABLE `datagrid_saved_filters` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `user_id` int(10) unsigned NOT NULL,
  `name` varchar(255) NOT NULL,
  `src` varchar(255) NOT NULL,
  `applied` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin NOT NULL CHECK (json_valid(`applied`)),
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `datagrid_saved_filters_user_id_name_src_unique` (`user_id`,`name`,`src`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- -----------------------------------------------------------------------------
-- SECTION: 6. جداول البنية التحتية والمهام (Infrastructure & System)
-- -----------------------------------------------------------------------------

-- Table: personal_access_tokens
DROP TABLE IF EXISTS `personal_access_tokens`;
CREATE TABLE `personal_access_tokens` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `tokenable_type` varchar(255) NOT NULL,
  `tokenable_id` bigint(20) unsigned NOT NULL,
  `name` varchar(255) NOT NULL,
  `token` varchar(64) NOT NULL,
  `abilities` text DEFAULT NULL,
  `last_used_at` timestamp NULL DEFAULT NULL,
  `expires_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `personal_access_tokens_token_unique` (`token`),
  KEY `personal_access_tokens_tokenable_type_tokenable_id_index` (`tokenable_type`,`tokenable_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Table: jobs
DROP TABLE IF EXISTS `jobs`;
CREATE TABLE `jobs` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `queue` varchar(255) NOT NULL,
  `payload` longtext NOT NULL,
  `attempts` tinyint(3) unsigned NOT NULL,
  `reserved_at` int(10) unsigned DEFAULT NULL,
  `available_at` int(10) unsigned NOT NULL,
  `created_at` int(10) unsigned NOT NULL,
  PRIMARY KEY (`id`),
  KEY `jobs_queue_index` (`queue`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Table: job_batches
DROP TABLE IF EXISTS `job_batches`;
CREATE TABLE `job_batches` (
  `id` varchar(255) NOT NULL,
  `name` varchar(255) NOT NULL,
  `total_jobs` int(11) NOT NULL,
  `pending_jobs` int(11) NOT NULL,
  `failed_jobs` int(11) NOT NULL,
  `failed_job_ids` text NOT NULL,
  `options` mediumtext DEFAULT NULL,
  `cancelled_at` int(11) DEFAULT NULL,
  `created_at` int(11) NOT NULL,
  `finished_at` int(11) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Table: failed_jobs
DROP TABLE IF EXISTS `failed_jobs`;
CREATE TABLE `failed_jobs` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `uuid` varchar(255) NOT NULL,
  `connection` text NOT NULL,
  `queue` text NOT NULL,
  `payload` longtext NOT NULL,
  `exception` longtext NOT NULL,
  `failed_at` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  UNIQUE KEY `failed_jobs_uuid_unique` (`uuid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Table: migrations
DROP TABLE IF EXISTS `migrations`;
CREATE TABLE `migrations` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `migration` varchar(255) NOT NULL,
  `batch` int(11) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=40 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

SET FOREIGN_KEY_CHECKS = 1;
