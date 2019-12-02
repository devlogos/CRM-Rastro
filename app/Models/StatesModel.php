<?php

/**
 * states model
 *
 * @author Giovane Pessoa
 */

namespace App\Models;

use App\Database;

class StatesModel
{
    public static function create()
    {
    }

    public static function read($id = null)
    {
        $where = '';

        if (!empty($id)) {
            $where = 'WHERE id = :id';
        }

        $sql = sprintf("SELECT id,name,initials FROM states %s", $where);

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
