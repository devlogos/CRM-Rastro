<?php

/**
 * sellers model
 *
 * @author Giovane Pessoa
 */

namespace App\Models;

use App\Database;

class SellersModel
{
    public static function create(
        $creationDate,
        $updateDate,
        $companyId,
        $name,
        $imageProfile,
        $email,
        $telephone,
        $user,
        $password,
        $cityId,
        $recordingTime,
        $sampleRate,
        $bitsPerSample,
        $sendAfterSale
    ) {
        $database = new database();

        $sellerId = 0;

        try {
            // consult the existence of the seller by email
            $sql = sprintf(
                "SELECT email FROM sellers WHERE email = '%s' AND IFNULL(email,0) <> 0 LIMIT 0,1",
                $email
            );

            $stmt = $database->prepare($sql);
            $stmt->execute();

            $result = $stmt->fetchAll(\PDO::FETCH_ASSOC);

            if (!empty($result)) {
                return -1;
            }

            // consult the existence of the seller by user
            $sql = sprintf(
                "SELECT user FROM sellers WHERE user = '%s' LIMIT 0,1",
                $user
            );

            $stmt = $database->prepare($sql);
            $stmt->execute();

            $result = $stmt->fetchAll(\PDO::FETCH_ASSOC);

            if (!empty($result)) {
                return -2;
            }

            // insert seller
            $sql =
                'INSERT INTO sellers (creation_date,update_date,company_id,name,url_image,email,telephone,user,password,hash,active) VALUES (:creation_date,:update_date,:company_id,:name,:url_image,:email,:telephone,:user,:password,:hash,:active);';

            $stmt = $database->prepare($sql);
            $stmt->bindParam(':creation_date', $creationDate);
            $stmt->bindParam(':update_date', $updateDate);
            $stmt->bindParam(':company_id', $companyId);
            $stmt->bindParam(':name', $name);
            $stmt->bindParam(':url_image', $imageProfile);
            $stmt->bindParam(':email', $email);
            $stmt->bindParam(':telephone', $telephone);
            $stmt->bindParam(':user', $user);
            $stmt->bindParam(':password', $password);
            $stmt->bindValue(
                ':hash',
                password_hash($password, PASSWORD_ARGON2I)
            );
            $stmt->bindValue(':active', 1);

            $stmt->execute();

            $sellerId = $database->lastInsertId();
        } catch (\PDOException $ex) {
            return -3;
        }

        $error = array();

        try {
            // insert seller settings
            $sql =
                'INSERT INTO seller_settings (id,recording_time,sample_rate,bits_per_sample,send_after_sale) VALUES (:id,:recording_time,:sample_rate,:bits_per_sample,:send_after_sale);';

            $stmt = $database->prepare($sql);
            $stmt->bindParam(':id', $sellerId);
            $stmt->bindParam(':recording_time', $recordingTime);
            $stmt->bindParam(':sample_rate', $sampleRate);
            $stmt->bindParam(':bits_per_sample', $bitsPerSample);
            $stmt->bindParam(':send_after_sale', $sendAfterSale);

            $stmt->execute();

            foreach ($cityId as $item) {
                if (!self::createCities($sellerId, $item)) {
                    array_push($error, $item);
                }
            }

            return $sellerId;
        } catch (\PDOException $ex) {
            if (count($error) > 0) {
                return -5;
            } else {
                return -4;
            }
        }
    }

    public static function createCities($sellerId, $cityId)
    {
        $database = new database();

        try {
            // insert seller cities
            $sql =
                'INSERT INTO sellers_cities (seller_id,city_id,active) VALUES (:seller_id,:city_id,:active);';

            $stmt = $database->prepare($sql);
            $stmt->bindParam(':seller_id', $sellerId);
            $stmt->bindParam(':city_id', $cityId);
            $stmt->bindValue(':active', 1);

            $stmt->execute();

            return true;
        } catch (\PDOException $ex) {
            return false;
        }
    }

