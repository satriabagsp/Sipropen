<?php

use yii\helpers\Html;
use yii\widgets\DetailView;

/* @var $this yii\web\View */
/* @var $model app\models\HasilProyeksiJumlahBulan */

$this->title = $model->no_proyeksi_jumlah;
$this->params['breadcrumbs'][] = ['label' => 'Hasil Proyeksi Jumlah Bulans', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="hasil-proyeksi-jumlah-bulan-view">

    <h1><?= Html::encode($this->title) ?></h1>

    <p>
        <?= Html::a('Update', ['update', 'id' => $model->no_proyeksi_jumlah], ['class' => 'btn btn-primary']) ?>
        <?= Html::a('Delete', ['delete', 'id' => $model->no_proyeksi_jumlah], [
            'class' => 'btn btn-danger',
            'data' => [
                'confirm' => 'Are you sure you want to delete this item?',
                'method' => 'post',
            ],
        ]) ?>
    </p>

    <?= DetailView::widget([
        'model' => $model,
        'attributes' => [
            'no_proyeksi_jumlah',
            'id_wilayah',
            'tahun_proyeksi',
            'jumlah_proyeksi',
        ],
    ]) ?>

</div>
