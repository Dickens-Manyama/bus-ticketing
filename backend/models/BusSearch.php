<?php
namespace backend\models;

use yii\base\Model;
use yii\data\ActiveDataProvider;
use common\models\Bus;

class BusSearch extends Bus
{
    public function rules()
    {
        return [
            [['id', 'seat_count'], 'integer'],
            [['type', 'plate_number'], 'safe'],
        ];
    }

    public function scenarios()
    {
        return Model::scenarios();
    }

    public function search($params)
    {
        $query = Bus::find();
        $dataProvider = new ActiveDataProvider([
            'query' => $query,
        ]);
        $this->load($params);
        if (!$this->validate()) {
            return $dataProvider;
        }
        $query->andFilterWhere([
            'id' => $this->id,
            'seat_count' => $this->seat_count,
        ]);
        $query->andFilterWhere(['like', 'type', $this->type])
              ->andFilterWhere(['like', 'plate_number', $this->plate_number]);
        return $dataProvider;
    }
} 