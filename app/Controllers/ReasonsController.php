<?php

/**
 * reasons controller
 *
 * @author Giovane Pessoa
 */

namespace App\Controllers;

use App\Models\ReasonsModel;

class ReasonsController
{
    public function view() {
        \App\View::make('Reasons/View');
    }

    public function create() {
        
    }

    public function read($companyId, $id) {
        // crud task for selection in the database
        $reasons = ReasonsModel::read($companyId, $id);

        if (count($reasons) > 0) {
            foreach ($reasons as $item) {
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