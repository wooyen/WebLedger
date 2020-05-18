<?php
# REPLACEMENT PLACEHOLDERS:
# SENSITIVE:DB_PASSWORD
# END

use yii\gii\generators\model\Generator;

$trustedIPs = [
	'127.0.0.1',
	'::1',
	'192.168.196.*',
];
return [
	'components' => [
		'db' => [
			'class' => 'yii\db\Connection',
			'dsn' => 'mysql:host=localhost;dbname=webledger',
			'username' => 'webledger',
			'password' => 'DB_PASSWORD',
			'charset' => 'utf8',
		],
		'mailer' => [
			'class' => 'yii\swiftmailer\Mailer',
			'viewPath' => '@common/mail',
			// send all mails to a file by default. You have to set
			// 'useFileTransport' to false and configure a transport
			// for the mailer to send real emails.
			'useFileTransport' => true,
		],
	],
	'bootstrap' => [
		'gii',
		'debug',
	],
	'modules' => [
		'gii' => [
			'class' => \yii\gii\Module::class,
			'allowedIPs' => $trustedIPs,
			'generators' => [
				'model' => [
					'class' => Generator::class,
					'templates' => [
						'tab' => '@common/giiTemplates/model/tab',
					],
				],
			],
		],
		'debug' => [
			'class' => \yii\debug\Module::class,
			'allowedIPs' => $trustedIPs,
		],
	],
];
