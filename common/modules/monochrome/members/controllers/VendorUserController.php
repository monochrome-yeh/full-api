<?php

namespace common\modules\monochrome\members\controllers;

use Yii;
use yii\helpers\Url;
use yii\filters\VerbFilter;
use yii\data\ActiveDataProvider;
use yii\web\NotFoundHttpException;
use yii\web\UploadedFile;
use common\components\monochrome\fileupload\models\Fileupload;
use backend\modules\monochrome\rbam\components;
use backend\modules\monochrome\rbam\components\Controller;
use backend\modules\monochrome\rbam\components\AccessControl;
use common\components\mark\trace\ActionTrace;
use common\modules\monochrome\members\Members;
use common\modules\monochrome\members\models\Vendor;
use common\modules\monochrome\members\models\VendorUser;
use common\modules\monochrome\members\models\SearchVendorUserSuper;
use common\modules\monochrome\members\models\VendorLoginForm;
use common\modules\monochrome\members\models\ResetPasswordForm;
use common\modules\monochrome\members\models\RequestPasswordForm;
use common\modules\monochrome\members\components\SecurityFilter;
use common\modules\monochrome\members\components\VendorAccessControl;
use frontend\modules\project_management\monochrome\project\models\Project;
use frontend\modules\project_management\monochrome\project\components\ProjectAccessControl;

/**
 * VendorUserController implements the CRUD actions for VendorUser model.
 */
