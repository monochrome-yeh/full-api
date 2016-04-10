<?php

namespace backend\modules\monochrome\optimize\controllers;

use Yii;
use yii\filters\VerbFilter;
use yii\web\NotFoundHttpException;
use backend\modules\monochrome\rbam\components\Controller;
use backend\modules\monochrome\rbam\components\AccessControl;
use common\modules\monochrome\members\components\SecurityFilter;

use backend\modules\monochrome\optimize\models\Chris;
use common\modules\monochrome\members\models\Vendor;
use common\modules\monochrome\members\models\VendorUser;
use common\modules\monochrome\taxonomy\models\Item;
use common\modules\monochrome\taxonomy\models\Type;
use frontend\modules\project_management\mark\guest\models\Guest;
use frontend\modules\project_management\monochrome\project\models\Project;
use frontend\modules\project_management\monochrome\project\models\ProjectSuper;
use frontend\modules\project_management\monochrome\project\models\house\HouseholdItem as HouseItem;
use frontend\modules\project_management\monochrome\project\models\house\Item as ProjectItem;
use frontend\modules\project_management\monochrome\order\models\Order;
use frontend\modules\project_management\monochrome\saleBonus\models\BonusOrder;

/**
 * @todo 要用ActiveRecord (Model) 修正資料，請務必save 指定欄位就好
 * @todo 要用ActiveRecord (Model) 修正資料，請務必save 指定欄位就好
 * @todo 要用ActiveRecord (Model) 修正資料，請務必save 指定欄位就好
 * @todo 要用ActiveRecord (Model) 修正資料，請務必save 指定欄位就好
 * @todo 要用ActiveRecord (Model) 修正資料，請務必save 指定欄位就好
 * @todo 要用ActiveRecord (Model) 修正資料，請務必save 指定欄位就好
 */

class DefaultController extends Controller
{
    public function behaviors()
    {
        return [
            'access' => [
                'class' => AccessControl::className(),
                'autoRules' => ['*'],
            ],
            'security' => [
                'class' => SecurityFilter::className(),
                'actions' => ['index'],
            ]
        ];
    }

    public function actionFixAds()
    {
        $orders = Yii::$app->mongodb->getCollection(Order::collectionName())->find();
        $count = 0;
        foreach ($orders as $order) {
            if (!is_array($order['ads'])) {
                $count++;
                $result = Yii::$app->mongodb->getCollection(Order::collectionName())->update(
                    ['_id' => $order['_id']],
                    ['ads' => array($order['ads'])]
                );
		//echo $order['ads'] . "<br/>";
            }
            //$order->save(false, ['_id']);
        }

        echo $count;

    }

    public function actionFixBonus()
    {
        $orders = Order::find()->all();
        foreach ($orders as $order) {
            $order->save(false, ['_id']);
        }

        $bonusOrders = BonusOrder::find()->where(['status' => BonusOrder::TYPE_WORK_OUT, 'bonus_date' => ['$exists' => true]])->all();
        foreach ($bonusOrders as $model) {
            $total = 0;

            foreach ($model->user_bonus as $user) {
                $total += $user['actualBonus'];
            }

            $model->total_bonus = $total;
            $model->save(true, ['_id', 'total_bonus', 'updated_at']);
        }
    }

    public function actionProjectRecord()
    {
        $projects = ProjectSuper::find()->where(['record' => ['$exists' => false]])->all();
        foreach ($projects as $project) {
            $result = [];
            $startTime = strtotime(Yii::$app->formatter->asDate($project->created_at));
            $project->active_date = $result['begin_date'] = $startTime;
            $project->expire_date = $result['end_date'] = $startTime + 86400 * 180;

            $result['range'] = 180;

            $project->record = [$result];
            $project->save(false, ['active_date', 'expire_date', 'record']);
        }
    }

    public function actionOrderAlert()
    {
        $orders = Order::find()->all();
        foreach ($orders as $order) {
            $order->current_status_date = is_string($order->current_status_date) ? strtotime($order->current_status_date) : $order->current_status_date;
            $order->save(true, ['current_status_date']);
        }
    }

    public function actionFixGuestBirthdayFormat()
    {
        $guests = Guest::find()->all();
        foreach ($guests as $guest) {
            $guest->save(false, ['_id']);
        }
    }

