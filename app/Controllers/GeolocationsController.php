<?php

/**
 * geolocations controller
 *
 * @author Giovane Pessoa
 */

namespace App\Controllers;

use App\Models\GeolocationsModel;

class GeolocationsController
{
    private $creationDate;
    private $userAgent;
    private $sellerId;
    private $saleId;
    private $latitude;
    private $longitude;
    
    public function setCreationDate($creationDate) {
        $this->creationDate = $creationDate;
    }
    
    public function setUserAgent($userAgent) {
        $this->userAgent = $userAgent;
    }

    public function setSellerId($sellerId) {
        $this->sellerId = $sellerId;
    }
    
    public function setSaleId($saleId) {
        $this->saleId = $saleId;
    }

    public function setLatitude($latitude) {
        $this->latitude = str_replace(",", ".", $latitude);
    }

    public function setLongitude($longitude) {
        $this->longitude = str_replace(",", ".", $longitude);
    }
    
    public function view() {
        \App\View::make('Geolocations/View');
    }

    public function create() {
        // crud task for insertion into the database
        return GeolocationsModel::create($this->creationDate,$this->userAgent,$this->sellerId,$this->saleId ? $this->saleId: null,$this->latitude,$this->longitude);
    }

    public function read($companyId, $dates, $type, $sellerId) {
        // crud task for selection in the database
        $geolocations = GeolocationsModel::read($companyId, $dates, $type, $sellerId);

        if (count($geolocations) > 0) {
            foreach ($geolocations as $item) {
                $creationDate = $item['creation_date'];
                $userAgent = $item['user_agent'];
                $sale = explode(',', $item['sale']);
                $latitude = $item['latitude'];
                $longitude = $item['longitude'];
                $statusColor = $item['status_color'];
                
                $jsonSale = null;
                
                foreach ($sale as $saleItem) {
                    $jsonSale[] = $saleItem;
                }

                $json[] = array(
                    'creation_date' => "{$creationDate}",
                    'user_agent' => "{$userAgent}",
                    'sale' => $jsonSale,
                    'latitude' => "{$latitude}",
                    'longitude' => "{$longitude}",
                    'status_color' => "{$statusColor}"
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