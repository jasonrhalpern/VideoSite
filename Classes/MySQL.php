<?php
/**
 * MySQL class to handle database related operations, implements Database.php
 *
 * This class handles a lot of the function related to database operations,
 * which includes connecting/disconnecting from the database,
 * inserting/deleting users, series, video metadata, etc.
 *
 * @author Jason Halpern
 */

require_once('/home/simawatkinto/AppConfig.php');
require_once('Database.php');

class MySQL implements Database{

    /**
     * @var mysqli object - Our handle to interact with the database
     */
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

    /**
     * @return bool True if connected to the database, False otherwise
     */
    public function isConnected(){

        //make sure we are connected to the database
        if($this->dbh){
            return true;
        }
        else{
            return false;
        }
    }

    /**
     * Close the connection to the database
     */
    public function disconnect(){
        $this->dbh->close();
    }


    /**
     * Add the user to the database
     *
     * @param User $newUser The user we want to insert into the database
     * @return bool True if the user has been inserted into the database, False otherwise
     */
    public function insertUser($newUser){

        $temp_id = 0;

        $query = $this->dbh->prepare("insert into users values(?, ?, ?, ?, ?, ?)");
        $query->bind_param("isssss", $temp_id, $newUser->getUsername(), $newUser->getName(), $newUser->getEmail(),
                                     $newUser->getEncryptedPassword(), $newUser->getJoined());

        return $this->isExecuted($query);

    }

    /**
     * Delete a user from the database
     *
     * @param User $user The user we want to delete from the database
     * @return bool True if the user has been deleted from the database, False otherwise
     */
    public function deleteUser($user){

        $query = $this->dbh->prepare("delete from users where username=?");
        $query->bind_param("s", $user->getUsername());

        return $this->isExecuted($query);
    }

    /**
     * Check if a user exists with the given email and password
     *
     * @param User $user The user we want to check and see if they exist in the DB
     * @return bool True if the user exists in the database, False otherwise
     */
    public function userExists($user){
        $query = $this->dbh->prepare("select * from users where email = ? and password = ?");
        $query->bind_param("ss", $user->getEmail(), $user->getEncryptedPassword());

        return $this->dataExists($query);
    }

    /**
     * Insert a new series into the database
     *
     * @param Series $series The series that we want to add to the database
     * @return bool True if the series has been added to the DB, false otherwise
     */
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

    /**
     * Delete a series from the database
     *
     * @param Series $series The series we want to delete from the database
     * @return bool True if the series has been deleted, False otherwise
     */
    public function deleteSeries($series){
        $query = $this->dbh->prepare("delete from series where title=?");
        $query->bind_param("s", $series->getTitle());

        return $this->isExecuted($query);
    }

    /**
     * Check if a series with this title already exists in the database
     *
     * @param Series $series The series we are looking up in the database
     * @return bool True if a series with this title exists, False otherwise
     */
    public function seriesExists($series){
        $query = $this->dbh->prepare("select * from series where title = ?");
        $query->bind_param("s", $series->getTitle());

        return $this->dataExists($query);
    }

    /**
     * Check if our database query returned any results
     *
     * @param mysqli $query The query we are executing
     * @return bool True if results have been found, False otherwise
     */
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

    /**
     * Check if the query was successfully executed
     *
     * @param mysqli $query The query that is being executed
     * @return bool True if the query was executed, False otherwise
     */
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