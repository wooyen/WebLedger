<?php

use yii\helpers\Url;

/* @var $this yii\web\View */
/* @var $username string */
/* @var $token string */

$verifyLink = Url::toRoute(['site/verify-email', 'token' => $token], true);
?>
Hello <?= $username ?>,

Follow the link below to verify your email:

<?= $verifyLink ?>
