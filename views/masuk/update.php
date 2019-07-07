<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model app\models\Masuk */

$this->title = 'Ubah akun: ' . $model->username;
$this->params['breadcrumbs'][] = ['label' => 'Masuks', 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->id, 'url' => ['view', 'id' => $model->id]];
$this->params['breadcrumbs'][] = 'Update';
?>
<div class="masuk-update">
	<div class="panel panel-info">

        <div class="panel-heading"><h1><?= Html::encode($this->title) ?></h1></div>
        <div class="panel-body">

		    <?= $this->render('_form', [
		        'model' => $model,
		    ]) ?>

        </div>
    </div>
</div>
