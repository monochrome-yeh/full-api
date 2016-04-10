<?php

namespace backend\modules\monochrome\rbam\controllers;

use Yii;
use backend\modules\monochrome\rbam\models\Item;
use yii\data\ActiveDataProvider;
use backend\modules\monochrome\rbam\components\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use backend\modules\monochrome\rbam\RBAM;
use common\modules\monochrome\members\Members;
use backend\modules\monochrome\rbam\components\AccessControl;
/**
 * ItemController implements the CRUD actions for Item model.
 */
class ItemController extends Controller
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
                ],
            ],
            // 'security' => [
            //     'class' => SecurityFilter::className(),
            //     'actions' => ['update'],
            // ]
        ];
    }

    /**
     * Lists all Item models.
     * @return mixed
     */
    public function actionIndex($type)
    {
        $dataProvider = new ActiveDataProvider([
            'query' => Item::find()->where(['type' => (int)$type]),
        ]);

        return $this->render('index', [
            'dataProvider' => $dataProvider,
            'type' => $type,
            'type_name' => $this->getItemTypeName($type),
        ]);
    }

    /**
     * Creates a new Item model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    public function actionCreate($type)
    {
        $model = new Item();
        $model->type = (int)$type;
        if ($model->load(Yii::$app->request->post())) {
            $model->_id = $model->name;
            if ($model->save()) {
                return $this->redirect(["update", 'id' => $model->_id]);
            }
        } else {
            return $this->render('create', [
                'model' => $model,
                'type_name' => $this->getItemTypeName($type),
            ]);
        }
    }

    /**
     * Updates an existing Item model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param integer $_id
     * @return mixed
     */
    public function actionUpdate($id)
    {
        $model = $this->findModel($id);

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            Yii::$app->session->setFlash('success', Members::t('app', 'Update Success.'));
            return $this->redirect(["update", 'id' => $model->_id]);
        } else {
            return $this->render('update', [
                'model' => $model,
                'type_name' => $this->getItemTypeName($model->type),
            ]);
        }
    }

    /**
     * Deletes an existing Item model.
     * If deletion is successful, the browser will be redirected to the 'index' page.
     * @param integer $_id
     * @return mixed
     */
    public function actionDelete($id)
    {
        $model = $this->findModel($id);
        $type = $model->type;
        $model->delete();

        return $this->redirect(['index', 'id' => $type]);
    }

    /**
     * Finds the Item model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $_id
     * @return Item the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = Item::findOne($id)) !== null) {
            return $model;
        } else {
            throw new NotFoundHttpException(Yii::t('common/app', 'The requested page does not exist.'));
        }
    }

    private function getItemTypeName($type)
    {
        switch ($type) {
            case 1:
                return 'Role';
                break;
            case 2:
                return 'Permission';
                break;
            case 3:
                return 'Field';
                break;
            default:
               throw new NotFoundHttpException('Type Index Error.');
                break;
        }
    }
}