    public static function read($companyId, $id = null)
    {
        $where = '';

        if (!empty($id)) {
            $where = 'AND A.id = :id';
        }

        $sql = sprintf(
            "SELECT A.id,A.url_image,A.name,A.email,A.telephone,A.user,A.password,(SELECT GROUP_CONCAT(DISTINCT Z.id) FROM sellers_cities X INNER JOIN cities Y ON X.city_id = Y.id INNER JOIN states Z ON Y.state_id = Z.id WHERE X.seller_id = A.id GROUP BY X.seller_id) AS state_id,(SELECT GROUP_CONCAT(DISTINCT X.city_id) FROM sellers_cities X WHERE X.seller_id = A.id GROUP BY X.seller_id) AS city_id,B.recording_time,B.sample_rate,B.bits_per_sample,B.send_after_sale,A.firebase_token FROM sellers A INNER JOIN seller_settings B ON A.id = B.id WHERE A.active = 1 AND A.company_id = :companyid %s ORDER BY A.id DESC",
            $where
        );

        $database = new database();

        $stmt = $database->prepare($sql);
        $stmt->bindParam(':companyid', $companyId, \PDO::PARAM_INT);

        if (!empty($where)) {
            $stmt->bindParam(':id', $id, \PDO::PARAM_INT);
        }

        $stmt->execute();

        $result = $stmt->fetchAll(\PDO::FETCH_ASSOC);

        return $result;
    }

    public static function readSellersAuthentication($user)
    {
        $sql =
            'SELECT A.id,B.id AS companyid,B.secretkey,A.url_image, A.name,A.email,A.telephone,A.user,A.password,A.hash,C.recording_time,C.stop_on_silence,C.sample_rate,C.channel_count,C.bits_per_sample,C.send_after_sale FROM sellers A INNER JOIN companies B ON A.company_id = B.id INNER JOIN seller_settings C ON A.id = C.id WHERE A.user = :user AND A.active = 1 AND B.active = 1';

        $database = new database();

        $stmt = $database->prepare($sql);
        $stmt->bindParam(':user', $user);

        $stmt->execute();

        $result = $stmt->fetchAll(\PDO::FETCH_ASSOC);

        return $result;
    }

    public static function readSellersGoals($id)
    {
        $database = new database();

        // get company id

        $sql =
            'SELECT sellers.company_id FROM sellers WHERE sellers.id = :sellerid';
        $stmt = $database->prepare($sql);
        $stmt->bindParam(':sellerid', $id, \PDO::PARAM_INT);
        $stmt->execute();
        $result = $stmt->fetchAll();

        $companyId = 0;

        if (!empty($result)) {
            $companyId = $result[0]['company_id'];
        }

        // get type period

        $sql =
            'SELECT sales_goal_settings.type_period FROM sales_goal_settings WHERE sales_goal_settings.company_id = :companyid';
        $stmt = $database->prepare($sql);
        $stmt->bindParam(':companyid', $companyId, \PDO::PARAM_INT);
        $stmt->execute();
        $result = $stmt->fetchAll();

        $typePeriod = 0;

        if (!empty($result)) {
            $typePeriod = $result[0]['type_period'];
        }

        // get its finished

        $sql =
            'SELECT status.id as its_finished FROM status WHERE status.its_finished = 1 LIMIT 0,1';
        $stmt = $database->prepare($sql);
        $stmt->execute();
        $result = $stmt->fetchAll();

        $itsFinished = 0;

        if (!empty($result)) {
            $itsFinished = $result[0]['its_finished'];
        }

        // get sales amount

        $sql = "SELECT COUNT(sales.id) AS sales_amount FROM sales WHERE {$typePeriod}(sales.update_date) = {$typePeriod}(CURDATE()) AND sales.seller_id = :sellerid";
        $stmt = $database->prepare($sql);
        $stmt->bindParam(':sellerid', $id);
        $stmt->execute();
        $result = $stmt->fetchAll();

        $salesAmount = 0;

        if (!empty($result)) {
            $salesAmount = $result[0]['sales_amount'];
        }

        // get total sales made

        $sql = "SELECT COUNT(sales.id) AS total_sales_made FROM sales WHERE {$typePeriod}(sales.update_date) = {$typePeriod}(CURDATE()) AND sales.seller_id = :sellerid AND sales.status_id = :itsfinished";
        $stmt = $database->prepare($sql);
        $stmt->bindParam(':sellerid', $id);
        $stmt->bindParam(':itsfinished', $itsFinished);
        $stmt->execute();
        $result = $stmt->fetchAll();

        $totalSalesMade = 0;

        if (!empty($result)) {
            $totalSalesMade = $result[0]['total_sales_made'];
        }

        // get challenge

        $sql =
            'SELECT sales_goal_settings.challenge FROM sales_goal_settings WHERE sales_goal_settings.company_id = :companyid';
        $stmt = $database->prepare($sql);
        $stmt->bindParam(':companyid', $companyId);
        $stmt->execute();
        $result = $stmt->fetchAll();

        $challenge = 0;

        if (!empty($result)) {
            $challenge = $result[0]['challenge'];
        }

        // get goals message

        $sql =
            'SELECT A.icon,A.percent,A.message FROM goal_setting_messages A INNER JOIN sales_goal_settings B ON A.goal_setting_id = B.id WHERE B.company_id = :companyid ORDER BY A.percent ASC';
        $stmt = $database->prepare($sql);
        $stmt->bindParam(':companyid', $companyId);
        $stmt->execute();
        $result = $stmt->fetchAll();

        $percentGoal = 0;
        $iconGoal = null;
        $messageGoal = null;

        $prevPercent = 0;

        if (!empty($result)) {
            foreach ($result as $item) {
                $percent = (100 / $challenge) * $totalSalesMade;

                if (
                    (float) $percent >= $prevPercent &&
                    (float) $percent <= (float) $item['percent']
                ) {
                    $percentGoal = $percent;
                    $iconGoal = $item['icon'];
                    $messageGoal = $item['message'];

                    break;
                }

                $prevPercent = (float) $item['percent'];
            }
        }

        $array = array();

        array_push($array, $salesAmount);
        array_push($array, $totalSalesMade);
        array_push($array, $challenge);
        array_push($array, $percentGoal);
        array_push($array, $iconGoal);
        array_push($array, $messageGoal);

        return $array;
    }

