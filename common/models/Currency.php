<?php

namespace common\models;

use common\behaviors\JsonBehavior;
use Yii;
use yii\behaviors\TimestampBehavior;
use yii\db\ActiveRecord;

/**
 * This is the model class for table "{{%currency}}".
 *
 * @property int $id
 * @property int|null $user_id
 * @property string $code
 * @property string $symbol
 * @property string $name
 * @property int $type
 * @property int $fraction
 * @property float $weight
 * @property int $created_at
 * @property int $updated_at
 *
 * @property User $user
 * @property Security[] $securities
 */
class Currency extends ActiveRecord {

	const TYPE_FIAT = 0;
	const TYPE_METAL = 1;
	const TYPE_BLOCK = 2;
	const TYPE_CUSTOMER = 9;
	const TYPES = [
		self::TYPE_FIAT,
		self::TYPE_METAL,
		self::TYPE_BLOCK,
		self::TYPE_CUSTOMER,
	];

	/**
	 * {@inheritdoc}
	 */
	public static function tableName() {
		return '{{%currency}}';
	}

	/**
	 * {@inheritdoc}
	 */
	public function behaviors() {
		return [
			TimestampBehavior::class,
			[
				'class' => JsonBehavior::class,
				'attributes' => ['name'],
			],
		];
	}

	/**
	 * {@inheritdoc}
	 */
	public function rules() {
		return [
			[['user_id', 'fraction'], 'integer'],
			[['type'], 'in', 'range' => self::TYPES],
			[['code', 'symbol', 'name'], 'required'],
			[['name'], 'each', 'rule' => ['string']],
			[['weight'], 'number'],
			[['code'], 'string', 'max' => 5],
			[['symbol'], 'string', 'max' => 16],
			[['code'], 'unique', 'targetAttribute' => ['user_id', 'code']],
			[['code'], 'unique', 'filter' => function($query) {
				$query->innerJoin(['u' => User::tableName()], 'user_id = u.id')->andWhere(['u.status' => User::STATUS_SYSTEM]);
			}, 'message' => Yii::t('common', '{attribute} "{value}" has already been taken by system user.')],
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
			'code' => Yii::t('common', 'Code'),
			'symbol' => Yii::t('common', 'Symbol'),
			'name' => Yii::t('common', 'Name'),
			'type' => Yii::t('common', 'Type'),
			'fraction' => Yii::t('common', 'Fraction'),
			'weight' => Yii::t('common', 'Weight'),
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
		return $this->hasOne(User::class, ['id' => 'user_id'])->inverseOf('myCurrencies');
	}

	/**
	 * Gets query for [[Securities]].
	 *
	 * @return \yii\db\ActiveQuery
	 */
	public function getSecurities() {
		return $this->hasMany(Security::className(), ['currency_id' => 'id']);
	}

}
