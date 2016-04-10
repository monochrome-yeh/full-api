<?php

namespace backend\modules\monochrome\trace\controllers;

use Yii;
use yii\filters\VerbFilter;
use yii\web\NotFoundHttpException;
use backend\modules\monochrome\trace\models\TraceLog;
use backend\modules\monochrome\trace\models\TraceLogSearch;
use backend\modules\monochrome\rbam\components\Controller;
use backend\modules\monochrome\rbam\components\AccessControl;

/**
 * TraceLogController implements the CRUD actions for TraceLog model.
 */
class TraceLogController extends Controller
{
    public function behaviors()
    {
        return [
            'access' => [
                'class' => AccessControl::className(),
                'autoRules' => ['*'],
            ],
            // 'verbs' => [
            //     'class' => VerbFilter::className(),
            //     'actions' => [
            //         'delete' => ['post'],
            //     ],
            // ],
        ];
    }

    /**
     * Lists all TraceLog models.
     * @return mixed
     */
    public function actionNormalUser()
    {
        $searchModel = new TraceLogSearch(['type' => TraceLog::NORMAL_USER]);
        $filterInfos = $searchModel->getFilterInfo();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams, $filterInfos['filterCondition']);

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
            'filterCategory' => $searchModel->getCategory(),
            'filterDropDownList' => $filterInfos['filterDropDownList'],
        ]);
    }

    /**
     * Lists all TraceLog models.
     * @return mixed
     */
    public function actionVendorUser()
    {
        $searchModel = new TraceLogSearch(['type' => TraceLog::VENDOR_USER]);
        $filterInfos = $searchModel->getFilterInfo();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams, $filterInfos['filterCondition']);

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
            'filterCategory' => $searchModel->getCategory(),
            'filterDropDownList' => $filterInfos['filterDropDownList'],
        ]);
    }

    /**
     * Lists all TraceLog models.
     * @return mixed
     */
    public function actionAdminUser()
    {
        $searchModel = new TraceLogSearch(['type' => TraceLog::ADMIN_USER]);
        $filterInfos = $searchModel->getFilterInfo();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams, $filterInfos['filterCondition']);

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
            'filterCategory' => $searchModel->getCategory(),
            'filterDropDownList' => $filterInfos['filterDropDownList'],
        ]);
    }

    /**
     * Lists all TraceLog models.
     * @return mixed
     */
    public function actionIndex()
    {
        $searchModel = new TraceLogSearch();
        $filterInfos = $searchModel->getFilterInfo();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams, $filterInfos['filterCondition']);

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
            'filterCategory' => $searchModel->getCategory(),
            'filterDropDownList' => $filterInfos['filterDropDownList'],
        ]);
    }

    /**
     * Displays a single TraceLog model.
     * @param integer $_id
     * @return mixed
     */
    // public function actionView($id)
    // {
    //     return $this->render('view', [
    //         'model' => $this->findModel($id),
    //     ]);
    // }

    /**
     * Creates a new TraceLog model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    // public function actionCreate()
    // {
    //     $model = new TraceLog();

    //     if ($model->load(Yii::$app->request->post()) && $model->save()) {
    //         return $this->redirect(['view', 'id' => (string)$model->_id]);
    //     } else {
    //         return $this->render('create', [
    //             'model' => $model,
    //         ]);
    //     }
    // }

    /**
     * Updates an existing TraceLog model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param integer $_id
     * @return mixed
     */
    // public function actionUpdate($id)
    // {
    //     $model = $this->findModel($id);

    //     if ($model->load(Yii::$app->request->post()) && $model->save()) {
    //         return $this->redirect(['view', 'id' => (string)$model->_id]);
    //     } else {
    //         return $this->render('update', [
    //             'model' => $model,
    //         ]);
    //     }
    // }

    /**
     * Deletes an existing TraceLog model.
     * If deletion is successful, the browser will be redirected to the 'index' page.
     * @param integer $_id
     * @return mixed
     */
    // public function actionDelete($id)
    // {
    //     $this->findModel($id)->delete();

    //     return $this->redirect(['index']);
    // }

    /**
     * Finds the TraceLog model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $_id
     * @return TraceLog the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    // protected function findModel($id)
    // {
    //     if (($model = TraceLog::findOne($id)) !== null) {
    //         return $model;
    //     } else {
    //         throw new NotFoundHttpException('The requested page does not exist.');
    //     }
    // }
}
