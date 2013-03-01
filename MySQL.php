<?php

/**
 * @author Jason Halpern
 */

require_once('/home/simawatkinto/AppConfig.php');
require_once('Database.php');

class MySQL implements Database
{

    protected  $dbh;

    public function __construct(){
        $this->connect();
    }

    public function __destruct() {
        $this->disconnect();
    }

    public function connect(){
        $this->dbh = new mysqli(AppConfig::getConnection(), AppConfig::getUsername(),
                                AppConfig::getPassword(), AppConfig::getTable());

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

    public function insert()
    {
        //should take parameters and table, might need several insert functions based on table
    }

    public function insertUser($newUser){

        $date = DateHelper::currentDate();
        $temp_id = 0;

        $query = $this->dbh->prepare("insert into users values(?, ?, ?, ?, ?, ?)");
        $query->bind_param("isssss", $temp_id, $newUser->getUsername(), $newUser->getName(), $newUser->getEmail(),
                                     $newUser->getEncryptedPassword(), $date);

        return $this->isQuerySuccessful($query);

    }

    public function isQuerySuccessful($query){

        $query->execute();
        $query->store_result();
        $querySuccess = $query->affected_rows;
        $query->close();

        /* check if successful */
        if($querySuccess !== 0){
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