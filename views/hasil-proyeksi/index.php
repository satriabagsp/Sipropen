<?php

use yii\helpers\Html;
use yii\grid\GridView;
use app\models\Laporan;
use app\models\HasilProyeksi;
use yii\data\ActiveDataProvider;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $dataProvider yii\data\ActiveDataProvider */

//Inisiasi variabel untuk menampilkan form.
$model = new Laporan();
if ($model->load(Yii::$app->request->post()) && $model->save()) {
    Yii::$app->session->setFlash('success', 'Hasil proyeksi berhasil dikirim ke pusat untuk diperiksa');
};

$huaa = '';

$this->title = 'Hasil Proyeksi Penduduk (' . Yii::$app->user->identity->username . ')' ;
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="hasil-proyeksi-index">
    <div class="panel panel-info">

    <?php if (Yii::$app->user->identity->role == 'provinsi'): ?>
        <div class="panel-heading"><h1><?= Html::encode($this->title) ?></h1></div>

        <!-- Cek apakah sudah melakukan proyeksi atau belum -->
            <?php
                //Mulai buat query sql
                    include "koneksi.php";
                    $sql_CekHasil = "SELECT *
                                FROM hasil_proyeksi
                                WHERE provinsi = '" . Yii::$app->user->identity->username . "'";
                    $query_CekHasil = mysqli_query($host,$sql_CekHasil) or die(mysqli_error());
                                
                //Cek apakah sudah ada atau belum
                    $cekHasil = 0;
                    while($row = mysqli_fetch_array($query_CekHasil))
                        { 
                            $cekHasil++;
                        };
            ?>
                    
                    <?php if (!$cekHasil): ?>
                        <div class="col-md-12">
                            <div class="jumbotron" style="background: #f0f0f0; margin-top: 20px; padding-top: 10px; padding-bottom: 10px">
                                <p>
                                    Belum terdapat proyeksi penduduk untuk <?= Html::encode(Yii::$app->user->identity->username) ?>. Silakan buat proyeksi penduduk kabupaten/kota <?= Html::a('di sini', ['/site/proykabkota']) ?> untuk selanjutnya dikirim ke pusat untuk diperiksa.
                                </p>
                            </div>
                        </div>
                    
                    <?php elseif ($cekHasil): ?>
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
  
                        <div class="col-md-12">
                            <div class="jumbotron" style="background: #f0f0f0; margin-top: 20px; padding-top: 10px; padding-bottom: 10px">
                                <p>
                                    <?php if ($cekPeriksa): ?>
                                        Hasil proyeksi penduduk <?= Html::encode(Yii::$app->user->identity->username) ?> sudah dikirim ke pusat untuk diperiksa.
                                    <?php elseif (!$cekPeriksa): ?>
                                        Hasil proyeksi penduduk <?= Html::encode(Yii::$app->user->identity->username) ?> <font color=red>belum dikirim</font> ke pusat untuk diperiksa, silakan kirimkan hasil proyeksi jika sudah dianggap benar dengan tombol di bawah.
                                    <?php endif; ?>
                                </p>
                            </div>
                        </div>

                        <div class="panel-body" style="overflow-x:auto;">
                            <p>Berikut adalah hasil proyeksi penduduk kabupaten/kota di <?= Html::encode(Yii::$app->user->identity->username)?>:</p>
                                <?= GridView::widget([
                                    'dataProvider' => $dataProvider,
                                    'options' => ['style' => 'font-size:12px;'],
                                    'columns' => [
                                        ['class' => 'yii\grid\SerialColumn'],

                                        'kab_kota',
                                        [
                                            'attribute' => 'p2015',
                                            'format'=> ['integer'],
                                            //'footer' => number_format($data['total2010'], 0 , "." ,  "," ),
                                        ],
                                        [
                                            'attribute' => 'p2016',
                                            'format'=> ['integer'],
                                            //'footer' => number_format($data['total2010'], 0 , "." ,  "," ),
                                        ],
                                        [
                                            'attribute' => 'p2017',
                                            'format'=> ['integer'],
                                            //'footer' => number_format($data['total2010'], 0 , "." ,  "," ),
                                        ],
                                        [
                                            'attribute' => 'p2018',
                                            'format'=> ['integer'],
                                            //'footer' => number_format($data['total2010'], 0 , "." ,  "," ),
                                        ],
                                        [
                                            'attribute' => 'p2019',
                                            'format'=> ['integer'],
                                            //'footer' => number_format($data['total2010'], 0 , "." ,  "," ),
                                        ],
                                        [
                                            'attribute' => 'p2020',
                                            'format'=> ['integer'],
                                            //'footer' => number_format($data['total2010'], 0 , "." ,  "," ),
                                        ],
                                        [
                                            'attribute' => 'p2021',
                                            'format'=> ['integer'],
                                            //'footer' => number_format($data['total2010'], 0 , "." ,  "," ),
                                        ],
                                        [
                                            'attribute' => 'p2022',
                                            'format'=> ['integer'],
                                            //'footer' => number_format($data['total2010'], 0 , "." ,  "," ),
                                        ],
                                        [
                                            'attribute' => 'p2023',
                                            'format'=> ['integer'],
                                            //'footer' => number_format($data['total2010'], 0 , "." ,  "," ),
                                        ],
                                        [
                                            'attribute' => 'p2024',
                                            'format'=> ['integer'],
                                            //'footer' => number_format($data['total2010'], 0 , "." ,  "," ),
                                        ],
                                        [
                                            'attribute' => 'p2025',
                                            'format'=> ['integer'],
                                            //'footer' => number_format($data['total2010'], 0 , "." ,  "," ),
                                        ],

                                        [
                                        'class' => 'yii\grid\ActionColumn',
                                        'template' => ' {view} '
                                        ],
                                    ],
                                ]); ?>
                        </div>

                        <div class="laporan-form">
                            <?php $form = ActiveForm::begin(); ?>
                                <div class="jumbotron" style="background: white; margin-top: 20px; padding-top: 10px; padding-bottom: 10px">
                                    <?php if (!$cekPeriksa): ?>
                                        <?= $form->field($model, 'pilihan')->checkbox(array('label'=>'Hasil proyeksi sudah diperiksa dan siap dikirim ke pusat.')); ?>
                                        <div class="form-group">
                                            <?= Html::submitButton('Kirim hasil proyeksi ke pusat', ['class' => 'btn btn-success']) ?>
                                            <?= Html::a('Hapus hasil proyeksi saat ini', ['/site/proyeksikan', 'status' => 'hapus'], ['class'=>'btn btn-danger']) ?>
                                        </div>
                                    <?php elseif ($cekPeriksa): ?>
                                        <div class="form-group">
                                            <?= Html::a('Hapus hasil proyeksi saat ini', ['/site/proyeksikan', 'status' => 'hapus'], ['class'=>'btn btn-danger']) ?>
                                        </div>
                                    <?php endif; ?>
                                </div>
                            <?php ActiveForm::end(); ?>
                        </div>
                        
                    <?php endif; ?>

    <?php elseif (Yii::$app->user->identity->role == 'kab_kota'): ?> 
        <div class="panel-heading"><h1><?= Html::encode($this->title) ?></h1></div>

        <!-- Periksa apakah hasil proyeksi sudah disetujui oleh pusat? -->
            <?php
                //Mulai buat query sql
                    include "koneksi.php";
                    $sql_query = "SELECT hasil_proyeksi.*, laporan.*
                                  FROM hasil_proyeksi, laporan
                                  WHERE laporan.perihal = 'Cek hasil proyeksi' 
                                  AND laporan.status = 'Disetujui' 
                                  AND hasil_proyeksi.provinsi = laporan.asal 
                                  AND hasil_proyeksi.kab_kota = '".Yii::$app->user->identity->username."'" ;
                    $query_mysql = mysqli_query($host,$sql_query) or die(mysqli_error());
                                    
                //Memasukan data dari database dalam bentuk ARRAY
                    $cekPeriksa = 0;
                    while($row = mysqli_fetch_array($query_mysql)){ 
                        $cekPeriksa++;
                    };
            ?>

            <?php if ($cekPeriksa): ?>
                <div class="panel-body" style="overflow-x:auto;">
                    <p>Hasil proyeksi penduduk <?= Html::encode(Yii::$app->user->identity->username) ?> sudah disetujui oleh pusat. Berikut adalah hasil proyeksi penduduk <?= Html::encode(Yii::$app->user->identity->username)?>:</p>
                        <?= GridView::widget([
                            'dataProvider' => $dataProvider,
                            'options' => ['style' => 'font-size:12px;'],
                            'columns' => [

                                ['class' => 'yii\grid\SerialColumn'],

                                'kab_kota',
                                [
                                    'attribute' => 'p2015',
                                    'format'=> ['integer'],
                                    //'footer' => number_format($data['total2010'], 0 , "." ,  "," ),
                                ],
                                [
                                    'attribute' => 'p2016',
                                    'format'=> ['integer'],
                                    //'footer' => number_format($data['total2010'], 0 , "." ,  "," ),
                                ],
                                [
                                    'attribute' => 'p2017',
                                    'format'=> ['integer'],
                                    //'footer' => number_format($data['total2010'], 0 , "." ,  "," ),
                                ],
                                [
                                    'attribute' => 'p2018',
                                    'format'=> ['integer'],
                                    //'footer' => number_format($data['total2010'], 0 , "." ,  "," ),
                                ],
                                [
                                    'attribute' => 'p2019',
                                    'format'=> ['integer'],
                                    //'footer' => number_format($data['total2010'], 0 , "." ,  "," ),
                                ],
                                [
                                    'attribute' => 'p2020',
                                    'format'=> ['integer'],
                                    //'footer' => number_format($data['total2010'], 0 , "." ,  "," ),
                                ],
                                [
                                    'attribute' => 'p2021',
                                    'format'=> ['integer'],
                                    //'footer' => number_format($data['total2010'], 0 , "." ,  "," ),
                                ],
                                [
                                    'attribute' => 'p2022',
                                    'format'=> ['integer'],
                                    //'footer' => number_format($data['total2010'], 0 , "." ,  "," ),
                                ],
                                [
                                    'attribute' => 'p2023',
                                    'format'=> ['integer'],
                                    //'footer' => number_format($data['total2010'], 0 , "." ,  "," ),
                                ],
                                [
                                    'attribute' => 'p2024',
                                    'format'=> ['integer'],
                                    //'footer' => number_format($data['total2010'], 0 , "." ,  "," ),
                                ],
                                [
                                    'attribute' => 'p2025',
                                    'format'=> ['integer'],
                                    //'footer' => number_format($data['total2010'], 0 , "." ,  "," ),
                                ],

                                [
                                    'class' => 'yii\grid\ActionColumn',
                                    'template' => ' {view} '
                                ],
                            ],
                        ]); ?>                                
                </div>

            <?php elseif (!$cekPeriksa): ?>
                <div class="col-md-12">
                    <div class="jumbotron" style="background: #f0f0f0; margin-top: 20px; padding-top: 10px; padding-bottom: 10px">
                        <p>
                            Hasil proyeksi penduduk <?= Html::encode(Yii::$app->user->identity->username) ?> <font color=red>belum disetujui</font> oleh pusat.                           
                        </p>
                    </div>
                </div>

            <?php endif; ?>


    <?php elseif (Yii::$app->user->identity->role == 'pusat'): ?>
        <!-- untuk membedakan apakah pusat melihat dari tabel laporan atau langsung lihat hasil proyeksi -->
        <?php if (!$dataProvider): ?>
            <div class="panel-heading"><h1>Hasil Proyeksi</h1></div>
            <div class="panel-body" style="overflow-x:auto;">  
                <p>Berikut adalah provinsi yang sudah membuat proyeksi penduduk:</p>
                <?php
                    //menampilkan tabel berisi daftar provinsi yang sudah membuat proyeksi
                    $dataHasilProy = new ActiveDataProvider([
                        'query' => HasilProyeksi::findBySql('SELECT provinsi FROM hasil_proyeksi group by provinsi'),
                        'pagination' => [
                            'pageSize' => 50,
                        ],
                        'sort' => [
                            'defaultOrder' => [
                            'provinsi' => SORT_ASC
                            ]
                        ],
                    ]);  ?>
                <?= GridView::widget([
                    'dataProvider' => $dataHasilProy,
                    'options' => ['style' => 'font-size:12px;'],
                    'columns' => [
                        ['class' => 'yii\grid\SerialColumn'],

                        'provinsi',

                        [
                            'label' => 'Pilihan',
                            'format' => 'raw',
                            'value' => function($model) {
                                return Html::a('Lihat hasil proyeksi', ['hasil-proyeksi/tambah', 'tambah1' => $model->provinsi]);
                            },
                        ],
                    ],
                ]); ?>
            </div>
            
        <?php elseif ($dataProvider): ?>
            <div class="panel-heading"><h1>Hasil Proyeksi Penduduk Kabupaten/kota (<?php echo $prov ?>)</h1></div>
            <div class="panel-body" style="overflow-x:auto;">
                <!-- Periksa apakah hasil proyeksi sudah disetujui atau belum -->
                <?php
                    //Buat koneksi ke DB
                        include "koneksi.php";
                        $sql_CekSetuju = "SELECT *
                                         FROM laporan
                                         WHERE perihal = 'Cek hasil proyeksi' 
                                         AND status = 'Disetujui'
                                         AND asal = '" . $prov . "'";
                        $query_CekSetuju = mysqli_query($host,$sql_CekSetuju) or die(mysqli_error());

                        $sql_CekTolak = "SELECT *
                                         FROM laporan
                                         WHERE perihal = 'Cek hasil proyeksi' 
                                         AND status = 'Periksa kembali'
                                         AND asal = '" . $prov . "'";
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
                    <!-- Cek Kondisi -->
                        <div class="col-md-12">
                            <div class="jumbotron" style="background: #f0f0f0; margin-top: 20px; padding-top: 10px; padding-bottom: 10px">
                                <p>
                                    <?php if ($cekSetuju): ?>
                                        Hasil proyeksi penduduk kabupaten/kota <?php echo $prov ?> <font color=green><b>telah disetujui</b></font>.
                                    <?php elseif ($cekTolak): ?>
                                        Hasil proyeksi penduduk kabupaten/kota <?php echo $prov ?> <font color=red><b>ditolak</b></font>, harap periksa kembali hasil proyeksi.
                                    <?php elseif (!$cekSetuju || !$cekTolak): ?>
                                        Hasil proyeksi belum diberi keputusan, silakan periksa hasil proyeksi berikut. Tekan <font color=green><b>"Setujui"</b></font> jika hasil proyeksi telah benar atau <font color=red><b>"Periksa Ulang"</b></font> jika masih terdapat kesalahan pada hasil proyeksi.
                                    <?php endif; ?>
                                </p>
                            </div>
                        </div>


                <p>Berikut adalah hasil proyeksi <?php echo $prov ?>:</p>  
                <?= GridView::widget([
                        'dataProvider' => $dataProvider,
                        'options' => ['style' => 'font-size:12px;'],
                        'columns' => [
                            ['class' => 'yii\grid\SerialColumn'],

                            'kab_kota',
                            [
                                'attribute' => 'p2015',
                                'format'=> ['integer'],
                                //'footer' => number_format($data['total2010'], 0 , "." ,  "," ),
                            ],
                            [
                                'attribute' => 'p2016',
                                'format'=> ['integer'],
                                //'footer' => number_format($data['total2010'], 0 , "." ,  "," ),
                            ],
                            [
                                'attribute' => 'p2017',
                                'format'=> ['integer'],
                                //'footer' => number_format($data['total2010'], 0 , "." ,  "," ),
                            ],
                            [
                                'attribute' => 'p2018',
                                'format'=> ['integer'],
                                //'footer' => number_format($data['total2010'], 0 , "." ,  "," ),
                            ],
                            [
                                'attribute' => 'p2019',
                                'format'=> ['integer'],
                                //'footer' => number_format($data['total2010'], 0 , "." ,  "," ),
                            ],
                            [
                                'attribute' => 'p2020',
                                'format'=> ['integer'],
                                //'footer' => number_format($data['total2010'], 0 , "." ,  "," ),
                            ],
                            [
                                'attribute' => 'p2021',
                                'format'=> ['integer'],
                                //'footer' => number_format($data['total2010'], 0 , "." ,  "," ),
                            ],
                            [
                                'attribute' => 'p2022',
                                'format'=> ['integer'],
                                //'footer' => number_format($data['total2010'], 0 , "." ,  "," ),
                            ],
                            [
                                'attribute' => 'p2023',
                                'format'=> ['integer'],
                                //'footer' => number_format($data['total2010'], 0 , "." ,  "," ),
                            ],
                            [
                                'attribute' => 'p2024',
                                'format'=> ['integer'],
                                //'footer' => number_format($data['total2010'], 0 , "." ,  "," ),
                            ],
                            [
                                'attribute' => 'p2025',
                                'format'=> ['integer'],
                                //'footer' => number_format($data['total2010'], 0 , "." ,  "," ),
                            ],

                            [
                            'class' => 'yii\grid\ActionColumn',
                            'template' => ' {view} '
                            ],
                        ],    
                    ]); ?>

                <!--div tombol setujui atau tidak-->
                    <div id="tombol" class="jumbotron" style="background: white; margin-top: 20px; padding-top: 10px; padding-bottom: 10px">
                        <div class="col-xs-12 col-sm-12 left-padding">
                            <?php if ($cekSetuju): //Jika sudah disetujui maka hanya ada tombol "Batalkan persetujuan"?>
                                <?= Html::a('Batalkan status persetujuan', ['/site/simpan', 'id' => $prov, 'status' => 'batal'], ['class' => 'btn btn-success']) ?>
                            <?php elseif ($cekTolak): //Jika sudah disetujui maka hanya ada tombol "Batalkan persetujuan"?>
                                <?= Html::a('Batalkan status periksa kembali', ['/site/simpan', 'id' => $prov, 'status' => 'batal'], ['class' => 'btn btn-success']) ?>
                            <?php elseif (!$cekSetuju || !$cekTolak): //Jika belum disetujui ada tombol "Setujui" dan "Periksa Kembali" ?> 
                                <?= Html::a('Setujui hasil proyeksi', ['/site/simpan', 'id' => $prov, 'status' => 'setuju'], ['class' => 'btn btn-success']) ?>
                                <?= Html::a('Periksa kembali hasil proyeksi', ['/site/simpan', 'id' => $prov, 'status' => 'tolak'], ['class' => 'btn btn-danger']) ?>
                            <?php endif; ?>
                        </div>
                    </div> 

            </div>
        
        <?php endif; ?>

    <?php endif; ?>

    </div>

</div>    

