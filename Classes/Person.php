<?php
/**
 * @author Jason Halpern
 */

abstract class Person
{
    protected $name;
    protected $email;
    protected $username;
    protected $id;
    protected $password;
    protected $db;
    protected $fileStorage;

    public function __construct($name, $email, $username, $id, $password){
        $this->name = $name;
        $this->email = $email;
        $this->username = $username;
        $this->id = $id;
        $this->password = $password;
        $this->db = new MySQL();
        $this->fileStorage = new S3();
    }

    public function getName()
    {
        return $this->name;
    }

    public function getEmail()
    {
        return $this->email;
    }

    public function getId()
    {
        return $this->id;
    }

    public function getUsername()
    {
        return $this->username;
    }

    public function getPassword()
    {
        return $this->password;
    }

    public function getDBConnection(){
        return $this->db->getDBConnection();
    }

    public function getEncryptedPassword(){
        $salt = AppConfig::getSalt();
        $password = strtolower($this->getPassword());
        $encrypted_password = md5($salt.$password);

        return $encrypted_password;
    }

    public function setEmail($newEmail)
    {
        $this->email = $newEmail;
    }

    public function setId($newId)
    {
        $this->id = $newId;
    }

    public function setUsername($newUsername)
    {
        $this->username = $newUsername;
    }

    public function setName($name){
        $this->name = $name;
    }

    public function setPassword($newPassword)
    {
        $this->password = $newPassword;
    }
}


?>