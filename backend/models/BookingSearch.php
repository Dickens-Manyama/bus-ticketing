<?php
namespace backend\models;

use yii\base\Model;
use yii\data\ActiveDataProvider;
use common\models\Booking;

class BookingSearch extends Booking
{
    public function rules()
    {
        return [
            [['id', 'user_id', 'bus_id', 'route_id', 'seat_id'], 'integer'],
            [['payment_info', 'status', 'payment_method', 'payment_status', 'ticket_status'], 'safe'],
        ];
    }

    public function scenarios()
    {
        return Model::scenarios();
    }

    public function search($params)
    {
        $query = Booking::find();
        $dataProvider = new ActiveDataProvider([
            'query' => $query,
        ]);
        $this->load($params);
        if (!$this->validate()) {
            return $dataProvider;
        }
        $query->andFilterWhere([
            'id' => $this->id,
            'user_id' => $this->user_id,
            'bus_id' => $this->bus_id,
            'route_id' => $this->route_id,
            'seat_id' => $this->seat_id,
            // 'payment_status' => $this->payment_status, // Removed because 'payment_status' does not exist
            'ticket_status' => $this->ticket_status,
        ]);
        $query->andFilterWhere(['like', 'payment_info', $this->payment_info]);
        return $dataProvider;
    }
} 