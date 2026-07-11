<?php

namespace app\models;

use yii\base\Model;
use yii\data\ActiveDataProvider;

class UserSearch extends User
{
    public string $full_name = '';

    public function rules(): array
    {
        return [
            [['id', 'status', 'is_first_login', 'login_attempts'], 'integer'],
            [['username', 'remarks', 'activation_pas_expires_at', 'last_login_at', 'blocked_banned_at', 'full_name'], 'safe'],
        ];
    }

    public function scenarios(): array
    {
        return Model::scenarios();
    }

    public function search($params)
    {
        $query = User::find()->alias('u')->joinWith(['profile p']);

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
            'sort' => [
                'defaultOrder' => ['id' => SORT_DESC],
                'attributes' => [
                    'id',
                    'username',
                    'status',
                    'is_first_login',
                    'login_attempts',
                    'last_login_at',
                    'activation_pas_expires_at',
                    'full_name' => [
                        'asc' => ['p.first_name' => SORT_ASC, 'p.other_names' => SORT_ASC],
                        'desc' => ['p.first_name' => SORT_DESC, 'p.other_names' => SORT_DESC],
                    ],
                ],
            ],
        ]);

        $this->load($params);

        if (!$this->validate()) {
            return $dataProvider;
        }

        $query->andFilterWhere([
            'u.id' => $this->id,
            'u.status' => $this->status,
            'u.is_first_login' => $this->is_first_login,
            'u.login_attempts' => $this->login_attempts,
        ]);

        $query->andFilterWhere(['like', 'u.username', $this->username])
            ->andFilterWhere(['like', 'u.remarks', $this->remarks]);

        if (!empty($this->full_name)) {
            $query->andWhere(['or',
                ['like', 'p.first_name', $this->full_name],
                ['like', 'p.other_names', $this->full_name],
            ]);
        }

        return $dataProvider;
    }
}
