<?php

namespace common\modules\monochrome\members\controllers;

use Yii;
use yii\helpers\Url;
use yii\filters\VerbFilter;
use yii\data\ActiveDataProvider;
use yii\web\NotFoundHttpException;
use common\components\mark\trace\ActionTrace;
use common\modules\monochrome\members\Members;
use common\modules\monochrome\members\models\User;
use common\modules\monochrome\members\models\AdminUser;
use common\modules\monochrome\members\models\LoginForm;
use common\modules\monochrome\members\models\AdminLoginForm;
use backend\modules\monochrome\rbam\components\Controller;
use backend\modules\monochrome\rbam\components\AccessControl;

/**
 * UserController implements the CRUD actions for User model.
 */
class UserController extends Controller
{
    public function behaviors()
    {
        return [
            'access' => [
                'class' => AccessControl::className(),
                'rules' => [
                    [
                        'actions' => ['login', 'admin-login'],
                        'allow' => true,
                        'roles' => ['@', '?'],
                    ],
                ],
                'autoRules' => ['*'],
            ],
            'verbs' => [
                'class' => VerbFilter::className(),
                'actions' => [
                    'delete' => ['post'],
                ],
            ],
            'actionTrace' => [
                'class' => ActionTrace::className(),
                'categories' => [
                    'action_trace' => ['*'],
                ],
            ]
            // 'security' => [
            //     'class' => SecurityFilter::className(),
            //     'actions' => ['update'],
            // ]
        ];
    }

    /**
     * Lists all User models.
     * @return mixed
     */
    public function actionIndex()
    {
        $dataProvider = new ActiveDataProvider([
            'query' => User::find()->where(['login.normal.account' => ['$exists' => true]]),
        ]);

        return $this->render('index', [
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * Creates a new User model.
     * If creation is successful, the browser will be redirected to the 'index' page.
     * @return mixed
     */
    public function actionCreate()
    {
        $user = new User(['scenario' => 'register']);

        if ($user->load(Yii::$app->request->post()) && $user->save() && $user->sendPasswordEmail()) {
            return $this->redirect(['index']);
        }

        return $this->render('create', [
            'user' => $user,
        ]);
    }

    /**
     * Updates an existing User model.
     * If update is successful, the browser will be redirected to the 'index' page.
     * @param integer $_id
     * @return mixed
     */
    public function actionUpdate($id)
    {
        $user = $this->findModel($id);
        $user->scenario = 'update';
        $user->activity = $user->status === User::STATUS_ACTIVE ? true : false;

        if ($user->load(Yii::$app->request->post()) && $user->save() && $user->sendPasswordEmail()) {
            Yii::$app->session->setFlash('success', Yii::t('common/app', 'Update Success.'));
        }

        return $this->render('update', [
            'user' => $user,
        ]);
    }

    /**
     * Updates an existing User model.
     * If update is successful, the browser will be redirected to the 'index' page.
     * @param integer $_id
     * @return mixed
     */
    public function actionProfile()
    {
        $id = Yii::$app->user->getId();
        $user = $this->findModel($id);
        $user->scenario = 'profile';

        if ($user->load(Yii::$app->request->post()) && $user->save()) {
            Yii::$app->session->setFlash('success', Yii::t('common/app', 'Update Success.'));
        }

        return $this->render('profile', [
            'user' => $user,
        ]);
    }

    /**
     * Deletes an existing User model.
     * If deletion is successful, the browser will be redirected to the 'index' page.
     * @param integer $_id
     * @return mixed
     */
    public function actionDelete($id)
    {
        $this->findModel($id)->delete();

        return $this->redirect(['index']);
    }

    // public function actionReset($id)
    // {
    //     $model = $this->findModel($id);

    //     if ($model->reset() && $model->sendPasswordEmail()) {
    //         Yii::$app->session->setFlash('success', Yii::t('common/app', 'Update Success.'));
    //     } else {
    //         Yii::$app->session->setFlash('danger', Members::t('app', 'Something Wrong.'));
    //     }

    //     return $this->redirect(Yii::$app->request->referrer);
    // }

    // public function actionEnable($id)
    // {
    //     $model = $this->findModel($id);

    //     if ($model->enable() && $model->sendStatusEmail('enable')) {
    //         Yii::$app->session->setFlash('success', Yii::t('common/app', 'Update Success.'));
    //     } else {
    //         Yii::$app->session->setFlash('danger', Members::t('app', 'Something Wrong.'));
    //     }

    //     return $this->redirect(Yii::$app->request->referrer);
    // }

    // public function actionDisable($id)
    // {
    //     $model = $this->findModel($id);

    //     if ($model->disable() && $model->sendStatusEmail('disable')) {
    //         Yii::$app->session->setFlash('success', Yii::t('common/app', 'Update Success.'));
    //     } else {
    //         Yii::$app->session->setFlash('danger', Members::t('app', 'Something Wrong.'));
    //     }

    //     return $this->redirect(Yii::$app->request->referrer);
    // }

    public function actionLogin()
    {
        if (!\Yii::$app->user->isGuest) {
            return $this->goHome();
        }

        $model = new LoginForm();

        if ($model->load(Yii::$app->request->post()) && $model->login()) {
            return $this->goBack(); 
        }

        return $this->render('login', [
            'model' => $model,
        ]);
    }

    public function actionAdminLogin()
    {
        if (!\Yii::$app->user->isGuest) {
            return $this->goHome();
        }

        $model = new AdminLoginForm();

        if ($model->load(Yii::$app->request->post()) && $model->login()) {
            return $this->goBack();
        }
        
        return $this->render('login', [
            'model' => $model,
        ]);
    }

    /**
     * Finds the User model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $_id
     * @return User the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
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
