<?php

namespace common\models;

use Yii;
use yii\behaviors\AttributeBehavior;
use yii\behaviors\TimestampBehavior;
use yii\db\ActiveRecord;

/**
 * This is the model class for table "{{%password_reset_token}}".
 *
 * @property int $id
 * @property int $user_id
 * @property string $token
 * @property string $requestIP
 * @property string|null $verifyIP
 * @property int $expire
 * @property int $created_at
 * @property int $updated_at
 *
 * @property User $user
 */
class PasswordResetToken extends ActiveRecord {
	/**
	 * {@inheritdoc}
	 */
	public static function tableName() {
		return '{{%password_reset_token}}';
	}

	/**
	 * {@inheritdoc}
	 */
	public function behaviors() {
		return [
			'time' => TimestampBehavior::class,
		];
	}

	/**
	 * {@inheritdoc}
	 */
	public function rules() {
		return [
			[['user_id', 'token', 'requestIP'], 'required'],
			[['user_id', 'expire'], 'integer'],
			[['token'], 'string', 'max' => 255],
			[['requestIP', 'verifyIP'], 'string', 'max' => 39],
			[['token'], 'unique'],
			[['user_id'], 'exist', 'skipOnError' => true, 'targetClass' => User::class, 'targetAttribute' => ['user_id' => 'id']],
		];
	}

	/**
	 * {@inheritdoc}
	 */
	public function attributeLabels() {
		return [
			'id' => Yii::t('common', 'ID'),
			'user_id' => Yii::t('common', 'User ID'),
			'token' => Yii::t('common', 'Token'),
			'requestIP' => Yii::t('common', 'Request Ip'),
			'verifyIP' => Yii::t('common', 'Verify Ip'),
			'expire' => Yii::t('common', 'Expire'),
			'created_at' => Yii::t('common', 'Created At'),
			'updated_at' => Yii::t('common', 'Updated At'),
		];
	}

	/**
	 * Gets query for [[User]].
	 *
	 * @return \yii\db\ActiveQuery
	 */
	public function getUser() {
		return $this->hasOne(User::class, ['id' => 'user_id']);
	}
}
