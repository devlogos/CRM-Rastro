<?php

/**
 * companies model
 *
 * @author Giovane Pessoa
 */

namespace App\Models;

use App\Database;

class CompaniesModel
{
    public static function create()
    {
    }

    public static function read($id = null)
    {
        $where = '';

        if (!empty($id)) {
            $where = 'AND id = :id';
        } else {
            $where = 'AND id = 0';
        }

        $sql = sprintf(
            "SELECT id,name,secretkey FROM companies WHERE active = 1 %s",
            $where
        );

        $database = new database();

        $stmt = $database->prepare($sql);

        if (!empty($where)) {
            $stmt->bindParam(':id', $id, \PDO::PARAM_INT);
        }

        $stmt->execute();

        $result = $stmt->fetchAll(\PDO::FETCH_ASSOC);

        return $result;
    }

    public static function update()
    {
    }

    public static function delete()
    {
    }
}
