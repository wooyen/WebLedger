<?php
namespace frontend\models;

use Yii;
use yii\base\Model;
use common\models\PasswordResetToken;
use common\models\User;

/**
 * Password reset request form
 */
class PasswordResetRequestForm extends Model
{
	public $email;


	/**
	 * {@inheritdoc}
	 */
	public function rules() {
		return [
			['email', 'trim'],
			['email', 'required'],
			['email', 'email'],
			['email', 'exist',
				'targetClass' => User::class,
				'filter' => ['status' => User::STATUS_ACTIVE],
				'message' => 'There is no user with this email address.'
			],
		];
	}

	/**
	 * Sends an email with a link, for resetting the password.
	 *
	 * @return bool whether the email was send
	 */
	public function sendEmail() {
		/* @var $user User */
		$user = User::findOne([
			'status' => User::STATUS_ACTIVE,
			'email' => $this->email,
		]);

		if (!$user) {
			Yii::info("Send password reset email failed: user not found for {$this->email}.", __METHOD__);
			return false;
		}
		if (($token = $user->validPasswordResetToken) == null) {
			$token = new PasswordResetToken;
			$token->user_id = $user->id;
			$token->requestIP = Yii::$app->request->userIP;
			$token->expire = time() + Yii::$app->params['user.passwordResetTokenExpire'];
			do {
				$token->token = Yii::$app->security->generateRandomString();
			} while (!$token->validate('token'));
			if (!$token->save()) {
				Yii::error([
					"Save new PasswordRestToken failed",
					'error' => $token->errors,
				], __METHOD__);
				return false;
			}
		}

		return Yii::$app->mailer->compose([
				'html' => 'passwordResetToken-html',
				'text' => 'passwordResetToken-text'
			], [
				'username' => $user->username,
				'token' => $token->token,
			])->setFrom([Yii::$app->params['supportEmail'] => Yii::$app->name . ' robot'])
			->setTo($this->email)
			->setSubject('Password reset for ' . Yii::$app->name)
			->send();
	}
}
