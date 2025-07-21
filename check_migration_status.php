<?php
/**
 * Check migration files and their status
 */

// MySQL PDO connection removed

echo "=== MIGRATION FILES STATUS CHECK ===\n\n";

// Get applied migrations from database
// $stmt = $pdo->query("SELECT version FROM migration ORDER BY apply_time");
// $appliedMigrations = $stmt->fetchAll(PDO::FETCH_COLUMN);

// echo "1. Applied Migrations in Database:\n";
// foreach ($appliedMigrations as $migration) {
//     echo "   ✅ $migration\n";
// }
// echo "\n";

// Get all migration files
$migrationFiles = glob(__DIR__ . '/console/migrations/*.php');
$migrationFileNames = [];

foreach ($migrationFiles as $file) {
    $migrationFileNames[] = basename($file, '.php');
}

// Sort migration files by timestamp
sort($migrationFileNames);

echo "2. Available Migration Files:\n";
foreach ($migrationFileNames as $migration) {
    // if (in_array($migration, $appliedMigrations)) {
    //     echo "   ✅ $migration (APPLIED)\n";
    // } else {
        echo "   ⏳ $migration (PENDING)\n";
    // }
}
echo "\n";

// Check for missing migrations
echo "3. Migration Analysis:\n";
// $pendingMigrations = array_diff($migrationFileNames, $appliedMigrations);
// $orphanedMigrations = array_diff($appliedMigrations, $migrationFileNames);

// if (empty($pendingMigrations)) {
//     echo "   ✅ All migration files have been applied\n";
// } else {
//     echo "   ⏳ Pending migrations (" . count($pendingMigrations) . "):\n";
//     foreach ($pendingMigrations as $migration) {
//         echo "     - $migration\n";
//     }
// }

// if (!empty($orphanedMigrations)) {
//     echo "   ⚠️  Orphaned migrations in database (" . count($orphanedMigrations) . "):\n";
//     foreach ($orphanedMigrations as $migration) {
//         echo "     - $migration (file not found)\n";
//     }
// }
echo "\n";

// Check migration file details
echo "4. Migration File Details:\n";
foreach ($migrationFileNames as $migration) {
    $filePath = __DIR__ . '/console/migrations/' . $migration . '.php';
    $fileSize = filesize($filePath);
    $fileTime = date('Y-m-d H:i:s', filemtime($filePath));
    
    // Extract class name from file
    $fileContent = file_get_contents($filePath);
    if (preg_match('/class\s+(\w+)\s+extends\s+Migration/', $fileContent, $matches)) {
        $className = $matches[1];
    } else {
        $className = 'Unknown';
    }
    
    // $status = in_array($migration, $appliedMigrations) ? 'APPLIED' : 'PENDING';
    echo "   📄 $migration\n";
    echo "      Class: $className\n";
    echo "      Size: " . number_format($fileSize) . " bytes\n";
    echo "      Modified: $fileTime\n";
    // echo "      Status: $status\n";
    echo "\n";
}

echo "=== MIGRATION STATUS CHECK COMPLETED ===\n";

// catch (PDOException $e) {
//     echo "Database connection failed: " . $e->getMessage() . "\n";
// } 