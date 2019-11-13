<?php

/**
 * products controller
 *
 * @author Giovane Pessoa
 */

namespace App\Controllers;

use App\Models\ProductsModel;

class ProductsController
{
    public function listView() {        
        \App\View::make('Products/List');
    }

    public function create() {
        
    }

    public function read() {
        
    }
    
    public function readProducts($categoryId, $id) {        
        // crud task for selection in the database
        $products = ProductsModel::readProducts($categoryId, $id);
        
        if (count($products) > 0) {
            foreach ($products as $item) {
                $id = $item['id'];
                $name = $item['name'];
                $description = $item['description'];                                
                $media_image = DOMAIN_UNSAFE . '/media/images/products/' . $item['media_image'];
                                
                $media_video = $item['media_video'] ? $item['media_video'] : DOMAIN_UNSAFE . '/media/video/products/' . $item['media_video'];

                $json[] = array(
                    'id' => "{$id}",
                    'category_id' => "{$categoryId}",
                    'name' => "{$name}",
                    'description' => "{$description}",
                    'media_image' => "{$media_image}",
                    'media_video' => "{$media_video}"
                );
            }
            return $json;
        }
    }

    public function readCategories($companyId, $id) {
        // crud task for selection in the database
        $categories = ProductsModel::readCategories($companyId, $id);

        if (count($categories) > 0) {
            foreach ($categories as $item) {
                $id = $item['id'];
                $name = $item['name'];
                $json[] = array(
                    'id' => "{$id}",
                    'name' => "{$name}"
                );
            }
            return $json;
        }
    }

    public function update() {
        
    }

    public function delete() {
        
    }
}