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
	public $password2;

	/**
	 * {@inheritdoc}
	 */
	public function rules()
	{
		return [
			[['username', 'email'], 'trim'],
			[['username', 'email', 'password', 'password2'], 'required'],
			['username', 'unique', 'targetClass' => User::class, 'message' => 'This username has already been taken.'],
			['username', 'string', 'min' => 2, 'max' => 16],
			['email', 'email'],
			['email', 'string', 'max' => 255],
			['email', 'unique', 'targetClass' => User::class, 'message' => 'This email address has already been taken.'],
			['password', 'string', 'min' => 6],
			['password2', 'compare', 'compareAttribute' => 'password'],
		];
	}

	/**
	 * {@inheritdoc}
	 */
	public function attributeLabels() {
		return [
			'username' => Yii::t('common', 'Username'),
			'email' => Yii::t('common', 'Email'),
			'password' => Yii::t('common', 'Password'),
			'password2' => Yii::t('common', 'Confirm password'),
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
