<?php
/**
 * UI/UX Pro Max Search (PHP runner)
 * Equivalent search engine over UI/UX Pro Max CSV datasets
 *
 * Usage:
 *   php search.php "<query>" [--domain <domain>] [--stack <stack>] [--max-results 3]
 */

$dataDir = dirname(__DIR__) . DIRECTORY_SEPARATOR . 'data';

$csvConfig = [
    'style' => [
        'file' => 'styles.csv',
        'search_cols' => ['Style ID', 'Style Category', 'Aliases', 'Keywords', 'Best For', 'Type', 'AI Prompt Keywords'],
        'output_cols' => ['Style ID', 'Style Category', 'Aliases', 'Status', 'Parent Style ID', 'Preferred Mode', 'Type', 'Keywords', 'Primary Colors', 'Effects & Animation', 'Best For', 'Light Mode ✓', 'Dark Mode ✓', 'Performance', 'Accessibility', 'Framework Compatibility', 'Complexity', 'AI Prompt Keywords', 'CSS/Technical Keywords', 'Implementation Checklist', 'Design System Variables']
    ],
    'color' => [
        'file' => 'colors.csv',
        'search_cols' => ['Product Type', 'Notes'],
        'output_cols' => ['Product Type', 'Primary', 'On Primary', 'Secondary', 'On Secondary', 'Accent', 'On Accent', 'Background', 'Foreground', 'Card', 'Card Foreground', 'Muted', 'Muted Foreground', 'Border', 'Destructive', 'On Destructive', 'Ring', 'Notes']
    ],
    'chart' => [
        'file' => 'charts.csv',
        'search_cols' => ['Data Type', 'Keywords', 'Best Chart Type', 'When to Use', 'When NOT to Use', 'Accessibility Notes'],
        'output_cols' => ['Data Type', 'Keywords', 'Best Chart Type', 'Secondary Options', 'When to Use', 'When NOT to Use', 'Data Volume Threshold', 'Color Guidance', 'Accessibility Grade', 'Accessibility Risk', 'Accessibility Notes', 'A11y Fallback', 'Library Recommendation', 'Interactive Level']
    ],
    'landing' => [
        'file' => 'landing.csv',
        'search_cols' => ['Pattern ID', 'Pattern Name', 'Aliases', 'Keywords', 'Conversion Optimization', 'Section Order'],
        'output_cols' => ['Pattern ID', 'Pattern Name', 'Aliases', 'Keywords', 'Section Order', 'Primary CTA Placement', 'Color Strategy', 'Conversion Optimization']
    ],
    'product' => [
        'file' => 'products.csv',
        'search_cols' => ['Product Type', 'Keywords', 'Primary Style Recommendation', 'Key Considerations'],
        'output_cols' => ['Product Type', 'Keywords', 'Primary Style Recommendation', 'Secondary Styles', 'Landing Page Pattern', 'Dashboard Style (if applicable)', 'Color Palette Focus']
    ],
    'ux' => [
        'file' => 'ux-guidelines.csv',
        'search_cols' => ['Category', 'Issue', 'Description', 'Platform'],
        'output_cols' => ['Category', 'Issue', 'Platform', 'Description', 'Do', "Don't", 'Code Example Good', 'Code Example Bad', 'Severity']
    ],
    'typography' => [
        'file' => 'typography.csv',
        'search_cols' => ['Font Pairing Name', 'Category', 'Mood/Style Keywords', 'Best For', 'Heading Font', 'Body Font'],
        'output_cols' => ['Font Pairing Name', 'Category', 'Heading Font', 'Body Font', 'Mood/Style Keywords', 'Best For', 'Google Fonts URL', 'CSS Import', 'Tailwind Config', 'Notes']
    ],
    'icons' => [
        'file' => 'icons.csv',
        'search_cols' => ['Category', 'Icon Name', 'Keywords', 'Best For', 'Library'],
        'output_cols' => ['Category', 'Icon Name', 'Keywords', 'Library', 'Import Code', 'Usage', 'Best For', 'Style', 'Semantic Role', 'Allowed Contexts']
    ],
    'gsap' => [
        'file' => 'motion.csv',
        'search_cols' => ['Category', 'Intensity Tier', 'Keywords', 'Trigger'],
        'output_cols' => ['Category', 'Intensity Tier', 'Trigger', 'Duration', 'Easing', 'GSAP Snippet', 'Framework Notes', 'Do', "Don't", 'Performance Notes']
    ],
    'react' => [
        'file' => 'react-performance.csv',
        'search_cols' => ['Category', 'Issue', 'Keywords', 'Description'],
        'output_cols' => ['Category', 'Issue', 'Platform', 'Description', 'Do', "Don't", 'Code Example Good', 'Code Example Bad', 'Severity']
    ],
    'web' => [
        'file' => 'app-interface.csv',
        'search_cols' => ['Category', 'Issue', 'Keywords', 'Description'],
        'output_cols' => ['Category', 'Issue', 'Platform', 'Description', 'Do', "Don't", 'Code Example Good', 'Code Example Bad', 'Severity']
    ],
    'google-fonts' => [
        'file' => 'google-fonts.csv',
        'search_cols' => ['Family', 'Category', 'Stroke', 'Classifications', 'Keywords', 'Subsets', 'Designers'],
        'output_cols' => ['Family', 'Category', 'Stroke', 'Classifications', 'Styles', 'Variable Axes', 'Subsets', 'Designers', 'Popularity Rank', 'Google Fonts URL']
    ]
];

