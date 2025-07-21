<?php
namespace backend\models;

use yii\base\Model;
use yii\data\ActiveDataProvider;
use common\models\Route;

class RouteSearch extends Route
{
    public function rules()
    {
        return [
            [['id'], 'integer'],
            [['origin', 'destination'], 'safe'],
        ];
    }

    public function scenarios()
    {
        return Model::scenarios();
    }

    public function search($params)
    {
        $query = Route::find();
        $dataProvider = new ActiveDataProvider([
            'query' => $query,
        ]);
        $this->load($params);
        if (!$this->validate()) {
            return $dataProvider;
        }
        $query->andFilterWhere(['id' => $this->id]);
        $query->andFilterWhere(['like', 'origin', $this->origin])
              ->andFilterWhere(['like', 'destination', $this->destination]);
        return $dataProvider;
    }
} 