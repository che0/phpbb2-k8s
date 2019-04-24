<?php
$admin_name = 'admin';
$admin_pass1 = 'admin';

$server_name = getenv('PHPBB_SERVER_NAME');
$server_port = getenv('PHPBB_SERVER_PORT');
$use_https = getenv('PHPBB_USE_HTTPS');

define('IN_PHPBB', true);
$phpbb_root_path = '/var/www/phpBB2/';
include($phpbb_root_path.'extension.inc');

// Initialise some basic arrays
$userdata = array();
$lang = array();
$error = false;

// Include some required functions
include($phpbb_root_path.'config.'.$phpEx);
include($phpbb_root_path.'includes/constants.'.$phpEx);
include($phpbb_root_path.'includes/functions.'.$phpEx);
include($phpbb_root_path.'includes/sessions.'.$phpEx);
include($phpbb_root_path.'db/mysql4.'.$phpEx);

for ($i = 0; $i < 60; $i++)
{
    $db = new sql_db($dbhost, $dbuser, $dbpasswd, $dbname, false);
    if(!$db->db_connect_id)
    {
        echo "Unable to connect to database, retrying\n";
        sleep(1);
    }
}

if(!$db->db_connect_id)
{
    echo "Unable to connect to database, FAIL\n";
    exit(1);
}

function update_config($update_config)
{
    global $table_prefix;
    while (list($config_name, $config_value) = each($update_config))
    {
        sql("UPDATE " . $table_prefix . "config 
            SET config_value = '$config_value' 
            WHERE config_name = '$config_name'");
    }
}


if ($db->sql_query("select 1 from " . $table_prefix . "config"))
{
    echo "Found phpBB configuration, setting basic values\n";
    update_config(array(
        'server_port'	=> $server_port,
        'server_name'	=> $server_name,
        'cookie_secure'	=> $use_https,
    ));
    echo "Runtime config done, continuing\n";
    exit(0);
}


echo "Found no phpBB configuration, installing\n";

function sql($query)
{
    global $db;
    if (!$db->sql_query($query))
    {
        echo $db->sql_error()['message'] . "\n";
        exit(1);
    }
}

error_reporting(E_ALL & ~E_NOTICE);
include($phpbb_root_path.'includes/sql_parse.'.$phpEx);

function load_sql_file($dbms_schema)
{
    global $table_prefix;
    $sql_query = @fread(@fopen($dbms_schema, 'r'), @filesize($dbms_schema));
    $sql_query = preg_replace('/phpbb_/', $table_prefix, $sql_query);

    $sql_query = remove_remarks($sql_query);
    $sql_query = split_sql_file($sql_query, ';');
    for ($i = 0; $i < sizeof($sql_query); $i++)
    {
        if (trim($sql_query[$i]) != '')
        {
            sql($sql_query[$i]);
        }
    }
}
load_sql_file($phpbb_root_path.'install/schemas/mysql_schema.sql');
load_sql_file($phpbb_root_path.'install/schemas/mysql_basic.sql');

sql("INSERT INTO " . $table_prefix . "config (config_name, config_value) VALUES ('board_startdate', " . time() . ")");
sql("INSERT INTO " . $table_prefix . "config (config_name, config_value) VALUES ('default_lang', 'english')");

update_config(array(
    'board_email'	=> 'admin@example.com',
    'script_path'	=> '/',
    'server_port'	=> $server_port,
    'server_name'	=> $server_name,
    'cookie_secure'	=> $use_https,
));

sql("UPDATE " . $table_prefix . "users 
    SET username = '" . str_replace("\'", "''", $admin_name) . "',
    user_password='" . str_replace("\'", "''", md5($admin_pass1)) . "',
    user_lang = 'english',
    user_email='" . str_replace("\'", "''", 'admin@example.com') . "'
    WHERE username = 'Admin'");

sql("UPDATE " . $table_prefix . "users SET user_regdate = " . time());

echo "Install done.\n";
