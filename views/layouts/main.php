<?php

/* @var $this \yii\web\View */
/* @var $content string */

use app\widgets\Alert;
use yii\helpers\Html;
use yii\bootstrap\Nav;
use yii\bootstrap\NavBar;
use yii\widgets\Breadcrumbs;
use app\assets\AppAsset;
use kartik\sidenav\SideNav;



AppAsset::register($this);
?>
<?php $this->beginPage() ?>
<!DOCTYPE html>
<html lang="<?= Yii::$app->language ?>">
<head>
    <meta charset="<?= Yii::$app->charset ?>">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
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
<body>
<?php $this->beginBody() ?>

<?php if (!Yii::$app->user->isGuest): ?>

    <?php if (Yii::$app->user->identity->role == 'provinsi' || Yii::$app->user->identity->role == 'kabkota'  || Yii::$app->user->identity->role == 'pusat' ): ?>

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
                        'label' => 'Kelola Akun',
                        'url' => ['/pengguna/index'],
                        'visible' => !Yii::$app->user->isGuest,
                        'active' => $this->context->route == 'pengguna/index' ||  $this->context->route == 'pengguna/view' ||  $this->context->route == 'pengguna/update' ||  $this->context->route == 'pengguna/create', 
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

            <div class="container-fluid">
                <br /><br /><br />
                <div class="kiri" style="padding-left: 0px; padding-right: 0px;background: #f0f0f0">
                    <?php
                        echo SideNav::widget([      
                            'type' => SideNav::TYPE_PRIMARY,
                            'heading' => false,

                            'items' => [
                                [
                                    'url' => ['/site/index'],
                                    'label' =>  'Dashboard',
                                    'icon' => 'home',
                                    'visible' => !Yii::$app->user->isGuest,
                                    'active' => $this->context->route == 'site/index' ||  $this->context->route == 'ubah-data-kabkota/index' ||  $this->context->route == 'ubah-data-kabkota/view' ||  $this->context->route == 'ubah-data-kabkota/update', 
                                ],
                                  //Yang bisa ngolah data master dan proyeksi cuman yang udah login.
                                [
                                    'label' => 'Data Master',
                                    'icon' => 'flag',
                                    'items' => [
                                        [
                                            'label' => 'Data Kabupaten/Kota', 
                                            'url' => ['/data-kabkota-lp/index'],
                                            'active' => $this->context->route == 'data-kabkota-lp/index' || $this->context->route == 'data-kabkota-lp/view' || $this->context->route == 'data-kabkota-lp/lihat' || $this->context->route == 'ubah-data-kabkota/create' || $this->context->route == 'ubah-data-kabkota/update' || $this->context->route == 'data-kabkota-lp/update' || $this->context->route == 'data-kabkota-lp/import',
                                        ],
                                        [
                                            'label' => 'Data Provinsi', 
                                            'url' => ['/data-provinsi-lp/index'],
                                            'active' => $this->context->route == 'data-provinsi-lp/index' || $this->context->route == 'data-provinsi-lp/view' || $this->context->route == 'data-provinsi-lp/lihat' || $this->context->route == 'data-provinsi-lp/lihatdetail' || $this->context->route == 'data-provinsi-lp/update' || $this->context->route == 'data-provinsi-lp/create' || $this->context->route == 'data-provinsi-lp/import',
                                        ],
                                    ],
                                    'visible' => !Yii::$app->user->isGuest,
                                ],
                                [
                                    'label' => 'Hitung Proyeksi',
                                    'icon' => 'play',
                                    'items' => [
                                        [
                                            'label' => 'Buat Proyeksi', 
                                            'url' => ['/site/proykabkota'],
                                            'active' => $this->context->route == 'site/proykabkota' || $this->context->route == 'site/pilihtahun'
                                        ],
                                        [
                                            'label' => 'Hasil Proyeksi Tahunan',
                                            'url' => ['/hasil-proyeksi-jumlah/index'],
                                            'active' => $this->context->route == 'hasil-proyeksi-jumlah/index' || $this->context->route == 'hasil-proyeksi-jumlah/lihat' || $this->context->route == 'hasil-proyeksi-jumlah/view'
                                        ],
                                        [
                                            'label' => 'Hasil Proyeksi Bulanan',
                                            'url' => ['/hasil-proyeksi-jumlah-bulan/index'],
                                            'active' => $this->context->route == 'hasil-proyeksi-jumlah-bulan/index' || $this->context->route == 'hasil-proyeksi-jumlah-bulan/lihat'
                                        ],
                                        [
                                            'label' => 'Hasil Proyeksi KU',
                                            'url' => ['/hasil-proyeksi-lp/index'],
                                            'active' => $this->context->route == 'hasil-proyeksi-lp/index' || $this->context->route == 'hasil-proyeksi-lp/lihatkabkota' || $this->context->route == 'hasil-proyeksi-lp/view' || $this->context->route == 'hasil-proyeksi-lp/lihat'
                                        ],
                                    ],
                                    'visible' => !Yii::$app->user->isGuest,
                                ],

                                  [
                                      'label' => 'Help',
                                      'icon' => 'question-sign',
                                      'items' => [
                                          ['label' => 'About', 'icon'=>'info-sign', 'url'=>'#'],
                                          ['label' => 'Contact', 'icon'=>'phone', 'url'=>'#'],
                                      ],
                                  ],
                              ],
                        ]);  
                    ?>

                    <?php if (Yii::$app->user->identity->role == 'provinsi'): ?>
                        <div style="margin-top: 20px; padding-top: 0px; padding-bottom: 10px;padding-left: 10px; padding-right: 10px;">
                            <p align="justify" style="font-size:14px;"><b>
                                Proyeksi penduduk tingkat kabupaten/kota dibuat dengan metode geometrik. </br><br>
                                Pastikan <?= Html::a('data penduduk tingkat kabupaten/kota', ['data-kab-kota/index']) ?> dan <?= Html::a('data proyeksi tingkat provinsi', ['proy-prov/index']) ?> telah benar.</b>
                            </p>
                        </div>
                    <?php endif; ?>
                </div>


                <div class="kanan" style="padding-left: 5px; padding-right: 0px;">
                    <?= $content ?>
                </div>


            </div>

        </div>

    <?php elseif (Yii::$app->user->identity->role == 'admin'): ?>
        <div class="wrap">
            <?php
            NavBar::begin([
                'brandLabel' => 'SIPROPEN - Admin',
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
                        'active' => $this->context->route == 'site/index',
                    ],

                    //Yang bisa ngolah data master dan proyeksi cuman yang udah login.
                    [
                        'label' => 'Kelola Akun',
                        'url' => ['/pengguna/index'],
                        'visible' => !Yii::$app->user->isGuest,
                        'active' => $this->context->route == 'pengguna/index' ||  $this->context->route == 'pengguna/view' ||  $this->context->route == 'pengguna/update' ||  $this->context->route == 'pengguna/create', 
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

    <?php endif; ?>

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

<footer class="footer">
    <div class="container">
        <p class="pull-left">&copy; Satria Bagus Panuntun <?= date('Y') ?></p>

        <p class="pull-right">Demografi BPS</p>
    </div>
</footer>



<?php $this->endBody() ?>
</body>
</html>
<?php $this->endPage() ?>