<?php

/**
 * status controller
 *
 * @author Giovane Pessoa
 */

namespace App\Controllers;

use App\Models\StatusModel;

class StatusController
{
    public function view() {
        \App\View::make('Status/View');
    }

    public function create() {
        
    }

    public function read($companyId,$id) {
        // crud task for selection in the database
        $status = StatusModel::read($companyId,$id);

        if (count($status) > 0) {
            foreach ($status as $item) {
                $id = $item['id'];
                $name = $item['name'];
                $color = $item['color'];
                $finished = $item['finished'] ? true : false;

                $json[] = array(
                    'id' => $id,
                    'name' => "{$name}",
                    'color' => "{$color}",
                    'finished' => $finished
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