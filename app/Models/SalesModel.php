<?php

/**
 * sales model
 *
 * @author Giovane Pessoa
 */

namespace App\Models;

use App\Database;

class SalesModel
{
    public static function create(
        $creation_date,
        $update_date,
        $code,
        $audio_time_stamp,
        $send_audio_file,
        $seller_id,
        $product_id,
        $district_id,
        $client_id,
        $note,
        $client_is_holder,
        $status_id,
        $reason_id,
        $shipping_id
    ) {
        try {
            $database = new database();

            $sql = 'SELECT * FROM sales A WHERE A.code = :code AND A.seller_id = :seller_id 
                    AND A.product_id = :product_id AND A.client_id = :client_id 
                    AND DATE_ADD(creation_date, INTERVAL 30 DAY) >= NOW()';

            $stmt = $database->prepare($sql);
            $stmt->bindParam(':code', $code);
            $stmt->bindParam(':seller_id', $seller_id);
            $stmt->bindParam(':product_id', $product_id);
            $stmt->bindParam(':client_id', $client_id);

            $stmt->execute();

            if ($stmt->rowCount() != 0) {
                return 0;
            }

            $sql =
                'INSERT INTO sales (creation_date,update_date,code,audio_time_stamp,send_audio_file,seller_id,product_id,district_id,client_id,note,client_is_holder,sector_id,status_id,reason_id,shipping_id) VALUES (:creation_date,:update_date,:code,:audio_time_stamp,:send_audio_file,:seller_id,:product_id,:district_id,:client_id,:note,:client_is_holder,:sector_id,:status_id,:reason_id,:shipping_id)';

            $stmt = $database->prepare($sql);
            $stmt->bindParam(':creation_date', $creation_date);
            $stmt->bindParam(':update_date', $update_date);
            $stmt->bindParam(':code', $code);
            $stmt->bindParam(':audio_time_stamp', $audio_time_stamp);
            $stmt->bindParam(':send_audio_file', $send_audio_file);
            $stmt->bindParam(':seller_id', $seller_id);
            $stmt->bindParam(':product_id', $product_id);
            $stmt->bindParam(':district_id', $district_id);
            $stmt->bindParam(':client_id', $client_id);
            $stmt->bindParam(':note', $note);
            $stmt->bindParam(':client_is_holder', $client_is_holder);
            $stmt->bindValue(':sector_id', SECTOR);
            $stmt->bindParam(':status_id', $status_id);
            $stmt->bindParam(':reason_id', $reason_id);
            $stmt->bindParam(':shipping_id', $shipping_id);

            $stmt->execute();

            return $database->lastInsertId();
        } catch (\PDOException $ex) {
            return 0;
        }
    }

    public static function createImageFile(
        $sale_id,
        $creation_date,
        $image_time_stamp
    ) {
        try {
            $sql =
                'INSERT INTO documents (sale_id, creation_date, image_time_stamp) VALUES (:sale_id, :creation_date, :image_time_stamp);';

            $database = new database();

            $stmt = $database->prepare($sql);
            $stmt->bindParam(':sale_id', $sale_id);
            $stmt->bindParam(':creation_date', $creation_date);
            $stmt->bindParam(':image_time_stamp', $image_time_stamp);

            $stmt->execute();

            return 1;
        } catch (\PDOException $ex) {
            return 0;
        }
    }

