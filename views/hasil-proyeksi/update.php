<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model app\models\HasilProyeksi */

$this->title = 'Update Hasil Proyeksi: ' . $model->kab_kota;
$this->params['breadcrumbs'][] = ['label' => 'Hasil Proyeksis', 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->kab_kota, 'url' => ['view', 'id' => $model->kab_kota]];
$this->params['breadcrumbs'][] = 'Update';
?>
<div class="hasil-proyeksi-update">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
