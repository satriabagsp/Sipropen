<?php

use adminlte\widgets\Menu;
use yii\helpers\Html;
use yii\helpers\Url;


?>
<!-- Left side column. contains the logo and sidebar -->
<aside class="main-sidebar">
    <!-- sidebar: style can be found in sidebar.less -->
    <section class="sidebar">
        <!-- sidebar menu: : style can be found in sidebar.less -->
        <?php if (Yii::$app->user->identity->role == 'provinsi' || Yii::$app->user->identity->role == 'kabkota'  || Yii::$app->user->identity->role == 'pusat' ): ?>
            <?=
                Menu::widget(
                    [
                        'options' => ['class' => 'sidebar-menu'],
                        'items' => [
                            ['label' => 'Menu', 'options' => ['class' => 'header']],
                            [
                                'label' => 'Dashboard', 
                                'icon' => 'fa fa-dashboard', 
                                'url' => ['/'], 
                                'active' => $this->context->route == 'site/index' ||  $this->context->route == 'ubah-data-kabkota/index' ||  $this->context->route == 'ubah-data-kabkota/view' ||  $this->context->route == 'ubah-data-kabkota/update', 
                            ],
                            [
                                'label' => 'Data Master',
                                'icon' =>  'glyphicon glyphicon-list-alt',
                                'url' => '#',
                                'items' => [
                                    [
                                        'label' => 'Data Kabupaten/kota',
                                        'icon' => 'glyphicon glyphicon-folder-open',
                                        'url' => ['data-kabkota-lp/index'],
    				                    'active' => $this->context->route == 'data-kabkota-lp/index' || $this->context->route == 'data-kabkota-lp/view' || $this->context->route == 'data-kabkota-lp/lihat' || $this->context->route == 'ubah-data-kabkota/create',
                                    ],
                                    [
                                        'label' => 'Data Provinsi',
                                        'icon' => 'glyphicon glyphicon-folder-open',
                                        'url' => ['/data-provinsi-lp/index'],
    				                    'active' => $this->context->route == 'data-provinsi-lp/index' || $this->context->route == 'data-provinsi-lp/view' || $this->context->route == 'data-provinsi-lp/lihat' || $this->context->route == 'data-provinsi-lp/lihatdetail',
                                    ]
                                ]
                            ],
                            [
                                'label' => 'Hitung Proyeksi',
                                'icon' => 'glyphicon glyphicon-tasks',
                                'url' => '#',
                                'items' => [
                                    [
                                        'label' => 'Buat Proyeksi',
                                        'icon' => 'glyphicon glyphicon-edit',
                                        'url' => ['/site/proykabkota'],
                                        'active' => $this->context->route == 'site/proykabkota' || $this->context->route == 'site/pilihtahun'
                                    ],
                                    [
                                        'label' => 'Hasil Proyeksi Tahunan',
                                        'icon' => 'glyphicon glyphicon-file',
                                        'url' => ['/hasil-proyeksi-jumlah/index'],
                                        'active' => $this->context->route == 'hasil-proyeksi-jumlah/index' || $this->context->route == 'hasil-proyeksi-jumlah/lihat'
                                    ],
                                    [
                                        'label' => 'Hasil Proyeksi Bulanan',
                                        'icon' => 'glyphicon glyphicon-file',
                                        'url' => ['/hasil-proyeksi-jumlah-bulan/index'],
                                        'active' => $this->context->route == 'hasil-proyeksi-jumlah-bulan/index' || $this->context->route == 'hasil-proyeksi-jumlah/lihat'
                                    ],
                                    [
                                        'label' => 'Hasil Proyeksi KU',
                                        'icon' => 'glyphicon glyphicon-file',
                                        'url' => ['/hasil-proyeksi-lp/index'],
                                        'active' => $this->context->route == 'hasil-proyeksi-lp/index' || $this->context->route == 'hasil-proyeksi-lp/lihatkabkota' || $this->context->route == 'hasil-proyeksi-lp/view' || $this->context->route == 'hasil-proyeksi-lp/lihat'
                                    ],
                                ]
                            ],
                            [
                                'label' => 'About',
                                'icon' =>  'glyphicon glyphicon-question-sign',
                                'url' => '#',
                                'items' => [
                                    [
                                        'label' => 'Info',
                                        'icon' => 'glyphicon glyphicon-info-sign',
                                        'url' => '#',
                                        'active' => $this->context->route == '/site/proykabkota'
                                    ],
                                    [
                                        'label' => 'Contact',
                                        'icon' => 'glyphicon glyphicon-phone-alt',
                                        'url' => '#',
                                        'active' => $this->context->route == '/hasil-proyeksi-jumlah/index'
                                    ]
                                ]
                            ],
                            [
                                'label' => 'Kelola Akun',
                                'icon' =>  'glyphicon glyphicon-user',
                                'url' => ['/pengguna/index'],
                                'active' => $this->context->route == 'pengguna/index' ||  $this->context->route == 'pengguna/view' ||  $this->context->route == 'pengguna/update' ||  $this->context->route == 'pengguna/create', 
                            ],
                            ['label' => Yii::$app->user->identity->wilayah_id.' - '.Yii::$app->user->identity->username, 'options' => ['class' => 'header']],
                        ],
                    ]
                )
            ?>

        <?php elseif (Yii::$app->user->identity->role == 'admin'): ?>
            <?=
                Menu::widget(
                    [
                        'options' => ['class' => 'sidebar-menu'],
                        'items' => [
                            ['label' => 'Menu', 'options' => ['class' => 'header']],
                            [
                                'label' => 'Dashboard', 
                                'icon' => 'fa fa-dashboard', 
                                'url' => ['/'], 
                                'active' => $this->context->route == 'site/index' ||  $this->context->route == 'ubah-data-kabkota/index' ||  $this->context->route == 'ubah-data-kabkota/view' ||  $this->context->route == 'ubah-data-kabkota/update', 
                            ],
                            [
                                'label' => 'Kelola Akun',
                                'icon' =>  'glyphicon glyphicon-user',
                                'url' => ['/pengguna/index'],
                                'active' => $this->context->route == 'pengguna/index' ||  $this->context->route == 'pengguna/view' ||  $this->context->route == 'pengguna/update' ||  $this->context->route == 'pengguna/create', 
                            ],
                            ['label' => Yii::$app->user->identity->wilayah_id.' - '.Yii::$app->user->identity->username, 'options' => ['class' => 'header']],
                        ],
                    ]
                )
            ?>

        <?php endif; ?>
        
    </section>
    <!-- /.sidebar -->
</aside>
