<?php

use yii\helpers\Html;


/* @var $this yii\web\View */
/* @var $model app\models\HasilProyeksi */

$this->title = 'Create Hasil Proyeksi';
$this->params['breadcrumbs'][] = ['label' => 'Hasil Proyeksis', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="hasil-proyeksi-create">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
