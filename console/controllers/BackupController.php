<?php
namespace console\controllers;

use Yii;
use yii\console\Controller;
use backend\models\BackupSchedule;

class BackupController extends Controller
{
    /**
     * Run scheduled backups for all plans due.
     */
    public function actionRun()
    {
        $now = time();
        $schedules = BackupSchedule::find()->where(['<=', 'next_run', $now])->all();
        foreach ($schedules as $schedule) {
            $this->stdout("Running backup for user #{$schedule->user_id} (plan: {$schedule->plan})...\n");
            $backupFile = $this->createBackup();
            if ($backupFile) {
                $schedule->last_run = $now;
                $schedule->next_run = $this->calculateNextRun($schedule->plan, $now);
                $schedule->updated_at = $now;
                $schedule->save(false);
                $this->stdout("Backup created: $backupFile\n");
            } else {
                $this->stderr("Backup failed for user #{$schedule->user_id}\n");
            }
        }
        $this->stdout("All due backups processed.\n");
    }

    /**
     * Create a database backup file in backend/web/backups/.
     * Returns the file path or false on failure.
     */
    protected function createBackup()
    {
        $backupDir = Yii::getAlias('@backend/web/backups');
        if (!is_dir($backupDir)) {
            mkdir($backupDir, 0777, true);
        }
        $filename = 'backup_' . date('Y-m-d_H-i-s') . '.sql';
        $filePath = $backupDir . DIRECTORY_SEPARATOR . $filename;
        $db = Yii::$app->db;
        $dsn = $db->dsn;
        preg_match('/host=([^;]+)/', $dsn, $hostMatch);
        preg_match('/dbname=([^;]+)/', $dsn, $dbMatch);
        $host = $hostMatch[1] ?? 'localhost';
        $dbname = $dbMatch[1] ?? '';
        $user = $db->username;
        $pass = $db->password;
        $cmd = "mysqldump -h$host -u$user -p$pass $dbname > \"$filePath\"";
        $result = null;
        system($cmd, $result);
        return file_exists($filePath) ? $filePath : false;
    }

    /**
     * Calculate the next run timestamp for a plan.
     */
    protected function calculateNextRun($plan, $from)
    {
        switch ($plan) {
            case 'daily':
                return strtotime('+1 day', $from);
            case 'weekly':
                return strtotime('+1 week', $from);
            case 'monthly':
                return strtotime('+1 month', $from);
            case 'yearly':
                return strtotime('+1 year', $from);
            case 'sixmonths':
                return strtotime('+6 months', $from);
            default:
                return $from;
        }
    }
} 