<?php

namespace backend\modules\monochrome\trace\models;

use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use common\components\mark\DayRange;
use backend\modules\monochrome\trace\models\TraceLog;

/**
 * TraceLogSearch represents the model behind the search form about `backend\modules\monochrome\trace\models\TraceLog`.
 */
class TraceLogSearch extends TraceLog
{
    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['log_time', 'category', 'prefix', 'message'], 'safe'],
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
    public function search($params, $filterCondition)
    {
        $query = TraceLog::find()->andFilterWhere($filterCondition)->orderby('log_time DESC');

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
        ]);

        if (!($this->load($params) && $this->validate())) {
            return $dataProvider;
        }

        $query->andFilterWhere(['like', 'category', $this->category])
            ->andFilterWhere(['like', 'prefix', $this->prefix])
            ->andFilterWhere(['like', 'message', $this->message]);

        if ($this->log_time != null) {
            $query->andFilterWhere([
                '$and' => [
                    ['log_time' => ['$gte' => DayRange::getDayRange(strtotime($this->log_time))['begin']]],
                    ['log_time' => ['$lte' => DayRange::getDayRange(strtotime($this->log_time))['end']]],
                ]
            ]);
        }

        return $dataProvider;
    }
}
