<?php
use yii\helpers\Html;
use yii\helpers\Url;

/* @var $this yii\web\View */
/* @var $username string */
/* @var $token string */

$resetLink = Url::toRoute(['site/reset-password', 'token' => $token], true);
?>
<div class="password-reset">
	<p>Hello <?= Html::encode($username) ?>,</p>

	<p>Follow the link below to reset your password:</p>

	<p><?= Html::a(Html::encode($resetLink), $resetLink) ?></p>
</div>
