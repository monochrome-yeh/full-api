<?php

namespace common\modules\monochrome\members\controllers;

use Yii;
use backend\modules\monochrome\rbam\components\AccessControl;
use yii\data\ActiveDataProvider;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use common\modules\monochrome\members\models\User;
use common\modules\monochrome\members\models\AdminUser;
use common\modules\monochrome\members\models\CVModel;
/**
 * CVController implements the CRUD actions for CVModel model.
 */
class CvController extends Controller
{
    /**
     * @inheritdoc
     */
    public function behaviors()
    {
        return [
            'access' => [
                'class' => AccessControl::className(),
                'rules' => [
                    [
                        'actions' => ['update'],
                        'allow' => true,
                        'roles' => ['@'],
                    ],
                ],
                'autoRules' => ['*'],
            ],
            'verbs' => [
                'class' => VerbFilter::className(),
                'actions' => [
                    'delete' => ['POST'],
                ],
            ],
        ];
    }

    /**
     * Lists all CVModel models.
     * @return mixed
     */
    /**
     * Lists all User models.
     * @return mixed
     */
    public function actionIndex()
    {
        $dataProvider = new ActiveDataProvider([
            'query' => User::find()->where(['login.normal.account' => ['$exists' => true], 'cv' => ['$exists' => true]]),
        ]);

        return $this->render('index', [
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * Displays a single CVModel model.
     * @param integer $_id
     * @return mixed
     */
    public function actionView($uid)
    {   
        $userModel = $this->findUserModel($uid);
        $model = new CVModel();
        $data = [];
        $data['CVModel'] = $userModel->cv['zh_tw'];
        $model->load($data);
        return $this->render('view', [
            'model' => $model,
            'uid' => (string)$userModel->_id,
        ]);
    }

    /**
     * Creates a new CVModel model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    public function actionCreate($uid)
    {
        $model = new CVModel();
        $userModel = $this->findUserModel($uid);

        if ($model->load(Yii::$app->request->post()) && $model->validate()) {
            $cv = [];
            $cv['zh_tw'] = $model->attributes;
            $userModel->cv = $cv;

            if($userModel->save()) {
                return $this->redirect(['view', 'uid' => (string)$userModel->_id]);                
            }
        } else {
            return $this->render('create', [
                'model' => $model,
            ]);
        }
    }

    /**
     * Updates an existing CVModel model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param integer $_id
     * @return mixed
     */
    public function actionUpdate($uid)
    {
        $userModel = $this->findUserModel($uid);
        if (!isset($userModel->cv)) {
            return $this->redirect(['create', 'uid' => (string)$userModel->_id]);
        }
        else {
            $model = new CVModel();
            $data = [];
            $data['CVModel'] = $userModel->cv['zh_tw'];
            $model->load($data);
            //$model->isNewRecord = false;      
        }


        if ($model->load(Yii::$app->request->post()) && $model->validate()) {
            $cv = $userModel->cv;
            $cv['zh_tw'] = $model->attributes;
            $userModel->cv = $cv;

            if($userModel->save()) {
                return $this->redirect(['view', 'uid' => (string)$userModel->_id]);                
            }
        } else {
            return $this->render('update', [
                'model' => $model,
                'uid' => (string)$userModel->_id,
            ]);
        }
    }

    /**
     * Deletes an existing CVModel model.
     * If deletion is successful, the browser will be redirected to the 'index' page.
     * @param integer $_id
     * @return mixed
     */
    // public function actionDelete($uid)
    // {
    //     $this->findModel($uid)->delete();

    //     return $this->redirect(['index']);
    // }

    /**
     * Finds the CVModel model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $_id
     * @return CVModel the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = CVModel::findOne($id)) !== null) {
            return $model;
        } else {
            throw new NotFoundHttpException('The requested page does not exist.');
        }
    }

    /**
     * Finds the User model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $_id
     * @return User the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findUserModel($id)
    {
        if (($model = User::findOne($id)) !== null) {
            return $model;
        } elseif ((Yii::$app->user->identity->role === Yii::$app->getModule('rbam')->superadmin_name) && (($model = AdminUser::findOne($id)) !== null)) {
            return $model;
        } else {
            throw new NotFoundHttpException(Yii::t('common/app', 'The requested page does not exist.'));
        }
    }    
}
