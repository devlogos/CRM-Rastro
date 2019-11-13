<?php

/**
 * reasons model
 *
 * @author Giovane Pessoa
 */

namespace App\Models;

use App\Database;

class ReasonsModel
{
    public static function create() {
        
    }

    public static function read($companyId, $id = null) {
        $where = '';

        if (!empty($id)) {
            $where = 'AND id = :id';
        }

        $sql = sprintf("SELECT id,name FROM reasons WHERE company_id = :companyid AND active = 1 %s", $where);

        $database = new database;

        $stmt = $database->prepare($sql);
        $stmt->bindParam(':companyid', $companyId);

        if (!empty($where)) {
            $stmt->bindParam(':id', $id, \PDO::PARAM_INT);
        }

        $stmt->execute();

        $result = $stmt->fetchAll(\PDO::FETCH_ASSOC);

        return $result;
    }

    public static function update() {
        
    }

    public static function delete() {
        
    }
}