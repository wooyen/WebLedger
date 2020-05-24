<?php

namespace frontend\tests\functional;

use common\fixtures\UserFixture;
use frontend\tests\FunctionalTester;

class LoginRequiredCest {
	protected $formId = '#change-email-form';


	/**
	 * Load fixtures before db transaction begin
	 * Called in _before()
	 * @see \Codeception\Module\Yii2::_before()
	 * @see \Codeception\Module\Yii2::loadFixtures()
	 * @return array
	 */
	public function _fixtures()
	{
		return [
			'user' => [
				'class' => UserFixture::class,
				'dataFile' => codecept_data_dir() . 'user.php',
			],
		];
	}

	public function _before(FunctionalTester $I) {
	}

	public function checkChangeEmail(FunctionalTester $I) {
		$this->checkLoginPageAndLogin('/site/change-email', $I);
	}

	public function checkVerifyEmail(FunctionalTester $I) {
		$this->checkLoginPageAndLogin('/site/verify-email', $I);
	}

	private function checkLoginPageAndLogin($route, FunctionalTester $I) {
		$I->amOnRoute($route);
		$I->seeInCurrentUrl('/site/login');
		$I->submitForm('#login-form', [
			'LoginForm[username]' => 'test2.test',
			'LoginForm[password]' => 'Test1234',
		]);
		$I->seeInCurrentUrl($route);
		$I->see('Logout (test2.test)', 'form button[type=submit]');
	}

}
