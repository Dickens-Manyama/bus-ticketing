<?php

namespace console\controllers;

use Yii;
use yii\console\Controller;
use yii\console\ExitCode;
use yii\helpers\Console;
use common\models\Bus;
use common\models\Seat;

class SeatController extends Controller
{
    /**
     * Regenerate seats for all buses with proper numbering
     */
    public function actionRegenerate()
    {
        $this->stdout("Starting seat regeneration for all buses...\n", Console::FG_YELLOW);
        
        $buses = Bus::find()->all();
        $totalBuses = count($buses);
        $processedBuses = 0;
        
        foreach ($buses as $bus) {
            $this->stdout("Processing bus: {$bus->plate_number} ({$bus->type}) - {$bus->seat_count} seats\n", Console::FG_CYAN);
            
            // Delete existing seats for this bus
            Seat::deleteAll(['bus_id' => $bus->id]);
            $this->stdout("  - Deleted existing seats\n", Console::FG_GREEN);
            
            // Generate new seats with proper numbering
            $layout = $bus->getSeatingLayout();
            $seatNumber = 1;
            $seatsCreated = 0;
            
            for ($row = 1; $row <= $layout['rows']; $row++) {
                foreach ($layout['pattern'] as $block) {
                    if ($block !== 'aisle') {
                        for ($i = 0; $i < $block; $i++) {
                            if ($seatNumber <= $bus->seat_count) {
                                $seat = new Seat();
                                $seat->bus_id = $bus->id;
                                $seat->seat_number = (string)$seatNumber;
                                $seat->status = 'available';
                                $seat->created_at = time();
                                $seat->updated_at = time();
                                
                                if ($seat->save()) {
                                    $seatsCreated++;
                                } else {
                                    $this->stderr("  - Error creating seat {$seatNumber}: " . json_encode($seat->errors) . "\n", Console::FG_RED);
                                }
                            }
                            $seatNumber++;
                        }
                    }
                }
            }
            
            $this->stdout("  - Created {$seatsCreated} seats\n", Console::FG_GREEN);
            $processedBuses++;
        }
        
        $this->stdout("\nSeat regeneration completed!\n", Console::FG_GREEN);
        $this->stdout("Processed {$processedBuses} buses out of {$totalBuses}\n", Console::FG_YELLOW);
        
        return ExitCode::OK;
    }
    
    /**
     * Validate seat numbering for all buses
     */
    public function actionValidate()
    {
        $this->stdout("Validating seat numbering for all buses...\n", Console::FG_YELLOW);
        
        $buses = Bus::find()->all();
        $totalBuses = count($buses);
        $validBuses = 0;
        $invalidBuses = 0;
        
        foreach ($buses as $bus) {
            $this->stdout("Validating bus: {$bus->plate_number} ({$bus->type})\n", Console::FG_CYAN);
            
            $seats = Seat::find()->where(['bus_id' => $bus->id])->orderBy(['seat_number' => SORT_ASC])->all();
            $seatCount = count($seats);
            
            if ($seatCount !== $bus->seat_count) {
                $this->stderr("  - INVALID: Expected {$bus->seat_count} seats, found {$seatCount}\n", Console::FG_RED);
                $invalidBuses++;
                continue;
            }
            
            // Check seat numbering
            $expectedSeatNumbers = range(1, $bus->seat_count);
            $actualSeatNumbers = array_map(function($seat) {
                return (int)$seat->seat_number;
            }, $seats);
            
            if ($expectedSeatNumbers !== $actualSeatNumbers) {
                $this->stderr("  - INVALID: Seat numbering mismatch\n", Console::FG_RED);
                $this->stderr("    Expected: " . implode(', ', $expectedSeatNumbers) . "\n", Console::FG_RED);
                $this->stderr("    Actual: " . implode(', ', $actualSeatNumbers) . "\n", Console::FG_RED);
                $invalidBuses++;
            } else {
                $this->stdout("  - VALID: {$seatCount} seats with correct numbering\n", Console::FG_GREEN);
                $validBuses++;
            }
        }
        
        $this->stdout("\nValidation completed!\n", Console::FG_GREEN);
        $this->stdout("Valid buses: {$validBuses}\n", Console::FG_GREEN);
        $this->stdout("Invalid buses: {$invalidBuses}\n", Console::FG_RED);
        
        return ExitCode::OK;
    }
    
    /**
     * Show seat layout for a specific bus
     */
    public function actionLayout($busId)
    {
        $bus = Bus::findOne($busId);
        if (!$bus) {
            $this->stderr("Bus not found with ID: {$busId}\n", Console::FG_RED);
            return ExitCode::UNSPECIFIED_ERROR;
        }
        
        $this->stdout("Seat layout for bus: {$bus->plate_number} ({$bus->type})\n", Console::FG_YELLOW);
        $this->stdout("Total seats: {$bus->seat_count}\n", Console::FG_CYAN);
        $this->stdout("Seating config: {$bus->seating_config}\n", Console::FG_CYAN);
        
        $layout = $bus->getSeatingLayout();
        $this->stdout("Layout configuration:\n", Console::FG_CYAN);
        $this->stdout("  - Rows: {$layout['rows']}\n");
        $this->stdout("  - Columns: {$layout['cols']}\n");
        $this->stdout("  - Pattern: " . implode(' | ', $layout['pattern']) . "\n");
        $this->stdout("  - Seats per row: {$layout['seats_per_row']}\n");
        
        $seats = Seat::find()->where(['bus_id' => $bus->id])->orderBy(['seat_number' => SORT_ASC])->all();
        $this->stdout("\nSeat numbers: " . implode(', ', array_map(function($seat) {
            return $seat->seat_number;
        }, $seats)) . "\n", Console::FG_GREEN);
        
        return ExitCode::OK;
    }
} 