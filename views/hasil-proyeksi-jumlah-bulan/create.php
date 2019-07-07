<?php

use yii\helpers\Html;


/* @var $this yii\web\View */
/* @var $model app\models\HasilProyeksiJumlahBulan */

$this->title = 'Create Hasil Proyeksi Jumlah Bulan';
$this->params['breadcrumbs'][] = ['label' => 'Hasil Proyeksi Jumlah Bulans', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="hasil-proyeksi-jumlah-bulan-create">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
