<?php
return [
	'active_token' => [
		'user_id' => 1,
		'token' => '0123456789ABCDEFGHIJKLMNOPQRSTU',
		'requestIP' => '127.0.0.1',
		'expire' => time() + 3600,
		'created_at' => time(),
		'updated_at' => time(),
	],
	'expired_token' => [
		'user_id' => 4,
		'token' => '123456789ABCDEFGHIJKLMNOPQRSTUV',
		'requestIP' => '127.0.0.1',
		'expire' => time() - 3600,
		'created_at' => time() - 7200,
		'updated_at' => time() - 7200,
	],
];

