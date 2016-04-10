<?php

namespace backend\modules\monochrome\topUp\controllers;

use Yii;
use yii\filters\VerbFilter;
use yii\web\NotFoundHttpException;
use common\components\mark\trace\ActionTrace;
use backend\modules\monochrome\rbam\components\Controller;
use backend\modules\monochrome\rbam\components\AccessControl;
use backend\modules\monochrome\topUp\models\ProjectTopUpLog;
use backend\modules\monochrome\topUp\models\ProjectTopUpLogSearch;

/**
 * RecordController implements the CRUD actions for ProjectTopUpLog model.
 */
class RecordController extends Controller
{
    public function behaviors()
    {
        return [
            'access' => [
                'class' => AccessControl::className(),
                'autoRules' => ['*'],
            ],
            'actionTrace' => [
                'class' => ActionTrace::className(),
                'categories' => [
                    'action_trace' => ['*'],
                ],
            ],
        ];
    }

    /**
     * Lists all ProjectTopUpLog models.
     * @return mixed
     */
    public function actionIndex()
    {
        $searchModel = new ProjectTopUpLogSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }
}
