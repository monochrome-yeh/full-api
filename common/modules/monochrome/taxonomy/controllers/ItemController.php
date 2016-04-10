<?php

namespace common\modules\monochrome\taxonomy\controllers;

use Yii;
use \MongoId;
use yii\filters\VerbFilter;
use yii\data\ActiveDataProvider;
use yii\web\NotFoundHttpException;
use common\components\mark\trace\ActionTrace;
use common\modules\monochrome\taxonomy\components\TypeAccessControl;
use common\modules\monochrome\taxonomy\models\Item;
use common\modules\monochrome\taxonomy\models\Type;
use common\modules\monochrome\taxonomy\Taxonomy;
use backend\modules\monochrome\rbam\components\Controller;
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
            'typeAccess' => [
                'class' => TypeAccessControl::className(),
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
     * Lists all Item models.
     * @return mixed
     */
    public function actionList()
    {
        $dataProvider = new ActiveDataProvider([
            'query' => Type::find()->where(['unique_name' => ['$in' => array_keys(Type::getTypeListByVendor(Yii::$app->user->getVendor()))]]),
        ]);

        return $this->render('list', [
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * Lists all Item models.
     * @return mixed
     */
    public function actionIndex($type)
    {
        $dataProvider = new ActiveDataProvider([
            'query' => Item::find()->where(['vid' => Yii::$app->user->getIdentity()->vid, 'type' => $type]),
        ]);

        return $this->render('index', [
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * Creates a new Item model.
     * If creation is successful, the browser will be redirected to the 'update' page.
     * @return mixed
     */
    public function actionCreate($type)
    {
        $model = new Item();

        if ($model->load(Yii::$app->request->post())) {
            $model->type = $type;
            $model->vid = Yii::$app->user->getIdentity()->vid;

            if ($model->save()) {
                Yii::$app->session->setFlash('success', Taxonomy::t('app', 'Create Success.'));
                return $this->redirect(['index', 'type' => $model->type]);
            } else {
                //var_dump($model->getErrors());exit;
                Yii::$app->session->setFlash('danger', Taxonomy::t('app', 'Something Wrong.'));
            }
        }

        return $this->render('create', [
            'model' => $model,
            'type' => Type::getTypeListByVendor(Yii::$app->user->getVendor()),
        ]);
    }

    /**
     * Displays a single Item model.
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
     * Updates an existing Item model.
     * If update is successful, the browser will be redirected to the 'update' page.
     * @param integer $_id
     * @return mixed
     */
    public function actionUpdate($id)
    {
        $model = $this->findModel($id);

        if ($model->load(Yii::$app->request->post()) && $model->save(true, ['name'])) {
            Yii::$app->session->setFlash('success', Taxonomy::t('app', 'Update Success.'));
            return $this->redirect(['update', 'id' => $id]);
        } else {
            return $this->render('update', [
                'model' => $model,
                'type' => Type::getTypeListByVendor(Yii::$app->user->getVendor()),
            ]);
        }
    }

    //這些項目要是被使用就不得被刪
    /**
     * Deletes an existing Item model.
     * If deletion is successful, the browser will be redirected to the 'index' page.
     * @param integer $_id
     * @return mixed
     */
    // public function actionDelete($id)
    // {
    //     $model = $this->findModel($id);

    //     $type = $model->type;

    //     $model->delete();

    //     return $this->redirect(['index', 'type' => $type]);
    // }

    /**
     * Finds the Item model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $_id
     * @return Item the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        $model = Item::findOne($id);
        if ($model !== null && $model->vid == Yii::$app->user->getIdentity()->vid && array_key_exists($model->type, Type::getTypeListByVendor(Yii::$app->user->getVendor()))) {
            return $model;
        } else {
            throw new NotFoundHttpException(Yii::t('common/app', 'The requested page does not exist.'));
        }
    }
}
