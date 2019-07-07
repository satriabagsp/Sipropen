<?php

use yii\helpers\Html;
use yii\grid\GridView;

/* @var $this yii\web\View */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'Ubah Data Kabkotas';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="ubah-data-kabkota-index">

    <h1><?= Html::encode($this->title) ?></h1>

    <p>
        <?= Html::a('Create Ubah Data Kabkota', ['create'], ['class' => 'btn btn-success']) ?>
    </p>

    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'columns' => [
            ['class' => 'yii\grid\SerialColumn'],

            'no_ubah_data',
            'id_wilayah',
            'tahun_data',
            'jenis_kelamin',
            'ku_5',
            //'ku_10',
            //'ku_15',
            //'ku_20',
            //'ku_25',
            //'ku_30',
            //'ku_35',
            //'ku_40',
            //'ku_45',
            //'ku_50',
            //'ku_55',
            //'ku_60',
            //'ku_65',
            //'ku_70',
            //'ku_75',
            //'ku_80',

            ['class' => 'yii\grid\ActionColumn'],
        ],
    ]); ?>
</div>
