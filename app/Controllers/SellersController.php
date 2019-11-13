<?php

/**
 * sellers controller
 *
 * @author Giovane Pessoa
 */

namespace App\Controllers;

use App\Models\SellersModel;
use App\Models\StatesModel;
use App\Authorize;

class SellersController
{
    private $sellerId;
    private $creationDate;
    private $updateDate;
    private $companyId;
    private $name;
    private $imageProfile;
    private $email;
    private $telephone;
    private $user;
    private $password;    
    private $cityId;
    private $recordingTime;
    private $sampleRate;
    private $bitsPerSample;
    private $sendAfterSale;    
    
    function setSellerId($sellerId){
        $this->sellerId = $sellerId;
    }
    
    function setCreationDate($creationDate) {
        $this->creationDate = $creationDate;
    }

    function setUpdateDate($updateDate) {
        $this->updateDate = $updateDate;
    }
    
    function setCompanyId($companyId){
        $this->companyId = $companyId;
    }
    
    function setName($name){
        $this->name = $name;
    }
    
    function setImageProfile($imageProfile){
        $this->imageProfile = $imageProfile;
    }
    
    function getImageProfile(){
        return $this->imageProfile;
    }
    
    function setEmail($email){
        $this->email = $email;
    }
    
    function setTelephone($telephone){
        $this->telephone = $telephone;
    }
    
    function setUser($user){
        $this->user = $user;
    }
    
    function setPassword($password){
        $this->password = $password;
    }
    
    function setCityId($cityId){
        $this->cityId = $cityId;
    }
    
    function setRecordingTime($recordingTime){
        $this->recordingTime = $recordingTime;
    }
    
    function setSampleRate($sampleRate){
        $this->sampleRate = $sampleRate;
    }
    
    function setBitsPerSample($bitsPerSample){
        $this->bitsPerSample = $bitsPerSample;
    }
    
    function setSendAfterSale($sendAfterSale){
        $this->sendAfterSale = $sendAfterSale;
    }

    public function view() {
        session_start();

        if (!isset($_SESSION['USER_COMPANY_ID'])) {
            header(sprintf('Location: %s', DOMAIN));

            exit;
        }
        
        $states = StatesModel::read();
        
        \App\View::make('Sellers/List', ['states' => $states]);
    }

    public function create() {
        $chr = "/@|&|\?|!|\./";
        preg_match_all($chr, $this->password, $resultValpass);

        if (count($resultValpass[0]) === 0) {
            exit(alert(3, 'Utilize em sua senha, elementos como (<strong>@, &, ?, !</strong> ou <strong>.</strong>)!'));
        } else {
            // crud task for insertion into the database
            $result = SellersModel::create($this->creationDate, $this->updateDate, $this->companyId, $this->name, $this->imageProfile, $this->email, $this->telephone, $this->user, $this->password, $this->cityId, $this->recordingTime, $this->sampleRate, $this->bitsPerSample, $this->sendAfterSale);

            if ($result == -1) {
                exit(alert(3, 'E-mail existente!'));
            } else if ($result == -2) {
                exit(alert(3, 'Usuário existente!'));
            } else {
                return $result;
            }
        }
    }
    
    public function createNotification($title, $message, $firebaseToken) {
        try {
            $data = array
                (
                'to' => $firebaseToken,
                'notification' => array
                    (
                    'title' => $title,
                    'body' => $message
                )
            );

            $headers = array
                (
                'Authorization: key=' . API_ACCESS_KEY,
                'Content-Type: application/json'
            );

            $ch = curl_init();
            curl_setopt($ch, CURLOPT_URL, 'https://fcm.googleapis.com/fcm/send');
            curl_setopt($ch, CURLOPT_POST, true);
            curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
            curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
            $result = curl_exec($ch);

            if ($result === FALSE) {
                return false;
            } else {
                return true;
            }
        } catch (Exception $ex) {
            return false;
        }
    }

