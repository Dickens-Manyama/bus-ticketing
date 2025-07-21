<?php
/**
 * Check for duplicate migration files
 */

echo "=== DUPLICATE MIGRATION FILES CHECK ===\n\n";

$migrationDir = __DIR__ . '/console/migrations/';
$migrationFiles = glob($migrationDir . '*.php');

if (empty($migrationFiles)) {
    echo "No migration files found.\n";
    exit;
}

echo "1. Migration Files Found: " . count($migrationFiles) . "\n\n";

// Check for duplicate filenames
echo "2. Filename Duplicates Check:\n";
$filenames = [];
$duplicateFilenames = [];

foreach ($migrationFiles as $file) {
    $filename = basename($file);
    if (isset($filenames[$filename])) {
        $duplicateFilenames[] = $filename;
    } else {
        $filenames[$filename] = $file;
    }
}

if (empty($duplicateFilenames)) {
    echo "   ✅ No duplicate filenames found\n";
} else {
    echo "   ❌ Duplicate filenames found:\n";
    foreach ($duplicateFilenames as $filename) {
        echo "      - $filename\n";
    }
}
echo "\n";

// Check for duplicate class names
echo "3. Class Name Duplicates Check:\n";
$classNames = [];
$duplicateClassNames = [];
$classToFile = [];

foreach ($migrationFiles as $file) {
    $content = file_get_contents($file);
    if (preg_match('/class\s+(\w+)\s+extends\s+Migration/', $content, $matches)) {
        $className = $matches[1];
        $filename = basename($file);
        
        if (isset($classNames[$className])) {
            $duplicateClassNames[] = $className;
        } else {
            $classNames[$className] = $file;
        }
        
        $classToFile[$className] = $filename;
    }
}

if (empty($duplicateClassNames)) {
    echo "   ✅ No duplicate class names found\n";
} else {
    echo "   ❌ Duplicate class names found:\n";
    foreach ($duplicateClassNames as $className) {
        echo "      - $className\n";
    }
}
echo "\n";

// Check for duplicate content (exact file duplicates)
echo "4. Content Duplicates Check:\n";
$fileHashes = [];
$duplicateContent = [];

foreach ($migrationFiles as $file) {
    $content = file_get_contents($file);
    $hash = md5($content);
    
    if (isset($fileHashes[$hash])) {
        $duplicateContent[] = [
            'file1' => basename($fileHashes[$hash]),
            'file2' => basename($file),
            'hash' => $hash
        ];
    } else {
        $fileHashes[$hash] = $file;
    }
}

if (empty($duplicateContent)) {
    echo "   ✅ No duplicate content found\n";
} else {
    echo "   ❌ Duplicate content found:\n";
    foreach ($duplicateContent as $duplicate) {
        echo "      - {$duplicate['file1']} and {$duplicate['file2']}\n";
    }
}
echo "\n";

// Check for similar content (partial duplicates)
echo "5. Similar Content Check:\n";
$similarFiles = [];

foreach ($migrationFiles as $i => $file1) {
    $content1 = file_get_contents($file1);
    $lines1 = explode("\n", $content1);
    
    for ($j = $i + 1; $j < count($migrationFiles); $j++) {
        $file2 = $migrationFiles[$j];
        $content2 = file_get_contents($file2);
        $lines2 = explode("\n", $content2);
        
        // Calculate similarity percentage
        $commonLines = array_intersect($lines1, $lines2);
        $totalLines = max(count($lines1), count($lines2));
        $similarity = (count($commonLines) / $totalLines) * 100;
        
        if ($similarity > 80) { // More than 80% similar
            $similarFiles[] = [
                'file1' => basename($file1),
                'file2' => basename($file2),
                'similarity' => round($similarity, 2)
            ];
        }
    }
}

if (empty($similarFiles)) {
    echo "   ✅ No highly similar files found\n";
} else {
    echo "   ⚠️  Similar files found (>80% similarity):\n";
    foreach ($similarFiles as $similar) {
        echo "      - {$similar['file1']} and {$similar['file2']} ({$similar['similarity']}% similar)\n";
    }
}
echo "\n";

// Check for timestamp conflicts
echo "6. Timestamp Conflicts Check:\n";
$timestamps = [];
$conflicts = [];

foreach ($migrationFiles as $file) {
    $filename = basename($file);
    if (preg_match('/m(\d{6})_(\d{6})/', $filename, $matches)) {
        $timestamp = $matches[1] . $matches[2];
        
        if (isset($timestamps[$timestamp])) {
            $conflicts[] = [
                'timestamp' => $timestamp,
                'file1' => $timestamps[$timestamp],
                'file2' => $filename
            ];
        } else {
            $timestamps[$timestamp] = $filename;
        }
    }
}

if (empty($conflicts)) {
    echo "   ✅ No timestamp conflicts found\n";
} else {
    echo "   ❌ Timestamp conflicts found:\n";
    foreach ($conflicts as $conflict) {
        echo "      - {$conflict['file1']} and {$conflict['file2']} (timestamp: {$conflict['timestamp']})\n";
    }
}
echo "\n";

// Summary
echo "7. Summary:\n";
$totalIssues = count($duplicateFilenames) + count($duplicateClassNames) + count($duplicateContent) + count($conflicts);

if ($totalIssues === 0) {
    echo "   ✅ No duplicate issues found. All migration files are unique.\n";
} else {
    echo "   ⚠️  Found $totalIssues potential duplicate issues.\n";
}

echo "\n=== DUPLICATE CHECK COMPLETED ===\n"; 