// Parse CLI arguments
$domain = null;
$stack = null;
$maxResults = 3;
$queryParts = [];

for ($i = 1; $i < count($argv); $i++) {
    $arg = $argv[$i];
    if ($arg === '--domain' && isset($argv[$i + 1])) {
        $domain = $argv[++$i];
    } elseif (str_starts_with($arg, '--domain=')) {
        $domain = substr($arg, 9);
    } elseif ($arg === '--stack' && isset($argv[$i + 1])) {
        $stack = $argv[++$i];
    } elseif (str_starts_with($arg, '--stack=')) {
        $stack = substr($arg, 8);
    } elseif ($arg === '--max-results' && isset($argv[$i + 1])) {
        $maxResults = (int)$argv[++$i];
    } elseif (str_starts_with($arg, '--max-results=')) {
        $maxResults = (int)substr($arg, 14);
    } elseif (!str_starts_with($arg, '--')) {
        $queryParts[] = $arg;
    }
}
$query = trim(implode(' ', $queryParts));

if (empty($query) && empty($domain) && empty($stack)) {
    echo "## UI Pro Max Search\n";
    echo "Usage: php search.php \"<query>\" [--domain <domain>] [--stack <stack>] [--max-results 3]\n";
    echo "Available domains: " . implode(', ', array_keys($csvConfig)) . "\n";
    echo "Available stacks: laravel, react, nextjs, vue, svelte, astro, swiftui, flutter, html-tailwind, etc.\n";
    exit(0);
}

// Check stack search
if ($stack) {
    $stackFile = $dataDir . DIRECTORY_SEPARATOR . 'stacks' . DIRECTORY_SEPARATOR . strtolower($stack) . '.csv';
    if (!file_exists($stackFile)) {
        echo "Error: Stack '{$stack}' not found. Check available stacks in data/stacks/.\n";
        exit(1);
    }
    searchCsv(
        $stackFile,
        $query,
        ['Category', 'Guideline', 'Description', 'Do', "Don't", 'Code Good', 'Code Bad'],
        ['Category', 'Guideline', 'Description', 'Do', "Don't", 'Code Good', 'Code Bad', 'Severity', 'Docs URL'],
        $maxResults,
        "Stack: {$stack}"
    );
    exit(0);
}

