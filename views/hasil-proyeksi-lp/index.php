<?php

use yii\helpers\Html;
use yii\bootstrap\Collapse;
use kartik\grid\GridView;
use app\models\DataKabKota;
use app\models\UbahData;
use app\models\Laporan;
use yii\widgets\ActiveForm;
use miloschuman\highcharts\Highcharts;
use miloschuman\highcharts\SeriesDataHelper;
use fedemotta\datatables\DataTables;


/* @var $this yii\web\View */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'Hasil Proyeksi Kabupaten/kota (' . Yii::$app->user->identity->username . ')';
$this->params['breadcrumbs'][] = $this->title;

?>

<div class="hasil-proyeksi-lp-index">
  <div class="panel panel-info">

    <div class="panel-heading"><h1><?= Html::encode($this->title) ?></h1></div>
    <div class="panel-body">
        <?php if (Yii::$app->user->identity->role == 'provinsi'): ?>

        	<?php if ($status_data == 'kosong'): ?>  

                <?php if ($status_proyeksi == 'Sudah ada proyeksi'): ?>

                    <p><b>silakan pilih kabupaten/kota yang akan dilihat:</b></p>
                    <?= DataTables::widget([
                        'dataProvider' => $dataProvider,
                        'options' => ['style' => 'font-size:12px;'],
                        'showFooter' => true,
                        'columns' => [
                             
                            'id_wilayah',
                            'nama_wilayah',
                            [
                                'label' => 'Pilihan',
                                'format' => 'raw',
                                'value' => function($model) {
                                    return Html::a('Lihat Kabupaten/kota', ['lihatkabkota', 'id_kabkota_terpilih' => $model->id_wilayah]);
                                },
                            ],
                        ],
                    ]);?>

                <?php elseif ($status_proyeksi == 'Belum ada data'): ?>
                
                    <div class="col-md-12">
                        <div class="jumbotron" style="background: #f0f0f0; margin-top: 20px; padding-top: 10px; padding-bottom: 10px">
                            <p>
                                <font color=green>Belum ada hasil proyeksi untuk <?= Html::encode(Yii::$app->user->identity->username)?>.</font> Silakan buat proyeksi <?= Html::a('di sini', ['/site/proykabkota']) ?>.
                            </p>
                        </div>
                    </div>

                <?php endif; ?>

            <?php elseif ($status_data == 'pilih kabkota'): ?>
                
                    <p><b>silakan pilih tahun proyeksi penduduk <?php echo $nama_kabkota_terpilih ?> yang akan dilihat:</b></p>
                    <?php $coba = "0"; ?>
                    <?= GridView::widget([
                        'dataProvider' => $dataProvider1,
                        'options' => ['style' => 'font-size:12px;'],
                        'showFooter' => true,
                        'columns' => [

                            [
                                'label' => 'Tahun Proyeksi',
                                'attribute' => 'tahun_proyeksi',
                            ],
                            [
                                'label' => 'Jumlah Penduduk',
                                'attribute' => 'jumlah_proyeksi',
                                'format'=> ['integer'],
                            ],
                            [
                                'label' => 'Pilihan',
                                'format' => 'raw',
                                'value' => function($model) {
                                    return Html::a('Lihat Tahun Proyeksi', ['view', 'id' => $model->no_proyeksi_jumlah]);
                                },
                            ],
                        ],
                    ]);?>


            <?php endif; ?>


        <?php elseif (Yii::$app->user->identity->role == 'kabkota'): ?> 

            <?php if ($status_proyeksi == 'Sudah ada proyeksi'): ?>


                <p><b>silakan pilih tahun proyeksi penduduk <?php echo $nama_kabkota_terpilih ?> yang akan dilihat:</b></p>
                <?php $coba = "0"; ?>
                <?= GridView::widget([
                    'dataProvider' => $dataProvider1,
                    'options' => ['style' => 'font-size:12px;'],
                    'showFooter' => true,
                    'columns' => [

                        [
                            'label' => 'Tahun Proyeksi',
                            'attribute' => 'tahun_proyeksi',
                        ],
                        [
                            'label' => 'Jumlah Penduduk',
                            'attribute' => 'jumlah_proyeksi',
                            'format'=> ['integer'],
                        ],
                        [
                            'label' => 'Pilihan',
                            'format' => 'raw',
                            'value' => function($model) {
                                return Html::a('Lihat Tahun Proyeksi', ['view', 'id' => $model->no_proyeksi_jumlah]);
                            },
                        ],
                    ],
                ]);?>


            <?php elseif ($status_proyeksi == 'Belum ada data'): ?>
                
                    <div class="col-md-12">
                        <div class="jumbotron" style="background: #f0f0f0; margin-top: 20px; padding-top: 10px; padding-bottom: 10px">
                            <p>
                                <font color=green>Belum ada hasil proyeksi untuk <?= Html::encode(Yii::$app->user->identity->username)?>.</font> Silakan buat proyeksi <?= Html::a('di sini', ['/site/proykabkota']) ?>.
                            </p>
                        </div>
                    </div>

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
                    
                    <p><b>silakan pilih kabupaten/kota yang akan dilihat:</b></p>
                    <?= DataTables::widget([
                        'dataProvider' => $dataProvider,
                        'options' => ['style' => 'font-size:12px;'],
                        'showFooter' => true,
                        'columns' => [
                             
                            'id_wilayah',
                            'nama_wilayah',
                            [
                                'label' => 'Pilihan',
                                'format' => 'raw',
                                'value' => function($model) {
                                    return Html::a('Lihat Kabupaten/kota', ['lihatkabkota', 'id_kabkota_terpilih' => $model->id_wilayah]);
                                },
                            ],
                        ],
                    ]);?>

                <?php elseif ($status_proyeksi == 'Belum ada data'): ?>
                    <div class="col-md-12">
                        <div class="jumbotron" style="background: #f0f0f0; margin-top: 20px; padding-top: 10px; padding-bottom: 10px">
                            <p>
                                <font color=green>Belum ada hasil proyeksi penduduk kabupaten/kota untuk <?= Html::encode($provinsi_terpilih)?>.</font>
                            </p>
                        </div>
                    </div>

                <?php endif; ?>
            

            <?php elseif ($status_data == 'pilih kabkota'): ?>
                
                    <p><b>silakan pilih tahun proyeksi penduduk <?php echo $nama_kabkota_terpilih ?> yang akan dilihat:</b></p>
                    <?php $coba = "0"; ?>
                    <?= GridView::widget([
                        'dataProvider' => $dataProvider1,
                        'options' => ['style' => 'font-size:12px;'],
                        'showFooter' => true,
                        'columns' => [

                            [
                                'label' => 'Tahun Proyeksi',
                                'attribute' => 'tahun_proyeksi',
                            ],
                            [
                                'label' => 'Jumlah Penduduk',
                                'attribute' => 'jumlah_proyeksi',
                                'format'=> ['integer'],
                            ],
                            [
                                'label' => 'Pilihan',
                                'format' => 'raw',
                                'value' => function($model) {
                                    return Html::a('Lihat Tahun Proyeksi', ['view', 'id' => $model->no_proyeksi_jumlah]);
                                },
                            ],
                        ],
                    ]);?>


            <?php endif; ?>
            

        <?php endif; ?>
    </div>
  </div>        
</div>