    public function read($companyId, $id) {
        // crud task for selection in the database
        $sellers = SellersModel::read($companyId, $id);

        if (count($sellers) > 0) {
            foreach ($sellers as $item) {
                $id = $item['id'];
                $urlImage = $item['url_image'] ? $item['url_image'] : null;
                $name = $item['name'];
                $email = $item['email'] ? $item['email'] : 'Indefinido';
                $telephone = $item['telephone'] ? $item['telephone'] : 'Indefinido';
                $user = $item['user'];
                $password = $item['password'];
                $states = explode(',', $item['state_id']);
                $cities = explode(',', $item['city_id']);
                $recordingTime = $item['recording_time'];
                $sampleRate = $item['sample_rate'];
                $bitsPerSample = $item['bits_per_sample'];
                $sendAfterSale = $item['send_after_sale'] ? true : false;
                $firebasetoken = $item['firebase_token'] ? $item['firebase_token'] : null;

                $goals = $this->readSellersGoals($id);

                $jsonStates = null;
                foreach ($states as $state) {
                    $jsonStates[] = (int) $state;
                }

                $jsonCities = null;
                foreach ($cities as $city) {
                    $jsonCities[] = (int) $city;
                }

                $json[] = array(
                    'id' => "{$id}",
                    'url_image' => $urlImage,
                    'name' => "{$name}",
                    'email' => "{$email}",
                    'telephone' => "{$telephone}",
                    'user' => "{$user}",
                    'password' => "{$password}",
                    'state_id' => $jsonStates,
                    'city_id' => $jsonCities,
                    'recording_time' => $recordingTime,
                    'sample_rate' => $sampleRate,
                    'bits_per_sample' => $bitsPerSample,
                    'send_after_sale' => $sendAfterSale,
                    'sales_amount' => $goals['sales_amount'],
                    'total_sales_made' => $goals['total_sales_made'],
                    'challenge' => $goals['challenge'],
                    'firebase_token' => $firebasetoken
                );
            }
            return $json;
        }
    }

    public function readSellersAuthentication($user, $password, $firebaseToken) {
        // crud task for selection in the database
        $sellers = SellersModel::readSellersAuthentication($user);

        if (!empty($sellers)) {     
            $id = $sellers[0]['id'];
            $companyId = $sellers[0]['companyid'];
            $secretKey = $sellers[0]['secretkey'];            
            $urlImage = DOMAIN_UNSAFE . '/media/images/sellers/' . $sellers[0]['url_image'];
            $name = $sellers[0]['name'];
            $email = $sellers[0]['email'];
            $telephone = $sellers[0]['telephone'];
            $user = $sellers[0]['user'];
            $keyword = $sellers[0]['password'];
            $recordingTime = $sellers[0]['recording_time'];
            $stopSilence = $sellers[0]['stop_on_silence'] ? 'true' : 'false';            
            $sampleRate = $sellers[0]['sample_rate'];
            $channelCount = $sellers[0]['channel_count'];
            $bitsPerSample = $sellers[0]['bits_per_sample'];
            $sendAfterSale = $sellers[0]['send_after_sale'] ? 'true' : 'false';
            $hash = $sellers[0]['hash'];
            if (password_verify($password, $hash)) {
                $resultToken = SellersModel::updateFirebaseToken($firebaseToken, $id);
                
                $resultToken = $resultToken ? true : false;
                                
                $json = array(
                    'id' => "{$id}",
                    'company_id' => "{$companyId}",
                    'token' => Authorize::create($secretKey),
                    'secret_key' => "{$secretKey}",
                    'url_image' => "{$urlImage}",
                    'name' => "{$name}",
                    'email' => "{$email}",
                    'telephone' => "{$telephone}",
                    'user' => "{$user}",
                    'keyword' => "{$keyword}",
                    'recording_time' => "{$recordingTime}",
                    'stop_on_silence' => "{$stopSilence}",
                    'sample_rate' => $sampleRate,
                    'channel_count' => $channelCount,
                    'bits_per_sample' => $bitsPerSample,
                    'send_after_sale' => "{$sendAfterSale}",
                    'update_firebase_token' => $resultToken
                );
                return $json;
            }
        }
    }
    
    public function readSellersGoals($id) {
        // crud task for selection in the database        
        $sellers = SellersModel::readSellersGoals($id);

        $sales_amount = $sellers[0];
        $total_sales_made = $sellers[1];
        $challenge = $sellers[2];        
        $percentGoal = $sellers[3];        
        $iconGoal = $sellers[4] ? DOMAIN_UNSAFE . '/media/images/goals/' . $sellers[4] : null;
        $messageGoal = $sellers[5];

        $json = array(
            'sales_amount' => (int) $sales_amount,
            'total_sales_made' => (int) $total_sales_made,
            'challenge' => (int) $challenge,
            'percent' => (int) $percentGoal,
            'icon_goal' => "{$iconGoal}",
            'message_goal' => "{$messageGoal}",
        );

        return $json;
    }

    public function update() {
        
    }
    
    public function updateSeller() {
        // crud task for updating in the database
        /*return SellersModel::updateSeller($this->name, $this->email, $this->telephone, $this->password, $this->sellerId);*/
        return SellersModel::updateSeller($this->password, $this->sellerId);
    }

    public function delete() {
        
    }
}