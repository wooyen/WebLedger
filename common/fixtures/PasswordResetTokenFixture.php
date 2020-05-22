<?php
namespace common\fixtures;

use common\models\PasswordResetToken;
use yii\test\ActiveFixture;

class PasswordResetTokenFixture extends ActiveFixture {
	public $modelClass = PasswordResetToken::class;
	public $depends = [
		UserFixture::class,
	];
}
