<?php

return [
    'targets' => [
        'cpl' => (float) env('ASSESSMENT_TARGET_CPL', 70),
        'kelulusan_tepat_waktu' => (float) env('ASSESSMENT_TARGET_KELULUSAN_TEPAT_WAKTU', 50),
        'ipk_rata_rata' => (float) env('ASSESSMENT_TARGET_IPK_RATA_RATA', 3),
        'rasio_mahasiswa_baru_pendaftar' => (float) env('ASSESSMENT_TARGET_RASIO_PENDAFTAR', 50),
    ],
    'on_time_months' => (int) env('ASSESSMENT_ON_TIME_MONTHS', 48),
];
