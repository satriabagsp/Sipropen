<?php
use yii\helpers\Html;
?>
<header class="main-header">

  <!-- Logo -->
  <a href="index.php" class="logo">
    <!-- mini logo for sidebar mini 50x50 pixels -->
    <span class="logo-lg"><b>SIPROPEN-KABKOT</b></span>
    <!-- logo for regular state and mobile devices -->
    <span class="logo-mini"><b>SPP</b></span>
  </a>
        
  <!-- Header Navbar: style can be found in header.less -->
  <nav class="navbar navbar-static-top" role="navigation">
    <!-- Sidebar Menu-->   
    <div class="navbar-custom-menu">
      <ul class="nav navbar-nav">
        <!-- User Account: style can be found in dropdown.less -->
        <li class="dropdown user user-menu">
          <a href="#" class="dropdown-toggle" data-toggle="dropdown">
					  <?= Html::img('@web/img/boxed-bg.png', ['class' => 'user-image', 'alt'=>'User Image']) ?>
            <span class="hidden-xs"><?= Html::encode(Yii::$app->user->identity->username)?></span>
          </a>
            <ul class="dropdown-menu">
              <!-- User image -->
              <li class="user-header">
                <?= Html::img('@web/img/boxed-bg.png', ['class' => 'img-circle', 'alt'=>'User Image']) ?>
                <p>
                  <?= Html::encode(Yii::$app->user->identity->username)?>
                </p>
              </li>
              <!-- Menu Body -->
              <li class="user-body">
                <div class="col-xs-12 text-center">
                  <?= Html::beginForm(['/site/logout'], 'post'); ?>
                  <?= Html::submitButton(
                    'SIGNOUT',
                    ['class'=>'btn btn-default']
                  ); ?>  
                  <?= Html::endForm(); ?>
                </div>
              </li>
            </ul>
        </li>
      </ul>
    </div>
  </nav>
</header>
