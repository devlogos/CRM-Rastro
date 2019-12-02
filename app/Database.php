<?php

/**
 * database settings
 *
 * @author Giovane Pessoa
 */

namespace App;

class Database extends \PDO
{
    public function __construct(
        $dsn = null,
        $username = null,
        $password = null,
        $options = array()
    ) {
        $dsn =
            $dsn != null
                ? $dsn
                : sprintf('mysql:dbname=%s;host=%s', MYSQL_DBNAME, MYSQL_HOST);
        $username = $username != null ? $username : MYSQL_USER;
        $password = $password != null ? $password : MYSQL_PASSWORD;
        $options = array(
            parent::ATTR_ERRMODE => parent::ERRMODE_EXCEPTION,
            parent::MYSQL_ATTR_INIT_COMMAND => "SET NAMES utf8mb4"
        );

        parent::__construct($dsn, $username, $password, $options);
    }
}
