<?php

/* @var $this \yii\web\View */
/* @var $content string */

use yii\helpers\Html;
use app\assets\AdminLteAsset;
use app\widgets\Alert;
use yii\bootstrap\Nav;
use yii\bootstrap\NavBar;
use yii\widgets\Breadcrumbs;
use app\assets\AppAsset;
use kartik\sidenav\SideNav;

//AppAsset::register($this);
$asset      = AdminLteAsset::register($this);
$baseUrl    = $asset->baseUrl;

?>

<?php $this->beginPage() ?>
<!DOCTYPE html>
<html lang="<?= Yii::$app->language ?>">
<head>
    <meta charset="<?= Yii::$app->charset ?>">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <?= Html::csrfMetaTags() ?>
    <title><?= Html::encode($this->title) ?></title>
    <?php $this->head() ?>

    <style type="text/css">
        body {
            background: white;
        }
        .content {
            margin-bottom: 30px;
            width: 100%;
            background: #fbfbfb;
            border-radius: 5px;
            padding: 10px;
        }
        .content:hover {
            background: #f5f5f5;
        }
        .content-title a {
            font-size: 18px;
            font-color: #333;
            width: 100%;
            border-bottom:1px dotted #ccc;
        }
        .content-detail {
            font-size: 10px;
            width: 100%;
            color: blue;
            margin-bottom: 10px;
        }
        .content-fill {
            width: 100%;
            font-size: 12px;
        }
        .my-navbar {
            background-color: black;
            font-color: white;
        }
        .kiri {
            position: fixed;
            width: 18.5%;
            float: left;
        }
        .kanan {
            width: 81%;
            float: right;
        }
        .kiri_main {
            width: 75%;
            float: left;
            margin-top: 0px; 
            margin-left: 0px; 
            padding-top: 10px; 
            padding-bottom: 10px;
        }
        .kanan_main {
            width: 24%;
            float: right;
        }
        .scroll {
            overflow: auto;
            overflow-y: hidden;
        }
        .tableDB {
            font-family: Arial, Helvetica, sans-serif;
            color: black;
            text-shadow: 1px 1px 0px #fff;
            background: white;
            border: #ccc 1px solid transparent;
        }  
        #centerTable { 
            margin: 0px auto; 
        }
    </style>

</head>
<body class="fixed skin-green sidebar-mini">
<?php $this->beginBody() ?>

    <?php if (!Yii::$app->user->isGuest): ?>

        <div class="wrapper">
            <?= $this->render('header.php', ['baserUrl' => $baseUrl, 'title'=>Yii::$app->name]) ?>
            <?= $this->render('leftside.php', ['baserUrl' => $baseUrl]) ?>
            <?= $this->render('content.php', ['content' => $content]) ?>
            <?= $this->render('footer.php', ['baserUrl' => $baseUrl]) ?>
            <?= $this->render('rightside.php', ['baserUrl' => $baseUrl]) ?>
        </div>

<?php elseif (Yii::$app->user->isGuest): ?>
    <div class="wrap">
        <?php
        NavBar::begin([
            'brandLabel' => 'SIPROPEN - Sistem Proyeksi Penduduk',
            'brandUrl' => Yii::$app->homeUrl,
            'innerContainerOptions' => ['class' => 'container-fluid'],
            'options' => [
                'class' => 'navbar-inverse navbar-fixed-top',
            ],
        ]);
        echo Nav::widget([
            'options' => ['class' => 'navbar-nav navbar-right'],
            'items' => [
                [
                    'label' => 'Dashboard', 
                    'url' => ['/site/index'],
                    'icon' => 'home',
                    'visible' => !Yii::$app->user->isGuest,
                ],

                //Yang bisa ngolah data master dan proyeksi cuman yang udah login.
                [
                    'label' => 'Data Master',
                    'items' => [
                        ['label' => 'Data Kabupaten/Kota', 'url' => ['/data-kab-kota/index'],],
                        ['label' => 'Data Proyeksi Provinsi', 'url' => ['/proy-prov/index'],],
                    ],
                    'visible' => !Yii::$app->user->isGuest,
                ],

                [
                    'label' => 'Hitung Proyeksi',
                    'items' => [
                        ['label' => 'Proyeksi Nasional', 'url' => ['/site/proynasional'],],
                        '<li class="divider"></li>',
                        ['label' => 'Proyeksi Provinsi', 'url' => ['/site/proyprovinsi'],],
                        '<li class="divider"></li>','<li class="dropdown-header">Proyeksi Kabupaten/kota</li>',
                        ['label' => 'Buat Proyeksi', 'url' => ['/site/proykabkota'],],
                        ['label' => 'Lihat Hasil Proyeksi', 'url' => ['/hasil-proyeksi/index'],],
                    ],
                    'visible' => !Yii::$app->user->isGuest,
                ],

                [
                    'label' => 'About', 
                    'url' => ['/site/about'],
                    'visible' => Yii::$app->user->isGuest,
                ],      

                //Untuk membedakan antara yang login dan tidak.
                Yii::$app->user->isGuest ? (
                    ['label' => 'Login', 'url' => ['/site/login']]
                ) : (
                    '<li>'
                    . Html::beginForm(['/site/logout'], 'post')
                    . Html::submitButton(
                        'Logout (BPS ' . Yii::$app->user->identity->username . ')',
                        ['class' => 'btn btn-link logout']
                    )
                    . Html::endForm()
                    . '</li>'
                )
            ],
        ]);
        NavBar::end();
        ?>

        <div class="container">
            <?= $content ?>
        </div>

    </div>

    <?php endif; ?>

<!--footer class="footer">
    <div class="container">
        <p class="pull-left">&copy; My Company <?//= date('Y') ?></p>

        <p class="pull-right"><?//= Yii::powered() ?></p>
    </div>
</footer-->

<?php $this->endBody() ?>
</body>
</html>
<?php $this->endPage() ?>
