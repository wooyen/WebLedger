<?php

namespace common\models;

use Yii;
use yii\base\NotSupportedException;
use yii\behaviors\TimestampBehavior;
use yii\db\ActiveRecord;
use yii\web\IdentityInterface;

/**
 * User model
 *
 * @property int $id
 * @property string $username
 * @property string $auth_key
 * @property string $password_hash
 * @property string $email
 * @property int $status
 * @property int $created_at
 * @property int $updated_at
 * @property string $password write-only password
 */
class User extends ActiveRecord implements IdentityInterface {
	const STATUS_DELETED = 0;
	const STATUS_INACTIVE = 9;
	const STATUS_ACTIVE = 10;
	const STATUS = [
		self::STATUS_DELETED,
		self::STATUS_INACTIVE,
		self::STATUS_ACTIVE,
	];

	const EMAIL_VERIFY_TOKEN_KEY = "EmailVerifyToken";
	const PASSWORD_RESET_TOKEN_KEY = "PasswordResetToken";

	/**
	 * {@inheritdoc}
	 */
	public static function tableName() {
		return '{{%user}}';
	}

	/**
	 * {@inheritdoc}
	 */
	public function behaviors() {
		return [
			TimestampBehavior::class,
		];
	}

	/**
	 * {@inheritdoc}
	 */
	public function rules() {
		return [
			['status', 'default', 'value' => self::STATUS_ACTIVE],
			[['username', 'password_hash'], 'required'],
			['status', 'in', 'range' => self::STATUS],
			[['username'], 'string', 'max' => 16],
			[['auth_key'], 'string', 'max' => 32],
			[['password_hash', 'email'], 'string', 'max' => 255],
			[['username'], 'unique'],
			[['email'], 'unique'],
			[['email'], 'email'],
		];
	}

	/**
	 * {@inheritdoc}
	 */
	public function attributeLabels() {
		return [
			'id' => Yii::t('common', 'ID'),
			'username' => Yii::t('common', 'Username'),
			'auth_key' => Yii::t('common', 'Auth Key'),
			'password_hash' => Yii::t('common', 'Password Hash'),
			'email' => Yii::t('common', 'Email'),
			'status' => Yii::t('common', 'Status'),
			'created_at' => Yii::t('common', 'Created At'),
			'updated_at' => Yii::t('common', 'Updated At'),
		];
	}

	/**
	 * {@inheritdoc}
	 */
	public static function findIdentity($id) {
		return static::findOne(['id' => $id, 'status' => self::STATUS_ACTIVE]);
	}

	/**
	 * {@inheritdoc}
	 */
	public static function findIdentityByAccessToken($token, $type = null) {
		throw new NotSupportedException('"findIdentityByAccessToken" is not implemented.');
	}

	/**
	 * Finds user by username
	 *
	 * @param string $username
	 * @return static|null
	 */
	public static function findByUsername($username) {
		return static::findOne(['username' => $username, 'status' => self::STATUS_ACTIVE]);
	}

	/**
	 * {@inheritdoc}
	 */
	public function getId() {
		return $this->getPrimaryKey();
	}

	/**
	 * {@inheritdoc}
	 */
	public function getAuthKey() {
		return $this->auth_key;
	}

	/**
	 * {@inheritdoc}
	 */
	public function validateAuthKey($authKey) {
		return $this->getAuthKey() === $authKey;
	}

	/**
	 * Validates password
	 *
	 * @param string $password password to validate
	 * @return bool if password provided is valid for current user
	 */
	public function validatePassword($password) {
		return Yii::$app->security->validatePassword($password, $this->password_hash);
	}

	/**
	 * Generates password hash from password and sets it to the model
	 *
	 * @param string $password
	 */
	public function setPassword($password) {
		$this->password_hash = Yii::$app->security->generatePasswordHash($password);
	}

	/**
	 * Generates "remember me" authentication key
	 */
	public function generateAuthKey() {
		$this->auth_key = Yii::$app->security->generateRandomString();
	}

	public function requestChangeEmail($email) {
		$token = Yii::$app->security->generateRandomString();
		Yii::$app->cache->set(self::EMAIL_VERIFY_TOKEN_KEY . $this->username, [$email, $token], 7*86400);
		return Yii::$app->mailer->compose([
			'html' => 'emailVerify-html',
			'text' => 'emailVerify-text',
		], [
			'username' => $this->username,
			'token' => $token,
		])->setFrom([Yii::$app->params['supportEmail'] => Yii::$app->name . ' robot'])
		->setTo($email)
		->setSubject('Verify your email address at ' . Yii::$app->name)
		->send();
	}

	/**
	 * Gets query for [[PasswordResetTokens]].
	 *
	 * @return \yii\db\ActiveQuery
	 */
	public function getPasswordResetToken() {
		return $this->hasMany(PasswordResetToken::class, ['user_id' => 'id']);
	}

	/**
	 * Gets query for valid [[PasswordResetTokens]].
	 *
	 * @return \yii\db\ActiveQuery
	 */
	public function getValidPasswordResetToken() {
		return $this->hasOne(PasswordResetToken::class, ['user_id' => 'id'])->andWhere(['verifyIP' => ''])->andWhere(['>', 'expire', time()]);
	}

}
