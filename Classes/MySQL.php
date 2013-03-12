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


    /*
     * add the user to the database, but check the details first to make sure a user
     * with this information doesn't already exist
     */
    public function insertUser($newUser){

        $temp_id = 0;

        $query = $this->dbh->prepare("insert into users values(?, ?, ?, ?, ?, ?)");
        $query->bind_param("isssss", $temp_id, $newUser->getUsername(), $newUser->getName(), $newUser->getEmail(),
                                     $newUser->getEncryptedPassword(), $newUser->getJoined());

        return $this->isExecuted($query);

    }

    /* delete a user from the database */
    public function deleteUser($user){

        $query = $this->dbh->prepare("delete from users where username=?");
        $query->bind_param("s", $user->getUsername());

        return $this->isExecuted($query);
    }

    /* check if a user exists with the given email and password */
    public function userExists($user){
        $query = $this->dbh->prepare("select * from users where email = ? and password = ?");
        $query->bind_param("ss", $user->getEmail(), $user->getEncryptedPassword());

        return $this->dataExists($query);
    }

    public function insertSeries($series){

        /* check if a user is trying to create a series with the same name as one that already exists */
        if($this->seriesExists($series))
            return false;

        $temp_id = 0;

        $query = $this->dbh->prepare("insert into series values(?, ?, ?, ?, ?, ?, ?)");
        $query->bind_param("iisisss", $temp_id, $series->getCreatorId(), $series->getCreatedDate(),
                    $series->getSeasonNum(), $series->getTitle(), $series->getDescription(), $series->getCategory());

        return $this->isExecuted($query);
    }

    public function deleteSeries($series){
        $query = $this->dbh->prepare("delete from series where title=?");
        $query->bind_param("s", $series->getTitle());

        return $this->isExecuted($query);
    }

    /* check if a series with this title already exists */
    public function seriesExists($series){
        $query = $this->dbh->prepare("select * from series where title = ?");
        $query->bind_param("s", $series->getTitle());

        return $this->dataExists($query);
    }

    /* check if our database query returned any results */
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

    /* check if the query was successfully executed */
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


}

?>