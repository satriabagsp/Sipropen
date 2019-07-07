<?php

use yii\helpers\Html;
use yii\widgets\DetailView;
use app\models\DataKabkotaLp;
use yii\data\ActiveDataProvider;
use yii\data\ArrayDataProvider;
use fedemotta\datatables\DataTables;
use miloschuman\highcharts\Highcharts;
use miloschuman\highcharts\SeriesDataHelper;
use yii\bootstrap\Collapse;
use kartik\grid\GridView;

/* @var $this yii\web\View */
/* @var $model app\models\DataKabKota */

$this->title = 'Data Master ' . Yii::$app->user->identity->username;
$this->params['breadcrumbs'][] = ['label' => 'Data Kabupaten Kota', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="data-kab-kota-view">
    <div class="panel panel-info">
    <div class="panel-heading"><h1><?php echo 'Data Master ' . $nama_wilayah ?></h1></div>
        <div class="panel-body">
            <?php for($tahun=0;$tahun<count($tahun_data);$tahun++){ 
                if(substr($tahun_data[$tahun], 3) == '0'){
                    $label = 'SENSUS '.$tahun_data[$tahun];
                }elseif(substr($tahun_data[$tahun], 3) == '5'){
                    $label = 'SUPAS '.$tahun_data[$tahun];
                };
            ?>

            <div class="col-md-6">
                <div class="panel panel-default">
                    <?= Highcharts::widget([ //buat grafik piramida penduduk
                        'scripts' => [
                            'modules/exporting',
                        ],
                        'options' => [
                            'title' => ['text' => 'Piramida penduduk '. $nama_wilayah],
                            'subtitle' => [
                                'text' => $label,
                            ],
                            'chart' => [
                                'type' => 'bar',
                            ],
                            'xAxis' => [
                                [
                                    'categories' => array_reverse($ku),
                                    'reversed' => 'false',
                                    'labels' => 
                                        [
                                            'step'=> 1
                                        ],
                                ],
                                [
                                    'opposite' => 'true',
                                    'reversed' => 'false',
                                    'categories' => array_reverse($ku),
                                    'linkedTo' => 0,
                                    'labels' => [
                                        'step' => 1,
                                    ],
                                ],
                            ],
                            'yAxis' => [
                                'title' => [
                                    'text' => '  '
                                ],
                            ],
                            'plotOptions' => [
                                'series' => [
                                    //'allowPointSelect' => 'true',
                                    //'cursor' => 'pointer',
                                    'stacking' => 'normal',
                                ],
                            ],
                            'series' => [
                                [
                                    'name' => 'Laki-laki',
                                    'data' => [
                                        [$data_laki2_pp[$tahun][15]],
                                        [$data_laki2_pp[$tahun][14]],
                                        [$data_laki2_pp[$tahun][13]],
                                        [$data_laki2_pp[$tahun][12]],
                                        [$data_laki2_pp[$tahun][11]],
                                        [$data_laki2_pp[$tahun][10]],
                                        [$data_laki2_pp[$tahun][9]],
                                        [$data_laki2_pp[$tahun][8]],
                                        [$data_laki2_pp[$tahun][7]],
                                        [$data_laki2_pp[$tahun][6]],
                                        [$data_laki2_pp[$tahun][5]],
                                        [$data_laki2_pp[$tahun][4]],
                                        [$data_laki2_pp[$tahun][3]],
                                        [$data_laki2_pp[$tahun][2]],
                                        [$data_laki2_pp[$tahun][1]],
                                        [$data_laki2_pp[$tahun][0]],
                                    ],
                                ],
                                [
                                    'name' => 'Perempuan',
                                    'data' => [
                                        [$data_perempuan_pp[$tahun][15]],
                                        [$data_perempuan_pp[$tahun][14]],
                                        [$data_perempuan_pp[$tahun][13]],
                                        [$data_perempuan_pp[$tahun][12]],
                                        [$data_perempuan_pp[$tahun][11]],
                                        [$data_perempuan_pp[$tahun][10]],
                                        [$data_perempuan_pp[$tahun][9]],
                                        [$data_perempuan_pp[$tahun][8]],
                                        [$data_perempuan_pp[$tahun][7]],
                                        [$data_perempuan_pp[$tahun][6]],
                                        [$data_perempuan_pp[$tahun][5]],
                                        [$data_perempuan_pp[$tahun][4]],
                                        [$data_perempuan_pp[$tahun][3]],
                                        [$data_perempuan_pp[$tahun][2]],
                                        [$data_perempuan_pp[$tahun][1]],
                                        [$data_perempuan_pp[$tahun][0]],
                                    ],
                                ],
                            ],
                        ],
                    ]); ?>
                </div>

                <!-- TABEL KELOMPOK UMUR 2010 -->
                <?php $gridColumns = [
                         
                    [
                        'label' => 'Kelompok Umur',
                        'attribute' => '0',
                        'width' => '25%',
                        'hAlign' => 'center',
                        'pageSummary' => 'Total',
                    ],       
                    [
                        'label' => 'Laki-laki',
                        'attribute' => '1',
                        'format' => 'integer',
                        'width' => '25%',
                        'hAlign' => 'center',
                        'pageSummary' => true,
                    ],
                    [
                        'label' => 'Perempuan',
                        'attribute' => '2',
                        'format' => 'integer',
                        'width' => '25%',
                        'hAlign' => 'center',
                        'pageSummary' => true,
                    ],
                    [
                        'label' => 'Total',
                        'attribute' => '3',
                        'format' => 'integer',
                        'width' => '25%',
                        'hAlign' => 'center',
                        'pageSummary' => true,
                    ],
                ];

                echo GridView::widget([
                    'dataProvider' => $provider[$tahun],
                    'options' => ['style' => 'font-size:12px;'],
                    'columns' => $gridColumns,
                    'containerOptions' => ['style' => 'overflow: auto'], 
                    'headerRowOptions' => ['class' => 'kartik-sheet-style'],
                    'showFooter' => false,
                    'summary'=>'',
                    'containerOptions' => ['style'=>'overflow: auto'], 
                    'beforeHeader'=>[
                        [
                            'columns'=>[
                                ['content'=> $nama_wilayah.' '.$label, 'options'=>['colspan'=>12, 'class'=>'text-center warning']], //cuma satu kolom header
                        //        ['content'=>'', 'options'=>['colspan'=>0, 'class'=>'text-center warning']], //uncomment kalau mau membuat header kolom-2
                          //      ['content'=>'', 'options'=>['colspan'=>0, 'class'=>'text-center warning']],
                            ], //uncomment kalau mau membuat header kolom-3
                            'options'=>['class'=>'skip-export'] 
                        ]
                    ],
                    'exportConfig' => [
                          //GridView::PDF => ['label' => 'Save as PDF'],
                          GridView::EXCEL => ['label' => 'Save as EXCEL'], //untuk menghidupkan button export ke Excell
                          GridView::HTML => ['label' => 'Save as HTML'], //untuk menghidupkan button export ke HTML
                          GridView::CSV => ['label' => 'Save as CSV'], //untuk menghidupkan button export ke CVS
                      ],
                      
                    'toolbar' =>  [
                        '{export}', 
                    ],
                    'pjax' => true,
                    'hover'=>true,
                    'bordered' => true,
                    'striped' => true,
                    'condensed' => false,
                    'responsive' => false,
                    'hover' => true,
                    'floatHeader' => false,
                    'showPageSummary' => true, //true untuk menjumlahkan nilai di suatu kolom, kebetulan pada contoh tidak ada angka.
                    'panel' => [
                        'type' => GridView::TYPE_DEFAULT,
                        'heading' => FALSE,
                        'footer' => FALSE,
                    ],

                ]); ?>

                <?php if (Yii::$app->user->identity->role == 'provinsi'): ?>
                    <span class="pull-right">
                        <?= Html::a('Ubah Data Laki-laki '.$tahun_data[$tahun], ['update', 'kabkota' => $nama_wilayah, 'id_kabkota' => $id_wilayah, 'tahun_dasar' => $tahun_data[$tahun], 'jenis_kelamin' => 'l'], ['class' => 'btn btn-success btn-sm']) ?>
                        <?= Html::a('Ubah Data Perempuan '.$tahun_data[$tahun], ['update', 'kabkota' => $nama_wilayah, 'id_kabkota' => $id_wilayah, 'tahun_dasar' => $tahun_data[$tahun], 'jenis_kelamin' => 'p'], ['class' => 'btn btn-success btn-sm']) ?>
                    </span>


                <?php elseif (Yii::$app->user->identity->role == 'kabkota'): ?>
                    <span class="pull-right">
                        <?= Html::a('Minta Ubah Data Laki-laki '.$tahun_data[$tahun], ['/ubah-data-kabkota/create', 'kabkota' => $nama_wilayah, 'id_kabkota' => $id_wilayah, 'tahun_dasar' => $tahun_data[$tahun], 'jenis_kelamin' => 'l'], ['class' => 'btn btn-success btn-sm']) ?>
                        <?= Html::a('Minta Ubah Data Perempuan '.$tahun_data[$tahun], ['/ubah-data-kabkota/create', 'kabkota' => $nama_wilayah, 'id_kabkota' => $id_wilayah, 'tahun_dasar' => $tahun_data[$tahun], 'jenis_kelamin' => 'p'], ['class' => 'btn btn-success btn-sm']) ?>
                    </span>


                <?php elseif (Yii::$app->user->identity->role == 'pusat'): ?>  
                

                <?php endif; ?>

            </div>
            <?php }; ?>

            <?php if (Yii::$app->user->identity->role == 'provinsi' || Yii::$app->user->identity->role == 'pusat'): ?>


            <?php elseif (Yii::$app->user->identity->role == 'kab_kota'): ?> 
                

            <?php endif; ?>
            
        </div>    
    </div>
</div>
