<?php

namespace common\modules\monochrome\members\models;

use Yii;
use common\modules\monochrome\members\Members;
use yii\data\ActiveDataProvider;

class SearchVendorUserSuper extends VendorUser
{
    public function rules()
    {
        return [
            [['username', 'account', 'vid'], 'safe'],
        ];
    }

    public function search($params)
    {
        $query = self::find()->filterWhere([
            'login.vendor' => ['$exists' => true],
            'status' => ['$ne' => VendorUser::STATUS_DELETED]
        ]);

        // create data provider
        $dataProvider = new ActiveDataProvider([
            'query' => $query,
        ]);

        if (!($this->load($params) && $this->validate())) {
            return $dataProvider;
        }

        $query->andFilterWhere(['like','login.vendor.username', $this->username == null ? null : (string)$this->username])
                ->andFilterWhere(['like','login.vendor.account', $this->account == null ? null : (string)$this->account])
                ->andFilterWhere(['login.vendor.vid' => $this->vid == null ? null : (string)$this->vid]);

        return $dataProvider;
    }
}
