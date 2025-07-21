<?php
namespace backend\controllers;

use Yii;
use yii\filters\AccessControl;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use backend\models\BackupSchedule;
use yii\filters\VerbFilter;

class BackupController extends Controller
{
    public function behaviors()
    {
        return [
            'access' => [
                'class' => AccessControl::class,
                'only' => ['index', 'create', 'delete'],
                'rules' => [
                    [
                        'allow' => true,
                        'roles' => ['@'],
                        'matchCallback' => function ($rule, $action) {
                            $role = Yii::$app->user->identity->role ?? null;
                            return in_array($role, ['admin', 'superadmin']);
                        }
                    ],
                ],
                'denyCallback' => function ($rule, $action) {
                    if (Yii::$app->user->isGuest) {
                        return Yii::$app->response->redirect(['site/login']);
                    } else {
                        throw new \yii\web\ForbiddenHttpException('You are not allowed to perform this action.');
                    }
                },
            ],
            'verbs' => [
                'class' => VerbFilter::class,
                'actions' => [
                    'delete' => ['POST'],
                ],
            ],
        ];
    }

    public function actionIndex()
    {
        $schedules = BackupSchedule::find()->orderBy(['created_at' => SORT_DESC])->all();
        return $this->render('index', [
            'schedules' => $schedules,
        ]);
    }

    public function actionCreate()
    {
        $model = new BackupSchedule();
        if ($model->load(Yii::$app->request->post())) {
            $model->user_id = Yii::$app->user->id;
            $model->created_at = time();
            $model->updated_at = time();
            $model->last_run = null;
            $model->next_run = $this->calculateNextRun($model->plan);
            if ($model->save()) {
                Yii::$app->session->setFlash('success', 'Backup schedule created.');
                return $this->redirect(['index']);
            } else {
                Yii::$app->session->setFlash('error', 'Failed to create backup schedule.');
            }
        }
        return $this->render('create', [
            'model' => $model,
        ]);
    }

    public function actionDelete($id)
    {
        $model = BackupSchedule::findOne($id);
        if (!$model) {
            Yii::$app->session->setFlash('error', 'Schedule not found.');
            return $this->redirect(['index']);
        }
        $model->delete();
        Yii::$app->session->setFlash('success', 'Backup schedule deleted.');
        return $this->redirect(['index']);
    }

    private function calculateNextRun($plan)
    {
        $now = time();
        switch ($plan) {
            case 'daily':
                return strtotime('+1 day', $now);
            case 'weekly':
                return strtotime('+1 week', $now);
            case 'monthly':
                return strtotime('+1 month', $now);
            case 'yearly':
                return strtotime('+1 year', $now);
            case 'sixmonths':
                return strtotime('+6 months', $now);
            default:
                return $now;
        }
    }
} 