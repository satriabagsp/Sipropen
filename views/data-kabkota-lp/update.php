<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model app\models\DataKabkotaLp */

$this->title = 'Ubah Data ' . $kabkota . ' ' .$tahun_dasar   ;
$this->params['breadcrumbs'][] = ['label' => 'Data Kabkota Lps', 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->no_data, 'url' => ['view', 'id' => $model->no_data]];
$this->params['breadcrumbs'][] = 'Update';
?>
<div class="data-kabkota-lp-update">
	<div class="panel panel-info">
		<div class="panel-heading"><h1><?= Html::encode($this->title) ?></h1></div>
		<div class="panel-body">

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
                                        <font color=green>Hasil proyeksi yang sudah dibuat akan dihapus jika terdapat perubahan data dasar kabupaten/kota.</font>
                                    </p></center> <?php
                                };      
                        ?>
                </div>
            </div>

		    <?= $this->render('_form', [
		        'model' => $model,
		    ]) ?>

		</div>

	</div>
</div>
