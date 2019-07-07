<?php

use yii\helpers\Html;
use app\models\DataKabkotaLP;
use yii\widgets\DetailView;
use yii\widgets\ActiveForm;
use app\models\Laporan;

/* @var $this yii\web\View */
/* @var $model app\models\UbahDataKabkota */

$this->title = 'Permintaan Ubah Data '.$kabkota.' '.$tahun_dasar;
$this->params['breadcrumbs'][] = ['label' => 'Ubah Data Kabkotas', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;

//Cari modl dengan no_data yang akan diganti
	$model_tampil = DataKabkotaLp::findOne($id_data);


?>

<script>
function bukaUbahData() {
    var tombol_ubah = document.getElementById('tombol_ubah');
    var isian = document.getElementById('ubah_data');
    if (isian.style.display === 'none') {
        isian.style.display = 'block';
        tombol_ubah.style.display = 'none';
    } else {
        isian.style.display = 'none';
        tombol_ubah.style.display = 'block';
    }
}
</script>

<div class="ubah-data-kabkota-create">
	<div class="panel panel-info">
	<div class="panel-heading"><h1><?= Html::encode($this->title) ?></h1></div>
		<div class="panel-body">

			<div class="col-md-6">

				<p><b>Berikut adalah data jumlah penduduk <?= Html::encode($kabkota)?> <?= Html::encode($tahun_dasar)?>:</b></p>

				<?= DetailView::widget([
	                'model' => $model_tampil,
	                'attributes' => [
	                	'id_wilayah', 
	                	[
	                		'label' => 'Nama Kabupaten/kota',
	                		'value' => $kabkota,
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
	        	<!-- Periksa apakah kabupaten/kota sudah melakukan permintaan perubahan data untuk tahun dan jk terkait? -->
                <?php
                    //Mulai Periksa
                        //Buat koneksi ke DB
                            include "koneksi.php";
                            $sql_CekHasil = "SELECT *
                                             FROM ubah_data_kabkota
                                             WHERE id_wilayah = '" . $id_kabkota . "'
                                             AND jenis_kelamin = '" . $jenis_kelamin . "'
                                             AND tahun_data = '" . $tahun_dasar . "'";
                            $query_CekHasil = mysqli_query($host,$sql_CekHasil) or die(mysqli_error());
                                        
                        //Memasukan data dari database dalam bentuk ARRAY
                            $cekHasil = 0;
                            while($row = mysqli_fetch_array($query_CekHasil))
                            { 
                                $cekHasil++;
                            }; 


                    //Jika belum melakukan permintaan perubahan data, maka munculkan form ubah data.
                        if (!$cekHasil){ ?>
                            <div class="col-md-12">
                                <div class="jumbotron" style="background: #f0f0f0; margin-top: 20px; padding-top: 10px; padding-bottom: 10px">
                                    <center><p>
                                        Silakan buat permintaan perubahan data jika terdapat kesalahan pada data jumlah penduduk <?= Html::encode(Yii::$app->user->identity->username)?> <i>(<?= Html::encode($jenis_kelamin)?>)</i> tahun <?= Html::encode($tahun_dasar)?>.
                                    </p></center>
                                </div>
                            </div>

                            <div id="ubah_data" style="display:none;">
                                </br></br></br>
                                <p><b>Isikan data ubahan pada form di bawah ini:</b></p>
                                <br>
                                <div class="ubah-data-form">
                                   	<?php $form = ActiveForm::begin(); ?>

								    <?= $form->field($model1, 'id_wilayah')->textInput(['maxlength' => true, 'readonly' => true, 'value' => $id_kabkota]) ?>

								    <?= $form->field($model1, 'tahun_data')->textInput(['readonly' => true, 'value' => $tahun_dasar]) ?>

								    <?= $form->field($model1, 'jenis_kelamin')->textInput(['maxlength' => true, 'readonly' => true, 'value' => $jenis_kelamin]) ?>

								    <?= $form->field($model1, 'ku_5')->textInput() ?>

								    <?= $form->field($model1, 'ku_10')->textInput() ?>

								    <?= $form->field($model1, 'ku_15')->textInput() ?>

								    <?= $form->field($model1, 'ku_20')->textInput() ?>

								    <?= $form->field($model1, 'ku_25')->textInput() ?>

								    <?= $form->field($model1, 'ku_30')->textInput() ?>

								    <?= $form->field($model1, 'ku_35')->textInput() ?>

								    <?= $form->field($model1, 'ku_40')->textInput() ?>

								    <?= $form->field($model1, 'ku_45')->textInput() ?>

								    <?= $form->field($model1, 'ku_50')->textInput() ?>

								    <?= $form->field($model1, 'ku_55')->textInput() ?>

								    <?= $form->field($model1, 'ku_60')->textInput() ?>

								    <?= $form->field($model1, 'ku_65')->textInput() ?>

								    <?= $form->field($model1, 'ku_70')->textInput() ?>

								    <?= $form->field($model1, 'ku_75')->textInput() ?>

								    <?= $form->field($model1, 'ku_80')->textInput() ?>

								    <?= $form->field($model2, 'tanggal')->hiddenInput(['maxlength' => true, 'value' => 'kosong'])->label(false) ?>

								    <?= $form->field($model2, 'waktu')->hiddenInput(['maxlength' => true, 'value' => 'kosong'])->label(false) ?>

								    <?= $form->field($model2, 'asal')->hiddenInput(['maxlength' => true, 'value' => 'kosong'])->label(false) ?>

								    <?= $form->field($model2, 'tujuan')->hiddenInput(['maxlength' => true, 'value' => 'kosong'])->label(false) ?>

								    <?= $form->field($model2, 'perihal')->hiddenInput(['maxlength' => true, 'value' => 'kosong'])->label(false) ?>

								    <?= $form->field($model2, 'status')->hiddenInput(['maxlength' => true, 'value' => 'kosong'])->label(false) ?>

								    <?= $form->field($model2, 'deskripsi')->textInput(['maxlength' => true, 'readonly' => true, 'value' => $jenis_kelamin.'-'.$tahun_dasar]) ?>

								    <div class="form-group">
								        <?= Html::submitButton('Save', ['class' => 'btn btn-success']) ?>
								        <?= Html::a(Yii::t('app', 'Batal'), '#.', ['class' => 'btn btn-danger', 'onclick' => "bukaUbahData()"]) ?>
								    </div>

								    <?php ActiveForm::end(); ?>

                                </div>
                            </div>

                            <!--div tombol untuk munculkan form ubah data -->
                                <div id="tombol_ubah" class="col-xs-12 col-sm-12 center-padding">
                                    <center>
                                        <?= Html::a(Yii::t('app', 'Buat permintaan ubah data'), '#.', ['class' => 'btn btn-success', 'onclick' => "bukaUbahData()"]) ?>
                                    </center>
                                </div>
                        <?php
                                                
                        } else{ //jika sudah ada maka diberi pilihan untuk membatalkan permintaan ubah data ?>
                            <div class="col-md-12">
                                <div class="jumbotron" style="background: #f0f0f0; margin-top: 20px; padding-top: 10px; padding-bottom: 10px">
                                    <center><p>
                                        <font color=green><b><?= Html::encode(Yii::$app->user->identity->username)?> sudah mengirim permintaan perubahan data penduduk <i>(<?= Html::encode($jenis_kelamin)?>)</i> tahun <?= Html::encode($tahun_dasar)?>, lihat data ubahan <i><?= Html::a('di sini', ['ubah-data-kabkota/view', 'nama_wilayah' => Yii::$app->user->identity->username, 'desk' => $jenis_kelamin.'-'.$tahun_dasar]) ?></i>.</b></font>

                                    	</br></br>

                                    	 Silakan hubungi BPS Provinsi terkait untuk memproses permintaan tersebut, atau hapus permintaan ubah data untuk membatalkan permintaan atau untuk mengajukan perubahan data ulang. <i>(permintaan perubahan data sebelumnya akan dihapus)</i>
                                    </p></center>
                                </div>
                            </div>

                            <!--div tombol untuk munculkan form ubah data -->
                                <div id="tombol" class="col-xs-12 col-sm-12 center-padding">
                                    	<center>
                                        <?= Html::a('Hapus atau Batalkan Permintaan Ubah Data', ['/site/simpan', 'id' => '', 'id_wilayah' => $id_kabkota, 'jenis_kelamin' => $jenis_kelamin, 'tahun_dasar' => $tahun_dasar, 'status' => 'hapus_laporan'], ['class' => 'btn btn-danger']) ?>
                                        </center>  
                                </div>
                        <?php
                        };
                ?>
			    

			</div>
	</div>
	</div>
</div>
