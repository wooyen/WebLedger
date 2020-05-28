<?php
namespace common\db;

class Migration extends \yii\db\Migration {
	public function addForeignKey($name, $table, $columns, $refTable, $refColumns, $delete = null, $update = null) {
		if ($this->db->driverName === 'sqlite') {
			return;
		}
		parent::addForeignKey($name, $table, $columns, $refTable, $refColumns, $delete, $update);
	}
}
