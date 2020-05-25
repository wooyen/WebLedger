<?php

use yii\db\Migration;

/**
 * Class m200519_170531_password_reset_token
 */
class m200519_170531_password_reset_token extends Migration {
	const TBL_NAME = '{{%password_reset_token}}';
	/**
	 * {@inheritdoc}
	 */
	public function safeUp() {
		$tableOptions = $this->db->driverName === 'mysql' ? 'CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci ENGINE=InnoDB' : null;
		$this->createTable(self::TBL_NAME, [
			'id' => $this->bigPrimaryKey(),
			'user_id' => $this->bigInteger()->notNull(),
			'token' => $this->string()->notNull()->unique(),
			'requestIP' => $this->char(39)->notNull(),
			'verifyIP' => $this->char(39)->notNull()->defaultValue(''),
			'expire' => $this->bigInteger()->notNull(),
			'created_at' => $this->bigInteger()->notNull(),
			'updated_at' => $this->bigInteger()->notNull(),
			'FOREIGN KEY fk_user (user_id) REFERENCES user (id) ON DELETE CASCADE ON UPDATE CASCADE',
		], $tableOptions);
		return true;
	}

	/**
	 * {@inheritdoc}
	 */
	public function safeDown() {
		$this->dropTable(self::TBL_NAME);
		return true;
	}

}
