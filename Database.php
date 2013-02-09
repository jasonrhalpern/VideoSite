<?php
/**
 * @author Jason Halpern
 */


interface Database
{
    public function connect();

    public function disconnect();

    public function insert();

    public function select();

    public function delete();

    public function update();
}

?>