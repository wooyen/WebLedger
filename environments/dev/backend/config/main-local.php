<?php
# REPLACEMENT PLACEHOLDERS:
# RANDOM:BACKEND_COOKIE_KEY32
# END

return [
	'components' => [
		'request' => [
			'cookieValidationKey' => 'BACKEND_COOKIE_KEY32',
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

