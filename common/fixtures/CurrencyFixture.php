<?php
namespace common\fixtures;

use common\models\Currency;
use yii\test\ActiveFixture;

class CurrencyFixture extends ActiveFixture {
	public $modelClass = Currency::class;
	public $depends = [
		UserFixture::class,
	];
}
