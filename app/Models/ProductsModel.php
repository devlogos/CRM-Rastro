<?php

/**
 * products model
 *
 * @author Giovane Pessoa
 */

namespace App\Models;

use App\Database;

class ProductsModel
{
    public static function create() {
        
    }

    public static function read() {
        
    }
    
    public static function readProducts($categoryId, $id = null) {
        $where = '';

        if (!empty($id)) {
            $where = 'AND id = :id';
        }

        $sql = sprintf("SELECT id,name,description,media_image,media_video FROM products WHERE product_category_id = :categoryid AND active = 1 %s", $where);

        $database = new database;

        $stmt = $database->prepare($sql);
        $stmt->bindParam(':categoryid', $categoryId);

        if (!empty($where)) {
            $stmt->bindParam(':id', $id, \PDO::PARAM_INT);
        }

        $stmt->execute();

        $result = $stmt->fetchAll(\PDO::FETCH_ASSOC);

        return $result;
    }

    public static function readCategories($companyId, $id = null) {
        $where = '';

        if (!empty($id)) {
            $where = 'AND id = :id';
        }

        $sql = sprintf("SELECT id,name FROM products_categories WHERE company_id = :companyid AND active = 1 %s", $where);

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