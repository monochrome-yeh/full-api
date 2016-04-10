<?php

namespace common\modules\monochrome\members\controllers;

use Yii;
use yii\helpers\Url;
use yii\web\UploadedFile;
use yii\filters\VerbFilter;
use yii\data\ActiveDataProvider;
use yii\web\NotFoundHttpException;
use backend\modules\monochrome\rbam\components\Controller;
use backend\modules\monochrome\rbam\components\AccessControl;
use common\components\mark\trace\ActionTrace;
use common\modules\monochrome\members\models\Vendor;
use common\modules\monochrome\members\models\VendorUser;
use common\modules\monochrome\members\Members;
use common\modules\monochrome\members\components\SecurityFilter;
use common\modules\monochrome\taxonomy\models\Type;
use common\components\monochrome\models\FileUpload;
use common\modules\monochrome\members\models\VendorIcons;
use common\modules\monochrome\members\models\user_settings\Settings;

/**
 * VendorController implements the CRUD actions for Vendor model.
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
     * Lists all Vendor models.
     * @return mixed
     */
    public function actionIndex()
    {
        $dataProvider = new ActiveDataProvider([
            'query' => Vendor::find(),
        ]);

        return $this->render('index', [
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * Creates a new Vendor model.
     * If creation is successful, the browser will be redirected to the 'update' page.
     * @return mixed
     */
    public function actionCreate()
    {
        $model = new Vendor();
        $user = new VendorUser(['scenario' => 'admin_register']);

        if ($model->load(Yii::$app->request->post()) && $user->load(Yii::$app->request->post())) {
            if ($model->save()) {
                $user->vid = $model->getId();
                $user->role = Yii::$app->getModule('members')->vendor_admin_role;
                $user->settings = [
                    'font_size' => Settings::FONT_SIZE_MEDIUM,
                ];
                if ($user->save()) {
                    $uid = $user->getId();
                    $model->admin = $uid;
                    $users = ($model->user != null) ? $model->user : [];
                    array_push($users, $uid);
                    $model->user = array_unique($users);

                    if ($model->save() && $user->sendAdminPasswordEmail($model) && $model->assign($user->getId())) {
                        Yii::$app->session->setFlash('success', Members::t('app', 'Create Success.'));

                        // import base taxonomy content
                        Yii::$app->getModule('taxonomy')->import_ad_media_edm_content($model->_id);
                        Yii::$app->getModule('taxonomy')->import_guest_status_content($model->_id);

                        return $this->redirect(['update', 'id' => (string)$model->_id]);
                    } else {
                        $user->delete();
                        $model->delete();
                    }
                } else {
                    $model->delete();
                }
            }
        }

        return $this->render('create', [
            'model' => $model,
            'user' => $user,
            'type' => Type::getTypeList(),
        ]);
    }

    /**
     * Updates an existing Vendor model.
     * If update is successful, the browser will be redirected to the 'update' page.
     * @param integer $_id
     * @return mixed
     */
    public function actionUpdate($id)
    {
        $model = $this->findModel($id);
        // import base taxonomy content
        Yii::$app->getModule('taxonomy')->install_required();
        Yii::$app->getModule('taxonomy')->import_ad_media_edm_content($model->_id);
        Yii::$app->getModule('taxonomy')->import_guest_status_content($model->_id);     
           
        $user = $this->findVendorUserModel($model->admin);
        $file = new VendorIcons();
        if ($model->load(Yii::$app->request->post()) && $user->load(Yii::$app->request->post())) {
            $files = [
                [UploadedFile::getInstance($file, 'file1'), 'key' => '57x57'],
                [UploadedFile::getInstance($file, 'file2'), 'key' => '72x72'],
                [UploadedFile::getInstance($file, 'file3'), 'key' => '114x114'],
                [UploadedFile::getInstance($file, 'file4'), 'key' => '144x144'],
            ];

            $checkFile = false;
            foreach ($files as $key => $value) {
               if (empty($value[0])) {
                    unset($files[$key]);
               } else {
                    $checkFile = true;
               }
            }
            $filenames = [];
            $icons = [];
            if ($checkFile && $file->validate()) {
                $icons = [];
                foreach ($files as $value) {
                    $namePath = "/vendor/{$id}/icons/{$value['key']}.png";
                    if (!Yii::$app->s3->uploadFile($value[0], $namePath)) {
                        $file->addError('icons', '上傳至AWS S3失敗。');
                    } else {
                        $filenames[$value['key']] = $namePath;
                        $icons['ui']['icons']['s_' . $value['key']] = $namePath;
                    }
                }
            }
            $model->settings = array_merge($model->settings, $icons);
            if (
                $model->save()
                && $user->save()
                && $user->sendPasswordEmail($model)
                && $model->assign($user->getId())
            ) {
                Yii::$app->session->setFlash('success', Yii::t('common/app', 'Update Success.'));

                return $this->redirect(['update', 'id' => (string)$model->_id]);
            }
        }

        return $this->render('update', [
            'file' => $file,
            'model' => $model,
            'user' => $user,
            'type' => Type::getTypeList(),
        ]);
    }

    /**
     * Deletes an existing Vendor model.
     * If deletion is successful, the browser will be redirected to the 'index' page.
     * @param integer $_id
     * @return mixed
     */
    public function actionDelete($id)
    {
        $this->findModel($id)->delete();

        return $this->redirect(['index']);
    }

    /**
     * Finds the Vendor model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $_id
     * @return Vendor the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = Vendor::findOne($id)) !== null) {
            return $model;
        } else {
            throw new NotFoundHttpException(Yii::t('common/app', 'The requested page does not exist.'));
        }
    }

    protected function findVendorUserModel($id)
    {
        if (($model = VendorUser::findOne($id)) !== null && $model->status == true) {
            return $model;
        } else {
            throw new NotFoundHttpException(Yii::t('common/app', 'The requested page does not exist.'));
        }
    }    
}
