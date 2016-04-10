<?php

namespace common\modules\monochrome\request\controllers;

use Yii;
use yii\filters\VerbFilter;
use yii\web\NotFoundHttpException;
use common\components\mark\trace\ActionTrace;
use common\modules\monochrome\request\Request;
use common\modules\monochrome\request\models\ToDoList;
use common\modules\monochrome\request\models\ToDoListSearch;
use backend\modules\monochrome\rbam\components\Controller;
use backend\modules\monochrome\rbam\components\AccessControl;

/**
 * ToDoListController implements the CRUD actions for ToDoList model.
 */
class ToDoListController extends Controller
{
    public function behaviors()
    {
        return [
            'access' => [
                'class' => AccessControl::className(),
                'autoRules' => ['*'],
            ],
            'verbs' => [
                'class' => VerbFilter::className(),
                'actions' => [
                    'delete' => ['post'],
                    'done' => ['post'],
                ],
            ],
            'actionTrace' => [
                'class' => ActionTrace::className(),
                'categories' => [
                    'action_trace' => ['*'],
                ],
            ]
        ];
    }

    /**
     * Lists all ToDoList models.
     * @return mixed
     */
    public function actionIndex()
    {
        $searchModel = new ToDoListSearch(['scenario' => 'toDoList']);
        $dataProvider = $searchModel->searchToDoList(Yii::$app->request->queryParams);

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    public function actionDone($id)
    {
        if (Yii::$app->request->isAjax || Yii::$app->request->isPost) {
            $model = $this->findModel($id);

            if ($model->done()) {
                Yii::$app->session->setFlash('success', Request::t('app', 'Verify Success'));
            } else {
                Yii::$app->session->setFlash('danger', Request::t('app', 'Verify Fail'));
            }

            return $this->redirect(Yii::$app->request->referrer);
        }

        throw new \Exception('Invalid Request');
    }

    public static function getStatusForView()
    {
        return [
            ToDoList::STATUS_NOT_YET => '<span class="label label-danger mark-label">'.Request::t('app', 'Not Yet').'<span>',
            ToDoList::STATUS_DONE => '<span class="label label-success mark-label">'.Request::t('app', 'Done').'<span>',
        ];
    }

    /**
     * Finds the ToDoList model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $_id
     * @return ToDoList the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = ToDoList::findOne($id)) !== null) {
            return $model;
        } else {
            throw new NotFoundHttpException('The requested page does not exist.');
        }
    }
}
