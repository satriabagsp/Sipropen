<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model app\models\Masuk */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="masuk-form">

	<?php $data[]="provinsi"; $data[]="kabkota"; $data[]="pusat"; $data[]="admin" ?>

    <?php $form = ActiveForm::begin(); ?>

    <?= $form->field($model, 'username')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'password')->passwordInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'role')->dropDownList(
    		$data, ['prompt' => 'Pilih role..']
    	) 
    ?>

    <?= $form->field($model, 'wilayah_id')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'email')->textInput(['maxlength' => true]) ?>

    <div class="form-group">
        <?= Html::submitButton('Simpan', ['class' => 'btn btn-success']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
