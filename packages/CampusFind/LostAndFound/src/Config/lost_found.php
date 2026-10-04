<?php

return [
    'claim_evidence_images' => [
        'disk' => 'lost_found_private',

        /* Operational security defaults; these are configurable, not domain policy. */
        'max_bytes' => (int) env('LOST_FOUND_EVIDENCE_IMAGE_MAX_BYTES', 2 * 1024 * 1024),
        'max_width' => (int) env('LOST_FOUND_EVIDENCE_IMAGE_MAX_WIDTH', 4096),
        'max_height' => (int) env('LOST_FOUND_EVIDENCE_IMAGE_MAX_HEIGHT', 4096),
        'max_pixels' => (int) env('LOST_FOUND_EVIDENCE_IMAGE_MAX_PIXELS', 12_000_000),

        'jpeg_quality' => (int) env('LOST_FOUND_EVIDENCE_JPEG_QUALITY', 90),
        'png_compression' => (int) env('LOST_FOUND_EVIDENCE_PNG_COMPRESSION', 6),
        'webp_quality' => (int) env('LOST_FOUND_EVIDENCE_WEBP_QUALITY', 90),
    ],

    'found_response_images' => [
        'disk' => 'lost_found_private',
        'max_bytes' => (int) env('LOST_FOUND_RESPONSE_IMAGE_MAX_BYTES', 2 * 1024 * 1024),
        'max_width' => (int) env('LOST_FOUND_RESPONSE_IMAGE_MAX_WIDTH', 4096),
        'max_height' => (int) env('LOST_FOUND_RESPONSE_IMAGE_MAX_HEIGHT', 4096),
        'max_pixels' => (int) env('LOST_FOUND_RESPONSE_IMAGE_MAX_PIXELS', 12_000_000),
        'jpeg_quality' => (int) env('LOST_FOUND_RESPONSE_JPEG_QUALITY', 90),
        'png_compression' => (int) env('LOST_FOUND_RESPONSE_PNG_COMPRESSION', 6),
        'webp_quality' => (int) env('LOST_FOUND_RESPONSE_WEBP_QUALITY', 90),
    ],

    'found_responses' => [
        'approved_dropoff_locations' => ['main_security', 'student_affairs', 'library_desk'],
    ],

    'matching' => [
        'algorithm_version' => 'deterministic-v1',
        'candidate_limit' => (int) env('LOST_FOUND_MATCH_CANDIDATE_LIMIT', 100),
        'result_limit' => (int) env('LOST_FOUND_MATCH_RESULT_LIMIT', 25),
        'weights' => [
            'category' => 30,
            'description' => 35,
            'location' => 20,
            'temporal' => 15,
        ],
        'temporal_decay_days' => (int) env('LOST_FOUND_MATCH_TEMPORAL_DECAY_DAYS', 30),
    ],
];
