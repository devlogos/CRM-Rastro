<?php

/**
 * sales controller
 *
 * @author Giovane Pessoa
 */

namespace App\Controllers;

use App\Models\SalesModel;
use App\Models\StatusModel;
use App\Models\SectorsModel;
use App\Models\ProductsModel;
use App\Models\StatesModel;
use App\Models\DistrictsModel;
use App\Models\ClientsModel;

class SalesController
{
    private $saleId;
    private $creationDate;
    private $updateDate;
    private $code;
    private $audioTimeStamp;
    private $imageTimeStamp;
    private $sendAudioFile;
    private $sellerId;
    private $productId;
    private $districtId;
    private $clientId;
    private $note;
    private $clientIsHolder;
    private $reasonId;
    private $statusId;
    private $sectorId;
    private $shippingId;
    
    function setSaleId($saleId) {
        $this->saleId = $saleId;
    }

    function setCreationDate($creationDate) {
        $this->creationDate = $creationDate;
    }

    function setUpdateDate($updateDate) {
        $this->updateDate = $updateDate;
    }

    function setCode($code) {
        $this->code = $code;
    }
    
    function setAudioTimeStamp($audioTimeStamp) {
        $this->audioTimeStamp = $audioTimeStamp;
    }
    
    function setImageTimeStamp($imageTimeStamp) {
        $this->imageTimeStamp = $imageTimeStamp;
    }
    
    function setSendAudioFile($sendAudioFile) {
        $this->sendAudioFile = $sendAudioFile;
    }

    function setSellerId($sellerId) {
        $this->sellerId = $sellerId;
    }

    function setProductId($productId) {
        $this->productId = $productId;
    }

    function setDistrictId($districtId) {
        $this->districtId = $districtId;
    }
    
    function setClientId($clientId) {
        $this->clientId = $clientId;
    }
    
    function setNote($note) {
        $this->note = $note;
    }

    function setClientIsHolder($clientIsHolder) {
        $this->clientIsHolder = $clientIsHolder;
    }
    
    function setStatusId($statudId) {
        $this->statusId = $statudId;
    }
    
    function setSectorId($statudId) {
        $this->sectorId = $statudId;
    }
    
    function setReasonId($reasonId) {
        $this->reasonId = $reasonId;
    } 

    function setShippingId($shippingId) {
        $this->shippingId = $shippingId;
    }        
    
    public function listView() {
        session_start();

        if (!isset($_SESSION['USER_COMPANY_ID'])) {
            header(sprintf('Location: %s', DOMAIN));

            exit;
        }

        $companyId = $_SESSION['USER_COMPANY_ID'] ? $_SESSION['USER_COMPANY_ID'] : null;
        
        $status = StatusModel::read($companyId);
        
        $sectors = SectorsModel::read($companyId);
        
        $categories = ProductsModel::readCategories($companyId);
        
        $states = StatesModel::read();
        
        $clients = ClientsModel::read($companyId);
        
        $sellers = \App\Models\SellersModel::read($companyId);
        
        \App\View::make('Sales/List', [ 'status' => $status, 'sectors' => $sectors, 'categories' => $categories, 'states' => $states, 'clients' => $clients, 'sellers' => $sellers]);
    }    
    
    public function funnelView() {
        session_start();

        if (!isset($_SESSION['USER_COMPANY_ID'])) {
            header(sprintf('Location: %s', DOMAIN));

            exit;
        }

        $companyId = $_SESSION['USER_COMPANY_ID'] ? $_SESSION['USER_COMPANY_ID'] : null;

        $status = StatusModel::read($companyId);

        $sectors = SectorsModel::read($companyId);

        $categories = ProductsModel::readCategories($companyId);

        $states = StatesModel::read();

        $clients = ClientsModel::read($companyId);

        $sellers = \App\Models\SellersModel::read($companyId);

        \App\View::make('Sales/Funnel', [ 'status' => $status, 'sectors' => $sectors, 'categories' => $categories, 'states' => $states, 'clients' => $clients, 'sellers' => $sellers]);
    }
    
    public function trackbackView() {
        session_start();

        $companyId = $_SESSION['USER_COMPANY_ID'] ? $_SESSION['USER_COMPANY_ID'] : null;

        $status = StatusModel::readForTrackback($companyId);

        $sellers = \App\Models\SellersModel::read($companyId);

        \App\View::make('Sales/Trackback', ['status' => $status, 'sellers' => $sellers]);
    }

    public function create() {
        // crud task for insertion into the database
        return SalesModel::create($this->creationDate, $this->updateDate, $this->code, $this->audioTimeStamp, $this->sendAudioFile, $this->sellerId, $this->productId, $this->districtId, $this->clientId, $this->note, $this->clientIsHolder, $this->statusId, $this->reasonId, $this->shippingId);
    }
    
