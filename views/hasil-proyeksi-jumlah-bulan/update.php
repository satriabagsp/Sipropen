<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model app\models\HasilProyeksiJumlahBulan */

$this->title = 'Update Hasil Proyeksi Jumlah Bulan: ' . $model->no_proyeksi_jumlah;
$this->params['breadcrumbs'][] = ['label' => 'Hasil Proyeksi Jumlah Bulans', 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->no_proyeksi_jumlah, 'url' => ['view', 'id' => $model->no_proyeksi_jumlah]];
$this->params['breadcrumbs'][] = 'Update';
?>
<div class="hasil-proyeksi-jumlah-bulan-update">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
