<?php

/**
 * status model
 *
 * @author Giovane Pessoa
 */

namespace App\Models;

use App\Database;

class StatusModel
{
    public static function create()
    {
    }

    public static function read($companyId, $id = null)
    {
        $where = '';

        if (!empty($id)) {
            $where = 'AND id = :id';
        }

        $sql = sprintf(
            "SELECT 0 AS id,'Todos' AS name,null AS color,0 AS finished,0 AS cancelled,0 AS scheduled UNION ALL SELECT id,name,color,its_finished AS finished,its_cancelled AS cancelled,its_scheduled AS scheduled FROM status WHERE company_id = :companyid %s",
            $where
        );

        $database = new database();

        $stmt = $database->prepare($sql);
        $stmt->bindParam(':companyid', $companyId);

        if (!empty($where)) {
            $stmt->bindParam(':id', $id, \PDO::PARAM_INT);
        }

        $stmt->execute();

        $result = $stmt->fetchAll(\PDO::FETCH_ASSOC);

        return $result;
    }

    public static function readForTrackback($companyId)
    {
        $sql =
            'SELECT C.id,C.name,C.color,C.its_finished,C.its_cancelled,C.its_scheduled FROM shipments A LEFT JOIN status_shipments B ON B.shipping_id = A.id INNER JOIN status C ON B.status_id = C.id WHERE C.company_id = :companyid';

        $database = new database();

        $stmt = $database->prepare($sql);
        $stmt->bindParam(':companyid', $companyId, \PDO::PARAM_INT);

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
