<?php

use yii\helpers\Html;
use app\models\DataKabkotaLp;
use yii\widgets\ActiveForm;
use miloschuman\highcharts\Highcharts;
use miloschuman\highcharts\SeriesDataHelper;
use fedemotta\datatables\DataTables;
use yii\data\ArrayDataProvider;
use yii\bootstrap\Collapse;
use kartik\grid\GridView;

//fungsi transpose array
function transpose($array) {
    array_unshift($array, null);
    return call_user_func_array('array_map', $array);
}

/* @var $this yii\web\View */
/* @var $dataProvider yii\data\ActiveDataProvider */

if($status == 'sudah ada data'){
$this->title = 'Data Jumlah Penduduk (' . $nama_provinsi . ')';
$this->params['breadcrumbs'][] = $this->title;
} elseif($status == 'belum ada data'){
    $this->title = 'Data Jumlah Penduduk (' . Yii::$app->user->identity->username . ')';
    $this->params['breadcrumbs'][] = $this->title;
}

?>

<div class="data-kab-kota-index">
  <div class="panel panel-info">

    <div class="panel-heading"><h1><?= Html::encode($this->title) ?></h1></div>
    <div class="panel-body">
        <?php if (Yii::$app->user->identity->role == 'provinsi' || Yii::$app->user->identity->role == 'kabkota'): ?>

            <?php if ($status == 'belum ada data'): ?>
                <div class="col-md-12">
                    <div class="jumbotron" style="background: #f0f0f0; margin-top: 20px; padding-top: 10px; padding-bottom: 10px">
                        <center><p>
                            Data proyeksi <?= Html::encode(Yii::$app->user->identity->username) ?> <font color=red>belum tersedia</font>, silakan <i>input</i> data hasil proyeksi provinsi.
                        </p></center>
                    </div>
                </div>
                <span class="pull-right">
                    <?= Html::a('Tambah Tahun Proyeksi Baru', ['create'], ['class' => 'btn btn-success btn-sm']) ?>
                    <?= Html::a('Import Data Baru', ['import'], ['class' => 'btn btn-warning btn-sm']) ?>
                </span>

            <?php elseif ($status == 'sudah ada data'): ?>
            
                <span class="pull-right">
                    <?= Html::a('Tambah Tahun Proyeksi Baru', ['create'], ['class' => 'btn btn-success btn-sm']) ?>
                    <?= Html::a('Import Data Baru', ['import'], ['class' => 'btn btn-warning btn-sm']) ?>
                    <?= Html::a('Hapus Semua Data', ['hapus'], ['class' => 'btn btn-danger btn-sm']) ?>
                </span>
                <p><b>Berikut adalah data proyeksi penduduk <?= Html::encode($nama_provinsi)?> hasil SUPAS2015:</b></p>

                <br>

                <?php $gridColumns = [
                    ['class' => 'kartik\grid\SerialColumn','width'=>'5%'],
                         
                    [
                        'label' => 'Tahun Proyeksi',
                        'attribute' => '0',
                        'width' => '45%',
                        'hAlign' => 'center',
                    ],       
                    [
                        'label' => 'Jumlah Penduduk',
                        'attribute' => '1',
                        'format' => 'integer',
                        'width' => '45%',
                        'hAlign' => 'center',
                    ],
                    [
                        'class' => 'yii\grid\ActionColumn',
                        'template' => '{view}'
                    ],
                ];

                echo GridView::widget([
                    'dataProvider' => $dataProvider,
                    'columns' => $gridColumns,
                    'options' => ['style' => 'font-size:12px;'],
                    'containerOptions' => ['style' => 'overflow: auto'], 
                    'headerRowOptions' => ['class' => 'kartik-sheet-style'],
                    'filterRowOptions' => ['class' => 'kartik-sheet-style'],
                    //'summary'=>'',
                    'containerOptions' => ['style'=>'overflow: auto'], 
                    'beforeHeader'=>[
                        [
                            'columns'=>[
                                ['content'=> $this->title, 'options'=>['colspan'=>12, 'class'=>'text-center warning']], //cuma satu kolom header
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
                        '{toggleData}' //uncoment untuk menghidupkan button menampilkan semua data..
                    ],
                    'pjax' => true,
                    'hover'=>true,
                    'bordered' => true,
                    'striped' => true,
                    'condensed' => false,
                    'responsive' => false,
                    'hover' => true,
                    'floatHeader' => false,
                    'showPageSummary' => false, //true untuk menjumlahkan nilai di suatu kolom, kebetulan pada contoh tidak ada angka.
                    'panel' => [
                        'type' => GridView::TYPE_DEFAULT,
                        'heading' => FALSE,
                        'footer' => FALSE,
                    ],

                ]);
                 ?>

                <p><b>Berikut adalah grafik hasil proyeksi <?= Html::encode(Yii::$app->user->identity->username)?> hasil SUPAS2015:</b></p>
                    <?php Highcharts::widget([
                        'id' => 'highchart-proyprov',
                        'options' => [
                            'title' => ['text' => 'Proyeksi Penduduk (' . Yii::$app->user->identity->username . ')'],
                            'chart' => ['type' => 'line'],
                            'subtitle' => [
                                'text' => 'Hasil SUPAS 2015'  
                            ],
                            'xAxis' => [
                                'categories' => new SeriesDataHelper($dataProvider, ['0:int']),
                             ],
                            'yAxis' => [
                                'title' => ['text' => 'Jumlah Penduduk'],
                            ],
                            'plotOptions' => [
                                'line' => [
                                    'dataLabels' => [
                                        'enabled' => 'true',
                                        'style' => [
                                            'color' => 'black',
                                            'fontSize' => '10px',
                                        ],
                                    ],
                                ],  
                            ],
                            'legend' => [
                                'layout' => 'horizontal',
                                'borderWidth' => 1,
                            ],
                            'series' => [
                                [
                                    'name' => 'Proyeksi Jumlah Penduduk Provinsi',
                                    'data' => new SeriesDataHelper($dataProvider, ['1:int']),
                                ],
                            ],   
                        ]
                    ]); ?> 
                    <div id='highchart-proyprov' style="border:1px solid grey;color:grey;"> </div>

            <?php endif; ?>
        

        <?php elseif (Yii::$app->user->identity->role == 'kabkota'): ?>

            

        <?php elseif (Yii::$app->user->identity->role == 'pusat'): ?>

            <?php if ($status_data == 'kosong'): ?>  
                <p><b>silakan pilih provinsi yang akan dilihat:</b></p>
                <?= GridView::widget([
                    'dataProvider' => $dataProvider,
                    'options' => ['style' => 'font-size:12px;'],
                    'showFooter' => true,
                    'columns' => [
                        ['class' => 'yii\grid\SerialColumn'],
                         
                        'id_wilayah',
                        'nama_wilayah',
                        [
                            'label' => 'Pilihan',
                            'format' => 'raw',
                            'value' => function($model) {
                                return Html::a('Lihat Provinsi', ['lihat', 'provinsi_terpilih' => $model->id_wilayah]);
                            },
                        ],
                    ],
                ]); ?>

            <?php elseif ($status_data == 'pilih_provinsi'): ?>
                <p><b>Data proyeksi jumlah penduduk wilayah <?php echo $nama_wilayah[0] ?>:</b></p>

                <table rules="row" style="text-align:center;" border="2" class="table">
                    <tr style="background-color: #9ca7ba;" border="2">
                        <th style="text-align:center">Tahun Proyeksi</th>
                        <th style="text-align:center">Jumlah Penduduk</th>
                        <th style="text-align:center">Pilihan</th> 
                    </tr>
                    <?php 
                        for($a=0;$a<count($tahun_data);$a++){
                        ?>
                        <tr>
                            <td><?php echo $tahun_data[$a]; ?></td>
                            <td><?php echo number_format($jumlah_provinsi[$a]); ?></td>
                            <td><u><i><?= Html::a('lihat detail tahun', ['lihatdetail', 'id_wilayah' => $id_wilayah[0], 'nama_wilayah' => $nama_wilayah[0], 'tahun_terpilih' => $tahun_data[$a]]) ?></i></u></td>
                        </tr>
                    <?php } ?>
                </table>

                <?php Highcharts::widget([
                    'id' => 'highchart-proyprov',
                    'options' => [
                        'title' => ['text' => 'Proyeksi Penduduk (' . $nama_wilayah[0] . ')'],
                        'chart' => ['type' => 'line'],
                        'subtitle' => [
                            'text' => 'Hasil SUPAS 2015'  
                        ],
                        'xAxis' => [
                            'categories' => new SeriesDataHelper($dataProvider, ['0:int']),
                         ],
                        'yAxis' => [
                            'title' => ['text' => 'Jumlah Penduduk'],
                        ],
                        'plotOptions' => [
                            'line' => [
                                'dataLabels' => [
                                    'enabled' => 'true',
                                    'style' => [
                                        'color' => 'black',
                                        'fontSize' => '10px',
                                    ],
                                ],
                            ],  
                        ],
                        'legend' => [
                            'layout' => 'horizontal',
                            'borderWidth' => 1,
                        ],
                        'series' => [
                            [
                                'name' => 'Proyeksi Jumlah Penduduk Provinsi',
                                'data' => new SeriesDataHelper($dataProvider, ['1:int']),
                            ],
                        ],   
                    ]
                ]); ?> 
                <div id='highchart-proyprov' style="border:1px solid grey;color:grey;"> </div>


            <?php endif; ?>
            

        <?php endif; ?>
    </div>
  </div>        
</div>

