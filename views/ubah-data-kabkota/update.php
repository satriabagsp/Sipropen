<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model app\models\UbahDataKabkota */

$this->title = 'Buat Permintaan Ubah Data ' . $nama_wilayah;
$this->params['breadcrumbs'][] = ['label' => 'Ubah Data Kabkotas', 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->no_ubah_data, 'url' => ['view', 'id' => $model->no_ubah_data]];
$this->params['breadcrumbs'][] = 'Update';
?>
<div class="ubah-data-kabkota-update">
<div class="panel panel-info">
	<div class="panel-heading"><h1><?= Html::encode($this->title) ?></h1></div>
		<div class="panel-body">

		    <?= $this->render('_form', [
		        'model' => $model,
		    ]) ?>

		</div>

	</div>
</div>
