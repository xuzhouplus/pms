<?php

return [
	'adminEmail' => 'admin@example.com',
	'senderEmail' => 'noreply@example.com',
	'senderName' => 'Example.com mailer',
	'hostDomain' => 'http://web.pms.test',
	'upload' => [
		'path' => '/home/vagrant/code/pms/web/upload/',
		'url' => 'http://pms.test',
		'extensions' => null,
		'maxSize' => 1024,
		'maxFiles' => 10,
		'mimeTypes' => '*'
	],
	'security' => [
		'digestAlg' => 'sha256',
		'privateKeyBits' => 1024,
		'privateKeyType' => OPENSSL_KEYTYPE_RSA,
		'encryptSecret' => 'xcvmnbiufs'
	],
	'userCacheRsaPrimaryKey' => 'user_rsa_primary_key:',
];
