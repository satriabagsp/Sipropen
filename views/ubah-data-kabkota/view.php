<?php

use yii\helpers\Html;
use app\models\DataKabkotaLP;
use yii\widgets\DetailView;
use yii\widgets\ActiveForm;
use app\models\Laporan;

/* @var $this yii\web\View */
/* @var $model app\models\UbahDataKabkota */

$this->title = 'Permintaan Ubah Data ' . $nama_wilayah . ' ' . $desk;
$this->params['breadcrumbs'][] = ['label' => 'Ubah Data Kabkotas', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;

//Inisiasi variabel jenis kelamin dan tahun
    $tahun_dasar = substr($desk, 2, 4);
    $jenis_kelamin = substr($desk, 0, 1);

?>


<div class="ubah-data-kabkota-view">
    <div class="panel panel-info">
    <div class="panel-heading"><h1><?= Html::encode($this->title) ?></h1></div>
        <div class="panel-body">

            <!-- Periksa apakah permintaan ubah data sudah disetujui atau belum -->
                <?php
                    //Buat koneksi ke DB
                        include "koneksi.php";
                        $sql_CekSetuju = "SELECT *
                                         FROM laporan
                                         WHERE perihal = 'Permintaan ubah data' 
                                         AND status = 'Disetujui'
                                         AND asal = '" . $nama_wilayah . "'
                                         AND deskripsi = '" . $desk . "'";
                        $query_CekSetuju = mysqli_query($host,$sql_CekSetuju) or die(mysqli_error());

                        $sql_CekTolak = "SELECT *
                                         FROM laporan
                                         WHERE perihal = 'Permintaan ubah data' 
                                         AND status = 'Periksa kembali'
                                         AND asal = '" . $nama_wilayah . "'
                                         AND deskripsi = '" . $desk . "'";
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

            <?php if (Yii::$app->user->identity->role == 'provinsi'): ?>
            <div class="col-md-12">
                <div class="jumbotron" style="background: #f0f0f0; margin-top: 20px; padding-top: 10px; padding-bottom: 10px">
                    <center><p>
                        <?php if (!$cekSetuju && !$cekTolak): ?>
                            Permintaan ubah data belum diberi keputusan, silakan periksa data ubahan. Tekan <font color=green><b>"Setujui"</b></font> jika hasil proyeksi telah benar atau <font color=red><b>"Periksa Ulang"</b></font> jika masih terdapat kesalahan pada data ubahan.
                        <?php elseif ($cekSetuju): ?>
                            Pemintaan ubah data <?php echo $nama_wilayah ?> <font color=green><b>telah disetujui</b></font>.<br>Berikut adalah data <?= Html::encode($nama_wilayah)?> yang telah diupdate.
                        <?php elseif ($cekTolak): ?>
                            Pemintaan ubah data <?php echo $nama_wilayah ?> <font color=red><b>ditolak</b></font>.
                        <?php endif; ?>
                    </p></center>
                </div>
            </div>
            <?php endif; ?>

            <?php if (Yii::$app->user->identity->role == 'kabkota'): ?>
            <div class="col-md-12">
                <div class="jumbotron" style="background: #f0f0f0; margin-top: 20px; padding-top: 10px; padding-bottom: 10px">
                    <center><p>
                        <?php if (!$cekSetuju && !$cekTolak): ?>
                            Permintaan ubah data belum diberi keputusan, silakan periksa kembali data ubahan jika masih terdapat kesalahan.
                        <?php elseif ($cekSetuju): ?>
                            Pemintaan ubah data <?php echo $nama_wilayah ?> <font color=green><b>telah disetujui</b></font>.
                        <?php elseif ($cekTolak): ?>
                            Pemintaan ubah data <?php echo $nama_wilayah ?> <font color=red><b>ditolak</b></font>. <br>Silakan periksa kembali data ubahan dan kirim ulang permintaan ubah data.
                        <?php endif; ?>
                    </p></center>
                </div>
            </div>
            <?php endif; ?>

            <div class="col-md-6">

                <p><b>Data jumlah penduduk <?= Html::encode($nama_wilayah)?>:</b></p>

                <?= DetailView::widget([
                    'model' => $model_data_kabkota,
                    'attributes' => [
                        'id_wilayah', 
                        [
                            'label' => 'Nama Kabupaten/kota',
                            'value' => $nama_wilayah,
                        ],
                        'tahun_dasar', 
                        'jenis_kelamin', 
                        [
                            'attribute' => 'ku_5',
                            'format' => 'integer',
                        ],
                        [
                            'attribute' => 'ku_10',
                            'format' => 'integer',
                        ],
                        [
                            'attribute' => 'ku_15',
                            'format' => 'integer',
                        ],
                        [
                            'attribute' => 'ku_20',
                            'format' => 'integer',
                        ],
                        [
                            'attribute' => 'ku_25',
                            'format' => 'integer',
                        ],
                        [
                            'attribute' => 'ku_30',
                            'format' => 'integer',
                        ],
                        [
                            'attribute' => 'ku_35',
                            'format' => 'integer',
                        ],
                        [
                            'attribute' => 'ku_40',
                            'format' => 'integer',
                        ],
                        [
                            'attribute' => 'ku_45',
                            'format' => 'integer',
                        ],
                        [
                            'attribute' => 'ku_50',
                            'format' => 'integer',
                        ],
                        [
                            'attribute' => 'ku_55',
                            'format' => 'integer',
                        ],
                        [
                            'attribute' => 'ku_60',
                            'format' => 'integer',

                        ],
                        [
                            'attribute' => 'ku_65',
                            'format' => 'integer',
                        ],
                        [
                            'attribute' => 'ku_70',
                            'format' => 'integer',
                        ],
                        [
                            'attribute' => 'ku_75',
                            'format' => 'integer',
                        ],
                        [
                            'attribute' => 'ku_80',
                            'format' => 'integer',
                        ],
                        
                    ],
                ]) ?>

            </div>

            <div class="col-md-6">

                <p><b>Data permintaan ubahan jumlah penduduk <?= Html::encode($nama_wilayah)?>:</b></p>

                <?= DetailView::widget([
                    'model' => $model_ubahan,
                    'attributes' => [
                        'id_wilayah', 
                        [
                            'label' => 'Nama Kabupaten/kota',
                            'value' => $nama_wilayah,
                        ],
                        'tahun_data', 
                        'jenis_kelamin', 
                        [
                            'attribute' => 'ku_5',
                            'format' => 'integer',
                        ],
                        [
                            'attribute' => 'ku_10',
                            'format' => 'integer',
                        ],
                        [
                            'attribute' => 'ku_15',
                            'format' => 'integer',
                        ],
                        [
                            'attribute' => 'ku_20',
                            'format' => 'integer',
                        ],
                        [
                            'attribute' => 'ku_25',
                            'format' => 'integer',
                        ],
                        [
                            'attribute' => 'ku_30',
                            'format' => 'integer',
                        ],
                        [
                            'attribute' => 'ku_35',
                            'format' => 'integer',
                        ],
                        [
                            'attribute' => 'ku_40',
                            'format' => 'integer',
                        ],
                        [
                            'attribute' => 'ku_45',
                            'format' => 'integer',
                        ],
                        [
                            'attribute' => 'ku_50',
                            'format' => 'integer',
                        ],
                        [
                            'attribute' => 'ku_55',
                            'format' => 'integer',
                        ],
                        [
                            'attribute' => 'ku_60',
                            'format' => 'integer',

                        ],
                        [
                            'attribute' => 'ku_65',
                            'format' => 'integer',
                        ],
                        [
                            'attribute' => 'ku_70',
                            'format' => 'integer',
                        ],
                        [
                            'attribute' => 'ku_75',
                            'format' => 'integer',
                        ],
                        [
                            'attribute' => 'ku_80',
                            'format' => 'integer',
                        ],
                        
                    ],
                ]) ?>

                <?php if (!$cekSetuju): ?>
                <span class="pull-right">
                    
                </span>
                <?php endif; ?>

            </div>


            <?php if (Yii::$app->user->identity->role == 'provinsi'): ?>
            <!--div tombol setujui atau tidak-->
                <span>
                    <div class="col-xs-12 col-sm-12 center-padding"><center>
                        <br>
                        <?php if ($cekSetuju): //Jika sudah disetujui maka hanya ada tombol "Batalkan persetujuan"?>
                            
                        <?php elseif ($cekTolak): //Jika sudah disetujui maka hanya ada tombol "Batalkan persetujuan"?>
                            <?= Html::a('Batalkan status periksa kembali', ['/site/simpan', 'id' => $nama_wilayah, 'status' => 'batal', 'id_wilayah' => $id_wilayah, 'tahun_dasar' => $tahun_dasar, 'jenis_kelamin' => $jenis_kelamin], ['class' => 'btn btn-success']) ?>
                        <?php elseif (!$cekSetuju || !$cekTolak): //Jika belum disetujui ada tombol "Setujui" dan "Periksa Kembali" ?> 
                            <?= Html::a('Setujui hasil proyeksi', ['/site/simpan', 'id' => $nama_wilayah, 'status' => 'setuju', 'id_wilayah' => $id_wilayah, 'tahun_dasar' => $tahun_dasar, 'jenis_kelamin' => $jenis_kelamin], ['class' => 'btn btn-success']) ?>
                            <?= Html::a('Periksa kembali hasil proyeksi', ['/site/simpan', 'id' => $nama_wilayah, 'status' => 'tolak', 'id_wilayah' => $id_wilayah, 'tahun_dasar' => $tahun_dasar, 'jenis_kelamin' => $jenis_kelamin], ['class' => 'btn btn-danger']) ?>
                            <?= Html::a('Edit data ubahan terlebih dahulu', ['update', 'id' => $model_ubahan->no_ubah_data, 'nama_wilayah' => $nama_wilayah], ['class' => 'btn btn-primary']) ?>
                        <?php endif; ?>
                    </center></div>
                </span> 

            <?php elseif (Yii::$app->user->identity->role == 'kabkota'): ?>
            <!--div tombol setujui atau tidak-->
                <span>
                    <div class="col-xs-12 col-sm-12 center-padding"><center>
                        <br>
                        <?php if ($cekTolak): ?>
                            <?= Html::a('Kirim ulang permintaan ubah data', ['/site/simpan', 'id' => $nama_wilayah, 'status' => 'kirim_ulang', 'id_wilayah' => $id_wilayah, 'tahun_dasar' => $tahun_dasar, 'jenis_kelamin' => $jenis_kelamin], ['class' => 'btn btn-success']) ?>
                            <?= Html::a('Edit data ubahan terlebih dahulu', ['update', 'id' => $model_ubahan->no_ubah_data, 'nama_wilayah' => $nama_wilayah], ['class' => 'btn btn-primary']) ?>

                        <?php elseif ($cekSetuju): ?>

                        <?php elseif (!$cekSetuju || !$cekTolak): ?>
                        <?= Html::a('Edit data ubahan terlebih dahulu', ['update', 'id' => $model_ubahan->no_ubah_data, 'nama_wilayah' => $nama_wilayah], ['class' => 'btn btn-primary']) ?>

                        <?php endif; ?>
                    </center></div>
                </span> 

            <?php endif; ?>

    </div>
</div>
</div>

