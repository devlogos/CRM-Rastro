<?php

/**
 * districts model
 *
 * @author Giovane Pessoa
 */

namespace App\Models;

use App\Database;

class DistrictsModel
{
    public static function create()
    {
    }

    public static function read()
    {
    }

    public static function readCities($stateId)
    {
        $where = '';

        if (!empty($stateId)) {
            $where = 'WHERE state_id = :state_id';
        }

        $sql = sprintf("SELECT id,name FROM cities %s", $where);

        $database = new database();

        $stmt = $database->prepare($sql);

        if (!empty($where)) {
            $stmt->bindParam(':state_id', $stateId, \PDO::PARAM_INT);
        }

        $stmt->execute();

        $result = $stmt->fetchAll(\PDO::FETCH_ASSOC);

        return $result;
    }

    public static function readDisctricts($cityId)
    {
        $where = '';

        if (!empty($cityId)) {
            $where = 'WHERE city_id = :city_id';
        }

        $sql = sprintf("SELECT id,name FROM districts %s", $where);

        $database = new database();

        $stmt = $database->prepare($sql);

        if (!empty($where)) {
            $stmt->bindParam(':city_id', $cityId, \PDO::PARAM_INT);
        }

        $stmt->execute();

        $result = $stmt->fetchAll(\PDO::FETCH_ASSOC);

        return $result;
    }

    public static function readDistrictsForSeller($sellerId)
    {
        $sql =
            'SELECT D.id, D.name FROM sellers_cities A LEFT JOIN sellers B ON A.seller_id = B.id LEFT JOIN cities C ON A.city_id = C.Id INNER JOIN districts D ON C.id = D.city_id WHERE B.id = :sellerid AND A.active = 1';

        $database = new database();

        $stmt = $database->prepare($sql);
        $stmt->bindParam(':sellerid', $sellerId, \PDO::PARAM_INT);

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
