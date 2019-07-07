<?php

/* @var $this yii\web\View */
/* @var $form yii\bootstrap\ActiveForm */
/* @var $model app\models\ContactForm */

use yii\helpers\Html;
use yii\bootstrap\ActiveForm;
use yii\captcha\Captcha;

$this->title = 'Proyeksi Penduduk ' . Yii::$app->user->identity->username . '' ;
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="site-contact">
        
    <!--Membedakan pusat, provinsi dan kab/kota.-->
    <?php if (Yii::$app->user->identity->role == 'provinsi'): ?>
        <h1><?= Html::encode($this->title) ?></h1>
        <p>
            <B> - MAAF FITUR PROYEKSI PENDUDUK BELUM TERSEDIA - </B>
        </p>

    <?php elseif (Yii::$app->user->identity->role == 'pusat'): ?> 
        <h1>Proyeksi Penduduk Kabupaten/kota di Indonesia</h1>
        <p>
            <B> - MAAF FITUR PROYEKSI PENDUDUK BELUM TERSEDIA - </B>
        </p>   

    <?php else: ?>

        <h1><?= Html::encode($this->title) ?></h1>
        <p>
           <B> - FITUR PROYEKSI PENDUDUK HANYA BISA DILAKUKAN PADA TINGKAT PROVINSI - </B>
        </p>
        
    <?php endif; ?>
</div>
