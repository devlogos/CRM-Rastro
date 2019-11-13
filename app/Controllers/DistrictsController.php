<?php

/**
 * districts controller
 *
 * @author Giovane Pessoa
 */

namespace App\Controllers;

use App\Models\DistrictsModel;

class DistrictsController
{
    public function view() {
        \App\View::make('Districts/View');
    }

    public function create() {
        
    }

    public function read($cityId) {
        // crud task for selection in the database
        $states = DistrictsModel::readDisctricts($cityId);

        if (count($states) > 0) {
            foreach ($states as $item) {
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

    public function readDistrictsForSeller($sellerId) {
        // crud task for selection in the database
        $districts = DistrictsModel::readDistrictsForSeller($sellerId);

        if (count($districts) > 0) {
            foreach ($districts as $item) {
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