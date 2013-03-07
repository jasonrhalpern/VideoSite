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

    public function insertUser($user);

    public function deleteUser($user);

    public function userExists($user);

    public function dataExists($query);

    public function isExecuted($query);

}

?>