<?php

/**
 * @author Jason Halpern
 */

class MySQL implements Database
{

    //check page 196 in php cookbook. Maybe add a query or dbh parameter

    public function __construct()
    {
    }

    public function connect()
    {
    }

    public function disconnect()
    {
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