    // public function actionSaveGuest()
    // {
    //     $result = ['status' => false, 'errorMessages' => 'Error'];

    //     if (Yii::$app->request->isPost) {
    //         $index = 0;
    //         $validate = true;
    //         $new_rows = $errorMessages = [];

    //         if (isset(Yii::$app->request->post()['Data'])) {
    //             foreach (Yii::$app->request->post()['Data'] as $info) {
    //                 $guest = new Guest;
    //                 $guestInfo['Guest'] = $info;
    //                 if ($guest->load($guestInfo) && $guest->validate()) {
    //                     $guest->created_at = strtotime($guest->created_at);
    //                     $guest->birthday = strtotime($guest->birthday);
    //                     $guest->created_at = (int)$guestInfo['Guest']['created_at'];
    //                     $guest->updated_at = (int)$guestInfo['Guest']['updated_at'];
    //                     $guestObject = $guest->attributes;
    //                     unset($guestObject['_id']);
    //                     $new_rows[] = $guestObject;
    //                     $index++;
    //                 } else {
    //                     foreach ($guest->getErrors() as $errors) {
    //                         foreach ($errors as $error) {
    //                             $errorMessages[] = $error;
    //                         }
    //                     }

    //                     $validate = false;
    //                 }
    //             }

    //             if ($validate) {
    //                 $result['status'] = true;
    //                 Yii::$app->mongodb->getCollection(Guest::collectionName())->batchInsert($new_rows);
    //             } else {
    //                 $result['errorMessages'] = 'Create ('.$index.') data(s).'.implode('，', $errorMessages);
    //             }
    //         } else {
    //             $result['errorMessages'] = 'Please generate data first';
    //         }
    //     } else {
    //         $result['errorMessages'] = 'POST only';
    //     }

    //     return json_encode($result);
    // }

    // {
    //     "vid" : {
    //         "name" : "vendor_name",
    //         "ad_media" : ["ad_media_id"],
    //         "request_rooms" : ["request_rooms_id"],
    //         "request_square_meters" : ["request_square_meters_id"],
    //         "pidlist" : [
    //             {
    //                 "pid" : "pid",
    //                 "sid" : ["sid"]
    //             }
    //         ]
    //     }
    // }
    // public function actionVendorInfoApi()
    // {
    //     $cms = $types = $items = $projects = $json = [];

    //     foreach (Type::find()->select(['unique_name'])->asArray()->all() as $type) {
    //         $types[$type['unique_name']] = (string)$type['_id'];
    //     }

    //     foreach (Vendor::find()->select(['name', 'settings'])->asArray()->all() as $vendor) {
    //         $vid = (string)$vendor['_id'];

    //         $json[$vid]['name'] = $vendor['name'];
    //         foreach (array_intersect($types, $vendor['settings']['cms']['list']) as $type_unique_name => $type_id) {
    //             $cms[$vid][$type_unique_name] = [];
    //         }
    //     }

    //     foreach (Item::find()->select(['vid', 'type'])->asArray()->all() as $item) {
    //         if (array_key_exists($item['vid'], $cms) && isset($cms[$item['vid']][$item['type']])) {
    //             $json[$item['vid']][$item['type']][] = (string)$item['_id'];
    //         }
    //     }

    //     foreach (Project::find()->select(['_id', 'vid'])->asArray()->all() as $project) {
    //         $json[$project['vid']]['pidlist'][]['pid'] = (string)$project['_id'];
    //     }

    //     foreach (VendorUser::find()->where(['status' => VendorUser::STATUS_ACTIVE, 'role' => ['$in' => [Yii::$app->getModule('members')->salesRoleName, Yii::$app->getModule('members')->managerRoleName]]])->select(['pid', 'login'])->asArray()->all() as $vendor_user) {
    //         if (array_key_exists($vendor_user['login']['vendor']['vid'], $json)) {
    //             foreach ($json[$vendor_user['login']['vendor']['vid']]['pidlist'] as $key => $pidlist) {
    //                 if (in_array($pidlist['pid'], $vendor_user['pid'])) {
    //                     $json[$vendor_user['login']['vendor']['vid']]['pidlist'][$key]['sid'][] = (string)$vendor_user['_id'];
    //                 }
    //             }
    //         }
    //     }

