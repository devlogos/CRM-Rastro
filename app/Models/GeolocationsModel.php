<?php

/**
 * geolocations model
 *
 * @author Giovane Pessoa
 */

namespace App\Models;

use App\Database;

class GeolocationsModel
{    
    public static function create($creation_date, $user_agent, $seller_id, $sale_id, $latitude, $longitude) {
        try {
            $sql = 'INSERT INTO geolocations (creation_date,user_agent,seller_id,sale_id,latitude,longitude) VALUES (:creation_date,:user_agent,:seller_id,:sale_id,:latitude,:longitude)';

            $database = new database;

            $stmt = $database->prepare($sql);

            $stmt->bindParam(':creation_date', $creation_date);
            $stmt->bindParam(':user_agent', $user_agent);
            $stmt->bindParam(':seller_id', $seller_id);
            $stmt->bindParam(':sale_id', $sale_id);
            $stmt->bindParam(':latitude', $latitude);
            $stmt->bindParam(':longitude', $longitude);

            $stmt->execute();

            return $database->lastInsertId();
        } catch (\PDOException $ex) {
            return 0;
        }
    }

    public static function read($companyId, $dates, $type, $sellerId = null) {
        $where = '';

        if (!empty($dates)) {
            // dates
            $inc = 0;

            if (count($dates) > 1) {
                foreach ($dates as $date) {
                    if (!empty($date)) {
                        if ($inc == 0) {
                            $dates[$inc] = 'AND (A.creation_date >= ' . "'{$date}'";
                        } else {
                            $dates[$inc] = ' AND A.creation_date <= ' . "'{$date}'";
                        }
                        $inc++;
                    }
                }

                $dates = implode('', $dates);
                $dates = sprintf('%s%s', $dates, ')');
            } else {
                $dates = '';
            }
        } else {
            $initialDate = dateInterval('-1M');
            $finalDate = dateInterval('1M');

            $dates = sprintf("AND (A.creation_date >= '%s' AND A.creation_date <= '%s') ", $initialDate, $finalDate);
        }

        if (!empty($sellerId)) {
            $where = 'AND B.id = :sellerid';
        }

        if ($type == 0) {
            $sql = sprintf("SELECT DISTINCT A.creation_date,A.user_agent,(SELECT GROUP_CONCAT(XX.code,',',XX.audio_time_stamp,',',XX.send_audio_file,',',XY.name,',',XXY.name,',',XXXY.name,',',YY.name) FROM sales XX INNER JOIN products XY ON XX.product_id = XY.id INNER JOIN sellers XXY ON XX.seller_id = XXY.id INNER JOIN clients XXXY ON XX.client_id = XXXY.id INNER JOIN status YY ON XX.status_id = YY.id WHERE XX.id = A.sale_id) AS sale,A.latitude,A.longitude,IFNULL((SELECT BB.color FROM sales AA INNER JOIN status BB ON AA.status_id = BB.id WHERE A.sale_id = AA.id),'#233747') AS status_color FROM geolocations A LEFT JOIN sellers B ON A.seller_id = B.id WHERE B.company_id = :companyid %s %s", $dates, $where);
        } else if ($type == 1) {
            $sql = sprintf("SELECT DISTINCT A.creation_date,A.user_agent,(SELECT GROUP_CONCAT(XX.code,',',XX.audio_time_stamp,',',XX.send_audio_file,',',XY.name,',',XXY.name,',',XXXY.name,',',YY.name) FROM sales XX INNER JOIN products XY ON XX.product_id = XY.id INNER JOIN sellers XXY ON XX.seller_id = XXY.id INNER JOIN clients XXXY ON XX.client_id = XXXY.id INNER JOIN status YY ON XX.status_id = YY.id WHERE XX.id = A.sale_id) AS sale,A.latitude,A.longitude,'#233747' AS status_color FROM geolocations A LEFT JOIN sellers B ON A.seller_id = B.id WHERE B.company_id = :companyid AND IFNULL(A.sale_id,0) = 0 %s %s", $dates, $where);
        } else if ($type == 2) {
            $sql = sprintf("SELECT DISTINCT A.creation_date,A.user_agent,(SELECT GROUP_CONCAT(XX.code,',',XX.audio_time_stamp,',',XX.send_audio_file,',',XY.name,',',XXY.name,',',XXXY.name,',',YY.name) FROM sales XX INNER JOIN products XY ON XX.product_id = XY.id INNER JOIN sellers XXY ON XX.seller_id = XXY.id INNER JOIN clients XXXY ON XX.client_id = XXXY.id INNER JOIN status YY ON XX.status_id = YY.id WHERE XX.id = A.sale_id) AS sale,A.latitude,A.longitude,D.color AS status_color FROM geolocations A LEFT JOIN sellers B ON A.seller_id = B.id INNER JOIN sales C ON C.id = A.sale_id LEFT JOIN status D ON C.status_id = D.id WHERE B.company_id = :companyid AND D.its_finished = 1 %s %s", $dates, $where);
        } else if ($type == 3) {
            $sql = sprintf("SELECT DISTINCT A.creation_date,A.user_agent,(SELECT GROUP_CONCAT(XX.code,',',XX.audio_time_stamp,',',XX.send_audio_file,',',XY.name,',',XXY.name,',',XXXY.name,',',YY.name) FROM sales XX INNER JOIN products XY ON XX.product_id = XY.id INNER JOIN sellers XXY ON XX.seller_id = XXY.id INNER JOIN clients XXXY ON XX.client_id = XXXY.id INNER JOIN status YY ON XX.status_id = YY.id WHERE XX.id = A.sale_id) AS sale,A.latitude,A.longitude,D.color AS status_color FROM geolocations A LEFT JOIN sellers B ON A.seller_id = B.id INNER JOIN sales C ON C.id = A.sale_id LEFT JOIN status D ON C.status_id = D.id WHERE B.company_id = :companyid AND D.its_cancelled = 1 %s %s", $dates, $where);
        } else if ($type == 4) {
            $sql = sprintf("SELECT DISTINCT A.creation_date,A.user_agent,(SELECT GROUP_CONCAT(XX.code,',',XX.audio_time_stamp,',',XX.send_audio_file,',',XY.name,',',XXY.name,',',XXXY.name,',',YY.name) FROM sales XX INNER JOIN products XY ON XX.product_id = XY.id INNER JOIN sellers XXY ON XX.seller_id = XXY.id INNER JOIN clients XXXY ON XX.client_id = XXXY.id INNER JOIN status YY ON XX.status_id = YY.id WHERE XX.id = A.sale_id) AS sale,A.latitude,A.longitude,D.color AS status_color FROM geolocations A LEFT JOIN sellers B ON A.seller_id = B.id INNER JOIN sales C ON C.id = A.sale_id LEFT JOIN status D ON C.status_id = D.id WHERE B.company_id = :companyid AND D.its_scheduled = 1 %s %s", $dates, $where);
        }

        $database = new database;

        $stmt = $database->prepare($sql);
        $stmt->bindParam(':companyid', $companyId);

        if (!empty($where)) {
            $stmt->bindParam(':sellerid', $sellerId, \PDO::PARAM_INT);
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