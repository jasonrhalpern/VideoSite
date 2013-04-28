<?php
/**
 * @author Jason Halpern
 * @since 4/28/2013
 */
class UserDao{

    protected $db;

    public function __construct(){
        $this->db = new MySQL();
    }

}
