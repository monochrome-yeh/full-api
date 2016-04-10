<?php

namespace common\modules\monochrome\request\models;

use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use common\components\mark\DayRange;
use common\modules\monochrome\request\models\ToDoList;

/**
 * ToDoListSearch represents the model behind the search form about `common\modules\monochrome\request\models\ToDoList`.
 */
class ToDoListSearch extends ToDoList
{
    public $userStatus;

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            // [['deadline', 'content', 'status', 'created_at'], 'safe', 'on' => 'assign'],
            // [['deadline', 'assigner', 'content', 'userStatus', 'created_at'], 'safe', 'on' => 'toDoList'],
            [['content', 'status', 'created_at'], 'safe', 'on' => 'assign'],
            [['assigner', 'content', 'userStatus', 'created_at'], 'safe', 'on' => 'toDoList'],
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
        $query = ToDoList::find()->where(['assigner' => Yii::$app->user->getId()]);

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
            'sort' => [
                'attributes' => [
                    'created_at',
                    'content',
                ],
                'defaultOrder' => [
                    'created_at' => SORT_DESC,
                ],
            ],
        ]);

        if (!($this->load($params) && $this->validate())) {
            return $dataProvider;
        }

        // if ($this->deadline != null) {
        //     $query->andFilterWhere(['deadline' => strtotime($this->deadline)]);
        // }

        if ($this->created_at != null) {
            $query->andFilterWhere([
                '$and' => [
                    ['created_at' => ['$gte' => DayRange::getDayRange(strtotime($this->created_at))['begin']]],
                    ['created_at' => ['$lte' => DayRange::getDayRange(strtotime($this->created_at))['end']]],
                ]
            ]);
        }

        $query->andFilterWhere(['like', 'content', $this->content])
            ->andFilterWhere(['status' => $this->status == null ? null : (int)$this->status]);

        return $dataProvider;
    }

    /**
     * Creates data provider instance with searchToDoList query applied
     *
     * @param array $params
     *
     * @return ActiveDataProvider
     */
    public function searchToDoList($params)
    {
        $query = ToDoList::find()->where(['assign_users' => Yii::$app->user->getId()]);

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
            'sort' => [
                'attributes' => [
                    'created_at',
                    'content',
                    'status,'
                ],
                'defaultOrder' => [
                    'created_at' => SORT_DESC,
                ],
            ],
        ]);

        if (!($this->load($params) && $this->validate())) {
            return $dataProvider;
        }

        // if ($this->deadline != null) {
        //     $query->andFilterWhere(['deadline' => strtotime($this->deadline)]);
        // }

        if ($this->created_at != null) {
            $query->andFilterWhere([
                '$and' => [
                    ['created_at' => ['$gte' => DayRange::getDayRange(strtotime($this->created_at))['begin']]],
                    ['created_at' => ['$lte' => DayRange::getDayRange(strtotime($this->created_at))['end']]],
                ]
            ]);
        }

        $query->andFilterWhere(['assigner' => $this->assigner == null ? null : $this->assigner])
            ->andFilterWhere(['like', 'content', $this->content]);

        if ($this->userStatus !== '') {
            $id = Yii::$app->user->getId();
            switch ((int)$this->userStatus) {
                case ToDoList::STATUS_NOT_YET:
                    $query->andFilterWhere(['assign_users' => $id, 'done_users' => ['$ne' => $id]]);
                    break;
                case ToDoList::STATUS_DONE:
                    $query->andFilterWhere(['assign_users' => $id, 'done_users' => $id]);
                    break;
                default:
                    $query->andFilterWhere(['_id' => 'nonexistence']);
                    break;
            }
        }

        return $dataProvider;
    }
}
