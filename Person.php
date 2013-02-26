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

    public function __construct($name, $email, $username, $id, $password){
        $this->name = $name;
        $this->email = $email;
        $this->username = $username;
        $this->id = $id;
        $this->password = $password;
    }

    public function getName()
    {
        return $this->name;
    }

    public function getEmail()
    {
        return $this->email;
    }

    public function setEmail($newEmail)
    {
        $this->email = $newEmail;
    }

    public function getId()
    {
        return $this->id;
    }

    public function setId($newId)
    {
        $this->id = $newId;
    }

    public function getUsername()
    {
        return $this->username;
    }

    public function setUsername($newUsername)
    {
        $this->username = $newUsername;
    }

    public function getPassword()
    {
        return $this->password;
    }

    public function setPassword($newPassword)
    {
        $this->password = $newPassword;
    }
}


?>