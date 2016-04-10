<?php

namespace backend\modules\monochrome\topUp\models;

use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use common\components\mark\DayRange;
use backend\modules\monochrome\topUp\models\ProjectTopUpLog;

/**
 * ProjectTopUpLogSearch represents the model behind the search form about `backend\modules\monochrome\topUp\models\ProjectTopUpLog`.
 */
class ProjectTopUpLogSearch extends ProjectTopUpLog
{
    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['vid', 'pid', 'creator', 'log_content', 'created_at'], 'safe'],
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
        $query = ProjectTopUpLog::find();

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
            'sort' => [
                'defaultOrder' => [
                    'created_at' => SORT_DESC,
                ],
            ],
        ]);

        if (!($this->load($params) && $this->validate())) {
            return $dataProvider;
        }

        $query->andFilterWhere(['vid' => $this->vid])
            ->andFilterWhere(['pid' => $this->pid])
            ->andFilterWhere(['creator' => $this->creator])
            ->andFilterWhere(['like', 'log_content', $this->log_content]);

        if ($this->created_at != null) {
            $query->andFilterWhere([
                '$and' => [
                    ['created_at' => ['$gte' => DayRange::getDayRange(strtotime($this->created_at))['begin']]],
                    ['created_at' => ['$lte' => DayRange::getDayRange(strtotime($this->created_at))['end']]],
                ]
            ]);
        }

        return $dataProvider;
    }
}
