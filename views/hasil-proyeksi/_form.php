<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model app\models\HasilProyeksi */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="hasil-proyeksi-form">

    <?php $form = ActiveForm::begin(); ?>

    <?= $form->field($model, 'provinsi')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'kab_kota')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'p2015')->textInput() ?>

    <?= $form->field($model, 'p2016')->textInput() ?>

    <?= $form->field($model, 'p2017')->textInput() ?>

    <?= $form->field($model, 'p2018')->textInput() ?>

    <?= $form->field($model, 'p2019')->textInput() ?>

    <?= $form->field($model, 'p2020')->textInput() ?>

    <?= $form->field($model, 'p2021')->textInput() ?>

    <?= $form->field($model, 'p2022')->textInput() ?>

    <?= $form->field($model, 'p2023')->textInput() ?>

    <?= $form->field($model, 'p2024')->textInput() ?>

    <?= $form->field($model, 'p2025')->textInput() ?>

    <div class="form-group">
        <?= Html::submitButton('Save', ['class' => 'btn btn-success']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
