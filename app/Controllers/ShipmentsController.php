<?php

/**
 * shipments controller
 *
 * @author Giovane Pessoa
 */

namespace App\Controllers;

use App\Models\ShipmentsModel;

class ShipmentsController
{
    public function view() {
        \App\View::make('Shipments/View');
    }

    public function create() {
        
    }
    
    public function read() {
        
    }

    public function readStatusId($shippingId) {
        // crud task for selection in the database
        $shipping = ShipmentsModel::readStatusId($shippingId)[0];

        return $shipping['status_id'];
    }

    public function update() {
        
    }

    public function delete() {
        
    }
}