    //     return json_encode($json);
    // }

    public function actionIndex()
    {
        return $this->render('index');
    }

    // public function actionChris()
    // {
    //     $model = new Chris();

    //     return $this->render('chris', [
    //         'model' => $model,
    //     ]);
    // }

    public function actionEnsureindex()
    {
        print_r($result = Yii::$app->mongodb->getCollection('taxonomy_item')->createIndex(
            ['vid' => 1]
        ));

        print_r($result = Yii::$app->mongodb->getCollection('Income_statement_expenses')->createIndex(
            ['pid' => 1]
        ));

        print_r($result = Yii::$app->mongodb->getCollection('Income_statement_taxonomy')->createIndex(
            [
                'pid' => 1,
                'path' => 1,
            ]
        ));

        print_r($result = Yii::$app->mongodb->getCollection('call_log')->createIndex(
            [
                'pid' => 1,
                'ad_media' => 1,
                'sid' => 1,
                'vid' => 1,
                'county' => 1,
                'district' => 1,
                'type' => 1,               
            ]
        ));

        print_r($result = Yii::$app->mongodb->getCollection('guest')->createIndex(
            [
                'pid' => 1,
                'vid' => 1,
                'sid' => 1,
                'ad_media' => 1,
                'status' => 1,
                'county' => 1,
                'district' => 1,
                'created_at' => 1,
                'updated_at' => 1,
            ]
        ));

        print_r($result = Yii::$app->mongodb->getCollection('guest_track_log')->createIndex(
            [
                'pid' => 1,
                'vid' => 1,
                'sid' => 1,
                'gid' => 1,
            ]
        ));

        print_r($result = Yii::$app->mongodb->getCollection('log_book')->createIndex(
            [
                'pid' => 1,
                'vid' => 1,
                'sid' => 1,
            ]
        ));        

        print_r($result = Yii::$app->mongodb->getCollection('house_building')->createIndex(
            ['pid' => 1]
        ));

        print_r($result = Yii::$app->mongodb->getCollection('house_item')->createIndex(
            [
                'pid' => 1,
                'type' => 1,
            ]
        ));

        print_r($result = Yii::$app->mongodb->getCollection('project')->createIndex(
            [
                'settings' => 1,
                'vid' => 1,
                'active' => 1,
                'active_date' => 1,
                'expire_date' => 1,
            ]
        ));

        print_r($result = Yii::$app->mongodb->getCollection('project_bonus_order')->createIndex(
            [
                'status' => 1,
                'user_bonus' => 1,
            ]
        ));

        print_r($result = Yii::$app->mongodb->getCollection('project_commission_period_order')->createIndex(
            [
                'pid' => 1,
                'periods' => 1,
            ]
        ));

        print_r($result = Yii::$app->mongodb->getCollection('project_order')->createIndex(
            [
                'pid' => 1,
                'status' => 1,
                //'items' => 1,
                'main_item' => 1,
            ]
        ));

        // print_r($result = Yii::$app->mongodb->getCollection('project_order')->createIndex(
        //     [
        //         'items' => 'hashed',
        //     ]
        // ));        

        print_r($result = Yii::$app->mongodb->getCollection('project_commission_period')->createIndex(
            ['pid' => 1]
        ));

        print_r($result = Yii::$app->mongodb->getCollection('user')->createIndex(
            [
                'pid' => 1,
                'login' => 1,
                'role' => 1,
                'status' => 1,
            ]
        ));

        print_r($result = Yii::$app->mongodb->getCollection('admin_user')->createIndex(
            [
                'login' => 1,
                'role' => 1,
                'status' => 1,
            ]
        ));

        print_r($result = Yii::$app->mongodb->getCollection('vendor')->createIndex(
            [
                'module' => 1,
                'status' => 1,
                'settings' => 1,
            ]
        ));

        print_r($result = Yii::$app->mongodb->getCollection('rbac_assignment')->createIndex(
            [
                '_id' => 1,
                'user_id' => 1,
            ]
        ));

        print_r($result = Yii::$app->mongodb->getCollection('rbac_item')->createIndex(
            [
                '_id' => 1,
                'type' => 1,
                'description' => 1,
                'name' => 1,
            ]
        ));

        print_r($result = Yii::$app->mongodb->getCollection('rbac_item_child')->createIndex(
            [
                'children' => 1,
            ]
        ));  


        print_r($result = Yii::$app->mongodb->getCollection('announcement')->createIndex(
            [
                'read' => 1,
            ]
        ));  

        print_r($result = Yii::$app->mongodb->getCollection('to_do_list')->createIndex(
            [
                'assign_users' => 1,
                'vid' => 1,
            ]
        ));

        print_r($result = Yii::$app->mongodb->getCollection('alert_item')->createIndex(
            [
                'type_item' => 1,
                'assign_item' => 1,
            ]
        ));                                               
    }

