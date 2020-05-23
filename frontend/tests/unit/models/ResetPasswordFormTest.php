<?php

namespace frontend\tests\unit\models;

use Codeception\Stub;
use Yii;
use common\fixtures\UserFixture;
use common\fixtures\PasswordResetTokenFixture;
use common\models\User;
use frontend\models\ResetPasswordForm;
use yii\base\InvalidArgumentException;
use yii\web\Request;

class ResetPasswordFormTest extends \Codeception\Test\Unit
{
	/**
	 * @var \frontend\tests\UnitTester
	 */
	protected $tester;


	public function _before()
	{
		$this->tester->haveFixtures([
			'user' => [
				'class' => UserFixture::class,
				'dataFile' => codecept_data_dir() . 'user.php'
			],
			'password_reset_token' => [
				'class' => PasswordResetTokenFixture::class,
				'dataFile' => codecept_data_dir() . 'password_reset_token.php',
			],
		]);
		$request = Stub::construct(Request::class, [[]], [
			'getUserIP' => '127.0.0.1',
		]);
		Yii::$app->set('request', $request);
	}

	public function testResetWrongToken()
	{
		$this->tester->expectThrowable(InvalidArgumentException::class, function() {
			new ResetPasswordForm('');
		});

		$this->tester->expectThrowable(InvalidArgumentException::class, function() {
			new ResetPasswordForm('notexistingtoken');
		});
		$this->tester->expectThrowable(InvalidArgumentException::class, function() {
			$token = $this->tester->grabFixture('password_reset_token', 'expired_token');
			new ResetPasswordForm($token['token']);
		});
	}

	public function testNotConfirmedPassword() {
		$token = $this->tester->grabFixture('password_reset_token', 'active_token');
		$model = new ResetPasswordForm($token['token']);
		$model->password = '123456';
		$model->password2 = '123';
		expect($model->resetPassword())->false();
		expect($model->hasErrors())->true();
		expect($model->getFirstError('password2'))->equals('Confirm Password must be equal to "Password".');
	}

	public function testShortPassword() {
		$token = $this->tester->grabFixture('password_reset_token', 'active_token');
		$model = new ResetPasswordForm($token['token']);
		$model->password = $model->password2 = '123';
		expect($model->resetPassword())->false();
		expect($model->hasErrors())->true();
		expect($model->getFirstError('password'))->equals('Password should contain at least 6 characters.');
	}

	public function testResetCorrectToken()	{
		$token = $this->tester->grabFixture('password_reset_token', 'active_token');
		$model = new ResetPasswordForm($token['token']);
		$model->password = $model->password2 = '123456';
		expect_that($model->resetPassword());
		$user = $this->tester->grabRecord(User::class, ['id' => $token['user_id']]);
		expect_that($user->validatePassword($model->password));
		$this->tester->expectThrowable(InvalidArgumentException::class, function() use ($token) {
			new ResetPasswordForm($token['token']);
		});
	}

}
