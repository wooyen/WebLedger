<?php

namespace frontend\tests\functional;

use Yii;
use common\fixtures\UserFixture;
use common\models\User;
use frontend\tests\FunctionalTester;

class VerifyEmailCest {

	const USERNAME = 'test2.test';
	
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
		$I->amOnRoute('/site/login');
		$I->submitForm('#login-form', [
			'LoginForm[username]' => self::USERNAME,
			'LoginForm[password]' => 'Test1234',
		]);
		Yii::$app->cache->flush();
	}

	public function checkNotExistToken(FunctionalTester $I) {
		$I->amOnRoute('/site/verify-email', ['token' => '']);
		$I->seeInCurrentUrl('/site/change-email');
		$I->canSee('Sorry, the token does not exist or has expired. Please resend your verify token to your Email.');
	}

	private function prepareToken($email, $token) {
		Yii::$app->cache->set(User::EMAIL_VERIFY_TOKEN_KEY . self::USERNAME, [$email, $token], 3600);
	}

	public function checkInvalidToken(FunctionalTester $I) {
		$this->prepareToken('email@mail.com', 'correct_token');
		$I->amOnRoute('/site/verify-email', ['token' => 'wrong_token']);
		$I->seeInCurrentUrl('/');
		$I->canSee('Sorry, we are unable to verify your account with provided token.');
	}

	public function checkSuccessVerification(FunctionalTester $I) {
		$this->prepareToken('email@mail.com', 'correct_token');
		$I->amOnRoute('/site/verify-email', ['token' => 'correct_token']);
		$I->seeInCurrentUrl('/');
		$I->canSee('Your email has been confirmed!');

		$I->seeRecord('common\models\User', [
		   'username' => 'test2.test',
		   'email' => 'email@mail.com',
		]);
	}
}
