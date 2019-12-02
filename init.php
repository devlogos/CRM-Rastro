<?php

/**
 * initialization
 *
 * @author Giovane Pessoa
 */

// assets path
define('ASSETS_PATH', 'https://200.253.156.60/rastro/assets');

// base path
define('BASE_PATH', dirname(__FILE__));

// access database
define('MYSQL_HOST', '200.253.156.60');
define('MYSQL_USER', 'rastro');
define('MYSQL_PASSWORD', 'rastro.logos');
define('MYSQL_DBNAME', 'rastro');

// domain reference
define('DOMAIN', 'https://200.253.156.60/rastro');
define('DOMAIN_UNSAFE', 'http://200.253.156.60/rastro');

// sector reference
define('SECTOR', 1);

// firebase reference
define('API_ACCESS_KEY', 'AAAA0s7qT8k:APA91bHMQ-JnfeACel0Dy14wQLbBtqYBbHyXdHJfKzokrBRuetTt_znzOeWmIIdsAJ1VeijnZRsTseY9z4Z5VBCbvLJmGT8BwXY3Q4oiKjwgQajSAMQYQafZqVy0RtcFlJu2k5Ib6Rpb');

// php settings
ini_set('display_errors', true);
error_reporting(E_ALL);
date_default_timezone_set('America/Fortaleza');