    public static function read(
        $companyId,
        $sellerId,
        $id = null,
        $dates,
        $status,
        $sectors,
        $limit
    ) {
        $whereSeller = '';
        $whereSale = '';
        $whereAttr = '';

        if (!empty($dates)) {
            // dates
            $inc = 0;

            if (count($dates) > 1) {
                foreach ($dates as $date) {
                    if (!empty($date)) {
                        if ($inc == 0) {
                            $dates[$inc] =
                                'AND (date(A.creation_date) >= ' . "'{$date}'";
                        } else {
                            $dates[$inc] =
                                ' AND date(A.creation_date) <= ' . "'{$date}'";
                        }
                        $inc++;
                    }
                }

                $dates = implode('', $dates);
                $dates = sprintf('%s%s', $dates, ') ');
            } else {
                $dates = dateInterval();
            }
        } else {
            $initialDate = dateInterval('-1M');
            $finalDate = dateInterval('1M');

            $dates = sprintf(
                "AND (date(A.creation_date) >= '%s' AND date(A.creation_date) <= '%s') ",
                $initialDate,
                $finalDate
            );
        }

        if (!empty($status)) {
            // status
            $inc = 0;

            if (count($status) >= 1) {
                foreach ($status as $_status) {
                    if (!empty($_status)) {
                        if ($inc == 0) {
                            $status[$inc] =
                                'AND (A.status_id = ' . "{$_status}";
                        } else {
                            $status[$inc] = ' OR A.status_id = ' . "{$_status}";
                        }
                        $inc++;
                    }
                }
                $status = implode('', $status);
                $status = sprintf('%s%s', $status, ') ');
            } else {
                $status = null;
            }
        }

        // sectors
        $inc = 0;

        if (!empty($sectors)) {
            if (count($sectors) >= 1) {
                foreach ($sectors as $sector) {
                    if (!empty($sector)) {
                        if ($inc == 0) {
                            $sectors[$inc] =
                                'AND (A.sector_id = ' . "{$sector}";
                        } else {
                            $sectors[$inc] = ' OR A.sector_id = ' . "{$sector}";
                        }
                        $inc++;
                    }
                }
            }

            $sectors = implode('', $sectors);
            $sectors = sprintf('%s%s', $sectors, ') ');
        }

        if (empty($limit)) {
            $limit = '0,50';
        } else {
            $limitBef = (int) $limit[0] == 1 ? 0 : $limit[0];
            $limitAft = $limit[1];

            $limit = $limitBef . ',' . $limitAft;
        }

        $whereAttr = ' ' . $dates . $status . $sectors;

        if (!empty($sellerId)) {
            $whereSeller = 'AND A.seller_id = :sellerId';
        }

        if (!empty($id)) {
            $whereSale = ' AND A.id = :id';
        }

        $sql = sprintf(
            "SELECT A.id,A.creation_date,A.update_date,A.code,A.audio_time_stamp,A.send_audio_file,B.Id AS seller_id,B.url_image AS seller_url_image,B.name AS seller_name,CA.id AS category_id,CA.name AS category_name,C.id AS product_id,C.name AS product_name,F.id AS district_id,F.name AS district_name,D.id AS client_id,D.name AS client_name,D.email AS client_email,D.telephone AS client_telephone,A.client_is_holder,A.note,IFNULL((SELECT id FROM sectors WHERE A.sector_id = sectors.id LIMIT 0,1),0) AS owner_id,IFNULL((SELECT name FROM sectors WHERE A.sector_id = sectors.id LIMIT 0,1),'Indefinido') AS owner_name,IFNULL((SELECT id FROM status WHERE A.status_id = status.id),0) AS status_id, IFNULL((SELECT name FROM status WHERE A.status_id = status.id),'Não definido') AS status_name, IFNULL((SELECT its_finished FROM status WHERE A.status_id = status.id),'Não definido') AS its_finished, IFNULL((SELECT its_cancelled FROM status WHERE A.status_id = status.id),'Não definido') AS its_cancelled, IFNULL((SELECT its_scheduled FROM status WHERE A.status_id = status.id),'Não definido') AS its_scheduled, IFNULL((SELECT name FROM reasons WHERE A.reason_id = reasons.id),'Não definido') AS reason_name, IFNULL((SELECT id FROM reasons WHERE A.reason_id = reasons.id),0) AS reason_id, IFNULL((SELECT name FROM reasons WHERE A.reason_id = reasons.id),'Não definido') AS reason_name, IFNULL((SELECT color FROM status WHERE A.status_id = status.id),null) AS status_color, A.shipping_id, IFNULL((SELECT XY.latitude FROM geolocations XY WHERE XY.sale_id = A.id),null) AS latitude, IFNULL((SELECT XY.longitude FROM geolocations XY WHERE XY.sale_id = A.id),null) AS longitude FROM sales A INNER JOIN sellers B ON A.seller_id = B.id INNER JOIN products C ON A.product_id = C.id RIGHT JOIN products_categories CA ON C.product_category_id = CA.id INNER JOIN clients D ON A.client_id = D.id INNER JOIN districts F ON A.district_id = F.id WHERE B.company_id = :companyid AND CA.company_id = :companyid AND D.company_id = :companyid %s%s%s ORDER BY A.creation_date DESC LIMIT %s",
            $whereSeller,
            $whereSale,
            $whereAttr,
            $limit
        );
        $sql = str_replace('  ', ' ', $sql);

        $database = new database();

        $stmt = $database->prepare($sql);
        $stmt->bindParam(':companyid', $companyId);

        if (!empty($whereSeller)) {
            $stmt->bindParam(':sellerId', $sellerId, \PDO::PARAM_INT);
        }

        if (!empty($whereSale)) {
            $stmt->bindParam(':id', $id, \PDO::PARAM_INT);
        }

        $stmt->execute();

        $result = $stmt->fetchAll(\PDO::FETCH_ASSOC);

        return $result;
    }

