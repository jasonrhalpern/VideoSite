<?php

/**
 * @author Jason Halpern
 */

class MySQL implements Database
{

    protected  $dbh;

    public function __construct(){
    }

    public function connect(){
        $this->dbh = new mysqli(AppConfig::getConnection(), AppConfig::getUsername(),
                                AppConfig::getPassword(), AppConfig::getTable());
    }

    public function disconnect(){
        $this->dbh->close();
    }

    public function insert()
    {
        //should take parameters and table, might need several insert functions based on table
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