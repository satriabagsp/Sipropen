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
    <div class="panel-heading"><h1><?php echo 'Data Proyeksi Penduduk ' . $nama_wilayah  . ' ' . $tahun_terpilih?></h1></div>
        <div class="panel-body">
            <div class="col-md-6">
                
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
                    'dataProvider' => $dataProvider_ku,
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
                                ['content'=> $nama_wilayah.' tahun '.$tahun_terpilih, 'options'=>['colspan'=>12, 'class'=>'text-center warning']], //cuma satu kolom header
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

                <?php if (Yii::$app->user->identity->role == 'provinsi' || Yii::$app->user->identity->role == 'pusat'): ?>
                    <span class="pull-right">
                        <?= Html::a('Ubah Data Laki-laki '.$tahun_terpilih, ['update', 'nama_wilayah' => $nama_wilayah, 'id_wilayah' => $id_wilayah, 'tahun_terpilih' => $tahun_terpilih, 'jenis_kelamin' => 'l'], ['class' => 'btn btn-success btn-sm']) ?>
                        <?= Html::a('Ubah Data Perempuan '.$tahun_terpilih, ['update', 'nama_wilayah' => $nama_wilayah, 'id_wilayah' => $id_wilayah, 'tahun_terpilih' => $tahun_terpilih, 'jenis_kelamin' => 'l'], ['class' => 'btn btn-success btn-sm']) ?>
                    </span>


                <?php elseif (Yii::$app->user->identity->role == 'kabkota'): ?>

                <?php endif; ?>

            </div>

            <div class="col-md-6">
                <div class="panel panel-default">
                    <?= Highcharts::widget([ //buat grafik piramida penduduk
                        'scripts' => [
                            'modules/exporting',
                        ],
                        'options' => [
                            'title' => ['text' => 'Piramida penduduk '. $nama_wilayah],
                            'subtitle' => [
                                'text' => 'Tahun Proyeksi '.  $tahun_terpilih,
                            ],
                            'chart' => [
                                'type' => 'bar',
                                //'zoomType' => 'x',
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
                                        [$data_laki2_pp[15]],
                                        [$data_laki2_pp[14]],
                                        [$data_laki2_pp[13]],
                                        [$data_laki2_pp[12]],
                                        [$data_laki2_pp[11]],
                                        [$data_laki2_pp[10]],
                                        [$data_laki2_pp[9]],
                                        [$data_laki2_pp[8]],
                                        [$data_laki2_pp[7]],
                                        [$data_laki2_pp[6]],
                                        [$data_laki2_pp[5]],
                                        [$data_laki2_pp[4]],
                                        [$data_laki2_pp[3]],
                                        [$data_laki2_pp[2]],
                                        [$data_laki2_pp[1]],
                                        [$data_laki2_pp[0]],
                                    ],
                                ],
                                [
                                    'name' => 'Perempuan',
                                    'data' => [
                                        [$data_perempuan_pp[15]],
                                        [$data_perempuan_pp[14]],
                                        [$data_perempuan_pp[13]],
                                        [$data_perempuan_pp[12]],
                                        [$data_perempuan_pp[11]],
                                        [$data_perempuan_pp[10]],
                                        [$data_perempuan_pp[9]],
                                        [$data_perempuan_pp[8]],
                                        [$data_perempuan_pp[7]],
                                        [$data_perempuan_pp[6]],
                                        [$data_perempuan_pp[5]],
                                        [$data_perempuan_pp[4]],
                                        [$data_perempuan_pp[3]],
                                        [$data_perempuan_pp[2]],
                                        [$data_perempuan_pp[1]],
                                        [$data_perempuan_pp[0]],
                                    ],
                                ],
                            ],
                        ],
                    ]); ?>
                </div>
                <br>
                <div class="panel panel-default">
                    <?= Highcharts::widget([ //buat grafik pie
                        'scripts' => [
                            'modules/exporting',
                        ],
                        'options' => [
                            'title' => ['text' => 'Distribusi Penduduk ' . $nama_wilayah . ' Menurut Kelompok Umur'],
                            'subtitle' => [
                                'text' => 'Tahun Proyeksi '.  $tahun_terpilih, 
                            ],
                            'chart' => [
                                'type' => 'pie',
                            ],
                            'plotOptions' => [
                                'pie' => [
                                    'allowPointSelect' => 'true',
                                    'cursor' => 'pointer',
                                    'dataLabels' => [
                                        'enabled' => 'false'
                                    ],
                                ],
                            ],
                            'series' => [
                                [
                                    'name' => 'Jumlah Penduduk',
                                    'data' => [
                                        ['Usia 0-4 tahun' , $data_jumlah[0]],
                                        ['Usia 5-9 tahun' , $data_jumlah[1]],
                                        ['Usia 10-14 tahun' , $data_jumlah[2]],
                                        ['Usia 15-19 tahun' , $data_jumlah[3]],
                                        ['Usia 20-24 tahun' , $data_jumlah[4]],
                                        ['Usia 25-29 tahun' , $data_jumlah[5]],
                                        ['Usia 30-34 tahun' , $data_jumlah[6]],
                                        ['Usia 35-39 tahun' , $data_jumlah[7]],
                                        ['Usia 40-44 tahun' , $data_jumlah[8]],
                                        ['Usia 45-49 tahun' , $data_jumlah[9]],
                                        ['Usia 50-54 tahun' , $data_jumlah[10]],
                                        ['Usia 55-59 tahun' , $data_jumlah[11]],
                                        ['Usia 60-64 tahun' , $data_jumlah[12]],
                                        ['Usia 65-69 tahun' , $data_jumlah[13]],
                                        ['Usia 70-74 tahun' , $data_jumlah[14]],
                                        ['Usia 75+ tahun' , $data_jumlah[15]],
                                    ],
                                ],
                            ],
                        ],
                    ]); ?>       
                </div>
            </div>
            


            <?php if (Yii::$app->user->identity->role == 'provinsi' || Yii::$app->user->identity->role == 'admin'): ?>


            <?php elseif (Yii::$app->user->identity->role == 'kab_kota'): ?> 
                

            <?php endif; ?>
            
        </div>    
    </div>
</div>
