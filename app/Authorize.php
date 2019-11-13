<?php

/**
 * authorize settings
 *
 * @author Giovane Pessoa
 */

namespace App;

use App\Models\CompaniesModel;

class Authorize extends \Firebase\JWT\JWT
{
    public static function create($key) {
        $payload = array(
            "iss" => DOMAIN,
            "iat" => time(),
            "exp" => time() + (360 * 260)
        );
        try {
            $jwt = parent::encode($payload, $key);

            return $jwt;
        } catch (Exception $e) {
            return null;
        }
    }

    public static function read($token, $secretKey, $companyId) {
        // crud task for selection in the database
        $companies = CompaniesModel::read($companyId);

        if (count($companies) == 0) {
            return false;
        }

        try {
            if (md5($companies[0]['name']) == $secretKey) {
                // uses a JWT decoding function
                $decoded = parent::decode($token, $companies[0]['secretkey'], array('HS256'));

                if (isset($decoded)) {
                    return true;
                } else {
                    return false;
                }
            } else {
                return false;
            }
        } catch (\Exception $ex) {
            return false;
        }
    }
}