<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model app\models\HasilProyeksiJumlah */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="hasil-proyeksi-jumlah-form">

    <?php $form = ActiveForm::begin(); ?>

    <?= $form->field($model, 'id_kabkota')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'tahun_proyeksi')->textInput() ?>

    <?= $form->field($model, 'jumlah_proyeksi')->textInput() ?>

    <div class="form-group">
        <?= Html::submitButton('Save', ['class' => 'btn btn-success']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
