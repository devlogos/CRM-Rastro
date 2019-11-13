<?php

/**
 * cities controller
 *
 * @author Giovane Pessoa
 */

namespace App\Controllers;

use App\Models\CitiesModel;

class CitiesController
{
    public function view() {
        \App\View::make('Cities/View');
    }

    public function create() {
        
    }
    
    public function read($stateId) {
        // crud task for selection in the database
        $cities = CitiesModel::read($stateId);

        if (count($cities) > 0) {
            foreach ($cities as $item) {
                $id = $item['id'];
                $name = $item['name'];

                $json[] = array(
                    'id' => $id,
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