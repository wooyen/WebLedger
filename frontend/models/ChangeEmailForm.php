<?php


namespace frontend\models;

use Yii;
use common\models\User;
use yii\base\Model;

class ChangeEmailForm extends Model
{
	/**
	 * @var string
	 */
	public $email;


	/**
	 * {@inheritdoc}
	 */
	public function rules()
	{
		return [
			['email', 'trim'],
			['email', 'required'],
			['email', 'email'],
			['email', 'unique', 'targetClass' => User::class, 'message' => 'This email address has already been taken.'],
		];
	}

	/**
	 * Sends confirmation email to user
	 *
	 * @return bool whether the email was sent
	 */
	public function changeEmail($user) {
		if (!$this->validate()) {
			return false;
		}
		return $user->requestChangeEmail($this->email);
	}
}
