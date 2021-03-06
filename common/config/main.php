<?php

use yii\caching\FileCache;
use yii\i18n\PhpMessageSource;
use yii\log\FileTarget;

return [
	'aliases' => [
		'@bower' => '@vendor/bower-asset',
		'@npm'   => '@vendor/npm-asset',
	],
	'vendorPath' => dirname(dirname(__DIR__)) . '/vendor',
	'components' => [
		'cache' => [
			'class' => FileCache::class,
		],
		'urlManager' => [
			'enablePrettyUrl' => true,
			'showScriptName' => false,
			'rules' => [
				'<controller:\w+>/<action:\w+>'=>'<controller>/<action>',
			],
		],
		'log' => [
			'traceLevel' => YII_DEBUG ? 3 : 0,
			'targets' => [
				'app' => [
					'class' => FileTarget::class,
					'rotateByCopy' => false,
					'levels' => [
						'error',
						'warning',
						'info',
					],
					'categories' => ['common*'],
				],
				'yii' => [
					'class' => FileTarget::class,
					'rotateByCopy' => false,
					'levels' => [
						'error',
						'warning',
						'info',
					],
					'categories' => ['yii*'],
					'logFile' => '@runtime/logs/yii.log',
				],
			],
		],
		'i18n' => [
			'translations' => [
				'yii' => [
					'class' => phpMessageSource::className(),
					'basePath' => '@yii/messages',
				],
				'common' => [
					'class' => phpMessageSource::className(),
					'basePath' => '@common/messages',
				],
			],
		],
	],
	'bootstrap' => [
		'log',
	],
];