    // public function actionFixBonusStatus1()
    // {
    // 	$result = Yii::$app->mongodb->getCollection('project_bonus_order')->update(
    // 		['status' => 1],
    // 		['status' => 0]
    // 	);

    // 	if ($result) {
    // 		Yii::$app->session->setFlash('success', Yii::t('common/app', 'Update Success.'));
    // 	} else {
    // 		Yii::$app->session->setFlash('danger', Yii::t('common/app', 'Update Fail.'));
    // 	}

    // 	return $this->redirect(['index']);
    // }

    // public function actionFixBonusStatus2()
    // {
    // 	$result = Yii::$app->mongodb->getCollection('project_bonus_order')->update(
    // 		['status' => 2],
    // 		['status' => 1]
    // 	);

    // 	if ($result) {
    // 		Yii::$app->session->setFlash('success', Yii::t('common/app', 'Update Success.'));
    // 	} else {
    // 		Yii::$app->session->setFlash('danger', Yii::t('common/app', 'Update Fail.'));
    // 	}

    // 	return $this->redirect(['index']);
    // }

    public function actionFixCallLogType1()
    {
        $result = Yii::$app->mongodb->getCollection('call_log')->update(
            ['type' => '1'],
            ['type' => 1]
        );

        if ($result) {
            Yii::$app->session->setFlash('success', Yii::t('common/app', 'Update Success.'));
        } else {
            Yii::$app->session->setFlash('danger', Yii::t('common/app', 'Update Fail.'));
        }

        return $this->redirect(['index']);
    }

    public function actionFixCallLogType2()
    {
        $result = Yii::$app->mongodb->getCollection('call_log')->update(
            ['type' => '2'],
            ['type' => 2]
        );

        if ($result) {
            Yii::$app->session->setFlash('success', Yii::t('common/app', 'Update Success.'));
        } else {
            Yii::$app->session->setFlash('danger', Yii::t('common/app', 'Update Fail.'));
        }

        return $this->redirect(['index']);
    }

    public function actionFixGuestStatus()
    {
        //use mongo shell to update type (string to int)
        /*
        db.guest.find( { 'status' : { $exists : 1 } } ).forEach( function (x) {   
          x.status = parseInt(x.status); // convert field to string
          db.guest.save(x);
        });

        or simple inline 
        db.guest.find({'status':{$exists:1}}).forEach(function(x){x.status=parseInt(x.status);db.guest.save(x);});
        */
       $f = 0;
       $vendors = Vendor::find()->distinct('_id');

       foreach ($vendors as $vendor) {
            $result = [];
            $result = Item::find()->where(['vid' => (string)$vendor, 'type' => 'guest_status'])->select(['spec_id'])
                ->asArray()->all();

            $data = [];    
            foreach ($result as $value) {
                $data[(string)$value['spec_id']] = (string)$value['_id'];
            }

            $guests = Guest::find()->where(['vid' => (string)$vendor, 'status' => ['$gt' => 0, '$lt' => 101]])->all();

            foreach ($guests as $guest) {
                if (!empty($data[$guest->status])) {
                    $guest->status = $data[$guest->status];
                    if ($guest->save(true, ['status'])) {
                        $f++;
                    }
                }    
            }
       }

       echo $f;
    }

    public function actionOrderSalesArrayFix()
    {
        $models = Yii::$app->mongodb->getCollection('project_order')->find();
        $result = '';

        foreach ($models as $model) {
            $newSales = array_values($model['sales']);

            Yii::$app->mongodb->getCollection('project_order')->update(
                [],
                ['sales' => $newSales]
            );
        }

        echo $result;
    }

