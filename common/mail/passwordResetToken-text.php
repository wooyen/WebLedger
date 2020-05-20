<?php
use yii\helpers\Url;

/* @var $this yii\web\View */
/* @var $username string */
/* @var $token string */

$resetLink = Url::toRoute(['site/reset-password', 'token' => $token], true);
?>
Hello <?= $username ?>,

Follow the link below to reset your password:

<?= $resetLink ?>
