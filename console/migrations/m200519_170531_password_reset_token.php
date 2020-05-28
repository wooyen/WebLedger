<?php

use common\db\Migration;

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
		return true;
	}

	/**
	 * {@inheritdoc}
	 */
	public function safeDown() {
		return true;
	}

}