    public function actionFixOrderMedia()
    {
        $models = Order::find()->all();
        $count = 0;
        foreach ($models as $model) {
            $model->ads = (array)$model->ads;
            if ($model->save(true, ['ads'])) {
                $count++;
            }
                
        }

        echo $count;
    }

    public function actionFixParking()
    {
        $models = Order::find()->all();
        $count = 0;
        foreach ($models as $model) {
            if ($model->save(true, ['parking_space_items'])) {
                $count++;
            }            
        }

        echo $count;
    }  

    public function actionFixHouseItem(){
        $models = HouseItem::find()->where(['no' => new \MongoRegex("/^[1-9]+(f|F|層|樓)+$/"), 'type' => ['$in' => [ProjectItem::TYPE_HOUSEHOLD]]])->all();
        $count = 0;
        //TODO 店面用這匯輯會爆....
        //may strlen > 1, no => $like 'f', 'F'
        //還要排除店面
        $text = '';
        foreach ($models as $model) {
                $model->no = (string)intval($model->no);
                if ($model->save(true, ['no'])) {
                    $text .= $model->no.'<br>';
                    $count++;
                }
                else {
                    $text = $model->getErrors();
                }             
        }

        // $models = HouseItem::find()->where(['type' => ['$in' => [ProjectItem::TYPE_HOUSEHOLD, ProjectItem::TYPE_STORE]]])->all();

        // foreach ($models as $model) {
        //         $max_floor = empty($model->floor) ? intval($model->no) : $model->floor;
        //         if($max_floor == 0) {
        //             $max_floor = HouseItem::find()->where([
        //                 'type' => ['$in' => [ProjectItem::TYPE_HOUSEHOLD, ProjectItem::TYPE_STORE]],
        //                 'pid' => $model->pid,
        //                 'bid' => $model->bid,
        //             ])->max('floor') + 1;

                    
        //         }

        //         $model->no = (string)$model->no;
        //         $model->floor = $max_floor;

        //         if ($model->save(true, ['no', 'floor'])) {
        //             $text .= $model->floor.'<br>';
        //             $count++;
        //         }                
        // }        

        echo '<pre>';
        echo $count.'<br>'; 
        print_r($text);       
    }

    //把使用者曾經有過的pid 加到pid經歷欄位(給看全案檢索用)
    public function actionFuep() {
        $users = VendorUser::find()->where(['login.vendor.vid' => ['$exists' => 1]])->all();
        $count = 0;
        foreach ($users as $user) {
            $pid = [];
            $my_guest = Guest::find()->where(['sid' => $user->getId()])->asArray()->select(['_id' => false, 'pid'])->all();

            foreach ($my_guest as $guest) {
                $pid[] = $guest['pid'];
            }

            $projects = Project::getListByVendor($user->vid);

            $user_pid = $user->pid;
            foreach ($user_pid as $key => $_pid) {
                if (!in_array($_pid, array_keys(array_merge($projects['active'], $projects['unActive'])))) {
                    unset($user_pid[$key]);        
                }
            }
            $user->pid = array_values((array)$user_pid);

            $user->experience_pid = array_values(array_unique(array_merge($user->pid, $pid)));


            //預設user 初次更改密碼記錄為 無
            $user->passwd_changed = '0';

            if ($user->save(true, ['pid', 'experience_pid', 'passwd_changed'])) {
                $count++;
            }    
        }

        echo $count;
    } 

    public function actionFixVendor(){
        echo '<pre>';
        //print_r(array_keys(Vendor::getVendorList()));
        echo '<br>';
        $users_vendor = VendorUser::find()->distinct('login.vendor.vid');
        print_r($users_vendor);
        $no_exists_vendor = array_values(array_diff($users_vendor, array_keys(Vendor::getVendorList())));
        print_r($no_exists_vendor);

        $no_exists_project = Project::find()->where(['vid' => ['$in' => $no_exists_vendor]])->all();

        print_r($no_exists_project);

        $no_exists_user =  VendorUser::find()->where(['login.vendor.vid' => ['$in' => $no_exists_vendor]])->all();
        print_r($no_exists_user);

        foreach ($no_exists_user as $user) {
            $user->delete();
        }
    }
}
