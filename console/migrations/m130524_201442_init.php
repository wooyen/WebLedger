<?php

use yii\db\Migration;

class m130524_201442_init extends Migration
{
	public function up()
	{
		$tableOptions = null;
		if ($this->db->driverName === 'mysql') {
			// http://stackoverflow.com/questions/766809/whats-the-difference-between-utf8-general-ci-and-utf8-unicode-ci
			$tableOptions = 'CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci ENGINE=InnoDB';
		}

		$this->createTable('{{%user}}', [
			'id' => $this->bigPrimaryKey(),
			'username' => $this->char(16)->notNull()->unique(),
			'auth_key' => $this->char(32)->notNull()->defaultValue(''),
			'password_hash' => $this->string()->notNull(),
			'email' => $this->string()->null()->unique(),
			'status' => $this->smallInteger()->notNull()->defaultValue(10),
			'created_at' => $this->bigInteger()->notNull(),
			'updated_at' => $this->bigInteger()->notNull(),
			'INDEX auth_key (auth_key)',
			'INDEX status (status)',
		], $tableOptions);
	}

	public function down()
	{
		$this->dropTable('{{%user}}');
	}
}
