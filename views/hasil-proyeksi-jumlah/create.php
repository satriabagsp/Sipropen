<?php

use yii\helpers\Html;


/* @var $this yii\web\View */
/* @var $model app\models\HasilProyeksiJumlah */

$this->title = 'Create Hasil Proyeksi Jumlah';
$this->params['breadcrumbs'][] = ['label' => 'Hasil Proyeksi Jumlahs', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="hasil-proyeksi-jumlah-create">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
