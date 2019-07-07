<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model app\models\UbahDataKabkota */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="ubah-data-kabkota-form">

    <?php $form = ActiveForm::begin(); ?>

    <?= $form->field($model, 'id_wilayah')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'tahun_data')->textInput() ?>

    <?= $form->field($model, 'jenis_kelamin')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'ku_5')->textInput() ?>

    <?= $form->field($model, 'ku_10')->textInput() ?>

    <?= $form->field($model, 'ku_15')->textInput() ?>

    <?= $form->field($model, 'ku_20')->textInput() ?>

    <?= $form->field($model, 'ku_25')->textInput() ?>

    <?= $form->field($model, 'ku_30')->textInput() ?>

    <?= $form->field($model, 'ku_35')->textInput() ?>

    <?= $form->field($model, 'ku_40')->textInput() ?>

    <?= $form->field($model, 'ku_45')->textInput() ?>

    <?= $form->field($model, 'ku_50')->textInput() ?>

    <?= $form->field($model, 'ku_55')->textInput() ?>

    <?= $form->field($model, 'ku_60')->textInput() ?>

    <?= $form->field($model, 'ku_65')->textInput() ?>

    <?= $form->field($model, 'ku_70')->textInput() ?>

    <?= $form->field($model, 'ku_75')->textInput() ?>

    <?= $form->field($model, 'ku_80')->textInput() ?>

    <div class="form-group">
        <?= Html::submitButton('Save', ['class' => 'btn btn-success']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
