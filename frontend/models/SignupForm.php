<?php
namespace frontend\models;

use Yii;
use yii\base\Model;
use common\models\User;

/**
 * Signup form
 */
class SignupForm extends Model
{
	public $username;
	public $email;
	public $password;


	/**
	 * {@inheritdoc}
	 */
	public function rules()
	{
		return [
			['username', 'trim'],
			['username', 'required'],
			['username', 'unique', 'targetClass' => User::class, 'message' => 'This username has already been taken.'],
			['username', 'string', 'min' => 2, 'max' => 16],

			['email', 'trim'],
			['email', 'required'],
			['email', 'email'],
			['email', 'string', 'max' => 255],
			['email', 'unique', 'targetClass' => User::class, 'message' => 'This email address has already been taken.'],

			['password', 'required'],
			['password', 'string', 'min' => 6],
		];
	}

	/**
	 * Signs user up.
	 *
	 * @return bool whether the creating new account was successful and email was sent
	 */
	public function signup()
	{
		if (!$this->validate()) {
			return null;
		}
		
		$user = new User();
		$user->username = $this->username;
		$user->setPassword($this->password);
		$user->generateAuthKey();
		if (!$user->save()) {
			return false;
		}
		Yii::$app->user->login($user);
		return $user->requestChangeEmail($this->email);

	}

}
