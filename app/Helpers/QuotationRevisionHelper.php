<?php

namespace App\Helpers;

use App\Models\Quotation;

class QuotationRevisionHelper
{
    /**
     * Extract root base quotation number (e.g. 'MSE/2026/0032' from 'MSE/2026/0032(R1-2)').
     */
    public static function getBaseQuotationNo(string $quotaId): string
    {
        $trimmed = trim($quotaId);
        if (preg_match('/^(MSE\/\d{4}\/\d+)/i', $trimmed, $m)) {
            return $m[1];
        }
        return preg_replace('/\s*\([^)]+\)$/', '', $trimmed);
    }

    /**
     * Parse major and minor revision components from a quota_id.
     *
     * Patterns:
     * - 'BASE'               => ['major' => 0, 'minor' => 0]
     * - 'BASE(R0-2)'         => ['major' => 0, 'minor' => 2]
     * - 'BASE(R1)'           => ['major' => 1, 'minor' => 0]
     * - 'BASE(R1-3)'         => ['major' => 1, 'minor' => 3]
     * - 'BASE(2)' (fallback) => ['major' => 0, 'minor' => 2]
     *
     * @return array{major: int, minor: int}
     */
    public static function parseRevision(string $quotaId): array
    {
        $trimmed = trim($quotaId);

        // Pattern: (RX-Y)
        if (preg_match('/\(R(\d+)-(\d+)\)$/i', $trimmed, $m)) {
            return ['major' => (int) $m[1], 'minor' => (int) $m[2]];
        }

        // Pattern: (RX)
        if (preg_match('/\(R(\d+)\)$/i', $trimmed, $m)) {
            return ['major' => (int) $m[1], 'minor' => 0];
        }

        // Pattern: (Y)
        if (preg_match('/\((\d+)\)$/i', $trimmed, $m)) {
            return ['major' => 0, 'minor' => (int) $m[1]];
        }

        return ['major' => 0, 'minor' => 0];
    }

    /**
     * Format a quotation number given base, major, and minor.
     */
    public static function formatQuotaId(string $baseNo, int $major, int $minor): string
    {
        if ($major === 0 && $minor === 0) {
            return $baseNo;
        }

        if ($minor > 0) {
            return "{$baseNo}(R{$major}-{$minor})";
        }

        return "{$baseNo}(R{$major})";
    }

    /**
     * Determine if a quotation qualifies as having gone through approval OR sent to customer.
     */
    public static function qualifiesForExternalRevision(Quotation $quotation, ?array $approvalStatus = null): bool
    {
        if (!empty($quotation->sent_to_customer)) {
            return true;
        }

        if ($approvalStatus !== null) {
            return (bool) ($approvalStatus['approved'] ?? false);
        }

        // Fallback check on quotation model fields
        $allApprovalsDone = ((int) $quotation->quto_app === 2)
            && (int) $quotation->dis_app !== 1
            && (int) $quotation->dis_app !== 0
            && (int) $quotation->freight_approval !== 1
            && (int) $quotation->freight_approval !== 2
            && !in_array($quotation->charge_approval_status, ['Pending Approval', 'Pending', 'Rejected'], true);

        return $allApprovalsDone;
    }

    /**
     * Generate the next quotation revision number and components.
     *
     * @return array{quota_id: string, revision_major: int, revision_minor: int, is_external: bool}
     */
    public static function generateNextRevision(Quotation $currentQuotation, bool $qualifiesForExternal): array
    {
        $baseNo = self::getBaseQuotationNo($currentQuotation->quota_id);

        // Fetch all siblings sharing this base number
        $siblings = Quotation::where('quota_id', 'like', $baseNo . '%')
            ->orderBy('id', 'asc')
            ->get();

        $parsedCurrent = self::parseRevision($currentQuotation->quota_id);
        $currentMajor = $parsedCurrent['major'];

        if ($qualifiesForExternal) {
            // Find max major across all siblings
            $maxMajor = 0;
            foreach ($siblings as $sib) {
                $rev = self::parseRevision($sib->quota_id);
                if ($rev['major'] > $maxMajor) {
                    $maxMajor = $rev['major'];
                }
            }

            $nextMajor = max($maxMajor + 1, 1);
            $nextMinor = 0;

            return [
                'quota_id' => self::formatQuotaId($baseNo, $nextMajor, $nextMinor),
                'revision_major' => $nextMajor,
                'revision_minor' => $nextMinor,
                'is_external' => true,
            ];
        }

        // Internal Revision under $currentMajor
        $maxMinor = 0;
        foreach ($siblings as $sib) {
            $rev = self::parseRevision($sib->quota_id);
            if ($rev['major'] === $currentMajor && $rev['minor'] > $maxMinor) {
                $maxMinor = $rev['minor'];
            }
        }

        $nextMinor = $maxMinor + 1;

        return [
            'quota_id' => self::formatQuotaId($baseNo, $currentMajor, $nextMinor),
            'revision_major' => $currentMajor,
            'revision_minor' => $nextMinor,
            'is_external' => false,
        ];
    }
}