    public static function update()
    {
    }

    /* public static function updateSeller($name, $email, $telephone, $password, $id) { */

    public static function updateSeller($password, $id)
    {
        try {
            $database = new database();

            /*
              $sql = 'SELECT email FROM sellers WHERE email = :email AND id <> :id';
              $stmt = $database->prepare($sql);
              $stmt->bindParam(':email', $email);
              $stmt->bindParam(':id', $id, \PDO::PARAM_INT);
              $stmt->execute();
              $result = $stmt->fetchAll();

              if (!empty($result)) {
              return 0;
              } else {
              $sql = 'UPDATE sellers SET name = :name, email = :email, telephone = :telephone, hash = :hash WHERE id = :id';

              $database = new database();

              $stmt = $database->prepare($sql);
              $stmt->bindParam(':name', $name);
              $stmt->bindParam(':email', $email);
              $stmt->bindParam(':telephone', $telephone);
              $stmt->bindParam(':hash', $password);
              $stmt->bindParam(':id', $id, \PDO::PARAM_INT);

              $stmt->execute();

              return 1;
              }
             */

            /* $sql = 'UPDATE sellers SET name = :name, email = :email, telephone = :telephone, hash = :hash WHERE id = :id'; */
            $sql = 'UPDATE sellers SET hash = :hash WHERE id = :id';

            $database = new database();

            $stmt = $database->prepare($sql);
            //$stmt->bindParam(':name', $name);
            //$stmt->bindParam(':email', $email);
            //$stmt->bindParam(':telephone', $telephone);
            $stmt->bindParam(':hash', $password);
            $stmt->bindParam(':id', $id, \PDO::PARAM_INT);

            $stmt->execute();

            return 1;
        } catch (\PDOException $ex) {
            return 0;
        }
    }

    public static function updateFirebaseToken($firebaseToken, $sellerId)
    {
        try {
            $sql =
                'UPDATE sellers SET firebase_token = :firebasetoken WHERE id = :id';

            $database = new database();

            $stmt = $database->prepare($sql);
            $stmt->bindParam(':firebasetoken', $firebaseToken);
            $stmt->bindParam(':id', $sellerId, \PDO::PARAM_INT);

            $stmt->execute();

            return 1;
        } catch (\PDOException $ex) {
            return 0;
        }
    }

    public static function delete()
    {
    }
}
