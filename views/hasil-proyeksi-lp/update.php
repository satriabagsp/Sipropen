<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model app\models\HasilProyeksiLp */

$this->title = 'Update Hasil Proyeksi Lp: ' . $model->no_proyeksi;
$this->params['breadcrumbs'][] = ['label' => 'Hasil Proyeksi Lps', 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->no_proyeksi, 'url' => ['view', 'id' => $model->no_proyeksi]];
$this->params['breadcrumbs'][] = 'Update';
?>
<div class="hasil-proyeksi-lp-update">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
