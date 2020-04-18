<?php
# REPLACEMENT PLACEHOLDERS:
# RANDOM:FRONTEND_COOKIE_KEY32
# END

return [
	'components' => [
		'request' => [
			'cookieValidationKey' => 'FRONTEND_COOKIE_KEY32',
		],
	],
	'bootstrap' => [
		'debug',
		'gii',
	],
	'modules' => [
		'debug' => [
			'class' => \yii\debug\Module::class,
		],
		'gii' => [
			'class' => \yii\gii\Module::class,
		],
	],
];