    public function createImageFile() {
        // crud task for insertion into the database
        return SalesModel::createImageFile($this->saleId, $this->creationDate, $this->imageTimeStamp);
    }

    public function read($companyId, $sellerId, $saleId, $dates, $status, $sectors, $limit) {
        // crud task for selection in the database
        $sales = SalesModel::read($companyId, $sellerId, $saleId, $dates, $status, $sectors, $limit);

        if (count($sales) > 0) {
            foreach ($sales as $item) {
                $id = $item['id'];
                $creation_date = $item['creation_date'];
                $update_date = $item['update_date'];
                $code = $item['code'];
                $audioTimeStamp = $item['audio_time_stamp'];
                $documents = SalesModel::readImagesAsDocuments($item['id']);
                if (count($documents) > 0) {
                    foreach ($documents as $document) {

                        $images[] = array(
                            'image' => "{$document['image_time_stamp']}"
                        );
                    }
                } else {
                    $images = array();
                }
                $imageTimeStamp = $images;
                $images = array();
                $sendAudioFile = $item['send_audio_file'] ? true : false;
                $seller_id = $item['seller_id'];
                $seller_url_image = $item['seller_url_image'] ? DOMAIN . '/media/images/sellers/' . $item['seller_url_image'] : ASSETS_PATH . '/img/seller.png';
                $seller_name = $item['seller_name'];
                $category_id = $item['category_id'];
                $category_name = $item['category_name'];
                $product_id = $item['product_id'];
                $product_name = $item['product_name'];
                $district_id = $item['district_id'];
                $district_name = $item['district_name'];
                $client_id = $item['client_id'];
                $client_name = $item['client_name'];
                $client_email = $item['client_email'];
                $client_telephone = $item['client_telephone'];
                $note = $item['note'];
                $client_is_holder = $item['client_is_holder'] ? true : false;
                $owner_id = 0;
                $owner_name = '';
                $status_id = $item['status_id'];
                $status_name = $item['status_name'];
                $its_finished = $item['its_finished'] ? true : false;                
                $its_cancelled = $item['its_cancelled'] ? true : false;                
                $reason_name = $item['reason_name'] ? true : false;
                $reason_id = $item['reason_id'];
                $reason_name = $item['reason_name'];
                $status_color = $item['status_color'];
                $shippingId = $item['shipping_id'];
                $latitude = $item['latitude'];
                $longitude = $item['longitude'];

                $json[] = array(
                    'id' => $id,
                    'creation_date' => "{$creation_date}",
                    'update_date' => "{$update_date}",
                    'code' => "{$code}",
                    'audio_path' => null,
                    'audio_time_stamp' => "{$audioTimeStamp}",
                    'image_time_stamp' => $imageTimeStamp,
                    'send_audio_file' => $sendAudioFile,
                    'seller_id' => "{$seller_id}",
                    'seller_url_image' => "{$seller_url_image}",
                    'seller_name' => "{$seller_name}",
                    'category_id' => "{$category_id}",
                    'category_name' => "{$category_name}",
                    'product_id' => "{$product_id}",
                    'product_name' => "{$product_name}",
                    'district_id' => "{$district_id}",
                    'district_name' => "{$district_name}",
                    'client_id' => "{$client_id}",
                    'client_name' => "{$client_name}",
                    'client_email' => "{$client_email}",
                    'client_telephone' => "{$client_telephone}",
                    'note' => "{$note}",
                    'client_is_holder' => $client_is_holder,
                    'owner_id' => "{$owner_id}",
                    'owner_name' => "{$owner_name}",
                    'status_id' => "{$status_id}",
                    'status_name' => "{$status_name}",
                    'its_finished' => $its_finished,
                    'its_cancelled' => $its_cancelled,
                    'reason_name' => $reason_name,
                    'reason_id' => "{$reason_id}",
                    'reason_name' => "{$reason_name}",
                    'status_color' => "{$status_color}",
                    'shipping_id' => "{$shippingId}",
                    'latitude' => (double) $latitude,
                    'longitude' => (double) $longitude
                );
            }
            return $json;
        }
    }

    public function update() {
        
    }
    
    public function updateAudioFile() {
        // crud task for updating in the database
        //return SalesModel::updateSendAudioFile($this->audioTimeStamp);
        return SalesModel::updateSendAudioFile($this->saleId);
    }

    public function updateStatus() {
        // crud task for updating in the database
        return SalesModel::updateStatus($this->statusId, $this->saleId);
    }
    
