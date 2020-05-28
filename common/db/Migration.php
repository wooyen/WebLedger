<?php
namespace common\db;

class Migration extends \yii\db\Migration {
	protected $mysqlOptions = 'CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci ENGINE InnoDB';

	public function createTable($table, $columns, $options = null) {
		if ($options === null && $this->db->driverName === 'mysql' && !empty($this->mysqlOptions)) {
			$options = $this->mysqlOptions;
		}
		parent::createTable($table, $columns, $options);
	}
	
	public function addForeignKey($name, $table, $columns, $refTable, $refColumns, $delete = null, $update = null) {
		if ($this->db->driverName === 'sqlite') {
			return;
		}
		parent::addForeignKey($name, $table, $columns, $refTable, $refColumns, $delete, $update);
	}
}