    public static function readImagesAsDocuments($saleId)
    {
        $sql = 'SELECT image_time_stamp FROM documents WHERE sale_id = :saleid';

        $database = new database();

        $stmt = $database->prepare($sql);
        $stmt->bindParam(':saleid', $saleId, \PDO::PARAM_INT);

        $stmt->execute();

        $result = $stmt->fetchAll(\PDO::FETCH_ASSOC);

        return $result;
    }

    public static function update(
        $update_date,
        $code,
        $audio_time_stamp,
        $send_audio_file,
        $note,
        $client_is_holder,
        $status_id,
        $reason_id,
        $shipping_id,
        $sale_id
    ) {
        try {
            $sql =
                'UPDATE sales SET 
                update_date = :update_date,
                code = :code,
                audio_time_stamp = :audio_time_stamp,
                send_audio_file = :send_audio_file,
                note = :note,
                client_is_holder = :client_is_holder,
                status_id = :status_id,
                reason_id = :reason_id,
                shipping_id = :shipping_id
                WHERE id = :id';

            $database = new database();

            $stmt = $database->prepare($sql);
            $stmt->bindParam(':update_date', $update_date);
            $stmt->bindParam(':code', $code);
            $stmt->bindParam(':audio_time_stamp', $audio_time_stamp);
            $stmt->bindParam(':send_audio_file', $send_audio_file);
            $stmt->bindParam(':note', $note);
            $stmt->bindParam(':client_is_holder', $client_is_holder);
            $stmt->bindParam(':status_id', $status_id);
            $stmt->bindParam(':reason_id', $reason_id);
            $stmt->bindParam(':shipping_id', $shipping_id);
            $stmt->bindParam(':id', $sale_id, \PDO::PARAM_INT);

            $stmt->execute();

            return $stmt->rowCount();
        } catch (\PDOException $ex) {
            return 0;
        }
    }

    public static function updateSendAudioFile($saleId)
    {
        /*
          try {
          $sql = 'UPDATE sales SET send_audio_file = 1 WHERE audio_time_stamp = :timestamp';

          $database = new database();

          $stmt = $database->prepare($sql);
          $stmt->bindParam(':timestamp', $timeStamp, \PDO::PARAM_INT);

          $stmt->execute();

          return 1;
          } catch (\PDOException $ex) {
          return 0;
          }
         */
        try {
            $sql = 'UPDATE sales SET send_audio_file = 1 WHERE id = :id';

            $database = new database();

            $stmt = $database->prepare($sql);
            $stmt->bindParam(':id', $saleId, \PDO::PARAM_INT);

            $stmt->execute();

            return 1;
        } catch (\PDOException $ex) {
            return 0;
        }
    }

    public static function updateStatus($statusId, $saleId)
    {
        try {
            $sql = 'UPDATE sales SET status_id = :statusId WHERE id = :saleId';

            $database = new database();

            $stmt = $database->prepare($sql);
            $stmt->bindParam(':statusId', $statusId);
            $stmt->bindParam(':saleId', $saleId, \PDO::PARAM_INT);

            $stmt->execute();

            return 1;
        } catch (\PDOException $ex) {
            return 0;
        }
    }

    public static function updateSector($sectorId, $saleId)
    {
        try {
            $sql = 'UPDATE sales SET sector_id = :sector_id WHERE id = :saleId';

            $database = new database();

            $stmt = $database->prepare($sql);
            $stmt->bindParam(':sector_id', $sectorId);
            $stmt->bindParam(':saleId', $saleId, \PDO::PARAM_INT);

            $stmt->execute();

            return 1;
        } catch (\PDOException $ex) {
            return 0;
        }
    }

    public static function delete()
    { }
}
