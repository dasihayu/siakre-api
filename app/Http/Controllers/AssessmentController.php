<?php

namespace App\Http\Controllers;

use App\Helpers\ApiResponse;
use App\Services\AssessmentPendidikanService;
use App\Services\CplCalculatorService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class AssessmentController extends Controller
{
    public function cpl(Request $request, CplCalculatorService $calculator): JsonResponse
    {
        $validated = $request->validate([
            'id_kurikulum' => 'required|integer|exists:kurikulum,id',
            'angkatan' => 'required|integer|min:1900|max:2100',
            'target' => 'sometimes|numeric|between:0,100',
        ]);

        $assessment = $calculator->calculate(
            (int) $validated['id_kurikulum'],
            (int) $validated['angkatan'],
            (float) ($validated['target'] ?? config('assessment.targets.cpl'))
        );

        return ApiResponse::success($assessment, 'Berhasil menghitung ketercapaian CPL');
    }

    public function pendidikanGap(Request $request, AssessmentPendidikanService $assessment): JsonResponse
    {
        $validated = $request->validate([
            'id_kurikulum' => 'sometimes|integer|exists:kurikulum,id',
            'angkatan' => 'sometimes|integer|min:1900|max:2100',
        ]);

        $result = $assessment->gap(
            isset($validated['id_kurikulum']) ? (int) $validated['id_kurikulum'] : null,
            isset($validated['angkatan']) ? (int) $validated['angkatan'] : null
        );

        return ApiResponse::success($result, 'Berhasil menghitung gap assessment pendidikan');
    }
}
