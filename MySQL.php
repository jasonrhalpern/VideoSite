<?php

/**
 * @author Jason Halpern
 */

require_once('/home/simawatkinto/AppConfig.php');
require_once('Database.php');

class MySQL implements Database{

    protected  $dbh;

    public function __construct(){
        $this->connect();
    }

    public function __destruct() {
        $this->dbh->close();
    }

    public function connect(){
        $this->dbh = new mysqli(AppConfig::getConnection(), AppConfig::getUsername(),
                                AppConfig::getPassword(), AppConfig::getTable());

    }

    public function getDBConnection(){
        return $this->dbh;
    }

    public function isConnected(){

        //make sure we are connected to the database
        if($this->dbh){
            return true;
        }
        else{
            return false;
        }
    }

    public function disconnect(){
        $this->dbh->close();
    }

    public function insert(){

    }

    public function insertUser($newUser){

        if($newUser->hasDuplicateEmail())
               return false;

        if($newUser->hasDuplicateUsername())
            return false;

        $date = DateHelper::currentDate();
        $temp_id = 0;

        $query = $this->dbh->prepare("insert into users values(?, ?, ?, ?, ?, ?)");
        $query->bind_param("isssss", $temp_id, $newUser->getUsername(), $newUser->getName(), $newUser->getEmail(),
                                     $newUser->getEncryptedPassword(), $date);

        return $this->isExecuted($query);

    }

    public function deleteUser($user){

        $query = $this->dbh->prepare("delete from users where username=?");
        $query->bind_param("s", $user->getUsername());

        return $this->isExecuted($query);
    }

    public function userExists($user){
        $query = $this->dbh->prepare("select * from users where email = ? and password = ?");
        $query->bind_param("ss", $user->getEmail(), $user->getEncryptedPassword());

        return $this->dataExists($query);
    }

    public function dataExists($query){
        $query->execute();
        $query->store_result();
        $dataExists = $query->num_rows;
        $query->close();

        /* check if this data already exists in the database */
        if($dataExists > 0){
            return true;
        }
        else{
            return false;
        }
    }

    public function isExecuted($query){

        $query->execute();
        $query->store_result();
        $querySuccess = $query->affected_rows;
        $query->close();

        /* check if successful */
        if($querySuccess !== -1){
            return true;
        }
        else{
            return false;
        }
    }

    public function select()
    {
        //need parameters and table name, might need to tweak this based on # of params
    }

    public function delete()
    {
    }

    public function update()
    {
    }
}

?>