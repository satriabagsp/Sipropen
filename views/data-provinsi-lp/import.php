<?php 
	use yii\widgets\ActiveForm;
	use yii\helpers\Html;
?>

<div class="data-kabkota-lp-import">
	<div class="panel panel-info">
		<div class="panel-heading"><h1>Import Data Hasil Proyeksi Provinsi</h1></div>
		<div class="panel-body">
			<?php $form = ActiveForm::begin(['options'=>['enctype'=>'multipart/form-data']]);?>

			<?= $form->field($modelImport,'fileImport')->fileInput() ?>

			<?= Html::submitButton('Import',['class'=>'btn btn-primary']);?>

			<?php ActiveForm::end();?>

		</div>

	</div>
</div>