<?php

/* @var $this \yii\web\View */
/* @var $content string */

/** @var AppVersion $appVersion */
$appVersion = $this->params['appVersion'];

use common\component\UserRestApiMediator;
use mmo\yii2\helpers\AppVersionHelper;
use mmo\yii2\models\AppVersion;
use yii\helpers\Html;
use yii\bootstrap\Nav;
use yii\bootstrap\NavBar;
use yii\widgets\Breadcrumbs;
use frontend\assets\AppAsset;
use common\widgets\Alert;

AppAsset::register($this);
\frontend\assets\WebpackAsset::register($this);
?>
<?php $this->beginPage() ?>
<!DOCTYPE html>
<html lang="pl">
<head>
    <meta charset="<?= Yii::$app->charset ?>">
    <link rel="icon" href="/image/sensisoft.png" type="image/x-icon"/>
    <link href="/css/lightbox.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css?family=Open+Sans:300" rel="stylesheet">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <?= Html::csrfMetaTags() ?>
    <title><?= Html::encode($this->title) ?></title>
    <script type="text/javascript">
        function getJWTToken() {
            return '<?= Yii::$app->session->get(UserRestApiMediator::JWT_SESSION_KEY) ?>';
        }
    </script>
    <?php $this->head() ?>
</head>
<body>
<?php $this->beginBody() ?>

<div class="wrap">
    <?php
    NavBar::begin([
        'brandLabel' => '<img src="/image/sensilabs-logo.png">',
        'brandUrl' => Yii::$app->homeUrl,
        'options' => [
            'class' => 'navbar-inverse navbar-fixed-top',
        ],
    ]);
    $menuItems = [
        ['label' => 'Home', 'url' => ['/site/index']],
        //['label' => 'About', 'url' => ['/site/about']],
        //['label' => 'Contact', 'url' => ['/site/contact']],
    ];
    if (Yii::$app->user->isGuest) {
        $menuItems[] = ['label' => 'Signup', 'url' => ['/site/signup']];
        $menuItems[] = ['label' => 'Login', 'url' => ['/site/login']];
    } else {
        $menuItems[] = ['label' => 'Dodaj Restaurację', 'url' => ['/restaurants/upload']];
        $menuItems[] = ['label' => 'Zamówienia', 'url' => ['/order/index']];
        $menuItems[] = ['label' => 'Rozliczenie', 'url' => ['/payment/manage']];
        $menuItems[] = ['label' => 'Historia', 'url' => ['/history/my']];
        $menuItems[] = [
            'label' => 'TY',
            'items' => [
                ['label' => 'Profil', 'url' => ['/profile']],
                [
                    'label' => 'Logout (' . Yii::$app->user->identity->username . ')',
                    'url' => ['/site/logout'],
                ]
            ]
        ];
    }
    echo Nav::widget([
        'options' => ['class' => 'navbar-nav navbar-right'],
        'items' => $menuItems,
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
    <div id="react-toastify"></div>
</div>

<footer class="footer">
    <div class="container">
        <p class="pull-left">
            &copy; Stasiu 2017 - <?= date('Y') ?>
        </p>
        <p class="pull-right">
            <?= Yii::t('app', 'Wersja') ?> <?= $appVersion->getVersion(); ?>
        </p>
    </div>
</footer>
<script src="/js/lightbox-plus-jquery.js"></script>
<?php $this->endBody() ?>
</body>
</html>
<?php $this->endPage() ?>
