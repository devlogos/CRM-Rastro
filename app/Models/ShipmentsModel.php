<?php

/**
 * shipments model
 *
 * @author Giovane Pessoa
 */

namespace App\Models;

use App\Database;

class ShipmentsModel
{
    public static function create()
    {
    }

    public static function read()
    {
    }

    public static function readStatusId($shippingId)
    {
        $sql =
            'SELECT B.id AS status_id FROM status_shipments A LEFT JOIN status B ON A.status_id = B.id LEFT JOIN shipments C ON A.shipping_id = C.id WHERE C.id = :id';

        $database = new database();

        $stmt = $database->prepare($sql);
        $stmt->bindParam(':id', $shippingId, \PDO::PARAM_INT);

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
