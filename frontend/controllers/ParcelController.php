<?php
namespace frontend\controllers;

use Yii;
use yii\web\Controller;
use yii\filters\AccessControl;
use yii\web\NotFoundHttpException;
use common\models\Parcel;

class ParcelController extends Controller
{
    public function behaviors()
    {
        return [
            'access' => [
                'class' => AccessControl::class,
                'only' => ['create', 'preview', 'payment', 'receipt'],
                'rules' => [
                    [
                        'allow' => true,
                        'roles' => ['@'],
                    ],
                ],
            ],
        ];
    }

    public function actionCreate()
    {
        $model = new Parcel();
        if ($model->load(Yii::$app->request->post())) {
            $model->user_id = Yii::$app->user->id;
            $model->price = $model->getCalculatedPrice();
            $model->tracking_number = 'PARCEL' . date('Ymd') . rand(1000, 9999);
            $model->status = Parcel::STATUS_PENDING;
            $model->payment_status = Parcel::PAYMENT_STATUS_PENDING;
            if ($model->save()) {
                return $this->redirect(['preview', 'id' => $model->id]);
            }
        }
        return $this->render('create', ['model' => $model]);
    }

    public function actionPreview($id)
    {
        $model = $this->findModel($id);
        if ($model->user_id !== Yii::$app->user->id) {
            throw new NotFoundHttpException('The requested parcel does not exist.');
        }
        return $this->render('preview', ['model' => $model]);
    }

    public function actionPayment($id)
    {
        $model = $this->findModel($id);
        if ($model->user_id !== Yii::$app->user->id) {
            throw new NotFoundHttpException('The requested parcel does not exist.');
        }
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
        if ($model->user_id !== Yii::$app->user->id) {
            throw new NotFoundHttpException('The requested parcel does not exist.');
        }
        return $this->render('receipt', ['model' => $model]);
    }

    protected function findModel($id)
    {
        if (($model = Parcel::findOne($id)) !== null) {
            return $model;
        }
        throw new NotFoundHttpException('The requested parcel does not exist.');
    }
} 