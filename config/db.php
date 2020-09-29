<?php

return [
    'class' => 'yii\db\Connection',
    'dsn' => 'mysql:host=localhost;dbname=pms',
    'username' => 'homestead',
    'password' => 'secret',
    'charset' => 'utf8mb4',
	'tablePrefix'=>'pms_',

    // Schema cache options (for production environment)
    'enableSchemaCache' => YII_ENV_PROD,
    'schemaCacheDuration' => 60,
    'schemaCache' => 'cache',
];
