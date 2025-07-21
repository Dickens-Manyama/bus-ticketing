<?php

namespace console\controllers;

use yii\console\Controller;
use yii\console\ExitCode;
use yii\helpers\Console;

class BookingController extends Controller
{
    public function actionCheckColumns()
    {
        $db = \Yii::$app->db;
        $schema = $db->getSchema();

        // Get table schema
        $tableSchema = $schema->getTableSchema('booking');

        if ($tableSchema) {
            $this->stdout("Booking table columns:\n", Console::FG_GREEN);
            foreach ($tableSchema->columns as $columnName => $column) {
                $this->stdout("- $columnName: " . $column->type . "\n");
            }
            
            // Check for specific columns
            $hasTicketStatus = isset($tableSchema->columns['ticket_status']);
            $hasScannedAt = isset($tableSchema->columns['scanned_at']);
            $hasScannedBy = isset($tableSchema->columns['scanned_by']);
            
            $this->stdout("\nColumn check:\n", Console::FG_YELLOW);
            $this->stdout("ticket_status: " . ($hasTicketStatus ? "✓" : "✗") . "\n");
            $this->stdout("scanned_at: " . ($hasScannedAt ? "✓" : "✗") . "\n");
            $this->stdout("scanned_by: " . ($hasScannedBy ? "✓" : "✗") . "\n");
            
            if ($hasTicketStatus && $hasScannedAt && $hasScannedBy) {
                $this->stdout("\n✅ All required columns exist!\n", Console::FG_GREEN);
            } else {
                $this->stdout("\n❌ Missing required columns!\n", Console::FG_RED);
            }
        } else {
            $this->stdout("❌ Booking table not found!\n", Console::FG_RED);
        }

        return ExitCode::OK;
    }
} 