<?php

namespace app\models\settings;

use yii\base\Model;
use yii\data\ActiveDataProvider;

/**
 * SmsTemplateSearch represents the model behind the search form of `app\models\settings\SmsTemplate`.
 */
class SmsTemplateSearch extends SmsTemplate
{
    public function rules()
    {
        return [
            [['id', 'status', 'created_by', 'updated_by'], 'integer'],
            [['name', 'description', 'template', 'created_at', 'updated_at'], 'safe'],
        ];
    }

    public function scenarios()
    {
        return Model::scenarios();
    }


    



    public function search($params, $formName = null)
    {
        $query = SmsTemplate::find();

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
        ]);

        $this->load($params, $formName);

        if (!$this->validate()) {
            return $dataProvider;
        }

        $query->andFilterWhere([
            'id' => $this->id,
            'status' => $this->status,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
            'created_by' => $this->created_by,
            'updated_by' => $this->updated_by,
        ]);

        $query->andFilterWhere(['like', 'name', $this->name])
            ->andFilterWhere(['like', 'description', $this->description])
            ->andFilterWhere(['like', 'template', $this->template]);

        return $dataProvider;
    }
}