// Resolve domain or search all/auto-detect
$targetDomain = $domain;
if (!$targetDomain) {
    // Auto-detect domain by query keywords
    $qLower = strtolower($query);
    if (str_contains($qLower, 'color') || str_contains($qLower, 'palette')) {
        $targetDomain = 'color';
    } elseif (str_contains($qLower, 'font') || str_contains($qLower, 'typography')) {
        $targetDomain = 'typography';
    } elseif (str_contains($qLower, 'chart') || str_contains($qLower, 'graph')) {
        $targetDomain = 'chart';
    } elseif (str_contains($qLower, 'icon')) {
        $targetDomain = 'icons';
    } elseif (str_contains($qLower, 'landing') || str_contains($qLower, 'hero')) {
        $targetDomain = 'landing';
    } elseif (str_contains($qLower, 'ux') || str_contains($qLower, 'accessibility') || str_contains($qLower, 'button')) {
        $targetDomain = 'ux';
    } else {
        $targetDomain = 'style';
    }
}

if (!isset($csvConfig[$targetDomain])) {
    echo "Error: Unknown domain '{$targetDomain}'.\n";
    exit(1);
}

$cfg = $csvConfig[$targetDomain];
$filePath = $dataDir . DIRECTORY_SEPARATOR . $cfg['file'];
if (!file_exists($filePath)) {
    echo "Error: File '{$cfg['file']}' not found in {$dataDir}.\n";
    exit(1);
}

searchCsv($filePath, $query, $cfg['search_cols'], $cfg['output_cols'], $maxResults, "Domain: {$targetDomain}");

function searchCsv(string $file, string $query, array $searchCols, array $outputCols, int $max, string $context): void {
    $fp = fopen($file, 'r');
    if (!$fp) {
        echo "Error reading {$file}\n";
        return;
    }

    $headers = fgetcsv($fp);
    if (!$headers) {
        fclose($fp);
        return;
    }

    $headerMap = array_flip($headers);
    $queryTerms = preg_split('/\s+/', strtolower($query), -1, PREG_SPLIT_NO_EMPTY);

    $scores = [];
    $rows = [];
    $rowIdx = 0;

    while (($row = fgetcsv($fp)) !== false) {
        $rowIdx++;
        $textToSearch = '';
        foreach ($searchCols as $sc) {
            if (isset($headerMap[$sc]) && isset($row[$headerMap[$sc]])) {
                $textToSearch .= ' ' . strtolower($row[$headerMap[$sc]]);
            }
        }

        $score = 0;
        foreach ($queryTerms as $term) {
            $count = substr_count($textToSearch, $term);
            if ($count > 0) {
                $score += ($count * 2);
            }
        }

        if ($score > 0 || empty($query)) {
            $scores[$rowIdx] = $score;
            $rows[$rowIdx] = $row;
        }
    }
    fclose($fp);

    arsort($scores);
    $topRows = array_slice(array_keys($scores), 0, $max);

    echo "## UI Pro Max Search Results ({$context} | Query: '{$query}')\n\n";

    if (empty($topRows)) {
        echo "No exact matches found. Showing first {$max} guidelines:\n\n";
        // fallback to first rows
        $topRows = array_slice(array_keys($rows), 0, $max);
    }

    $resultNum = 1;
    foreach ($topRows as $idx) {
        if (!isset($rows[$idx])) continue;
        $row = $rows[$idx];
        echo "### Result {$resultNum}\n";
        foreach ($outputCols as $col) {
            if (isset($headerMap[$col]) && !empty($row[$headerMap[$col]])) {
                $val = trim($row[$headerMap[$col]]);
                if (strlen($val) > 300) {
                    $val = substr($val, 0, 300) . '...';
                }
                echo "- **{$col}:** {$val}\n";
            }
        }
        echo "\n";
        $resultNum++;
    }
}
