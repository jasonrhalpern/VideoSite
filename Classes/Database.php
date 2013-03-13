<?php
/**
 * @author Jason Halpern
 */


interface Database
{
    public function connect();
    public function disconnect();
    public function getDBConnection();
    public function isConnected();
    public function dataExists($query);
    public function isExecuted($query);

    public function insertUser($user);
    public function deleteUser($user);
    public function userExists($user);

    public function insertSeries($series);
    public function deleteSeries($series);
    public function seriesExists($series);
    public function incrSeasonNum($series);

}

?>