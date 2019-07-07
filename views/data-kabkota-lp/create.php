<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model app\models\DataKabkotaLp */

$this->title = 'Tambah Kabupaten/kota ' . Yii::$app->user->identity->username;
$this->params['breadcrumbs'][] = ['label' => 'Data Kabkota Lps', 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->no_data, 'url' => ['view', 'id' => $model->no_data]];
$this->params['breadcrumbs'][] = 'Update';
?>
<div class="data-kabkota-lp-create">
	<div class="panel panel-info">
		<div class="panel-heading"><h1><?= Html::encode($this->title) ?></h1></div>
		<div class="panel-body">

		    <?= $this->render('_form', [
		        'model' => $model,
		    ]) ?>

		</div>

	</div>
</div>
