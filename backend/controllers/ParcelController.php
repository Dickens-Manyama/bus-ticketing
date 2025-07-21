<?php
namespace backend\controllers;

use Yii;
use yii\web\Controller;
use yii\filters\AccessControl;
use yii\web\NotFoundHttpException;
use common\models\Parcel;
use backend\models\ParcelSearch;

class ParcelController extends Controller
{
    public function behaviors()
    {
        return [
            'access' => [
                'class' => AccessControl::class,
                'rules' => [
                    [
                        'allow' => true,
                        'roles' => ['@'],
                        'matchCallback' => function () {
                            $role = Yii::$app->user->identity->role ?? null;
                            return in_array($role, ['admin', 'superadmin']);
                        }
                    ],
                ],
            ],
        ];
    }

    public function actionIndex()
    {
        $searchModel = new ParcelSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);
        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    public function actionCreate()
    {
        $model = new Parcel();
        
        // Debug: Log the request
        Yii::info('Create action called. POST data: ' . json_encode(Yii::$app->request->post()), 'parcel');
        
        if ($model->load(Yii::$app->request->post())) {
            // Debug: Log the loaded model data
            Yii::info('Model loaded with data: ' . json_encode($model->attributes), 'parcel');
            
            // Always calculate price server-side
            $model->price = $model->getCalculatedPrice();
            $model->tracking_number = 'PARCEL' . date('Ymd') . rand(1000, 9999);
            $model->status = Parcel::STATUS_PENDING;
            $model->payment_status = Parcel::PAYMENT_STATUS_PENDING;
            
            // Set default values if not provided
            if (empty($model->parcel_category)) {
                $model->parcel_category = Parcel::CATEGORY_OTHER;
            }
            if (empty($model->departure_date)) {
                $model->departure_date = time();
            }
            
            // Debug: Log the final model data before save
            Yii::info('Final model data before save: ' . json_encode($model->attributes), 'parcel');
            
            if ($model->save()) {
                Yii::$app->session->setFlash('success', 'Parcel created successfully!');
                Yii::info('Parcel saved with ID: ' . $model->id . '. Redirecting to preview.', 'parcel');
                return $this->redirect(['preview', 'id' => $model->id]);
            } else {
                Yii::$app->session->setFlash('error', 'Failed to create parcel. Please check the form.');
                Yii::error('Parcel save errors: ' . json_encode($model->errors));
            }
        } else {
            // Debug: Log if model didn't load
            Yii::info('Model did not load from POST data', 'parcel');
        }
        
        return $this->render('create', ['model' => $model]);
    }

    public function actionPreview($id)
    {
        $model = $this->findModel($id);
        return $this->render('preview', ['model' => $model]);
    }

    public function actionPayment($id)
    {
        $model = $this->findModel($id);
        if ($model->payment_status === Parcel::PAYMENT_STATUS_PAID) {
            Yii::$app->session->setFlash('info', 'This parcel has already been paid.');
            return $this->redirect(['receipt', 'id' => $model->id]);
        }
        if (Yii::$app->request->isPost) {
            $model->payment_status = Parcel::PAYMENT_STATUS_PAID;
            $model->payment_date = time();
            $model->status = Parcel::STATUS_CONFIRMED;
            if ($model->save()) {
                Yii::$app->session->setFlash('success', 'Payment successful.');
                return $this->redirect(['receipt', 'id' => $model->id]);
            }
        }
        return $this->render('payment', ['model' => $model]);
    }

    public function actionReceipt($id)
    {
        $model = $this->findModel($id);
        return $this->render('receipt', ['model' => $model]);
    }

    public function actionTest()
    {
        Yii::$app->session->setFlash('success', 'Test action working!');
        return $this->redirect(['index']);
    }

    protected function findModel($id)
    {
        if (($model = Parcel::findOne($id)) !== null) {
            return $model;
        }
        throw new NotFoundHttpException('The requested parcel does not exist.');
    }
} 