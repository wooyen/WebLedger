<?php
namespace common\behaviors;

use yii\base\Behavior;
use yii\base\Event;
use yii\db\BaseActiveRecord;
use yii\helpers\Json;

/**
 * JsonBehavior automatically encode the attributes in Json format before it is saved to database and decode it after 
 * it is retrived from the database.
 *
 * To use JsonBehavior, configure the [[attributes]] property which should specify the list of attributes that need to 
 * be saved in the json format.
 *
 * ~~~
 * use common\behaviors\JsonBehavior;
 *
 * public function behaviors() {
 *     return [
 *         [
 *             'class' => JsonBehavior::class,
 *             'attribute' => ['attr1', 'attr2'],
 *         ],
 *     ];
 * }
 * ~~~
 *
 * @author WU Yan <wuyan@jzsec.com>
 */
class JsonBehavior extends Behavior {
	/**
	 * @var array list of attributes that are to be saved in json format
	 */
	public $attributes = [];

	private $decoded_attributes = [];
	public function events() {
		return [
			BaseActiveRecord::EVENT_AFTER_FIND => 'decode',
			BaseActiveRecord::EVENT_AFTER_INSERT => 'restore',
			BaseActiveRecord::EVENT_AFTER_UPDATE => 'restore',
			BaseActiveRecord::EVENT_BEFORE_INSERT => 'encode',
			BaseActiveRecord::EVENT_BEFORE_UPDATE => 'encode',
		];
	}

	public function decode($event) {
		foreach ($this->attributes as $attribute) {
			if (empty($this->owner->$attribute)) {
				$this->owner->$attribute = null;
				continue;
			}
			$this->owner->$attribute = Json::decode($this->owner->$attribute, true);
		}
	}

	public function encode($event) {
		foreach ($this->attributes as $attribute) {
			$this->decoded_attributes[$attribute] = $this->owner->$attribute;
			$this->owner->$attribute = Json::encode($this->owner->$attribute, JSON_UNESCAPED_UNICODE);
		}
	}

	public function restore($event) {
		foreach ($this->attributes as $attribute) {
			if (array_key_exists($attribute, $this->decoded_attributes)) {
				$this->owner->$attribute = $this->decoded_attributes[$attribute];
			} else {
				$this->owner->$attribute = Json::decode($this->owner->$attribute, true);
			}
		}
	}
}
