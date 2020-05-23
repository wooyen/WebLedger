<?php

use yii\caching\ArrayCache;

return [
	'id' => 'app-common-tests',
	'basePath' => dirname(__DIR__),
	'components' => [
		'cache' => [
			'class' => ArrayCache::class,
			'serializer' => false,
		],
		'user' => [
			'class' => 'yii\web\User',
			'identityClass' => 'common\models\User',
		],
	],
];
