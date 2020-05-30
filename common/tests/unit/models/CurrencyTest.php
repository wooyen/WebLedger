<?php

namespace common\tests\unit\models;

use Yii;
use common\models\Currency;
use common\models\User;
use common\fixtures\CurrencyFixture;
use common\fixtures\UserFixture;

/**
 * Login form test
 */
class CurrencyTest extends \Codeception\Test\Unit {
	/**
	 * @var \common\tests\UnitTester
	 */
	protected $tester;


	/**
	 * @return array
	 */
	public function _fixtures() {
		return [
			'user' => [
				'class' => UserFixture::class,
				'dataFile' => codecept_data_dir() . 'user.php'
			],
			'currency' => [
				'class' => CurrencyFixture::class,
				'dataFile' => codecept_data_dir() . 'currency.php',
			],
		];
	}

	public function testNewCurrency() {
		$model = new Currency([
			'user_id' => 1,
			'code' => 'GBP',
			'symbol' => '£',
			'name' => [
				'en' => 'Great Britain Pound',
				'zh-CN' => '英镑',
			],
			'type' => 1,
			'fraction' => 100,
			'weight' => 0.19,
		]);
		expect('New currency cna be save.', $model->save())->true();
		expect('The name of currency is an array.', $model->name)->array();
		expect('The created_at field has been filled.', $model->created_at)->notEquals(0);
		expect('The created_at field is a time passed.', $model->created_at)->lessOrEquals(time());
	}

	public function testUniqToSystem() {
		$model = new Currency([
			'user_id' => 2,
			'code' => 'USD',
			'symbol' => '$',
			'name' => [
				'en' => 'US Dollar',
			],
			'type' => 1,
			'fraction' => 100,
			'weight' => 0.25,
		]);
		expect('New currency with the same code owned by system user can not be saved.', $model->save())->false();
		expect('Error on the code field.', $model->getFirstError('code'))->equals('Code "USD" has already been taken by system user.');
	}

	public function testDupToOtherUser() {
		$user = new User([
			'username' => 'test',
		]);
		$user->setPassword('');
		expect($user->save())->true();
		$model = new Currency([
			'user_id' => $user->id,
			'code' => 'EUR',
			'symbol' => "\100",
			'name' => [
				'en' => 'Euro',
			],
			'type' => 1,
			'fraction' => 100, 
			'weight' => 0.23,
		]);
		expect('New currency with the same code owned by other user is allowed', $model->save())->true();
	}

	public function testFindFromUser() {
		$alice = $this->tester->grabFixture('user', 'alice');
		$models = $alice->myCurrencies;
		expect('Alice have one currency', count($models))->equals(1);
		expect('Alice hav currency of JPY', $models[0]->code)->equals('JPY');
		$models = $alice->allCurrencies;
		expect('3 currencies are avaliable for alice', count($models))->equals(3);
		expect('First one is USD', $models[0]->code)->equals('USD');
		expect('Second one is JPY', $models[1]->code)->equals('JPY');
		expect('Third one is MRU', $models[2]->code)->equals('MRU');
	}

	public function testInverseOfUser() {
		$alice = $this->tester->grabFixture('user', 'alice');
		expect($alice->myCurrencies[0]->user)->equals($alice);
		$usd = $this->tester->grabFixture('currency', 'usd');
		foreach ($usd->user->myCurrencies as $currency) {
			if ($currency->id == $usd->id) {
				expect($currency)->equals($usd);
			}
		}
	}
}