class VendorUserController extends Controller
{
    public function behaviors()
    {
        return [
            'access' => [
                'class' => AccessControl::className(),
                'rules' => [
                    [
                        'actions' => ['request', 'token', 'login'],
                        'allow' => true,
                        'roles' => ['?'],
                    ],
                    [
                        'actions' => ['login', 'first-change-password'],
                        'allow' => true,
                        'roles' => ['@'],
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
            'vendorAccess' => [
                'class' => VendorAccessControl::className(),
            ],
            'project' => [
                'class' => ProjectAccessControl::className(),
            ],
            'security' => [
                'class' => SecurityFilter::className(),
                'actions' => ['profile'],
            ],
            'actionTrace' => [
                'class' => ActionTrace::className(),
                'categories' => [
                    'action_trace' => ['*'],
                ],
            ],
            'httpCache_login' => [
                'class' => \yii\filters\HttpCache::className(),
                'only' => ['login'],
                'lastModified' => function ($action, $params) {
                    $date = Vendor::find()->where(['_id' => Yii::$app->request->get('vid')])->max('updated_at');
                    return $date;
                },
                'cacheControlHeader' => 'private, proxy-revalidate, max-age=86400',           
            ],
            'httpCache_list' => [
                'class' => \yii\filters\HttpCache::className(),
                'only' => ['index', 'list'],
                'lastModified' => function ($action, $params) {
                    $date = VendorUser::find()->where(['login.vendor.vid' => Yii::$app->request->get('vid')])->max('updated_at');
                    return $date;
                },
                'cacheControlHeader' => 'private, proxy-revalidate, max-age=86400',                               
            ],      
            // 'pageCache' => [
            //     'class' => 'yii\filters\PageCache',
            //     'only' => ['index', 'list', 'view'],
            //     'duration' => 3600,
            //     // 'dependency' => [
            //     //     'class' => 'yii\caching\FileDependency',
            //     //     'fileName' => 'example.txt'
            //     // ],
            //     'variations' => [
            //         \Yii::$app->language,
            //     ]
            // ],                                                  
        ];
    }

    public function actionLogin($vid = null)
    {
        if (!\Yii::$app->user->isGuest) {
            return $this->goHome();
        }

        $vendor = $this->findVendorModelByIdOrAlias($vid);

        $model = new VendorLoginForm();
        $model->vid = (string)$vendor->_id;

        if ($model->load(Yii::$app->request->post()) && $model->login()) {
            return $this->goBack();
        }

        return $this->render('vendor-login', [
            'model' => $model,
            'vid' => $vid,
            'vendor_name' => Vendor::getVendor($vid) ? Vendor::getVendor($vid)->name : Yii::$app->name,
        ]);
    }

    public function actionRequest($vid)
    {
        if (!\Yii::$app->user->isGuest) {
            return $this->goHome();
        }

        $vendor = $this->findVendorModelByIdOrAlias($vid);

        $model = new RequestPasswordForm();
        $model->vid = (string)$vendor->_id;

        if ($model->load(Yii::$app->request->post()) && $model->validate() && $model->requestPasswordReset()) {
            Yii::$app->session->setFlash('success', Members::t('app', 'You have already reset password recently.'));
        }

        return $this->render('request-password', [
            'model' => $model,
        ]);
    }

    public function actionToken($token)
    {
        if (!\Yii::$app->user->isGuest) {
            return $this->goHome();
        }

        $user = VendorUser::findByPasswordResetToken($token);
        $model = new ResetPasswordForm();

        if (!empty($user)) {
            if ($model->load(Yii::$app->request->post()) && $model->validate() && $model->resetPassword($user)) {
                return $this->redirect(["/login/{$user->vid}"]);
            }

            return $this->render('reset-password', [
                'model' => $model,
            ]);

        } else {
            throw new NotFoundHttpException(Yii::t('app', 'Page not found.'));
        }
    }

    public function actionFirstChangePassword()
    {
        if (\Yii::$app->user->isGuest) {
            return $this->goHome();
        }

        $user = Yii::$app->user->getIdentity();
        $model = new ResetPasswordForm();

        if (!empty($user) && $user->passwd_changed === 0) {
            
            if ($model->load(Yii::$app->request->post()) && $model->validate() && $model->resetPassword($user)) {
                Yii::$app->session->setFlash('success', Yii::t('common/app', 'Update Success.'));        
                return $this->redirect(["/profile"]);
            }
            else {
                Yii::$app->session->setFlash('warning', Members::t('app', 'You must change Your password at first login.'));                
            }

            return $this->render('reset-password', [
                'model' => $model,
            ]);

        } else {
            throw new NotFoundHttpException(Yii::t('app', 'Page not found.'));
        }
    }

    // public function actionIndex($vid) {}
    /**
     * Lists all Users in specific vendor.
     * @return mixed
     */
    public function actionIndex($vid)
    {
        // filter admin role
        $dataProvider = new ActiveDataProvider([
            'query' => VendorUser::find()->where([
                'login.vendor.vid' => $vid,
                'role' => ['$ne' => 'admin'],
                'status' => ['$nin' => [VendorUser::STATUS_DELETED, VendorUser::STATUS_VENDOR_DELETED]]
            ]),
        ]);

        return $this->render('index', [
            'vid' => $vid,
            'dataProvider' => $dataProvider,
        ]);
    }

    public function actionProjectUser($pid)
    {
        $dataProvider = new ActiveDataProvider([
            'query' => VendorUser::find()->where([
                'pid' => $pid,
                'status' => VendorUser::STATUS_ACTIVE,
                'role' => ['$in' => [Yii::$app->getModule('members')->managerRoleName, Yii::$app->getModule('members')->salesRoleName]],
            ]),
        ]);

        return $this->render('index', [
            'vid' => Yii::$app->user->identity->vid,
            'dataProvider' => $dataProvider,
        ]);
    }

    public function actionList()
    {
        $searchModel = new SearchVendorUserSuper();

        return $this->render('list', [
            'searchModel' => $searchModel,
        ]);
    }

    // public function actionCreate($vid) {}
    /**
     * Creates a new Vendor User model.
     * If creation is successful, the browser will be redirected to the 'index' page.
     * @return mixed
     */
    public function actionCreate($vid)
    {
        $user = new VendorUser(['scenario' => 'register']);
        $user->vid = $vid;
        $user->settings = ['font_size' => 'medium'];
        $vendor = $this->findVendorModelByIdOrAlias($vid);

        // 因為admin不算1個人數，所以用小於等於
        if (VendorUser::find()->where([
            'login.vendor.vid' => $vid,
            'status'           => ['$nin' => [VendorUser::STATUS_DELETED, VendorUser::STATUS_VENDOR_DELETED]]
            ])->count() <= $vendor->maximum_members
        ) {
            if ($user->load(Yii::$app->request->post())) {
                
                $experience_pid = array_values(array_unique(array_merge((array)$user->experience_pid, (array)$user->pid)));
                $user->experience_pid = (array)$experience_pid;

                if ($user->save()) {            
                    $users = ($vendor->user != null) ? $vendor->user : [];
                    array_push($users, (string)$user->_id);
                    $vendor->user = array_unique($users);

                    if ($vendor->save() && $user->sendPasswordEmail($vendor)) {
                        return $this->redirect(['index', 'vid' => $vid]);
                    } else {
                        $user->delete();
                    }
                }
            }
        } else {
            Yii::$app->session->setFlash('danger', Members::t('app', 'Already to top limit. If you want to increase accounts, please contact us (02)8770-5011'));
        }

        return $this->render('create', [
            'user' => $user,
            'projects' => Project::getListByVendor($vid),
        ]);
    }

    // public function actionUpdateUser($uid) {}
    /**
     * Updates an existing Vendor User model.
     * If update is successful, the browser will be redirected to the 'index' page.
     * @param integer $_id
     * @return mixed
     */
    public function actionUpdate($id)
    {
        $user = $this->findModel($id);
        if (Yii::$app->user->getId() !== $id) {
            $user->scenario = 'update';
            $user->activity = $user->status === VendorUser::STATUS_ACTIVE ? true : false;

            $vendor = $this->findVendorModel($user->vid);

            if ($user->load(Yii::$app->request->post())) {

                $experience_pid = array_values(array_unique(array_merge((array)$user->experience_pid, (array)$user->pid)));
                $user->experience_pid = (array)$experience_pid;

                if ($user->save() && $user->sendPasswordEmail($vendor)) {
                    Yii::$app->session->setFlash('success', Yii::t('common/app', 'Update Success.'));
                }
            }

            return $this->render('update', [
                'user' => $user,
                'projects' => Project::getListByVendor($user->vid),
            ]);
        } else {
            throw new NotFoundHttpException(Yii::t('app', 'Page not found.'));
        }
    }

    // public function actionUpdateUser($uid) {}
    /**
     * Updates an existing Vendor User model.
     * If update is successful, the browser will be redirected to the 'index' page.
     * @param integer $_id
     * @return mixed
     */
    public function actionProfile()
    {
        $user = $this->findModel(Yii::$app->user->getId());
        $user->scenario = 'profile';
        $fu = new Fileupload();

        if (Yii::$app->request->isPost && !empty($_POST['Fileupload'])) {
            $fu->setSettings([
                'type' => 'image',
                'skipOnEmpty' => true,
                'fileName' => 'avatar',
                'encrypted' => false,
                'maxSize' => 1024*1024*150,
                'path' => "/vendor/{$user->vid}/user/{$user->getId()}/",
            ]);
            $fu->file = UploadedFile::getInstance($fu, 'file');

            if ($fu->upload()) {
                if (!empty($fu->getItemsPath())) {
                    $user->avatar = $fu->getItemsPath();
                }                
            }
        }
            
        if ($user->load(Yii::$app->request->post()) && $user->save()) {
            Yii::$app->session->setFlash('success', Yii::t('common/app', 'Update Success.'));
            return $this->redirect(['/profile']);
        }
        else {
            return $this->render('profile', [
                'user' => $user,
                'fu' => $fu,
            ]);
        }    
    }

    /**
     * Deletes an existing Vendor User model.
     * If deletion is successful, the browser will be redirected to the 'index' page.
     * @param integer $_id
     * @return mixed
     */
    public function actionDelete($id)
    {
        $model = $this->findModel($id);

        $vid = $model->vid;

        if (Yii::$app->user->isSuperadmin()) {
            $model->delete(true);
        }
        else {
            $model->delete();
        }
        

        return $this->redirect(['index', 'vid' => $vid]);
    }

    public function actionReset($id)
    {
        $model = $this->findModel($id);
        $vendor = $this->findVendorModel($model->vid);

        if ($model->reset() && $model->sendResetPasswordEmail($vendor)) {
            Yii::$app->session->setFlash('success', Yii::t('common/app', 'Update Success.'));
        } else {
            Yii::$app->session->setFlash('danger', Members::t('app', 'Something Wrong.'));
        }

        return $this->redirect(Yii::$app->request->referrer);
    }

    public function actionEnable($id)
    {
        $model = $this->findModel($id);

        if ($model->enable() && $model->sendEnableEmail()) {
            Yii::$app->session->setFlash('success', Yii::t('common/app', 'Update Success.'));
        } else {
            Yii::$app->session->setFlash('danger', Members::t('app', 'Something Wrong.'));
        }

        return $this->redirect(Yii::$app->request->referrer);
    }

    public function actionDisable($id)
    {
        $model = $this->findModel($id);

        if ($model->disable() && $model->sendDisableEmail()) {
            Yii::$app->session->setFlash('success', Yii::t('common/app', 'Update Success.'));
        } else {
            Yii::$app->session->setFlash('danger', Members::t('app', 'Something Wrong.'));
        }

        return $this->redirect(Yii::$app->request->referrer);
    }

    /**
     * Finds the Vendor User model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $_id
     * @return Vendor the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = VendorUser::findOne($id)) !== null) {
            return $model;
        } else {
            throw new NotFoundHttpException(Yii::t('app', 'Page not found.'));
        }
    }

    /**
     * Finds the Vendor model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $_id
     * @return Vendor the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findVendorModel($id)
    {
        if (($model = Vendor::findOne($id)) !== null) {
            return $model;
        } else {
            throw new NotFoundHttpException(Yii::t('app', 'Page not found.'));
        }
    }

    protected function findVendorModelByIdOrAlias($id_or_alias)
    {
        try {
            $mongoId = new \MongoId($id_or_alias);
        } catch (\MongoException $ex) {
            $mongoId = '';
        }

        $vendor = Vendor::find()->where([
            '$or' => [
                ['_id' => $mongoId],
                ['alias' => $id_or_alias],
            ]
        ])->one();

        if ($vendor !== null && $vendor->status == true) {
            return $vendor;
        } else {
            throw new NotFoundHttpException(Yii::t('app', 'Page not found.'));
        }
    }
}
