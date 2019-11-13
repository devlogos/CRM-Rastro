<?php

/**
 * users model
 *
 * @author Giovane Pessoa
 */

namespace App\Models;

use App\Database;

class UsersModel
{
    public static function create() {
        
    }

    public static function read() {
        
    }

    public static function readUsersAuthentication($email) {
        $sql = "SELECT B.secretkey,A.url_image AS user_url_image,A.id AS user_id,A.name AS user_name,A.email AS user_email,A.hash,B.id AS user_company_id,B.name AS user_company_name, IFNULL((SELECT GROUP_CONCAT(DISTINCT C.id SEPARATOR ',') FROM users_sectors X LEFT JOIN users B ON X.user_id = B.id LEFT JOIN sectors C ON X.sector_id = C.id WHERE X.user_id = A.id), 0) AS sectors_id, IFNULL((SELECT GROUP_CONCAT(DISTINCT C.name SEPARATOR ',') FROM users_sectors X LEFT JOIN users B ON X.user_id = B.id LEFT JOIN sectors C ON X.sector_id = C.id WHERE X.user_id = A.id), 0) AS sectors_name, IFNULL((SELECT GROUP_CONCAT(DISTINCT X.status_id SEPARATOR ',') FROM status_users_sectors X INNER JOIN users_sectors Y ON X.user_sector_id = Y.Id WHERE Y.user_id = A.id), 0) AS status_id FROM users A INNER JOIN companies B ON A.company_id = B.id WHERE A.email = :email AND A.active = 1 AND B.active = 1";

        $database = new Database;

        $stmt = $database->prepare($sql);
        $stmt->bindParam(':email', $email);
        
        $stmt->execute();

        $result = $stmt->fetchAll(\PDO::FETCH_ASSOC);
        
        return $result;
    }
    
    public static function readUsersPermissions($id, $column) {
        $sql = "SELECT A.{$column} FROM user_permissions A WHERE A.user_id = :id";

        $database = new database;

        $stmt = $database->prepare($sql);
        $stmt->bindParam(':id', $id, \PDO::PARAM_INT);
         
        $stmt->execute();

        $result = $stmt->fetchAll(\PDO::FETCH_ASSOC);
        
        return $result;
    }
    
    public static function readColumnsUsersPermissions() {
        $sql = "SELECT COLUMN_NAME FROM INFORMATION_SCHEMA.COLUMNS WHERE TABLE_SCHEMA = :schema AND TABLE_NAME = 'user_permissions' AND (COLUMN_NAME <> 'Id' AND COLUMN_NAME <> 'user_id')";

        $database = new database;

        $stmt = $database->prepare($sql);
        $stmt->bindValue(':schema', MYSQL_DBNAME);
         
        $stmt->execute();

        $result = $stmt->fetchAll(\PDO::FETCH_ASSOC);
        
        return $result;
    }

    public static function update() {
        
    }

    public static function delete() {
        
    }
}