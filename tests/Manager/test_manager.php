<?php

namespace Tests\Manager;

use Illuminate\Support\Facades\File;
use Illuminate\Support\Str;

class SmartTestManager
{
    protected $basePath;
    protected $historyFile;
    protected $ignoredFiles = ['ExampleTest.php', 'Pest.php'];

    public function __construct()
    {
        $this->basePath = base_path();
        $this->historyFile = base_path('.test_history.log');
    }

    public function getInventory()
    {
        // 1. Scan Scenarios (MD files)
        $scenarioFiles = glob($this->basePath . '/tests/TestCases/*.md');
        $modules = [];
        $totalDefinedScenarios = 0;
        $totalCodedCustomTests = 0;
        $mappedTestFiles = [];

        foreach ($scenarioFiles as $file) {
            $content = file_get_contents($file);
            $id = basename($file, '.md');
            $title = $this->extractTitle($content) ?? $id;
            $scenarios = substr_count($content, '- [ ]') + substr_count($content, '- [x]');
            if ($scenarios === 0) {
                // Try numeric list pattern: 1. [UI] or 1. [Validation]
                preg_match_all('/^\d+\.\s*\[/m', $content, $numericMatches);
                $scenarios = count($numericMatches[0]);
            }

            if ($scenarios === 0) {
                // Try "#### T01:" pattern
                preg_match_all('/^#{3,4}\s+T\d+:/m', $content, $tMatches);
                $scenarios = count($tMatches[0]);
            }

            // Find corresponding PHP Test file
            // Heuristic: OffersCreate -> tests/Feature/Offers/CreateOfferTest.php
            // We search for *CreateOfferTest.php or similar
            $testFile = $this->findTestFileForModule($id);
            $codedCount = 0;

            if ($testFile) {
                $codedCount = $this->countTestsInFile($testFile);
                $mappedTestFiles[] = realpath($testFile);
            }

            $modules[] = [
                'id' => $id,
                'name' => $title,
                'scenarios' => $scenarios,
                'coded_tests' => $codedCount,
                'test_file' => $testFile ? basename($testFile) : null
            ];

            $totalDefinedScenarios += $scenarios;
            $totalCodedCustomTests += $codedCount;
        }

        // 2. Scan for System/Other Tests
        // Find all feature tests
        $allTestFiles = $this->recursiveGlob($this->basePath . '/tests/Feature', '*.php');
        $systemTestsCount = 0;

        foreach ($allTestFiles as $file) {
            $realPath = realpath($file);
            if (in_array(basename($file), $this->ignoredFiles)) {
                continue; // Skip ignored
            }

            // If this file was already mapped to a module, skip it here
            if (in_array($realPath, $mappedTestFiles)) {
                continue;
            }

            $systemTestsCount += $this->countTestsInFile($file);
        }

        // Count default Example tests just for info (or group them into system)
        $exampleTestsCount = 0;
        if (file_exists($this->basePath . '/tests/Feature/ExampleTest.php')) {
            $exampleTestsCount = $this->countTestsInFile($this->basePath . '/tests/Feature/ExampleTest.php');
        }

        return [
            'modules' => $modules,
            'summary' => [
                'total_defined_scenarios' => $totalDefinedScenarios,
                'total_coded_custom_tests' => $totalCodedCustomTests,
                'system_tests' => $systemTestsCount + $exampleTestsCount
            ],
            'history' => $this->getHistory()
        ];
    }

    protected function findTestFileForModule($moduleId)
    {
        // Strategy 1: CreateOfferTest.php for OffersCreate
        // Convert OffersCreate to CreateOffer
        // Or simple search
        $searchName = $moduleId;

        // Try mapping commonly used names strategies
        // OffersCreate -> CreateOfferTest
        $parts = preg_split('/(?=[A-Z])/', $moduleId, -1, PREG_SPLIT_NO_EMPTY);
        $reversed = array_reverse($parts);
        $variation1 = implode('', $reversed) . 'Test.php'; // CreateOffersTest.php
        $variation2 = implode('', $parts) . 'Test.php';    // OffersCreateTest.php
        $variation3 = 'Create' . str_replace('Create', '', $moduleId) . 'Test.php'; // One off logic

        // Let's just fuzzy search in tests/Feature
        $files = $this->recursiveGlob($this->basePath . '/tests/Feature', '*Test.php');

        foreach ($files as $file) {
            $base = basename($file);
            // Search for exact moduleId matches or variations
            if ($moduleId === 'OffersCreate' && str_contains($base, 'CreateOfferTest')) {
                return $file;
            }
            if ($moduleId === 'AssetsTab' && str_contains($base, 'AssetFormTest')) {
                return $file;
            }
            if ($moduleId === 'ContactsTab' && str_contains($base, 'ContactFormTest')) {
                return $file;
            }
            if (str_contains($base, $moduleId)) {
                return $file;
            }
        }

        // Manual overrides
        if ($moduleId === 'ProjectManagement') {
            // Prefer ProjectCreateTest if available
            return $this->basePath . '/tests/Feature/Livewire/ProjectCreateTest.php';
        }

        if ($moduleId === 'SystemSmokeTest') {
            return $this->basePath . '/tests/Feature/System/SmokeTest.php';
        }

        return null;
    }

    protected function countTestsInFile($path)
    {
        if (!file_exists($path))
            return 0;
        $content = file_get_contents($path);

        // Count Pest 'test(' and 'it(' calls, avoiding Volt::test
        preg_match_all('/(?<!Volt::)(test|it)\s*\(/', $content, $matches);
        return count($matches[0]);
    }

    protected function recursiveGlob($dir, $pattern)
    {
        $files = glob($dir . '/' . $pattern);
        foreach (glob($dir . '/*', GLOB_ONLYDIR) as $subdir) {
            $files = array_merge($files, $this->recursiveGlob($subdir, $pattern));
        }
        return $files;
    }

    protected function extractTitle($content)
    {
        if (preg_match('/#\s+(.*)/', $content, $matches)) {
            return trim($matches[1]);
        }
        return null;
    }

    public function getHistory($limit = 5)
    {
        if (!file_exists($this->historyFile)) {
            return [];
        }

        $lines = file($this->historyFile, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
        $lines = array_reverse($lines);
        $history = [];

        foreach (array_slice($lines, 0, $limit) as $line) {
            $parts = explode('|', $line);
            if (count($parts) >= 3) {
                $history[] = [
                    'date' => $parts[0],
                    'module' => $parts[1],
                    'result' => $parts[2],
                    'details' => $parts[3] ?? ''
                ];
            }
        }

        return $history;
    }

    public function logResult($module, $status, $details = '')
    {
        $entry = sprintf(
            "%s|%s|%s|%s\n",
            date('Y-m-d H:i:s'),
            $module,
            $status,
            $details
        );

        file_put_contents($this->historyFile, $entry, FILE_APPEND);
    }
}

// Simple CLI Router
if (php_sapi_name() === 'cli' && isset($argv[1])) {
    require __DIR__ . '/../../vendor/autoload.php';
    $app = require_once __DIR__ . '/../../bootstrap/app.php';
    $kernel = $app->make(\Illuminate\Contracts\Console\Kernel::class);
    $kernel->bootstrap();

    $manager = new SmartTestManager();

    if ($argv[1] === 'inventory') {
        echo json_encode($manager->getInventory(), JSON_PRETTY_PRINT);
    }

    if ($argv[1] === 'log' && isset($argv[2])) {
        $module = $argv[2];
        $status = $argv[3] ?? 'UNKNOWN';
        $details = $argv[4] ?? '';
        $manager->logResult($module, $status, $details);
    }
}
