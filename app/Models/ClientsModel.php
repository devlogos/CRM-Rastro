<?php

/**
 * clients model
 *
 * @author Giovane Pessoa
 */

namespace App\Models;

use App\Database;

class ClientsModel
{
    public static function create(
        $creation_date,
        $update_date,
        $company_id,
        $name,
        $email,
        $telephone
    ) {
        try {
            $sql =
                'INSERT INTO clients (creation_date,update_date,company_id,name,email,telephone,active) VALUES (:creation_date,:update_date,:company_id,:name,:email,:telephone,:active)';

            $database = new database();

            $stmt = $database->prepare($sql);

            $stmt->bindParam(':creation_date', $creation_date);
            $stmt->bindParam(':update_date', $update_date);
            $stmt->bindParam(':company_id', $company_id);
            $stmt->bindParam(':name', $name);
            $stmt->bindParam(':email', $email);
            $stmt->bindParam(':telephone', $telephone);
            $stmt->bindValue(':active', 1);

            $stmt->execute();

            return $database->lastInsertId();
        } catch (\PDOException $ex) {
            return 0;
        }
    }

    public static function createClientFromCRM(
        $creation_date,
        $update_date,
        $company_id,
        $name,
        $email,
        $telephone
    ) {
        try {
            $database = new database();

            $sql = 'SELECT email FROM clients WHERE email = :email';
            $stmt = $database->prepare($sql);
            $stmt->bindParam(':email', $email);
            $stmt->execute();

            $result = $stmt->fetchAll(\PDO::FETCH_ASSOC);

            if (!empty($result[0]['email'])) {
                return 1;
            }

            $sql =
                'INSERT INTO clients (creation_date,update_date,company_id,name,email,telephone,active) VALUES (:creation_date,:update_date,:company_id,:name,:email,:telephone,:active)';

            $stmt = $database->prepare($sql);

            $stmt->bindParam(':creation_date', $creation_date);
            $stmt->bindParam(':update_date', $update_date);
            $stmt->bindParam(':company_id', $company_id);
            $stmt->bindParam(':name', $name);
            $stmt->bindParam(':email', $email);
            $stmt->bindParam(':telephone', $telephone);
            $stmt->bindValue(':active', 1);

            $stmt->execute();

            return $database->lastInsertId();
        } catch (\PDOException $ex) {
            return 0;
        }
    }

    public static function read($companyId, $id = null, $email = null)
    {
        $where = '';

        if (!empty($id)) {
            $where = 'AND id = :id ';
        }

        if (!empty($email)) {
            $where = $where . 'AND email = :email';
        }

        $sql = sprintf(
            "SELECT id,name,email,telephone FROM clients WHERE company_id = :companyid %s",
            $where
        );

        $database = new database();

        $stmt = $database->prepare($sql);
        $stmt->bindParam(':companyid', $companyId, \PDO::PARAM_INT);

        if (!empty($where)) {
            $stmt->bindParam(':id', $id, \PDO::PARAM_INT);
        }

        if (!empty($email)) {
            $stmt->bindParam(':email', $email);
        }

        $stmt->execute();

        $result = $stmt->fetchAll(\PDO::FETCH_ASSOC);

        return $result;
    }

    public static function update()
    {
    }

    public static function delete($id)
    {
        $sql = 'DELETE FROM clients WHERE id = :id';

        $database = new database();

        $stmt = $database->prepare($sql);

        $stmt->bindParam(':id', $id, \PDO::PARAM_INT);

        $stmt->execute();
    }
}
