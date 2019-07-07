<?php

use yii\helpers\Html;
use yii\grid\GridView;
use fedemotta\datatables\DataTables;

/* @var $this yii\web\View */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'Kelola Data Pengguna';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="masuk-index">
    <div class="panel panel-info">

        <div class="panel-heading"><h1><?= Html::encode($this->title) ?></h1></div>
        <div class="panel-body">

        <?php if (Yii::$app->user->identity->role == 'admin'): ?>
        <p>
            <?= Html::a('Tambah Akun Baru', ['create'], ['class' => 'btn btn-success']) ?>
            <?= Html::a('Import Excel', ['import'], ['class' => 'btn btn-warning']) ?>
        </p>
        <?php endif; ?>

        <?= DataTables::widget([
            'dataProvider' => $dataProvider,
            'columns' => [
                ['class' => 'yii\grid\SerialColumn'],

                'username',
                'password',
                'role',
                'wilayah_id',
                'email',

                ['class' => 'yii\grid\ActionColumn'],
            ],
        ]); ?>  


        </div>
    </div>
</div>
