<?php

namespace frontend\tests\unit\models;

use Codeception\Stub;
use Codeception\Test\Unit;
use Yii;
use common\fixtures\UserFixture;
use common\models\User;
use frontend\models\ChangeEmailForm;
use yii\mail\MessageInterface;
use yii\web\Controller;

class ChangeEmailFormTest extends Unit
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
			]
		]);
		Yii::$app->controller = Stub::construct(Controller::class, ['fake', Yii::$app]);
	}

	public function testExistingEmailAddress()
	{
		$model = new ChangeEmailForm();
		$model->attributes = [
			'email' => 'brady.renner@rutherford.com',
		];

		expect($model->validate())->false();
		expect($model->hasErrors())->true();
		expect($model->getFirstError('email'))->equals('This email address has already been taken.');
	}

	public function testEmptyEmailAddress()
	{
		$model = new ChangeEmailForm();
		$model->attributes = [
			'email' => ''
		];

		expect($model->validate())->false();
		expect($model->hasErrors())->true();
		expect($model->getFirstError('email'))->equals('Email cannot be blank.');
	}

	public function testWrongEmailAddress() {
		$model = new ChangeEmailForm;
		$model->attributes = [
			'email' => 'not-a-email',
		];

		expect($model->validate())->false();
		expect($model->hasErrors())->true();
		expect($model->getFirstError('email'))->equals('Email is not a valid email address.');
	}

	public function testSuccessfullyResend() {
		$email = 'new@mail.com';
		$model = new ChangeEmailForm();
		$model->attributes = [
			'email' => $email,
		];

		expect($model->validate())->true();
		expect($model->hasErrors())->false();

		$user = $this->tester->grabFixture('user', 'general_user');
		expect($model->changeEmail($user))->true();
		$this->tester->seeEmailIsSent();

		$mail = $this->tester->grabLastSentEmail();

		expect('valid email is sent', $mail)->isInstanceOf(MessageInterface::class);
		expect($mail->getTo())->hasKey($email);
		expect($mail->getFrom())->hasKey(\Yii::$app->params['supportEmail']);
		expect($mail->getSubject())->equals('Verify your email address at ' . \Yii::$app->name);
		$cached = Yii::$app->cache->get(User::EMAIL_VERIFY_TOKEN_KEY . $user->username);
		expect($cached)->array();
		expect($cached[0])->equals($email);
		expect(str_replace("=\r\n", '', $mail->toString()))->stringContainsString($cached[1]);
	}
}
