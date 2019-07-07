<?php

use yii\helpers\Html;
use yii\widgets\DetailView;

/* @var $this yii\web\View */
/* @var $model app\models\HasilProyeksi */

$this->title = $model->kab_kota;
$this->params['breadcrumbs'][] = ['label' => 'Hasil Proyeksis', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>

<div class="hasil-proyeksi-view">
    <div class="panel panel-info">
        <div class="panel-heading"><h1><?= Html::encode($this->title) ?></h1></div>
        <div class="panel-body">
            
            <?= DetailView::widget([
                'model' => $model,
                'attributes' => [
                    'provinsi',
                    'kab_kota',
                    'p2015',
                    'p2016',
                    'p2017',
                    'p2018',
                    'p2019',
                    'p2020',
                    'p2021',
                    'p2022',
                    'p2023',
                    'p2024',
                    'p2025',
                ],
            ]) ?>

            <?php if (Yii::$app->user->identity->role == 'provinsi' || Yii::$app->user->identity->role == 'pusat'): ?> 
                <span class="pull-right">
                    <?= Html::a('Sunting', ['update', 'id' => $model->kab_kota], ['class' => 'btn btn-primary']) ?>
                    <?= Html::a('Hapus', ['delete', 'id' => $model->kab_kota], [
                        'class' => 'btn btn-danger',
                        'data' => [
                            'confirm' => 'Apakah akan menghapus data demografi ' . $this->title . '?',
                            'method' => 'post',
                        ],
                    ]) ?>
                </span>

            <?php elseif (Yii::$app->user->identity->role == 'kab_kota'): ?> 
                <p>
                    <?= Html::a('Sunting', ['update', 'id' => $model->kab_kota], ['class' => 'btn btn-primary']) ?>
                </p>

            <?php endif; ?>
            
        </div>    
    </div>
</div>
