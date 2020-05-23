<?php
namespace frontend\tests\unit\models;

use Codeception\Stub;
use Yii;
use common\fixtures\UserFixture;
use common\models\User;
use frontend\models\SignupForm;
use yii\web\Controller;

class SignupFormTest extends \Codeception\Test\Unit
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

	public function testCorrectSignup() {
		$name = 'somebody';
		$email = 'some_email@example.com';
		$passwd = 'some_password';
		$model = new SignupForm([
			'username' => $name,
			'email' => $email,
			'password' => $passwd,
			'password2' => $passwd,
		]);

		$result = $model->signup();
		expect($result)->true();

		/** @var \common\models\User $user */
		$user = $this->tester->grabRecord(User::class, [
			'username' => $name,
			'email' => null,
			'status' => User::STATUS_ACTIVE
		]);
		expect_that($user);
		$cached = Yii::$app->cache->get(User::EMAIL_VERIFY_TOKEN_KEY . $user->username);
		expect_that('email verify token cached', $cached);
		expect('Cached email verify token is an array', $cached)->array();
		expect('Element 0 of cached email verify token data is the email', $cached[0])->equals($email);
		expect('The password hash saved is correct', $user->validatePassword($passwd))->true();

		$this->tester->seeEmailIsSent();

		$mail = $this->tester->grabLastSentEmail();
		expect($mail)->isInstanceOf('yii\mail\MessageInterface');
		expect($mail->getTo())->hasKey('some_email@example.com');
		expect($mail->getFrom())->hasKey(\Yii::$app->params['supportEmail']);
		expect($mail->getSubject())->equals('Account registration at ' . \Yii::$app->name);
		expect(str_replace("=\r\n", "", $mail->toString()))->stringContainsString($cached[1]);
	}

	public function testNotCorrectSignup()
	{
		$model = new SignupForm([
			'username' => 'troy.becker',
			'email' => 'nicolas.dianna@hotmail.com',
			'password' => 'some_password',
			'password2' => 'some_password2',
		]);

		expect_not($model->signup());
		expect_that($model->getErrors('username'));
		expect_that($model->getErrors('email'));

		expect($model->getFirstError('username'))->equals('This username has already been taken.');
		expect($model->getFirstError('email'))->equals('This email address has already been taken.');
		expect($model->getFirstError('password2'))->equals('Confirm password must be equal to "Password".');
	}
}
