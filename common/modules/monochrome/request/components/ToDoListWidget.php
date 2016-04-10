<?php

namespace common\modules\monochrome\request\components;

use Yii;
use yii\base\Widget;
use common\modules\monochrome\request\models\ToDoList;

class ToDoListWidget extends Widget
{
    private $_items = [];

    public $number = 5;
    public $roleScope;

	public function init()
	{
		parent::init();

        if (!is_int($this->number)) {
            throw new \Exception('Invalid Argument');
        } else {
            if ($this->number > 0) {
                $myId = Yii::$app->user->getId();
                $myAssigners = ToDoList::getAssigners();
                foreach (ToDoList::find()->andWhere(['assign_users' => $myId, 'done_users' => ['$ne' => $myId]])->asArray()->orderby('created_at DESC')->limit($this->number)->all() as $toDoList) {
                    $this->_items[] = [
                        'id' => (string)$toDoList['_id'],
                        'assigner' => isset($myAssigners[$toDoList['assigner']]) ? $myAssigners[$toDoList['assigner']] : '',
                        'content' => (string)$toDoList['content'],
                        'created_at' => Yii::$app->formatter->asWDateNoYear($toDoList['created_at']),
                    ];
                }
            }
        }
	}

	public function run()
	{
        $roleScope = (array)$this->roleScope;
        if ($roleScope != null && in_array(isset(Yii::$app->user->identity->role) ? Yii::$app->user->identity->role : '', $roleScope)) {
    		return $this->render('list', [
    			'items' => $this->_items,
    		]);
        }
	}
}
