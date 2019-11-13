<?php

/**
 * clients controller
 *
 * @author Giovane Pessoa
 */

namespace App\Controllers;

use App\Models\ClientsModel;

class ClientsController
{
    private $creationDate;
    private $updateDate;
    private $companyId;
    private $name;
    private $email;
    private $telephone;

    function setCreationDate($creationDate) {
        $this->creationDate = $creationDate;
    }

    function setUpdateDate($updateDate) {
        $this->updateDate = $updateDate;
    }

    function setCompanyId($companyId) {
        $this->companyId = $companyId;
    }

    function setName($name) {
        $this->name = $name;
    }

    function setEmail($email) {
        $this->email = $email;
    }

    function setTelephone($telephone) {
        $this->telephone = $telephone;
    }

    public function view() {
        \App\View::make('Clients/View');
    }

    public function create() {
        // crud task for insertion into the database
        return ClientsModel::create($this->creationDate, $this->updateDate, $this->companyId, $this->name, $this->email, $this->telephone);
    }

    public function read($companyId, $id) {
        // crud task for selection in the database
        $clients = ClientsModel::read($companyId, $id);

        if (count($clients) > 0) {
            foreach ($clients as $item) {
                $id = $item['id'];
                $name = $item['name'];
                $email = $item['email'];
                $telephone = $item['telephone'];

                $json[] = array(
                    'id' => $id,
                    'name' => $name,
                    'email' => $email,
                    'telephone' => $telephone
                );
            }

            return $json;
        }
    }

    public function update() {
        
    }

    public function delete($id) {
        // crud task for removal in the database
        ClientsModel::delete($id);
    }
}