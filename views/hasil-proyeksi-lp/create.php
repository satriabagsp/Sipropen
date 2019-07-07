<?php

use yii\helpers\Html;


/* @var $this yii\web\View */
/* @var $model app\models\HasilProyeksiLp */

$this->title = 'Create Hasil Proyeksi Lp';
$this->params['breadcrumbs'][] = ['label' => 'Hasil Proyeksi Lps', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="hasil-proyeksi-lp-create">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
