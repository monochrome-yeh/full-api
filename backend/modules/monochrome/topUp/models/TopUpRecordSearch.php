<?php

namespace backend\modules\monochrome\topUp\models;

use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use backend\modules\monochrome\topUp\models\TopUpRecord;

/**
 * TopUpRecordSearch represents the model behind the search form about `backend\modules\monochrome\topUp\models\TopUpRecord`.
 */
class TopUpRecordSearch extends TopUpRecord
{
    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['vid', 'creator', 'price', 'month', 'status'], 'safe'],
        ];
    }

    /**
     * @inheritdoc
     */
    public function scenarios()
    {
        // bypass scenarios() implementation in the parent class
        return Model::scenarios();
    }

    /**
     * Creates data provider instance with search query applied
     *
     * @param array $params
     *
     * @return ActiveDataProvider
     */
    public function search($params)
    {
        $query = TopUpRecord::find();

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
        ]);

        if (!($this->load($params) && $this->validate())) {
            return $dataProvider;
        }

        $query->andFilterWhere(['vid' => $this->vid])
            ->andFilterWhere(['creator' => $this->creator])
            ->andFilterWhere(['price' => $this->price == null ? null : (int)$this->price])
            ->andFilterWhere(['month' => $this->month == null ? null : (int)$this->month])
            ->andFilterWhere(['status' => $this->status == null ? null : (int)$this->status]);

        return $dataProvider;
    }
}
