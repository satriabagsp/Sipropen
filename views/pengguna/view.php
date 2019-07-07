<?php

use yii\helpers\Html;
use yii\widgets\DetailView;

/* @var $this yii\web\View */
/* @var $model app\models\Masuk */

$this->title = 'Akun - '.$model->username;
$this->params['breadcrumbs'][] = ['label' => 'Masuks', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="masuk-view">
    <div class="panel panel-info">

        <div class="panel-heading"><h1><?= Html::encode($this->title) ?></h1></div>
        <div class="panel-body">

            <p>
                <?= Html::a('Ubah', ['update', 'id' => $model->id], ['class' => 'btn btn-primary']) ?>
                <?= Html::a('Hapus', ['delete', 'id' => $model->id], [
                    'class' => 'btn btn-danger',
                    'data' => [
                        'confirm' => 'Are you sure you want to delete this item?',
                        'method' => 'post',
                    ],
                ]) ?>
            </p>

            <?= DetailView::widget([
                'model' => $model,
                'attributes' => [
                    'id',
                    'username',
                    'password',
                    'role',
                    'wilayah_id',
                    'email',
                ],
            ]) ?>


        </div>
    </div>
</div>
