<?php

namespace frontend\tests\unit\models;

use Codeception\Stub;
use Yii;
use common\fixtures\PasswordResetTokenFixture;
use common\fixtures\UserFixture as UserFixture;
use common\models\User;
use frontend\models\PasswordResetRequestForm;
use yii\web\Controller;
use yii\web\Request;

class PasswordResetRequestFormTest extends \Codeception\Test\Unit
{
	/**
	 * @var \frontend\tests\UnitTester
	 */
	protected $tester;


	public function _before() {
		$this->tester->haveFixtures([
			'user' => [
				'class' => UserFixture::class,
				'dataFile' => codecept_data_dir() . 'user.php',
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
		Yii::$app->controller = Stub::construct(Controller::class, ['fake', Yii::$app]);
	}

	public function testSendMessageWithWrongEmailAddress() {
		$model = new PasswordResetRequestForm();
		$model->email = 'not-existing-email@example.com';
		expect_not($model->sendEmail());
	}

	public function testNotSendEmailsToInactiveUser() {
		$user = $this->tester->grabFixture('user', 'inactive_user');
		$model = new PasswordResetRequestForm();
		$model->email = $user['email'];
		expect_not($model->sendEmail());
	}

	public function testSendEmailSuccessfully() {
		$userFixture = $this->tester->grabFixture('user', 'active_user');
		
		$model = new PasswordResetRequestForm();
		$model->email = $userFixture['email'];
		expect(User::findOne($userFixture['id'])->validPasswordResetToken)->null();
		expect($model->sendEmail())->true();
		$PRT = User::findOne($userFixture['id'])->validPasswordResetToken;
		expect($PRT)->notNull();
		$emailMessage = $this->tester->grabLastSentEmail();
		expect('valid email is sent', $emailMessage)->isInstanceOf('yii\mail\MessageInterface');
		expect($emailMessage->getTo())->hasKey($model->email);
		expect($emailMessage->getFrom())->hasKey(Yii::$app->params['supportEmail']);
		expect(str_replace("=\r\n", '', $emailMessage->toString()))->stringContainsString($PRT->token);
	}
}
