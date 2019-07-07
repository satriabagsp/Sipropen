<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model app\models\HasilProyeksiLp */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="hasil-proyeksi-lp-form">

    <?php $form = ActiveForm::begin(); ?>

    <?= $form->field($model, 'id_kabkota')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'tahun_proyeksi')->textInput() ?>

    <?= $form->field($model, 'jenis_kelamin')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'kup_5')->textInput() ?>

    <?= $form->field($model, 'kup_10')->textInput() ?>

    <?= $form->field($model, 'kup_15')->textInput() ?>

    <?= $form->field($model, 'kup_20')->textInput() ?>

    <?= $form->field($model, 'kup_25')->textInput() ?>

    <?= $form->field($model, 'kup_30')->textInput() ?>

    <?= $form->field($model, 'kup_35')->textInput() ?>

    <?= $form->field($model, 'kup_40')->textInput() ?>

    <?= $form->field($model, 'kup_45')->textInput() ?>

    <?= $form->field($model, 'kup_50')->textInput() ?>

    <?= $form->field($model, 'kup_55')->textInput() ?>

    <?= $form->field($model, 'kup_60')->textInput() ?>

    <?= $form->field($model, 'kup_65')->textInput() ?>

    <?= $form->field($model, 'kup_70')->textInput() ?>

    <?= $form->field($model, 'kup_75')->textInput() ?>

    <?= $form->field($model, 'kup_80')->textInput() ?>

    <div class="form-group">
        <?= Html::submitButton('Save', ['class' => 'btn btn-success']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
