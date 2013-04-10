<?php
/**
 * This abstract class is being used to represent many field and operations
 * that will be common to all types of people using the site (i.e. Users, Admin,
 * Producers). Fields such as username, email, password, etc., and the
 * getters/setters associated with such fields will be needed by the variety of
 * classes that subclass this one.
 *
 * @author Jason Halpern
 * @since 4/5/2013
 */
require_once('S3.php');
require_once('MySQL.php');

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

    /**
     * @return string The name of the person
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * @return string The email of the person
     */
    public function getEmail()
    {
        return $this->email;
    }

    /**
     * @return int The unique id of the person
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @return string The username of the person
     */
    public function getUsername()
    {
        return $this->username;
    }

    /**
     * @return string The password of the person
     */
    public function getPassword()
    {
        return $this->password;
    }

    /**
     * @return mysqli The database handle
     */
    public function getDBConnection(){
        return $this->db->getDBConnection();
    }

    /**
     * @return string The encrypted password
     */
    public function getEncryptedPassword(){
        $salt = AppConfig::getSalt();
        $password = strtolower($this->getPassword());
        $encrypted_password = md5($salt.$password);

        return $encrypted_password;
    }

    /**
     * Set the email
     *
     * @param string $newEmail The email of the person
     */
    public function setEmail($newEmail)
    {
        $this->email = $newEmail;
    }

    /**
     * Set the id
     *
     * @param int $newId The id of the person
     */
    public function setId($newId)
    {
        $this->id = $newId;
    }

    /**
     * Set the username
     *
     * @param string $newUsername The username of the person
     */
    public function setUsername($newUsername)
    {
        $this->username = $newUsername;
    }

    /**
     * Set the name
     *
     * @param string $name The name of the person
     */
    public function setName($name){
        $this->name = $name;
    }

    /**
     * Set the password
     *
     * @param string $newPassword The password of the person
     */
    public function setPassword($newPassword)
    {
        $this->password = $newPassword;
    }
}


?>