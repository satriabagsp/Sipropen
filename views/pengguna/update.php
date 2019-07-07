<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model app\models\Masuk */

$this->title = 'Ubah akun: ' . $model->username;
$this->params['breadcrumbs'][] = ['label' => 'Masuks', 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->id, 'url' => ['view', 'id' => $model->id]];
$this->params['breadcrumbs'][] = 'Update';
?>
<div class="masuk-update">
	<div class="panel panel-info">

        <div class="panel-heading"><h1><?= Html::encode($this->title) ?></h1></div>
        <div class="panel-body">

        	<?php if (Yii::$app->user->identity->role == 'admin'): ?>

			    <?= $this->render('_form', [
			        'model' => $model,
			    ]) ?>

		    <?php elseif (Yii::$app->user->identity->role == 'provinsi' || Yii::$app->user->identity->role == 'kabkota'  || Yii::$app->user->identity->role == 'pusat' ): ?>

			    <?php $form = ActiveForm::begin(); ?>

			    <?php $data[]="provinsi"; $data[]="kabkota"; $data[]="pusat"; $data[]="admin" ?>

			    <?= $form->field($model, 'password')->passwordInput(['maxlength' => true]) ?>

			    <?= $form->field($model, 'email')->textInput(['maxlength' => true]) ?>

			    <div class="form-group">
			        <?= Html::submitButton('Save', ['class' => 'btn btn-success']) ?>
			    </div>

			    <?php ActiveForm::end(); ?>

		    <?php endif; ?>

        </div>
    </div>
</div>
