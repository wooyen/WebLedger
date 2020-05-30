<?php

use common\db\Migration;

/**
 * Class m200525_030602_basic_regions_currencies
 */
class m200525_030602_currency extends Migration {

	const CURRENCY = '{{%currency}}';
	const EXCHANGE = '{{%exchange}}';
	const SECURITY = '{{%security}}';
	const USER = '{{%user}}';
	/**
	 * {@inheritdoc}
	 */
	public function safeUp() {
		$this->createTable(self::CURRENCY, [
			'id' => $this->bigPrimaryKey(),
			'user_id' => $this->bigInteger(),
			'code' => $this->char(5)->notNull(),
			'symbol' => $this->char(16)->notNull(),
			'name' => $this->json()->notNull(),
			'type' => $this->tinyInteger()->notNull(),
			'fraction' => $this->integer()->unsigned()->notNull()->defaultValue(1),
			'weight' => $this->decimal(3,3)->notNull()->defaultValue(0),
			'created_at' => $this->bigInteger()->notNull(),
			'updated_at' => $this->bigInteger()->notNull(),
		]);
		$this->createIndex('code', self::CURRENCY, ['user_id', 'code'], true);
		$this->createIndex('type', self::CURRENCY, 'type');
		$this->createIndex('weight', self::CURRENCY, 'weight');
		$this->addForeignKey('user', self::CURRENCY, 'user_id', self::USER, 'id', 'CASCADE', 'CASCADE');
		$this->createTable(self::EXCHANGE, [
			'id' => $this->bigPrimaryKey(),
			'user_id' => $this->bigInteger(),
			'code' => $this->char(8)->notNull(),
			'symbol' => $this->char(16)->notNull(),
			'name' => $this->json()->notNull(),
			'weight' => $this->decimal(3,3)->notNull()->defaultValue(0),
			'created_at' => $this->bigInteger()->notNull(),
			'updated_at' => $this->bigInteger()->notNull(),
		]);
		$this->createIndex('code', self::EXCHANGE, ['user_id', 'code'], true);
		$this->createIndex('weight', self::EXCHANGE, 'weight');
		$this->addForeignKey('user', self::EXCHANGE, 'user_id', self::USER, 'id', 'CASCADE', 'CASCADE');
		$this->createTable(self::SECURITY, [
			'id' => $this->bigPrimaryKey(),
			'user_id' => $this->bigInteger(),
			'code' => $this->char(8)->notNull(),
			'symbol' => $this->char(16)->notNull(),
			'name' => $this->json()->notNull(),
			'type' => $this->tinyInteger(),
			'currency_id' => $this->bigInteger()->notNull(),
			'exchange_id' => $this->bigInteger()->notNull(),
			'created_at' => $this->bigInteger()->notNull(),
			'updated_at' => $this->bigInteger()->notNull(),
		]);
		$this->createIndex('code', self::SECURITY, ['user_id', 'exchange_id', 'code'], true);
		$this->createIndex('type', self::SECURITY, 'type');
		$this->addForeignKey('user', self::SECURITY, 'user_id', self::USER, 'id', 'CASCADE', 'CASCADE');
		$this->addForeignKey('currency', self::SECURITY, 'currency_id', self::CURRENCY, 'id', 'RESTRICT', 'CASCADE');
		$this->addForeignKey('exchnge', self::SECURITY, 'exchange_id', self::EXCHANGE, 'id', 'RESTRICT', 'CASCADE');
		return true;
	}

	/**
	 * {@inheritdoc}
	 */
	public function safeDown() {
		$this->dropTable(self::SECURITY);
		$this->dropTable(self::EXCHANGE);
		$this->dropTable(self::CURRENCY);
		return true;
	}

}
