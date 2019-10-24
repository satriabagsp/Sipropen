<?php

/* @var $this yii\web\View */
/* @var $form yii\bootstrap\ActiveForm */
/* @var $model app\models\ContactForm */

use yii\helpers\Html;
use app\models\DataKabKota;
use app\models\DataKabkota2;
use app\models\DataKabkotaLp;
use app\models\DataProvinsiLp;
use app\models\HasilProyeksi;
use yii\data\ActiveDataProvider;
use yii\grid\GridView;
use yii\widgets\ActiveForm;
use yii\helpers\ArrayHelper;

$this->title = 'Proyeksi Penduduk Kabupaten/kota (' . Yii::$app->user->identity->username . ')' ;
$this->params['breadcrumbs'][] = $this->title;

?>

<div class="site-proykabkota">
     <div class="panel panel-info"> 
        
        <!--Membedakan pusat, provinsi dan kab/kota.-->
        <?php if (Yii::$app->user->identity->role == 'provinsi'): ?>
            
            <div class="panel-heading"><h1>Proyeksi Penduduk Kabupaten/kota</h1></div>
            <div class="col-md-12">
                <div class="jumbotron" style="background: #f0f0f0; margin-top: 20px; padding-top: 10px; padding-bottom: 10px">
                    <!-- Cek apakah sudah melakukan proyeksi atau belum -->
                        <?php
                            include "koneksi.php";
                            $id_provinsi = substr(Yii::$app->user->identity->wilayah_id, 0, 2);
                            $sql_CekHasil = "SELECT *
                                             FROM hasil_proyeksi_jumlah, master_wilayah
                                             WHERE hasil_proyeksi_jumlah.id_wilayah = master_wilayah.id_wilayah
                                             AND SUBSTRING(master_wilayah.id_wilayah,1,2) = '" . $id_provinsi . "'";
                            $query_CekHasil = mysqli_query($host,$sql_CekHasil) or die(mysqli_error());
                                    
                            //Memasukan data dari database dalam bentuk ARRAY
                                $cekHasil = 0;
                                while($row = mysqli_fetch_array($query_CekHasil))
                                    { 
                                        $cekHasil++;
                                    };

                                if ($cekHasil){
                                    ?>
                                    <center><p>
                                        <font color=green><b>Data sudah berhasil diproyeksikan.</b></font> <br><br>Silakan lihat hasil proyeksi <?= Html::a('di sini', ['/hasil-proyeksi-jumlah/index']) ?> <br>atau <br>Buat proyeksi baru dengan <font color=red> menghapus proyeksi lama </font><?= Html::a('di sini.', ['proyeksikan2', 'status' => 'hapus', 'tahun_dasar' => '', 'tahun_target' => '', 'panjang_tahun' => '']) ?>
                                    </p></center> <?php
                                } else{
                                    ?>
                                    <center> <p>
                                        Data belum diproyeksikan, silakan buat proyeksi.
                                    </p></center> <?php
                                };
                                    //Simpan hasil proyeksi ke database
                                    //Buat nama2 kolom di tabel hasil proyeksi jadi 1 array        
                        ?>

                </div>
            </div>
            
            <div class="panel-body" style="overflow-x:auto;">

                <?php if ($cek == 'pilih_tahun'): ?>

                    <?php if (!$cekHasil): ?>
                        <?php $form = ActiveForm::begin(); ?>

                        <?= $form->field($model_td, 'tahun_dasar')->dropDownList(
                                ArrayHelper::map(DataKabkotaLp::find()->groupBy('tahun_dasar')->all(), 'tahun_dasar', 'tahun_dasar'),['prompt'=>'Pilih tahun dasar..']
                            )
                        ?>

                        <?= $form->field($model_td, 'tahun_target')->dropDownList(
                                ArrayHelper::map(DataKabkotaLp::find()->groupBy('tahun_dasar')->all(), 'tahun_dasar', 'tahun_dasar'),['prompt'=>'Pilih tahun target..']
                            )
                        ?>

                        <?= $form->field($model_td, 'panjang_tahun')->dropDownList(
                                ArrayHelper::map(DataProvinsiLp::find()->groupBy('tahun_data')->all(), 'tahun_data', 'tahun_data'),['prompt'=>'Pilih panjang proyeksi..']
                            )
                        ?>

                        <div class="form-group">
                            <?= Html::submitButton('Cek Validitas', ['class' => 'btn btn-success']) ?>
                        </div>

                        <?php ActiveForm::end(); ?>
                    <?php endif; ?>

                <?php elseif ($cek == 'tampil_tahun'): ?>

                    <p><b>Berikut adalah informasi pertumbuhan penduduk <?= Html::encode(Yii::$app->user->identity->username)?>:</b></p>
                        <?= GridView::widget([
                            'dataProvider' => $dataProvider1,
                            'options' => ['style' => 'font-size:12px;'],
                            'showFooter' => true,
                            'columns' => [
                                ['class' => 'yii\grid\SerialColumn'],
                            
                                [
                                    'label' => 'Kabupaten/kota',
                                    'attribute' => '1',
                                    'footer' => Yii::$app->user->identity->username,
                                ],
                                [
                                    'label' => 'Jumlah Penduduk '.$tahun_dasar,
                                    'attribute' => '2',
                                    'format'=> ['integer'],
                                    'footer' => number_format($total_tahun_dasar),              
                                ],
                                [
                                    'label' => 'Jumlah Penduduk '.$tahun_target,
                                    'attribute' => '3',
                                    'format'=> ['integer'],
                                    'footer' => number_format($total_tahun_target),
                                ],
                                [
                                    'label' => 'LPP Tahunan',
                                    'attribute' => '4',
                                ],
                                [
                                    'label' => 'LPP Bulanan',
                                    'attribute' => '5',
                                ],
                                [
                                    'label' => 'Sex Ratio',
                                    'attribute' => '6',
                                ],
                            ],
                        ]); ?>

                        <div style="background: #f0f0f0; margin-top: 20px; padding-top: 10px; padding-bottom: 10px">
                            <?php //UJI VALIDASI
                                //Validasi data.
                                //Mencari lpp_tahunan (laju pertumbuhan) yang tidak valid.
                                    $kesalahan = 0;
                                    for($i=0;$i<count($kabkota);$i++){
                                        if ($lpp_tahunan[$i]<=0 || $lpp_tahunan[$i]>=0.03 || $lpp_tahunan[$i]>=0.07) {
                                            $kesalahan++;
                                        };
                                    };

                                //Membuat daftar kesalahan.
                                if ($kesalahan){
                                    echo '<b>&nbsp;Peringatan! Terdapat ' . $kesalahan . ' kesalahan, mohon diperiksa kembali: </b><br>';
                                    $kesalahan_0persen = [];
                                    $kesalahan_3persen = [];
                                    $kesalahan_7persen = [];
                                    for($i=0;$i<count($kabkota);$i++){
                                        if ($lpp_tahunan[$i]<=0) {
                                            $kesalahan_0persen[] = $kabkota[$i];
                                        } else if ($lpp_tahunan[$i]>=0.03 && $lpp_tahunan[$i]<0.07) {
                                            $kesalahan_3persen[] = $kabkota[$i];
                                        } else if ($lpp_tahunan[$i]>=0.07) {
                                            $kesalahan_7persen[] = $kabkota[$i];
                                        };
                                    };
                                    if($kesalahan_0persen){
                                        echo '<font color=red><b><br>&nbsp;Kabupaten/kota dengan LPP bernilai 0: <br>';
                                        for($i=0;$i<count($kesalahan_0persen);$i++){
                                            echo '&nbsp;&nbsp;&nbsp;- ' . $kesalahan_0persen[$i] . '<br>';
                                        };
                                        echo '</b></font>';
                                    }
                                    elseif($kesalahan_3persen){
                                        echo '<font color=red><b><br>&nbsp;Kabupaten/kota dengan LPP lebih dari 3%: <br>';
                                        for($i=0;$i<count($kesalahan_3persen);$i++){
                                            echo '&nbsp;&nbsp;&nbsp;- ' . $kesalahan_3persen[$i] . '<br>';
                                        };
                                        echo '</b></font>';
                                    }
                                    elseif($kesalahan_7persen){
                                        echo '<font color=red><b><br>&nbsp;Kabupaten/kota dengan LPP lebih dari 7%: <br>';
                                        for($i=0;$i<count($kesalahan_7persen);$i++){
                                            echo '&nbsp;&nbsp;&nbsp;- ' . $kesalahan_7persen[$i] . '<br>';
                                        };
                                        echo '</b></font>';
                                    };
                                    echo '<br><font color=blue><b> &nbsp;Jika sudah tidak terdapat kesalahan, silahkan lanjutkan proses penghitungan proyeksi. </b></font><br>';
                                } else{
                                    echo 'Data telah valid, silakan lanjut ke proses buat proyeksi.';
                                };

                            ?>  
                        </div> 
  
                        </br>
                             
                        <!--div tombol buat proyeksi-->
                        <span>
                            <!-- Jika terdapat kesalahan maka tombol buat proyeksi tidak dapat diakses -->
                            <div class="col-xs-12 col-sm-12 left-padding"><center>
                                <?php if ($kesalahan): ?>
                                    <?= Html::a('Buat Proyeksi Penduduk', ['proyeksikan2', 'status' => 'buat_baru', 'tahun_dasar' => $tahun_dasar, 'tahun_target' => $tahun_target, 'panjang_tahun' => $panjang_tahun], ['class'=>'btn btn-success', 'disabled' => 'disabled']) ?>
                                <?php elseif (!$kesalahan): ?> 
                                    <?= Html::a('Buat Proyeksi Penduduk', ['proyeksikan2', 'status' => 'buat_baru', 'tahun_dasar' => $tahun_dasar, 'tahun_target' => $tahun_target, 'panjang_tahun' => $panjang_tahun], ['class'=>'btn btn-success']) ?>
                                <?php endif; ?>    
                            </center></div>
                        </span>    
                
                <?php endif; ?>
            </div>   
                                           
                        


        <?php elseif (Yii::$app->user->identity->role == 'pusat'): ?> 
            <div class="panel-heading"><h1>Proyeksi Penduduk Kabupaten/kota di Indonesia</h1></div>
            <div class="panel-body">
                <div class="col-xs-12 col-sm-12 center-padding" style="background: #f0f0f0; margin-top: 20px; padding-top: 10px; padding-bottom: 10px">
                    <center>
                    <H4><B> - FITUR HITUNG PROYEKSI PENDUDUK KABUPATEN/KOTA HANYA BISA DILAKUKAN PADA TINGKAT PROVINSI - </B></H4>
                    <BR>
                    <?= Html::a('Lihat hasil proyeksi penduduk yang sudah dibuat' , ['/hasil-proyeksi/index'], ['class'=>'btn btn-success']) ?>
                    </center>
                </div>
            </div>

        <?php elseif (Yii::$app->user->identity->role == 'kabkota'): ?>
            <div class="panel-heading"><h1><?= Html::encode($this->title) ?></h1></div>
            <div class="panel-body">
                <div class="col-xs-12 col-sm-12 center-padding" style="background: #f0f0f0; margin-top: 20px; padding-top: 10px; padding-bottom: 10px">
                    <center>
                    <H4><B> - FITUR HITUNG PROYEKSI PENDUDUK KABUPATEN/KOTA HANYA BISA DILAKUKAN PADA TINGKAT PROVINSI - </B></H4>
                    <BR>
                    <?= Html::a('Lihat Hasil Proyeksi Penduduk '. Html::encode(Yii::$app->user->identity->username) , ['/hasil-proyeksi-jumlah/index'], ['class'=>'btn btn-success']) ?>
                    </center>
                </div>            
            </div>
                
        <?php endif; ?>

    </div>
</div>
