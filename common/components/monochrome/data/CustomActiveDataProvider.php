<?php

namespace common\components\monochrome\data;

use yii\data\ActiveDataProvider;

class CustomActiveDataProvider extends ActiveDataProvider
{
	public $isSearch = [];

	private $_models;

	public function prepareModels($forcePrepare = false)
	{
		$this->_models = parent::prepareModels();
        $tmp = [];

        if (is_array($this->isSearch) && !empty($this->isSearch)) {
            foreach ($this->isSearch as $attribute => $searchValue) {
                if (!empty($searchValue)) {
                    foreach ($this->_models as $key => $model) {
                        if ($model->$attribute != $searchValue) {
                            $tmp[] = $key;
                        }
                    }
                }
            }

            foreach ($tmp as $value) {
                unset($this->_models[$value]);
            }
        }

    	return $this->_models;
    }

    // private function merge()
    // {
    //     $houseItems = [];
    //     $houseIds = [];
    //     $orders = [];

    //     foreach ($this->_models as $house) {
    //         $houseItems[(string)$house->_id] = $house;
    //         $houseIds[] = (string)$house->_id;
    //     }

    //     $orders = $this->mergeQuery->andWhere(['main_item' => ['$in' => $houseIds]])->all();
    //     foreach ($orders as $order) {
    //         if (array_key_exists((string)$order->main_item, $houseItems)) {
    //             $houseItems[(string)$order->main_item]->order = $order;
    //         }
    //     }

    //     return $houseItems;
    // }
}
