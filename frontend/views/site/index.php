<?php

/* @var $this yii\web\View */
use yii\bootstrap\Html;
$this->title = 'My Yii Application';
?>
<div class="site-index">

    <div class="jumbotron">

        <?php if (!Yii::$app->user->isGuest) {?>

        <h1>    <?= 'Hello&nbsp;', Yii::$app->user->identity->username ?></h1>

            <?= Html::beginForm(['/site/logout'], 'post').
            Html::submitButton(
            'Exit',
            ['class' => 'btn btn-lg btn-success']
            )
            . Html::endForm()
           ?>

        <?php }?>
    </div>

</div>
