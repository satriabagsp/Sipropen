<?php

use yii\helpers\Html;
use app\models\DataKabkotaLp;
use yii\widgets\ActiveForm;
use miloschuman\highcharts\Highcharts;
use miloschuman\highcharts\HighchartsAsset;
use miloschuman\highcharts\SeriesDataHelper;
use fedemotta\datatables\DataTables;
use yii\data\ArrayDataProvider;
use yii\bootstrap\Collapse;
use kartik\grid\GridView;
use app\models\Laporan;

//Inisiasi variabel untuk menampilkan form.
$model = new Laporan();
if ($model->load(Yii::$app->request->post()) && $model->save()) {
    Yii::$app->session->setFlash('success', 'Hasil proyeksi berhasil dikirim ke pusat untuk diperiksa');
};

//fungsi transpose array
function transpose($array) {
    array_unshift($array, null);
    return call_user_func_array('array_map', $array);
}

/* @var $this yii\web\View */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'Proyeksi Penduduk Tahunan Kabupaten/kota (' . Yii::$app->user->identity->username . ')';
$this->params['breadcrumbs'][] = $this->title;

?>

<script>
    function bukaDivSemua() {
        var divSemua = document.getElementById('divSemua');
        if (divSemua.style.display === 'none') {
            divSemua.style.display = 'block';
        } else {
            divSemua.style.display = 'none';
        };
    };
    function bukaDivTotal() {
        var divTotal = document.getElementById('divTotal');
        if (divTotal.style.display === 'none') {
            divTotal.style.display = 'block';
        } else {
            divTotal.style.display = 'none';
        };
    };
    function bukaDivLaki2() {
        var divLaki2 = document.getElementById('divLaki2');
        if (divLaki2.style.display === 'none') {
            divLaki2.style.display = 'block';
        } else {
            divLaki2.style.display = 'none';
        };
    };
    function bukaDivPerempuan() {
        var divPerempuan = document.getElementById('divPerempuan');
        if (divPerempuan.style.display === 'none') {
            divPerempuan.style.display = 'block';
        } else {
            divPerempuan.style.display = 'none';
        };
    };
</script>

