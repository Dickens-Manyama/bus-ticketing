<?php
namespace backend\models;

use yii\base\Model;
use yii\data\ActiveDataProvider;
use common\models\User;

class UserSearch extends User
{
    public $created_date_from;
    public $created_date_to;

    public function rules()
    {
        return [
            [['id', 'status'], 'integer'],
            [['username', 'email', 'role', 'profile_picture'], 'safe'],
            [['created_date_from', 'created_date_to'], 'safe'],
        ];
    }

    public function scenarios()
    {
        return Model::scenarios();
    }

    public function search($params)
    {
        $query = User::find();
        $dataProvider = new ActiveDataProvider([
            'query' => $query,
            'sort' => [
                'defaultOrder' => ['created_at' => SORT_DESC],
            ],
            'pagination' => [
                'pageSize' => 20,
            ],
        ]);
        
        $this->load($params);
        
        if (!$this->validate()) {
            return $dataProvider;
        }

        // Basic filters
        $query->andFilterWhere(['id' => $this->id])
              ->andFilterWhere(['status' => $this->status])
              ->andFilterWhere(['like', 'username', $this->username])
              ->andFilterWhere(['like', 'email', $this->email])
              ->andFilterWhere(['like', 'role', $this->role]);

        // Date range filter
        if ($this->created_date_from) {
            $query->andWhere(['>=', 'created_at', strtotime($this->created_date_from . ' 00:00:00')]);
        }
        if ($this->created_date_to) {
            $query->andWhere(['<=', 'created_at', strtotime($this->created_date_to . ' 23:59:59')]);
        }

        return $dataProvider;
    }

    public function attributeLabels()
    {
        return array_merge(parent::attributeLabels(), [
            'created_date_from' => 'Created From',
            'created_date_to' => 'Created To',
        ]);
    }
} 