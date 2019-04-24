<?php
$dbms = 'mysql4';
$dbhost = getenv('PHPBB_DB_HOST');
$dbname = getenv('PHPBB_DB_NAME');
$dbuser = getenv('PHPBB_DB_USER');
$dbpasswd = getenv('PHPBB_DB_PASSWORD');

$table_prefix = 'phpbb_';

define('PHPBB_INSTALLED', true);
