<?php

use yii\helpers\Html;
use yii\grid\GridView;
use app\models\DataKabkotaLp;
use yii\widgets\ActiveForm;
use miloschuman\highcharts\Highcharts;
use miloschuman\highcharts\SeriesDataHelper;
use fedemotta\datatables\DataTables;
use yii\data\ArrayDataProvider;

//fungsi transpose array
function transpose($array) {
    array_unshift($array, null);
    return call_user_func_array('array_map', $array);
}

/* @var $this yii\web\View */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'Data Jumlah Penduduk (' . Yii::$app->user->identity->username . ')';
$this->params['breadcrumbs'][] = $this->title;

?>

<div class="data-kab-kota-index">
  <div class="panel panel-info">

    <div class="panel-heading"><h1><?= Html::encode($this->title) ?></h1></div>
    <div class="panel-body">
        <?php if (Yii::$app->user->identity->role == 'provinsi' || Yii::$app->user->identity->role == 'kabkota'): ?>
         
            <?php if ($status_proyeksi == 'sudah ada data'): ?>
                <p><b>Data jumlah penduduk <?= Html::encode(Yii::$app->user->identity->username)?>:</b></p>

                <?php 
                    //inisiasi array kolom yang akan ditampilkan
                    $gridColumns = []; 
                    $gridColumns[] = [
                        'label' => 'Kode Kabupaten/kota',
                        'attribute' => '0',
                    ];

                    if(Yii::$app->user->identity->role == 'provinsi'){
                        $gridColumns[] = [
                            'label' => 'Kabupaten/kota',
                            'attribute' => '1',
                            'footer' => Yii::$app->user->identity->username,
                        ];
         
                        for($att=2;$att<count($attributes);$att++) {
                            //Membedakan label SENSUS dan SUPAS, yaitu angka belakangnya 0 dan 5.
                            if(substr($tahun_data[$att-2], 3) == '0'){
                                $label = 'SENSUS '.$tahun_data[$att-2];
                            }elseif(substr($tahun_data[$att-2], 3) == '5'){
                                $label = 'SUPAS '.$tahun_data[$att-2];
                            }else{
                                $label = $tahun_data[$att-2];
                            };
                            $gridColumns[] = [
                                'label' => $label,
                                'attribute' => $att,
                                'format'=> ['integer'],
                                'footer' => number_format($total_tahun[$att-2]),
                            ];
                        };

                    } elseif(Yii::$app->user->identity->role == 'kabkota'){
                        $gridColumns[] = [
                            'label' => 'Kabupaten/kota',
                            'attribute' => '1',
                        ];
         
                        for($att=2;$att<count($attributes);$att++) {
                            //Membedakan label SENSUS dan SUPAS, yaitu angka belakangnya 0 dan 5.
                            if(substr($tahun_data[$att-2], 3) == '0'){
                                $label = 'SENSUS '.$tahun_data[$att-2];
                            }elseif(substr($tahun_data[$att-2], 3) == '5'){
                                $label = 'SUPAS '.$tahun_data[$att-2];
                            };
                            $gridColumns[] = [
                                'label' => $label,
                                'attribute' => $att,
                                'format'=> ['integer'],
                            ];
                        };
                    };

                    $gridColumns[] = [
                        'class' => 'yii\grid\ActionColumn',
                        'template' => ' {view} '
                    ];
                ?>
                <?= DataTables::widget([
                    'dataProvider' => $dataProvider,
                    'options' => ['style' => 'font-size:12px;'],
                    'showFooter' => true,
                    'columns' => $gridColumns,
                ]);?>

                <div>
                    <?= Highcharts::widget([
                        'options' => [
                            'title' => ['text' => 'Jumlah Penduduk Kabupaten/kota'],
                            'chart' => ['type' => 'column'],
                            'xAxis' => [
                                'categories' => new SeriesDataHelper($dataProvider, ['1']),
                                'crosshair' => 'true',
                                'labels' => [
                                    'style' => [
                                        'color' => 'black',
                                        'fontSize' => '8px',
                                    ],
                                ],    
                            ],
                            'subtitle' => [
                                'text' => 'SENSUS 2010 dan SUPAS 2015'  
                            ],
                            'yAxis' => [
                                'tickInterval' => 30000,
                                'title' => ['text' => 'Jumlah Penduduk'],
                            ],
                            'legend' => [
                                'layout' => 'horizontal',
                                'borderWidth' => 1,
                            ],
                            'series' => [
                                [
                                    'name' => 'Jumlah Penduduk Hasil SENSUS 2010',
                                    'data' => new SeriesDataHelper($dataProvider, ['2:int']),
                                ],
                                [
                                    'name' => 'Jumlah Penduduk Hasil SUPAS 2015',
                                    'data' => new SeriesDataHelper($dataProvider, ['3:int']),
                                ],
                            ],
                        ]
                    ]); ?> 
                </div>
                <br>
                <?php if (Yii::$app->user->identity->role == 'provinsi'): ?>
                    <span class="pull-right">
                        <?= Html::a('Tambah Kabupaten/kota Baru', ['create'], ['class' => 'btn btn-success btn-sm']) ?>
                        <?= Html::a('Hapus Semua Data', ['hapus'], ['class' => 'btn btn-danger btn-sm']) ?>
                    </span>
                <?php endif; ?>

            <?php elseif ($status_proyeksi == 'belum ada data'): ?>
                <div class="col-md-12">
                    <div class="jumbotron" style="background: #f0f0f0; margin-top: 20px; padding-top: 10px; padding-bottom: 10px">
                        <center><p>
                            Data kabupaten/kota <?= Html::encode(Yii::$app->user->identity->username) ?> <font color=red>belum tersedia</font>, silakan <i>input</i> data kabupaten/kota.
                        </p></center>
                    </div>
                </div>
                <span class="pull-right">
                    <?= Html::a('Tambah Kabupaten/kota Baru', ['create'], ['class' => 'btn btn-success btn-sm']) ?>
                    <?= Html::a('Import Data Baru', ['import'], ['class' => 'btn btn-warning btn-sm']) ?>
                </span>
            
            <?php endif; ?>

        <?php elseif (Yii::$app->user->identity->role == 'pusat'): ?>

            <?php if ($status_data == 'kosong'): ?>  

                <p><b>Data jumlah penduduk kabupaten/kota:</b></p>
                <?= DataTables::widget([
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
                                return Html::a('Lihat Provinsi', ['lihat', 'provinsi_terpilih' => $model->nama_wilayah, 'id_provinsi_terpilih' => $model->id_wilayah]);
                            },
                        ],
                    ],
                ]); ?>

            <?php elseif ($status_data == 'pilih_provinsi'): ?>

                <?php if ($status_proyeksi == 'sudah ada data'): ?>

                    <p><b>Data jumlah penduduk kabupaten/kota <?php echo $provinsi_terpilih ?>:</b></p>
                    <?php 
                        //inisiasi array kolom yang akan ditampilkan
                        $gridColumns = []; 
                        $gridColumns[] = [
                            'label' => 'Kode Kabupaten/kota',
                            'attribute' => '0',
                        ];
                        $gridColumns[] = [
                            'label' => 'Kabupaten/kota',
                            'attribute' => '1',
                            'footer' => Yii::$app->user->identity->username,
                        ];
                        for($att=2;$att<count($attributes);$att++) {
                            //Membedakan label SENSUS dan SUPAS, yaitu angka belakangnya 0 dan 5.
                            if(substr($tahun_data[$att-2], 3) == '0'){
                                $label = 'SENSUS '.$tahun_data[$att-2];
                            }elseif(substr($tahun_data[$att-2], 3) == '5'){
                                $label = 'SUPAS '.$tahun_data[$att-2];
                            };
                            $gridColumns[] = [
                                'label' => $label,
                                'attribute' => $att,
                                'format'=> ['integer'],
                                'footer' => number_format($total_tahun[$att-2]),
                            ];
                        };
                        $gridColumns[] = [
                            'class' => 'yii\grid\ActionColumn',
                            'template' => ' {view} '
                        ];
                    ?>
                    <?= DataTables::widget([
                        'dataProvider' => $dataProvider,
                        'options' => ['style' => 'font-size:12px;'],
                        'showFooter' => true,
                        'columns' => $gridColumns,
                    ]);?>

                    <div>
                        <?= Highcharts::widget([
                            'options' => [
                                'title' => ['text' => 'Jumlah Penduduk Kabupaten/kota'],
                                'chart' => ['type' => 'column'],
                                'xAxis' => [
                                    'categories' => new SeriesDataHelper($dataProvider, ['1']),
                                    'crosshair' => 'true',
                                    'labels' => [
                                        'style' => [
                                            'color' => 'black',
                                            'fontSize' => '8px',
                                        ],
                                    ],    
                                ],
                                'subtitle' => [
                                    'text' => 'SENSUS 2010 dan SUPAS 2015'  
                                ],
                                'yAxis' => [
                                    'tickInterval' => 30000,
                                    'title' => ['text' => 'Jumlah Penduduk'],
                                ],
                                'legend' => [
                                    'layout' => 'horizontal',
                                    'borderWidth' => 1,
                                ],
                                'series' => [
                                    [
                                        'name' => 'Jumlah Penduduk Hasil SENSUS 2010',
                                        'data' => new SeriesDataHelper($dataProvider, ['2:int']),
                                    ],
                                    [
                                        'name' => 'Jumlah Penduduk Hasil SUPAS 2015',
                                        'data' => new SeriesDataHelper($dataProvider, ['3:int']),
                                    ],
                                ],
                            ]
                        ]); ?> 
                    </div>

                <?php elseif ($status_proyeksi == 'belum ada data'): ?>
                    <div class="col-md-12">
                        <div class="jumbotron" style="background: #f0f0f0; margin-top: 20px; padding-top: 10px; padding-bottom: 10px">
                            <center><p>
                                Data kabupaten/kota <?= Html::encode($provinsi_terpilih) ?> <font color=red>belum tersedia</font>, silakan <i>input</i> data kabupaten/kota.
                            </p></center>
                        </div>
                    </div>
                
                <?php endif; ?>

            <?php endif; ?>
            
        <?php endif; ?>
    </div>
  </div>        
</div>

