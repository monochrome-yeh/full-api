<?php

namespace backend\modules\monochrome\topUp\controllers;

use Yii;
use yii\filters\VerbFilter;
use yii\web\NotFoundHttpException;
use common\components\mark\trace\ActionTrace;
use backend\modules\monochrome\rbam\components\Controller;
use backend\modules\monochrome\rbam\components\AccessControl;
use backend\modules\monochrome\topUp\models\TopUpRecord;
use backend\modules\monochrome\topUp\models\TopUpRecordSearch;

/**
 * VendorController implements the CRUD actions for TopUpRecord model.
 */
class VendorController extends Controller
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
                    'cancel' => ['post'],
                ],
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
     * Lists all TopUpRecord models.
     * @return mixed
     */
    public function actionIndex()
    {
        $searchModel = new TopUpRecordSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * Displays a single TopUpRecord model.
     * @param integer $_id
     * @return mixed
     */
    public function actionView($id)
    {
        return $this->render('view', [
            'model' => $this->findModel($id),
        ]);
    }

    /**
     * Creates a new TopUpRecord model.
     * If creation is successful, the browser will be redirected to the 'index' page.
     * @return mixed
     */
    public function actionCreate()
    {
        $model = new TopUpRecord(['scenario' => 'create']);

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            return $this->redirect(['index']);
        } else {
            return $this->render('create', [
                'model' => $model,
            ]);
        }
    }

    /**
     * Deletes an existing TopUpRecord model.
     * If deletion is successful, the browser will be redirected to the 'index' page.
     * @param integer $_id
     * @return mixed
     */
    public function actionCancel($id)
    {
        $this->findModel($id)->cancel();

        return $this->redirect(['index']);
    }

    /**
     * Finds the TopUpRecord model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $_id
     * @return TopUpRecord the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = TopUpRecord::findOne($id)) !== null) {
            return $model;
        } else {
            throw new NotFoundHttpException('The requested page does not exist.');
        }
    }
}
