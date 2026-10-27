<?php

namespace App\Http\Controllers;

use App\Services\LeadImportService;
use App\Services\LeadTemplateService;
use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\StreamedResponse;

class LeadImportController extends Controller
{
    protected LeadImportService $importService;
    protected LeadTemplateService $templateService;

    public function __construct(LeadImportService $importService, LeadTemplateService $templateService)
    {
        $this->importService = $importService;
        $this->templateService = $templateService;
    }

    /**
     * Download sample template for general or expo leads.
     */
    public function downloadTemplate(Request $request, string $type): StreamedResponse
    {
        if (!in_array($type, ['general', 'expo'])) {
            abort(404, 'Invalid template type.');
        }

        $format = strtolower($request->query('format', 'xlsx'));
        if (!in_array($format, ['xlsx', 'csv'])) {
            $format = 'xlsx';
        }

        return $this->templateService->downloadTemplate($type, $format);
    }

    /**
     * Handle bulk upload for general or expo leads.
     */
    public function import(Request $request, string $type): JsonResponse
    {
        if (!in_array($type, ['general', 'expo'])) {
            return response()->json([
                'success' => false,
                'message' => 'Invalid lead type specified.'
            ], 400);
        }

        $request->validate([
            'file' => 'required|file|max:40960',
            'update_existing' => 'nullable|boolean',
            'expo_event' => 'nullable|string|max:255',
        ]);

        $file = $request->file('file');
        $ext = strtolower($file->getClientOriginalExtension());
        if (!in_array($ext, ['xlsx', 'xls', 'csv'])) {
            return response()->json([
                'success' => false,
                'message' => 'Invalid file format. Please upload an Excel (.xlsx, .xls) or CSV file.'
            ], 422);
        }

        // Store temporarily
        $tempPath = $file->getRealPath();
        $updateExisting = (bool)$request->input('update_existing', false);
        $expoEvent = $request->input('expo_event');
        $userId = Auth::id() ?? 1;

        try {
            $result = $this->importService->import(
                $tempPath,
                $type,
                $updateExisting,
                $expoEvent,
                $userId
            );

            return response()->json($result);
        } catch (Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'An unexpected error occurred during processing: ' . $e->getMessage(),
                'total_rows' => 0,
                'imported' => 0,
                'updated' => 0,
                'skipped' => 0,
                'failed' => 0,
                'skipped_rows' => [],
                'error_rows' => []
            ], 500);
        }
    }
}
