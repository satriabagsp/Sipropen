<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model app\models\DataKabkotaLp */

$this->title = 'Tambah Tahun Proyeksi ' . Yii::$app->user->identity->username;
$this->params['breadcrumbs'][] = ['label' => 'Data Kabkota Lps', 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->id_nomor, 'url' => ['view', 'id' => $model->id_nomor]];
$this->params['breadcrumbs'][] = 'Update';
?>
<div class="data-provinsi-lp-create">
	<div class="panel panel-info">
		<div class="panel-heading"><h1><?= Html::encode($this->title) ?></h1></div>
		<div class="panel-body">

		    <?= $this->render('_form', [
		        'model' => $model,
		    ]) ?>

		</div>

	</div>
</div>