<div class="data-kab-kota-index">
  <div class="panel panel-info">

    <div class="panel-heading"><h1><?= Html::encode($this->title) ?></h1></div>
    <div class="panel-body">

        <?php if (Yii::$app->user->identity->role == 'provinsi'): ?>
            <?php if ($status_proyeksi == 'Sudah ada proyeksi'): ?>

                <!-- Periksa apakah hasil proyeksi sudah dikirim ke pusat? -->
                    <?php
                        //Mulai buat query sql
                            include "koneksi.php";
                            $sql_query = "SELECT *
                                        FROM laporan
                                        WHERE perihal = 'Cek hasil proyeksi' 
                                        AND asal = '" . Yii::$app->user->identity->username . "'";
                            $query_mysql = mysqli_query($host,$sql_query) or die(mysqli_error());
                                                    
                        //Memasukan data dari database dalam bentuk ARRAY
                            $cekPeriksa = 0;
                            while($row = mysqli_fetch_array($query_mysql)){ 
                                $cekPeriksa++;
                            };
                    ?>

                <!-- Periksa apakah hasil proyeksi sudah disetujui atau belum -->
                    <?php
                        //Buat koneksi ke DB
                            include "koneksi.php";
                            $sql_CekSetuju = "SELECT *
                                             FROM laporan
                                             WHERE perihal = 'Cek hasil proyeksi' 
                                             AND status = 'Disetujui'
                                             AND asal = '" . Yii::$app->user->identity->username . "'";
                            $query_CekSetuju = mysqli_query($host,$sql_CekSetuju) or die(mysqli_error());

                            $sql_CekTolak = "SELECT *
                                             FROM laporan
                                             WHERE perihal = 'Cek hasil proyeksi' 
                                             AND status = 'Periksa kembali'
                                             AND asal = '" . Yii::$app->user->identity->username . "'";
                            $query_CekTolak = mysqli_query($host,$sql_CekTolak) or die(mysqli_error());
                                        
                        //Memasukan data dari database dalam bentuk ARRAY
                            $cekSetuju = 0; $cekTolak = 0;
                            while($row = mysqli_fetch_array($query_CekSetuju)){ 
                                $cekSetuju++;
                            };
                            while($row = mysqli_fetch_array($query_CekTolak)){ 
                                $cekTolak++;
                            };
                    ?>

                <div class="col-md-12">
                    <div class="jumbotron" style="background: #f0f0f0; margin-top: 20px; padding-top: 10px; padding-bottom: 10px">
                        <center><p>
                            <?php if ($cekPeriksa): ?>
                                <?php if (!$cekSetuju && !$cekTolak): ?>
                                    Hasil proyeksi penduduk <?= Html::encode(Yii::$app->user->identity->username) ?> sudah dikirim ke pusat untuk diperiksa.
                                <?php elseif ($cekSetuju): ?>
                                    Hasil proyeksi penduduk <?= Html::encode(Yii::$app->user->identity->username) ?> sudah disetujui oleh pusat.
                                <?php elseif ($cekTolak): ?>
                                    Hasil proyeksi penduduk <?= Html::encode(Yii::$app->user->identity->username) ?> ditolak oleh pusat. Harap cek kembali hasil proyeksi.
                                <?php endif; ?>
                            <?php elseif (!$cekPeriksa): ?>
                                Hasil proyeksi penduduk <?= Html::encode(Yii::$app->user->identity->username) ?> <font color=red>belum dikirim</font> ke pusat untuk diperiksa, silakan kirimkan hasil proyeksi jika sudah dianggap benar dengan tombol di bawah.
                            <?php endif; ?>
                        </p></center>
                    </div>
                </div>
                
                <p><b>Berikut adalah data hasil proyeksi penduduk kabupaten/kota <?= Html::encode(Yii::$app->user->identity->username)?>:</b></p>
                <?php 

                //BUAT NAMA HEADER KOLOM
                $gridColumns = []; 
                    $gridColumns[] = [
                        'label' => 'Kode',
                        'attribute' => '0',
                        'hAlign' => 'center',
                        'pageSummary' => Yii::$app->user->identity->wilayah_id,
                    ];
                    $gridColumns[] = [
                        'label' => 'Kabupaten/kota',
                        'attribute' => '1',
                        'hAlign' => 'center',
                        'pageSummary' => Yii::$app->user->identity->username,
                    ];
                    for($coba=2;$coba<count($cekkk);$coba++) {
                        $gridColumns[] = [
                            'attribute' => $coba,
                            'label'=> 2013+$coba,
                            'pageSummary' => true,
                            'format'=> ['integer'],
                            'hAlign' => 'center',
                        ];
                    };
                        $gridColumns[] = [
                            'class' => 'yii\grid\ActionColumn',
                            'template' => '{view}'
                        ];
                        
                echo GridView::widget([
                    'dataProvider' => $dataProvider,
                    'options' => ['style' => 'font-size:12px;'],
                    'columns' => $gridColumns,
                    'containerOptions' => ['style' => 'overflow: auto'], 
                    'headerRowOptions' => ['class' => 'kartik-sheet-style'],
                    'showFooter' => true,
                    'summary'=>'',
                    'containerOptions' => ['style'=>'overflow: auto'], 
                    'beforeHeader'=>[
                        [
                            'columns'=>[
                                ['content'=> $this->title, 'options'=>['colspan'=>count($gridColumns), 'class'=>'text-center warning']], //cuma satu kolom header
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
                    'pjax' => false,
                    'bordered' => true,
                    'striped' => true,
                    'condensed' => true,
                    'responsive' => true,
                    'hover' => true,
                    'floatHeader' => false,
                    'showPageSummary' => true, //true untuk menjumlahkan nilai di suatu kolom, kebetulan pada contoh tidak ada angka.
                    'panel' => [
                        'type' => GridView::TYPE_DEFAULT,
                        'heading' => false,
                        'footer' => false,
                    ],

                ]);
                 ?>

                <!--div tombol untuk munculkan form ubah data (muncul kalo udah disetujui) -->
                <?php //if ($cekSetuju): ?>
                <span class="pull-right">
                    <center><p>
                        <?= Html::a(Yii::t('app', 'Lihat data laki-laki'), '#.', ['class' => 'btn btn-primary btn-sm', 'onclick' => "bukaDivLaki2()"]) ?>
                        <?= Html::a(Yii::t('app', 'Lihat data perempuan'), '#.', ['class' => 'btn btn-warning btn-sm', 'onclick' => "bukaDivPerempuan()"]) ?>
                    </p></center>
                </span>
                </br></br>
                <?php //endif; ?>

                

                <div id="divLaki2" style="display:none;">
                    <p><b>Berikut adalah data hasil proyeksi penduduk laki-laki kabupaten/kota <?= Html::encode(Yii::$app->user->identity->username)?>:</b></p>
                    <?php 

                    $gridColumns = []; 
                        $gridColumns[] = [
                            'label' => 'Kode',
                            'attribute' => '0',
                            'hAlign' => 'center',
                            'pageSummary' => Yii::$app->user->identity->wilayah_id,
                        ];
                        $gridColumns[] = [
                            'label' => 'Kabupaten/kota',
                            'attribute' => '1',
                            'hAlign' => 'center',
                            'pageSummary' => Yii::$app->user->identity->username,
                        ];
                        for($coba=2;$coba<count($cekkk);$coba++) {
                            $gridColumns[] = [
                                'attribute' => $coba,
                                'label'=> 2013+$coba,
                                'pageSummary' => true,
                                'format'=> ['integer'],
                                'hAlign' => 'center',
                            ];
                        };

                    echo GridView::widget([
                        'dataProvider' => $dataProvider_laki2,
                        'options' => ['style' => 'font-size:12px;'],
                        'columns' => $gridColumns,
                        'containerOptions' => ['style' => 'overflow: auto'], 
                        'headerRowOptions' => ['class' => 'kartik-sheet-style'],
                        'showFooter' => true,
                        'summary'=>'',
                        'containerOptions' => ['style'=>'overflow: auto'], 
                        'beforeHeader'=>[
                            [
                                'columns'=>[
                                    ['content'=> 'Data Hasil Proyeksi Penduduk Laki-laki ('. Yii::$app->user->identity->username .')', 'options'=>['colspan'=>count($gridColumns), 'class'=>'text-center warning']], //cuma satu kolom header
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
                        'pjax' => false,
                        'bordered' => true,
                        'striped' => true,
                        'condensed' => true,
                        'responsive' => true,
                        'hover' => true,
                        'floatHeader' => false,
                        'showPageSummary' => true, //true untuk menjumlahkan nilai di suatu kolom, kebetulan pada contoh tidak ada angka.
                        'panel' => [
                            'type' => GridView::TYPE_DEFAULT,
                            'heading' => false,
                            'footer' => false,
                        ],

                    ]);
                     ?>
                </div>

                <div id="divPerempuan" style="display:none;">
                    <p><b>Berikut adalah data hasil proyeksi penduduk perempuan kabupaten/kota <?= Html::encode(Yii::$app->user->identity->username)?>:</b></p>
                    <?php 

                    $gridColumns = []; 
                        $gridColumns[] = [
                            'label' => 'Kode',
                            'attribute' => '0',
                            'hAlign' => 'center',
                            'pageSummary' => Yii::$app->user->identity->wilayah_id,
                        ];
                        $gridColumns[] = [
                            'label' => 'Kabupaten/kota',
                            'attribute' => '1',
                            'hAlign' => 'center',
                            'pageSummary' => Yii::$app->user->identity->username,
                        ];
                        for($coba=2;$coba<count($cekkk);$coba++) {
                            $gridColumns[] = [
                                'attribute' => $coba,
                                'label'=> 2013+$coba,
                                'pageSummary' => true,
                                'format'=> ['integer'],
                                'hAlign' => 'center',
                            ];
                        };

                    echo GridView::widget([
                        'dataProvider' => $dataProvider_perempuan,
                        'options' => ['style' => 'font-size:12px;'],
                        'columns' => $gridColumns,
                        'containerOptions' => ['style' => 'overflow: auto'], 
                        'headerRowOptions' => ['class' => 'kartik-sheet-style'],
                        'showFooter' => true,
                        'summary'=>'',
                        'containerOptions' => ['style'=>'overflow: auto'], 
                        'beforeHeader'=>[
                            [
                                'columns'=>[
                                    ['content'=> 'Data Hasil Proyeksi Penduduk Perempuan ('. Yii::$app->user->identity->username .')', 'options'=>['colspan'=>count($gridColumns), 'class'=>'text-center warning']], //cuma satu kolom header
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
                        'pjax' => false,
                        'bordered' => true,
                        'striped' => true,
                        'condensed' => true,
                        'responsive' => true,
                        'hover' => true,
                        'floatHeader' => false,
                        'showPageSummary' => true, //true untuk menjumlahkan nilai di suatu kolom, kebetulan pada contoh tidak ada angka.
                        'panel' => [
                            'type' => GridView::TYPE_DEFAULT,
                            'heading' => false,
                            'footer' => false,
                        ],

                    ]);
                     ?>
                </div>

                <!--Nampilin tombol untuk kirim hasil proyeksi yang muncul kalo belum disetujui-->
                <?php if (!$cekSetuju): ?>
                </br>
                <div class="laporan-form"><center>
                    <?php $form = ActiveForm::begin(); ?>
                        <span>
                            <?php if (!$cekPeriksa): ?>
                                <?= $form->field($model, 'tanggal')->hiddenInput(['maxlength' => true, 'value' => 'kosong'])->label(false) ?>

                                <?= $form->field($model, 'waktu')->hiddenInput(['maxlength' => true, 'value' => 'kosong'])->label(false) ?>

                                <?= $form->field($model, 'asal')->hiddenInput(['maxlength' => true, 'value' => 'kosong'])->label(false) ?>

                                <?= $form->field($model, 'tujuan')->hiddenInput(['maxlength' => true, 'value' => 'kosong'])->label(false) ?>

                                <?= $form->field($model, 'perihal')->hiddenInput(['maxlength' => true, 'value' => 'kosong'])->label(false) ?>

                                <?= $form->field($model, 'status')->hiddenInput(['maxlength' => true, 'value' => 'kosong'])->label(false) ?>
                                
                                <?= $form->field($model, 'deskripsi')->textInput(['maxlength' => true]) ?>
                                
                                <div class="form-group">
                                    <?= Html::submitButton('Kirim hasil proyeksi ke pusat', ['class' => 'btn btn-success']) ?>
                                    <?= Html::a('Hapus hasil proyeksi saat ini', ['/site/proyeksikan2', 'status' => 'hapus', 'tahun_dasar' => '', 'tahun_target' => '', 'panjang_tahun' => ''], ['class'=>'btn btn-danger']) ?>
                                </div>
                            <?php elseif ($cekPeriksa): ?>
                                <div class="form-group">
                                    <?= Html::a('Hapus hasil proyeksi saat ini', ['/site/proyeksikan2', 'status' => 'hapus', 'tahun_dasar' => '', 'tahun_target' => '', 'panjang_tahun' => ''], ['class'=>'btn btn-danger']) ?>
                                </div>
                            <?php endif; ?>
                        </span>
                    <?php ActiveForm::end(); ?>
                </center></div>
                <?php endif; ?>

            <?php elseif ($status_proyeksi == 'Belum ada data'): ?>
                
                <div class="col-md-12"><center>
                    <div class="jumbotron" style="background: #f0f0f0; margin-top: 20px; padding-top: 10px; padding-bottom: 10px">
                        <p>
                            <font color=green>Belum ada hasil proyeksi untuk <?= Html::encode(Yii::$app->user->identity->username)?>.</font> Silakan buat proyeksi <?= Html::a('di sini', ['/site/proykabkota']) ?>.
                        </p>
                    </div>
                </center></div>

            <?php endif; ?>



        <?php elseif (Yii::$app->user->identity->role == 'kabkota' || $stat == 'pilih'): ?>

            <?php if ($status_proyeksi == 'Sudah ada proyeksi'): ?>

                <p><b>Berikut adalah data hasil proyeksi penduduk <?= Html::encode(Yii::$app->user->identity->username)?>:</b></p>
                    <?php 

                    $gridColumns = []; 
                        $gridColumns[] = [
                            'label' => 'Kode',
                            'attribute' => '0',
                            'hAlign' => 'center',
                        ];
                        $gridColumns[] = [
                            'label' => 'Kabupaten/kota',
                            'attribute' => '1',
                            'hAlign' => 'center',
                        ];
                        for($coba=2;$coba<count($cekkk);$coba++) {
                            $gridColumns[] = [
                                'attribute' => $coba,
                                'label'=> 2013+$coba,
                                'format'=> ['integer'],
                                'hAlign' => 'center',
                            ];
                        };

                    echo GridView::widget([
                        'dataProvider' => $dataProvider,
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
                                    ['content'=> $this->title, 'options'=>['colspan'=>count($gridColumns), 'class'=>'text-center warning']], //cuma satu kolom header
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
                        'pjax' => false,
                        'bordered' => true,
                        'striped' => true,
                        'condensed' => true,
                        'responsive' => true,
                        'hover' => true,
                        'floatHeader' => false,
                        'showPageSummary' => false, //true untuk menjumlahkan nilai di suatu kolom, kebetulan pada contoh tidak ada angka.
                        'panel' => [
                            'type' => GridView::TYPE_DEFAULT,
                            'heading' => false,
                            'footer' => false,
                        ],

                    ]);
                     ?>

                <!--div tombol untuk munculkan form ubah data (muncul kalo udah disetujui) -->
                    <?php //if ($cekSetuju): ?>
                    <span class="pull-right">
                        <center><p>
                            <?= Html::a(Yii::t('app', 'Lihat data laki-laki'), '#.', ['class' => 'btn btn-primary', 'onclick' => "bukaDivLaki2()"]) ?>
                            <?= Html::a(Yii::t('app', 'Lihat data perempuan'), '#.', ['class' => 'btn btn-warning', 'onclick' => "bukaDivPerempuan()"]) ?>
                        </p></center>
                    </span>
                    </br></br>
                    <?php //endif; ?>

                    <div id="divLaki2" style="display:none;">
                        <?php 

                        $gridColumns = []; 
                            $gridColumns[] = [
                                'label' => 'Kode',
                                'attribute' => '0',
                                'hAlign' => 'center',
                                'pageSummary' => Yii::$app->user->identity->wilayah_id,
                            ];
                            $gridColumns[] = [
                                'label' => 'Kabupaten/kota',
                                'attribute' => '1',
                                'hAlign' => 'center',
                                'pageSummary' => Yii::$app->user->identity->username,
                            ];
                            for($coba=2;$coba<count($cekkk);$coba++) {
                                $gridColumns[] = [
                                    'attribute' => $coba,
                                    'label'=> 2013+$coba,
                                    'pageSummary' => true,
                                    'format'=> ['integer'],
                                    'hAlign' => 'center',
                                ];
                            };

                        echo GridView::widget([
                            'dataProvider' => $dataProvider_laki2,
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
                                        ['content'=> 'Data Hasil Proyeksi Penduduk Laki-laki ('. Yii::$app->user->identity->username .')', 'options'=>['colspan'=>count($gridColumns), 'class'=>'text-center warning']], //cuma satu kolom header
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
                            'pjax' => false,
                            'bordered' => true,
                            'striped' => true,
                            'condensed' => true,
                            'responsive' => true,
                            'hover' => true,
                            'floatHeader' => false,
                            'showPageSummary' => false, //true untuk menjumlahkan nilai di suatu kolom, kebetulan pada contoh tidak ada angka.
                            'panel' => [
                                'type' => GridView::TYPE_DEFAULT,
                                'heading' => false,
                                'footer' => false,
                            ],

                        ]);
                         ?>
                    </div>

                    <div id="divPerempuan" style="display:none;">
                        <?php 

                        $gridColumns = []; 
                            $gridColumns[] = [
                                'label' => 'Kode',
                                'attribute' => '0',
                                'hAlign' => 'center',
                                'pageSummary' => Yii::$app->user->identity->wilayah_id,
                            ];
                            $gridColumns[] = [
                                'label' => 'Kabupaten/kota',
                                'attribute' => '1',
                                'hAlign' => 'center',
                                'pageSummary' => Yii::$app->user->identity->username,
                            ];
                            for($coba=2;$coba<count($cekkk);$coba++) {
                                $gridColumns[] = [
                                    'attribute' => $coba,
                                    'label'=> 2013+$coba,
                                    'pageSummary' => true,
                                    'format'=> ['integer'],
                                    'hAlign' => 'center',
                                ];
                            };

                        echo GridView::widget([
                            'dataProvider' => $dataProvider_perempuan,
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
                                        ['content'=> 'Data Hasil Proyeksi Penduduk Perempuan ('. Yii::$app->user->identity->username .')', 'options'=>['colspan'=>count($gridColumns), 'class'=>'text-center warning']], //cuma satu kolom header
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
                            'pjax' => false,
                            'bordered' => true,
                            'striped' => true,
                            'condensed' => true,
                            'responsive' => true,
                            'hover' => true,
                            'floatHeader' => false,
                            'showPageSummary' => false, //true untuk menjumlahkan nilai di suatu kolom, kebetulan pada contoh tidak ada angka.
                            'panel' => [
                                'type' => GridView::TYPE_DEFAULT,
                                'heading' => false,
                                'footer' => false,
                            ],

                        ]);
                         ?>
                    </div>

                <br>

                <p><b>Berikut adalah grafik hasil proyeksi <?= Html::encode(Yii::$app->user->identity->username)?> hasil SUPAS2015:</b></p>
                    <?php Highcharts::widget([
                        'id' => 'highchart-proyprov',
                        'scripts' => [
                            'modules/exporting',
                            'themes/grid-light'
                        ],
                        'options' => [
                            'title' => ['text' => 'Proyeksi Penduduk (' . Yii::$app->user->identity->username . ')'],
                            'chart' => ['type' => 'line'],
                            'subtitle' => [
                                'text' => 'Hasil SUPAS 2015'  
                            ],
                            'xAxis' => [
                                'categories' => $tahun_proyeksi,
                             ],
                            'yAxis' => [
                                //'min' => 0,
                                //'max' => 2100000,
                                //'tickInterval' => 30000,
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
                                    'name' => $nama[0],
                                    'data' => $data_grafik,
                                ],
                            ],   
                        ]
                    ]); ?> 
                    <div id='highchart-proyprov'> </div>

            <?php elseif ($status_proyeksi == 'Belum ada data'): ?>
                <div class="col-md-12"><center>
                    <div class="jumbotron" style="background: #f0f0f0; margin-top: 20px; padding-top: 10px; padding-bottom: 10px">
                        <p>
                            <font color=green>Belum ada hasil proyeksi untuk <?= Html::encode(Yii::$app->user->identity->username)?>.</font> Silakan buat proyeksi <?= Html::a('di sini', ['/site/proykabkota']) ?>.
                        </p>
                    </div>
                <center></div>

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
                <?php if ($status_proyeksi == 'Sudah ada proyeksi'): ?>
                <!-- Periksa apakah hasil proyeksi sudah disetujui atau belum -->
                    <?php
                        //Buat koneksi ke DB
                            include "koneksi.php";
                            $sql_CekSetuju = "SELECT *
                                             FROM laporan
                                             WHERE perihal = 'Cek hasil proyeksi' 
                                             AND status = 'Disetujui'
                                             AND asal = '" . $provinsi_terpilih . "'";
                            $query_CekSetuju = mysqli_query($host,$sql_CekSetuju) or die(mysqli_error());

                            $sql_CekTolak = "SELECT *
                                             FROM laporan
                                             WHERE perihal = 'Cek hasil proyeksi' 
                                             AND status = 'Periksa kembali'
                                             AND asal = '" . $provinsi_terpilih . "'";
                            $query_CekTolak = mysqli_query($host,$sql_CekTolak) or die(mysqli_error());
                                        
                        //Memasukan data dari database dalam bentuk ARRAY
                            $cekSetuju = 0; $cekTolak = 0;
                            while($row = mysqli_fetch_array($query_CekSetuju))
                            { 
                                $cekSetuju++;
                            };
                            while($row = mysqli_fetch_array($query_CekTolak))
                            { 
                                $cekTolak++;
                            };
                    ?>
                    <!-- Cek kondisi sudah disetujui atau belum -->
                        <div class="col-md-12">
                            <div class="jumbotron" style="background: #f0f0f0; margin-top: 20px; padding-top: 10px; padding-bottom: 10px">
                                <center><p>
                                    <?php if ($cekSetuju): ?>
                                        Hasil proyeksi penduduk kabupaten/kota <?php echo $provinsi_terpilih ?> <font color=green><b>telah disetujui</b></font>.
                                    <?php elseif ($cekTolak): ?>
                                        Hasil proyeksi penduduk kabupaten/kota <?php echo $provinsi_terpilih ?> <font color=red><b>ditolak</b></font>, harap periksa kembali hasil proyeksi.
                                    <?php elseif (!$cekSetuju || !$cekTolak): ?>
                                        Hasil proyeksi belum diberi keputusan, silakan periksa hasil proyeksi berikut. Tekan <font color=green><b>"Setujui"</b></font> jika hasil proyeksi telah benar atau <font color=red><b>"Periksa Ulang"</b></font> jika masih terdapat kesalahan pada hasil proyeksi.
                                    <?php endif; ?>
                                </p></center>
                            </div>
                        </div>

                <p><b>Berikut adalah data hasil proyeksi penduduk kabupaten/kota <?php echo $provinsi_terpilih ?>:</b></p>
                <?php 

                    $gridColumns = []; 
                        $gridColumns[] = [
                            'label' => 'Kode',
                            'attribute' => '0',
                            'hAlign' => 'center',
                            'pageSummary' => $id_provinsi_terpilih,
                        ];
                        $gridColumns[] = [
                            'label' => 'Kabupaten/kota',
                            'attribute' => '1',
                            'hAlign' => 'center',
                            'pageSummary' => $provinsi_terpilih,
                        ];
                        for($coba=2;$coba<count($cekkk);$coba++) {
                            $gridColumns[] = [
                                'attribute' => $coba,
                                'label'=> 2013+$coba,
                                'pageSummary' => true,
                                'format'=> ['integer'],
                                'hAlign' => 'center',
                            ];
                        };
                            $gridColumns[] = [
                                'class' => 'yii\grid\ActionColumn',
                                'template' => '{view}'
                            ];

                    echo GridView::widget([
                        'dataProvider' => $dataProvider,
                        'options' => ['style' => 'font-size:12px;'],
                        'columns' => $gridColumns,
                        'containerOptions' => ['style' => 'overflow: auto'], 
                        'headerRowOptions' => ['class' => 'kartik-sheet-style'],
                        'showFooter' => true,
                        'summary'=>'',
                        'containerOptions' => ['style'=>'overflow: auto'], 
                        'beforeHeader'=>[
                            [
                                'columns'=>[
                                    ['content'=> $this->title, 'options'=>['colspan'=>count($gridColumns), 'class'=>'text-center warning']], //cuma satu kolom header
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
                        'pjax' => false,
                        'bordered' => true,
                        'striped' => true,
                        'condensed' => true,
                        'responsive' => true,
                        'hover' => true,
                        'floatHeader' => false,
                        'showPageSummary' => true, //true untuk menjumlahkan nilai di suatu kolom, kebetulan pada contoh tidak ada angka.
                        'panel' => [
                            'type' => GridView::TYPE_DEFAULT,
                            'heading' => false,
                            'footer' => false,
                        ],

                    ]);
                    ?>

                <!--div tombol untuk munculkan form ubah data (muncul kalo udah disetujui) -->
                <?php //if ($cekSetuju): ?>
                <span class="pull-right">
                    <center><p>
                        <?= Html::a(Yii::t('app', 'Lihat data laki-laki'), '#.', ['class' => 'btn btn-primary btn-xs', 'onclick' => "bukaDivLaki2()"]) ?>
                        <?= Html::a(Yii::t('app', 'Lihat data perempuan'), '#.', ['class' => 'btn btn-warning btn-xs', 'onclick' => "bukaDivPerempuan()"]) ?>
                    </p></center>
                </span>
                </br></br>
                <?php //endif; ?>               

                <div id="divLaki2" style="display:none;">
                    <p><b>Berikut adalah data hasil proyeksi penduduk laki-laki kabupaten/kota <?= Html::encode(Yii::$app->user->identity->username)?>:</b></p>
                    <?php 

                    $gridColumns = []; 
                        $gridColumns[] = [
                            'label' => 'Kode',
                            'attribute' => '0',
                            'hAlign' => 'center',
                            'pageSummary' => Yii::$app->user->identity->wilayah_id,
                        ];
                        $gridColumns[] = [
                            'label' => 'Kabupaten/kota',
                            'attribute' => '1',
                            'hAlign' => 'center',
                            'pageSummary' => Yii::$app->user->identity->username,
                        ];
                        for($coba=2;$coba<count($cekkk);$coba++) {
                            $gridColumns[] = [
                                'attribute' => $coba,
                                'label'=> 2013+$coba,
                                'pageSummary' => true,
                                'format'=> ['integer'],
                                'hAlign' => 'center',
                            ];
                        };

                    echo GridView::widget([
                        'dataProvider' => $dataProvider_laki2,
                        'options' => ['style' => 'font-size:12px;'],
                        'columns' => $gridColumns,
                        'containerOptions' => ['style' => 'overflow: auto'], 
                        'headerRowOptions' => ['class' => 'kartik-sheet-style'],
                        'showFooter' => true,
                        'summary'=>'',
                        'containerOptions' => ['style'=>'overflow: auto'], 
                        'beforeHeader'=>[
                            [
                                'columns'=>[
                                    ['content'=> 'Data Hasil Proyeksi Penduduk Laki-laki ('. Yii::$app->user->identity->username .')', 'options'=>['colspan'=>count($gridColumns), 'class'=>'text-center warning']], //cuma satu kolom header
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
                        'pjax' => false,
                        'bordered' => true,
                        'striped' => true,
                        'condensed' => true,
                        'responsive' => true,
                        'hover' => true,
                        'floatHeader' => false,
                        'showPageSummary' => true, //true untuk menjumlahkan nilai di suatu kolom, kebetulan pada contoh tidak ada angka.
                        'panel' => [
                            'type' => GridView::TYPE_DEFAULT,
                            'heading' => false,
                            'footer' => false,
                        ],

                    ]);
                     ?>
                </div>

                <div id="divPerempuan" style="display:none;">
                    <p><b>Berikut adalah data hasil proyeksi penduduk perempuan kabupaten/kota <?= Html::encode(Yii::$app->user->identity->username)?>:</b></p>
                    <?php 

                    $gridColumns = []; 
                        $gridColumns[] = [
                            'label' => 'Kode',
                            'attribute' => '0',
                            'hAlign' => 'center',
                            'pageSummary' => Yii::$app->user->identity->wilayah_id,
                        ];
                        $gridColumns[] = [
                            'label' => 'Kabupaten/kota',
                            'attribute' => '1',
                            'hAlign' => 'center',
                            'pageSummary' => Yii::$app->user->identity->username,
                        ];
                        for($coba=2;$coba<count($cekkk);$coba++) {
                            $gridColumns[] = [
                                'attribute' => $coba,
                                'label'=> 2013+$coba,
                                'pageSummary' => true,
                                'format'=> ['integer'],
                                'hAlign' => 'center',
                            ];
                        };

                    echo GridView::widget([
                        'dataProvider' => $dataProvider_perempuan,
                        'options' => ['style' => 'font-size:12px;'],
                        'columns' => $gridColumns,
                        'containerOptions' => ['style' => 'overflow: auto'], 
                        'headerRowOptions' => ['class' => 'kartik-sheet-style'],
                        'showFooter' => true,
                        'summary'=>'',
                        'containerOptions' => ['style'=>'overflow: auto'], 
                        'beforeHeader'=>[
                            [
                                'columns'=>[
                                    ['content'=> 'Data Hasil Proyeksi Penduduk Perempuan ('. Yii::$app->user->identity->username .')', 'options'=>['colspan'=>count($gridColumns), 'class'=>'text-center warning']], //cuma satu kolom header
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
                        'pjax' => false,
                        'bordered' => true,
                        'striped' => true,
                        'condensed' => true,
                        'responsive' => true,
                        'hover' => true,
                        'floatHeader' => false,
                        'showPageSummary' => true, //true untuk menjumlahkan nilai di suatu kolom, kebetulan pada contoh tidak ada angka.
                        'panel' => [
                            'type' => GridView::TYPE_DEFAULT,
                            'heading' => false,
                            'footer' => false,
                        ],

                    ]);
                     ?>
                </div>

                <!--div tombol setujui atau tidak-->
                    <span>
                        <div class="col-xs-12 col-sm-12 left-padding">
                            <center>
                            <?php if ($cekSetuju): //Jika sudah disetujui maka hanya ada tombol "Batalkan persetujuan"?>
                                <?= Html::a('Batalkan status persetujuan', ['/site/simpan', 'id' => $provinsi_terpilih, 'status' => 'batal', 'id_wilayah' => '', 'tahun_dasar' => '', 'jenis_kelamin' => ''], ['class' => 'btn btn-danger']) ?>
                            <?php elseif ($cekTolak): //Jika sudah disetujui maka hanya ada tombol "Batalkan persetujuan"?>
                                <?= Html::a('Batalkan status periksa kembali', ['/site/simpan', 'id' => $provinsi_terpilih, 'status' => 'batal', 'id_wilayah' => '', 'tahun_dasar' => '', 'jenis_kelamin' => ''], ['class' => 'btn btn-success']) ?>
                            <?php elseif (!$cekSetuju || !$cekTolak): //Jika belum disetujui ada tombol "Setujui" dan "Periksa Kembali" ?> 
                                <?= Html::a('Setujui hasil proyeksi', ['/site/simpan', 'id' => $provinsi_terpilih, 'status' => 'setuju', 'id_wilayah' => '', 'tahun_dasar' => '', 'jenis_kelamin' => ''], ['class' => 'btn btn-success']) ?>
                                <?= Html::a('Periksa kembali hasil proyeksi', ['/site/simpan', 'id' => $provinsi_terpilih, 'status' => 'tolak', 'id_wilayah' => '', 'tahun_dasar' => '', 'jenis_kelamin' => ''], ['class' => 'btn btn-danger']) ?>
                            <?php endif; ?>
                            </center>
                        </div>
                    </span> 

                <?php elseif ($status_proyeksi == 'Belum ada data'): ?>
                    <div class="col-md-12">
                        <div class="jumbotron" style="background: #f0f0f0; margin-top: 20px; padding-top: 10px; padding-bottom: 10px">
                            <p>
                                <font color=green>Belum ada hasil proyeksi penduduk kabupaten/kota untuk <?= Html::encode($provinsi_terpilih)?>.</font>
                            </p>
                        </div>
                    </div>

                <?php endif; ?>
            <?php endif; ?>
            

        <?php endif; ?>
    </div>
  </div>        
</div>

