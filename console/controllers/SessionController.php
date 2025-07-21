<?php

namespace console\controllers;

use Yii;
use yii\console\Controller;

class SessionController extends Controller
{
    /**
     * Clear all sessions
     */
    public function actionClear()
    {
        $session = Yii::$app->session;
        $session->destroy();
        echo "All sessions cleared successfully.\n";
    }
    
    /**
     * Show current session status
     */
    public function actionStatus()
    {
        $session = Yii::$app->session;
        echo "Session ID: " . $session->getId() . "\n";
        echo "Session is active: " . ($session->isActive ? 'Yes' : 'No') . "\n";
        echo "Session timeout: " . ini_get('session.gc_maxlifetime') . " seconds\n";
    }
} 