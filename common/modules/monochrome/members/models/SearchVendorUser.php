<?php

namespace common\modules\monochrome\members\models;

use Yii;
use common\modules\monochrome\members\Members;
use yii\data\ActiveDataProvider;

class SearchVendorUser extends VendorUser
{
    public function rules()
    {
        return [
            ['username', 'safe'],
        ];
    }

    public function search($params, $pid)
    {
        $query = self::find()->filterWhere([
            'pid' => $pid,
            'role' => ['$in' => [Yii::$app->getModule('members')->salesRoleName, Yii::$app->getModule('members')->managerRoleName]],
            'status' => VendorUser::STATUS_ACTIVE,
        ]);

        // create data provider
        $dataProvider = new ActiveDataProvider([
            'query' => $query,
            'pagination' => [
                'pageSize' => 5,
            ],
        ]);

        if (!($this->load($params) && $this->validate())) {
            return $dataProvider;
        }

        $query->andFilterWhere(['like','username', $this->username == null ? null : (string)$this->username]);

        return $dataProvider;
    }
}
