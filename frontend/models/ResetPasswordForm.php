<?php
namespace frontend\models;

use common\models\PasswordResetToken;
use Yii;
use yii\base\InvalidArgumentException;
use yii\base\Model;

/**
 * Password reset form
 */
class ResetPasswordForm extends Model
{
	public $password;
	public $password2;

	/**
	 * @var \common\models\PasswordResetToken
	 */
	private $_token;


	/**
	 * Creates a form model given a token.
	 *
	 * @param string $token
	 * @param array $config name-value pairs that will be used to initialize the object properties
	 * @throws InvalidArgumentException if token is empty or not valid
	 */
	public function __construct($token, $config = []) {
		if (empty($token) || !is_string($token)) {
			throw new InvalidArgumentException('Password reset token cannot be blank.');
		}
		$this->_token = PasswordResetToken::find()->where(['token' => $token, 'verifyIP' => ''])->andWhere(['>', 'expire', time()])->one();
		if (!$this->_token) {
			throw new InvalidArgumentException('Wrong password reset token.');
		}
		parent::__construct($config);
	}

	/**
	 * {@inheritdoc}
	 */
	public function rules() {
		return [
			[['password', 'password2'], 'required'],
			['password', 'string', 'min' => 6],
			['password2', 'compare', 'compareAttribute' => 'password'],
		];
	}

	/**
	 * {@inheritdoc}
	 */
	public function attributeLabels() {
		return [
			'password' => Yii::t('common', 'Password'),
			'password2' => Yii::t('common', 'Confirm Password'),
		];
	}

	/**
	 * Resets password.
	 *
	 * @return bool if password was reset.
	 */
	public function resetPassword() {
		$user = $this->_token->user;
		$user->setPassword($this->password);
		if (!$user->save(false)) {
			return false;
		}
		$this->_token->verifyIP = Yii::$app->request->userIP;
		return $this->_token->save();
	}
}