    public function updateSector() {
        // crud task for updating in the database
        return SalesModel::updateSector($this->sectorId, $this->saleId);
    }
    
    public function convertImages($paths, $time_stamp) {
        $refSizeImage = null;
        $docPerPage = 2;
        $backWitdh = 0;
        $backHeight = 0;
        $background = null;
        $nextImage = null;
        $dstXImage = 0;
        $dstYImage = 0;

        $inc = 0;

        try {
            if (count($paths) > 1) {
                $incHeight = 0;
                foreach ($paths as $item) {
                    $inc++;

                    $imagePath = DOMAIN_UNSAFE . '/' . $item;

                    if ($inc == 1) {
                        $refSizeImage = imagecreatefromjpeg($imagePath);

                        if (imagesx($refSizeImage) > imagesy($refSizeImage)) {
                            $refSizeImage = imagerotate($refSizeImage, 90, 100);
                        }

                        $backWitdh = imagesx($refSizeImage) * $docPerPage;
                        $backHeight = imagesy($refSizeImage) * (ceil(count($paths) / $docPerPage));

                        $background = imagecreatetruecolor($backWitdh, $backHeight);

                        $whiteBackground = imagecolorallocate($background, 255, 255, 255);

                        imagefill($background, 0, 0, $whiteBackground);

                        imagecopymerge($background, $refSizeImage, 0, 0, 0, 0, imagesx($refSizeImage), imagesy($refSizeImage), 100);
                    }

                    if ($inc > 1) {
                        $nextImage = imagecreatefromjpeg($imagePath);

                        if (imagesx($nextImage) > imagesy($nextImage)) {
                            $nextImage = imagerotate($nextImage, 90, 100);
                        }

                        $itemImgX = imagesx($nextImage);
                        $itemImgY = imagesy($nextImage);

                        if (($inc % $docPerPage) == 0) {
                            $dstXImage = ($itemImgX * $docPerPage) - $itemImgX;
                            $dstYImage = $incHeight;

                            $incHeight = $incHeight + $itemImgY;
                        } else {
                            $dstXImage = 0;
                            $dstYImage = ($itemImgY * $docPerPage) - $itemImgY;
                        }

                        imagecopymerge($background, $nextImage, $dstXImage, $dstYImage, 0, 0, imagesx($nextImage), imagesy($nextImage), 100);
                    }
                }
            } else {
                $onlyOneImage = imagecreatefromjpeg($paths[0]);

                if (imagesx($onlyOneImage) > imagesy($onlyOneImage)) {
                    $onlyOneImage = imagerotate($onlyOneImage, 90, 100);
                }

                $backWitdh = imagesx($onlyOneImage);
                $backHeight = imagesy($onlyOneImage);

                $background = imagecreatetruecolor($backWitdh, $backHeight);

                imagecopymerge($background, $onlyOneImage, 0, 0, 0, 0, imagesx($onlyOneImage), imagesy($onlyOneImage), 100);
            }

            header('Content-type: image/png');

            $newImgPath = 'media/documents/images/' . $time_stamp . '.png';
            $newPdfPath = 'media/documents/pdf/' . $time_stamp . '.pdf';

            imagepng($background, $newImgPath);

            // create pdf with specific size

            if (count($paths) % 2 == 0) {
                $format = 'A4-L';
            } else {
                $format = 'A4-P';
            }

            $mpdf = new \Mpdf\Mpdf([
                'format' => $format,
                'margin_left' => 0,
                'margin_right' => 0,
                'margin_top' => 0,
                'margin_bottom' => 0,
                'margin_header' => 0,
                'margin_footer' => 0,
            ]);

            //$mpdf->SetDisplayMode('fullpage');
            // resize image proportionally
            $ratio = $backWitdh / $backHeight;

            $width = $mpdf->w;
            $height = $mpdf->w / $ratio;

            $temp = imagecreatetruecolor($width, $height);

            // create image with new dimensions
            imagecopyresampled($temp, $background, 0, 0, 0, 0, (int) $width, (int) $height, $backWitdh, $backHeight);

            $posTop = ((int) $mpdf->h - (int) $height) / 2;

            $mpdf->WriteHTML('');
            $mpdf->Image($newImgPath, 0, $posTop, (int) $width, (int) $height, "png", "", true, true);
            $mpdf->Output($newPdfPath);

            unlink($newImgPath);
            
            $pathOpen = DOMAIN_UNSAFE . '/' . $newPdfPath;
            
            echo "<script>window.open(\"$pathOpen\", '_blank')</script>";

            return 1;
        } catch (Exception $ex) {
            return 0;
        }
    }

    public function delete() {
        
    }
}