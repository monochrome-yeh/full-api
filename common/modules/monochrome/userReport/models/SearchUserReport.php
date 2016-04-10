<?php

namespace common\modules\monochrome\userReport\models;

use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use common\modules\monochrome\userReport\models\UserReport;

/**
 * SearchUserReport represents the model behind the search form about `common\modules\monochrome\userReport\models\UserReport`.
 */
class SearchUserReport extends UserReport
{
    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['_id', 'uid', 'desc', 'type', 'created_at', 'updated_at'], 'safe'],
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
        $query = UserReport::find();

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
        ]);

        $this->load($params);

        if (!$this->validate()) {
            // uncomment the following line if you do not want to return any records when validation fails
            // $query->where('0=1');
            return $dataProvider;
        }

        $query->andFilterWhere(['like', '_id', $this->_id])
            ->andFilterWhere(['like', 'uid', $this->uid])
            ->andFilterWhere(['like', 'desc', $this->desc])
            ->andFilterWhere(['like', 'type', $this->type])
            ->andFilterWhere(['like', 'created_at', $this->created_at])
            ->andFilterWhere(['like', 'updated_at', $this->updated_at]);

        return $dataProvider;
    }
}
