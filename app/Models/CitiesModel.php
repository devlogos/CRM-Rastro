<?php

/**
 * cities model
 *
 * @author Giovane Pessoa
 */

namespace App\Models;

use App\Database;

class CitiesModel
{
    public static function create() {
        
    }

    public static function read($stateId) {
        $name = 'A.name';
        $where = '';
        
        $states = explode(',',$stateId);        

        if (count($states) == 1) {
            if (!empty($stateId)) {
                $where = sprintf('WHERE A.state_id = %s', $stateId);
            }
        }
        else {
            for ($i=0; $i < count($states); $i++){
                if ($i == 0){
                    $where .= sprintf('WHERE (A.state_id = %s', $states[$i]);
                }
                else{
                    $where .= sprintf(' OR A.state_id = %s', $states[$i]);
                }
            }
            
            $name = "CONCAT(B.initials,'-',A.name) AS name";
            $where .= ')';
        }

        $sql = sprintf("SELECT A.id,%s FROM cities A INNER JOIN states B ON A.state_id = B.id  %s", $name, $where);                

        $database = new database;

        $stmt = $database->prepare($sql);

        $stmt->execute();

        $result = $stmt->fetchAll(\PDO::FETCH_ASSOC);

        return $result;
    }

    public static function update() {
        
    }

    public static function delete() {
        
    }
}