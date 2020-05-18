<?php
use yii\helpers\Html;
use yii\helpers\Url;

/* @var $this yii\web\View */
/* @var $username string */
/* @var $token string */

$verifyLink = Url::toRoute(['site/verify-email', 'token' => $token], true);
?>
<div class="verify-email">
	<p>Hello <?= Html::encode($username) ?>,</p>

	<p>Follow the link below to verify your email:</p>

	<p><?= Html::a(Html::encode($verifyLink), $verifyLink) ?></p>
</div>
