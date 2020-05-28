<?php

use common\db\Migration;

class m130524_201442_init extends Migration {
	const USER = '{{%user}}';
	const TOKEN = '{{%password_reset_token}}';

	public function safeUp() {
		$tableOptions = null;
		if ($this->db->driverName === 'mysql') {
			// http://stackoverflow.com/questions/766809/whats-the-difference-between-utf8-general-ci-and-utf8-unicode-ci
			$tableOptions = 'CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci ENGINE=InnoDB';
		}

		$this->createTable(self::USER, [
			'id' => $this->bigPrimaryKey(),
			'username' => $this->char(16)->notNull()->unique(),
			'auth_key' => $this->char(32)->notNull()->defaultValue(''),
			'password_hash' => $this->string()->notNull(),
			'email' => $this->string()->null()->unique(),
			'status' => $this->smallInteger()->notNull()->defaultValue(10),
			'created_at' => $this->bigInteger()->notNull(),
			'updated_at' => $this->bigInteger()->notNull(),
		], $tableOptions);
		$this->createIndex('auth_key', '{{%user}}', 'auth_key');
		$this->createIndex('status', '{{%user}}', 'status');

		$this->createTable(self::TOKEN, [
			'id' => $this->bigPrimaryKey(),
			'user_id' => $this->bigInteger()->notNull(),
			'token' => $this->string()->notNull()->unique(),
			'requestIP' => $this->char(39)->notNull(),
			'verifyIP' => $this->char(39)->notNull()->defaultValue(''),
			'expire' => $this->bigInteger()->notNull(),
			'created_at' => $this->bigInteger()->notNull(),
			'updated_at' => $this->bigInteger()->notNull(),
		], $tableOptions);
		$this->addForeignKey('user', self::TOKEN, 'user_id', self::USER, 'id', 'CASCADE', 'CASCADE');
	}

	public function safeDown() {
		$this->dropTable(self::TOKEN);
		$this->dropTable(self::USER);
	}
}
