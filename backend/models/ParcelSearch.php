<?php
namespace backend\models;

use yii\base\Model;
use yii\data\ActiveDataProvider;
use common\models\Parcel;

class ParcelSearch extends Parcel
{
    public function rules()
    {
        return [
            [['id', 'user_id', 'route_id'], 'integer'],
            [['tracking_number', 'parcel_type', 'parcel_category', 'status', 'payment_status', 'sender_name', 'recipient_name'], 'safe'],
        ];
    }

    public function scenarios()
    {
        return Model::scenarios();
    }

    public function search($params)
    {
        $query = Parcel::find();
        $dataProvider = new ActiveDataProvider([
            'query' => $query,
            'sort' => ['defaultOrder' => ['created_at' => SORT_DESC]],
        ]);
        $this->load($params);
        if (!$this->validate()) {
            return $dataProvider;
        }
        $query->andFilterWhere(['id' => $this->id, 'user_id' => $this->user_id, 'route_id' => $this->route_id]);
        $query->andFilterWhere(['like', 'tracking_number', $this->tracking_number])
            ->andFilterWhere(['like', 'parcel_type', $this->parcel_type])
            ->andFilterWhere(['like', 'parcel_category', $this->parcel_category])
            ->andFilterWhere(['like', 'status', $this->status])
            ->andFilterWhere(['like', 'payment_status', $this->payment_status])
            ->andFilterWhere(['like', 'sender_name', $this->sender_name])
            ->andFilterWhere(['like', 'recipient_name', $this->recipient_name]);
        return $dataProvider;
    }
} 