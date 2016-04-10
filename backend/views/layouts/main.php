<?php

use kartik\nav\NavX;
use yii\helpers\Html;
use yii\bootstrap\Nav;
use yii\bootstrap\NavBar;
use yii\widgets\Breadcrumbs;
use frontend\widgets\Alert;
use backend\assets\AppAsset;
use backend\modules\monochrome\topUp\TopUp;
use common\modules\monochrome\members\Members;
use common\modules\monochrome\userReport\UserReport;
use common\modules\monochrome\announcement\models\Announcement;
use common\modules\monochrome\announcement\Announcement as AnnouncementModule;

/* @var $this \yii\web\View */
/* @var $content string */

AppAsset::register($this);

$this->registerCss(
"
div.required > label:after {
    content: ' *';
    color: red;
}

@media (max-width: 767px) {
    .navbar-nav .open .dropdown-menu > li > a, .navbar-nav .open .dropdown-menu .dropdown-header {
        padding: 6px 15px 6px 25px;
    }

    .navbar-nav ul ul a {
        text-indent: 2em;
    }

    .navbar-nav ul ul ul a {
        text-indent: 4em;
    }

    .dropdown-menu {
        font-size:15px;
    }

    h1 {
        font-size: 28px;
    }    
}
"
);

$badgeCount1 = Announcement::unreadCount(Yii::$app->user->getId());

$badge1 = $badgeCount1 > 0 ? '<span class="badge alert-danger">'.$badgeCount1.'</span>' : '';

?>
<?php $this->beginPage() ?>
<!DOCTYPE html>
<html lang="<?= Yii::$app->language ?>">
<head>
    <meta charset="<?= Yii::$app->charset ?>">
    <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no">
    <?= Html::csrfMetaTags() ?>
    <title><?= Html::encode($this->title) ?></title>
    <?php $this->head() ?>
</head>
<body>
    <?php $this->beginBody() ?>
    <div class="wrap">
        <?php
            NavBar::begin([
                'brandLabel' => '樂誌科技代銷系統管理後台',
                'brandUrl' => Yii::$app->homeUrl,
                'options' => [
                    'class' => 'navbar-inverse navbar-fixed-top',
                ],
            ]);
            if (Yii::$app->user->isSuperadmin()) {
                $menuItems = [
                    [
                        'label' => 'System',
                        'items' => [
                            [
                                'label' => 'RBAM',
                                'items' => [
                                    ['label' => 'Role', 'url' => ['/rbam/item/index/1']],
                                    ['label' => 'Permission', 'url' => ['/rbam/item/index/2']],
                                    ['label' => 'Field', 'url' => ['/rbam/item/index/3']]
                                ]
                            ],
                            [
                                'label' => 'Trace',
                                'items' => [
                                    ['label' => 'All Trace Log', 'url' => ['/trace/trace-log/index']],
                                    ['label' => 'Normal User Trace Log', 'url' => ['/trace/trace-log/normal-user']],
                                    ['label' => 'Vendor User Trace Log', 'url' => ['/trace/trace-log/vendor-user']],
                                    ['label' => 'Admin User Trace Log', 'url' => ['/trace/trace-log/admin-user']],
                                ]
                            ],
                            [
                                'label' => AnnouncementModule::t('app', 'Announcement'),
                                'url' => ['/announcement/announcement/index'],
                            ],
                            [
                                'label' => UserReport::t('app', 'Report List'),
                                'url' => ['/userReport/user-report/index']
                            ]                                                        
                        ]
                    ], 
                    [
                        'label' => 'Vendor Work',
                        'items' => [
                            ['label' => 'Vendor Manager', 'url' => ['/members/vendor/index']],
                            ['label' => 'Project Manager', 'url' => ['/project/project-super/index']],
                            [
                                'label' => TopUp::t('app', 'Top Up Work'),
                                'items' => [
                                    ['label' => TopUp::t('app', 'Vendor Top Up Record'), 'url' => ['/top-up/vendor/index']],
                                    ['label' => TopUp::t('app', 'Project Top Up Log'), 'url' => ['/top-up/record/index']],
                                ],
                            ],
                        ],
                    ],
                    [
                        'label' => 'CMS',
                        'url' => ['/taxonomy/type/index'],
                    ],                         
                    [
                        'label' => 'Users',
                        'items' => [
                            ['label' => 'Normal Users', 'url' => ['/members/user/index']],
                            ['label' => 'Vendor Users', 'url' => ['/members/vendor-user/list']],
                        ]
                    ],               
                ];
            }
            if (Yii::$app->user->isGuest) {
                $menuItems[] = ['label' => 'Login', 'url' => ['/login']];
            } else {
                $menuItems[] = [
                    'label' => Yii::$app->user->identity->username.$badge1,
                    'items' => [
                        ['label' => Members::t('app', 'Profile'), 'url' => ['/members/user/profile']],
                        ['label' => AnnouncementModule::t('app', 'Announcement').$badge1, 'url' => ['/announcement/list']],
                        ['label' => UserReport::t('app', 'Suggestion And Report'), 'url' => ['/user-report/report']],
                        ['label' => Members::t('app', 'Logout'), 'url' => ['/logout'], 'linkOptions' => ['data-method' => 'post']],
                    ],
                    
                    
                ];
            }
            echo Navx::widget([
                'options' => ['class' => 'navbar-nav navbar-right'],
                'items' => $menuItems,
                'encodeLabels' => false,
            ]);
            NavBar::end();
        ?>

        <div class="container">
        <?= Breadcrumbs::widget([
            'links' => isset($this->params['breadcrumbs']) ? $this->params['breadcrumbs'] : [],
        ]) ?>
        <?= Alert::widget() ?>
        <?= $content ?>
        </div>
    </div>

    <footer class="footer">
        <div class="container">
        <p class="pull-left">&copy; My Company <?= date('Y') ?></p>
        <p class="pull-right"><?= Yii::powered() ?></p>
        </div>
    </footer>

    <?php $this->endBody() ?>
</body>
</html>
<?php $this->endPage() ?>
