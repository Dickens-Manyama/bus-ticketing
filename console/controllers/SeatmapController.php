<?php
namespace console\controllers;

use yii\console\Controller;
use common\models\Bus;
use common\models\Seat;
use Yii;

class SeatmapController extends Controller
{
    /**
     * Regenerate seats for all Luxury, Semi-Luxury, and Middle Class buses.
     * Usage: php yii seatmap/regenerate-all
     */
    public function actionRegenerateAll()
    {
        $types = ['Luxury', 'Semi-Luxury', 'Middle Class'];
        $count = 0;
        foreach (Bus::find()->where(['type' => $types])->all() as $bus) {
            Bus::generateSeatsForBus($bus);
            $count++;
        }
        echo "Regenerated seats for $count buses.\n";
    }
} 