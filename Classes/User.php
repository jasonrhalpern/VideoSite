<?php
/**
 * This class encapsulates all the details for a user on our site. It extends the
 * Person class in Person.php
 *
 * @author Jason Halpern
 * @since 4/5/2013
 */

require_once('Person.php');
require_once('DateHelper.php');
require_once('HelperFunc.php');
require_once('MySQL.php');

class User extends Person{

    protected $joined; //date

    public function __construct($name, $email, $username, $id, $password){

        parent::__construct($name, $email, $username, $id, $password);
        $this->joined = DateHelper::currentDate();
    }

    /**
     * Check if this user exists in the database
     *
     * @return bool True if this user has registered with our site, False otherwise
     */
    public function isRegistered(){
        return $this->db->userExists($this);
    }

    /**
     * Register a user. Before registering, we have to make sure the user's
     * details are unique
     *
     * @return bool True if we registered the user, False otherwise
     */
    public function register(){

        if($this->hasDuplicateEmail())
            return false;

        if($this->hasDuplicateUsername())
            return false;

        if(!$this->hasValidEmail())
            return false;

        if(!$this->hasValidPassword())
            return false;

        if(!$this->hasValidUsername())
            return false;

        if(!$this->hasOnlyAlphanumericCharacters())
            return false;

        return $this->db->insertUser($this);

    }

    /**
     * Check if there is a registered user with the email and password provided
     *
     * @param string $email
     * @param string $password
     * @return bool|User A User object if we could log the user in, False otherwise
     */
    public static function login($email, $password){

        $user = new User(null, null, null, null, null);
        $user->setEmail($email);
        $user->setPassword($password);

        if($user->isRegistered()){
            $user->loadDetails();
            return $user;
        }
        else{
            return false;
        }

    }

    /**
     * Load all of the user details from the database
     */
    public function loadDetails(){
        $query = $this->getDBConnection()->prepare("select * from users where email = ? and password = ?");
        $query->bind_param("ss", $this->getEmail(), $this->getEncryptedPassword());
        MySQL::getUserInfo($this, $query);
    }

    /**
     * Check if this user is trying to register with an email already in the system
     *
     * @return bool True if this user has a duplicate email, False otherwise
     */
    public function hasDuplicateEmail(){
        $query = $this->getDBConnection()->prepare("select * from users where email = ?");
        $query->bind_param("s", $this->getEmail());

        return $this->db->dataExists($query);
    }

    /**
     * Check if this user is trying to register with a username already in the system
     *
     * @return bool True if the user has a duplicate username, False otherwise
     */
    public function hasDuplicateUsername(){

        $query = $this->getDBConnection()->prepare("select * from users where username = ?");
        $query->bind_param("s", $this->getUsername());

        return $this->db->dataExists($query);

    }

    /**
     * Check if the email address provided is valid
     *
     * @return bool True if the email address is valid, False otherwise
     */
    public function hasValidEmail(){

        //test that email address is valid format
        $qtext = '[^\\x0d\\x22\\x5c\\x80-\\xff]';
        $dtext = '[^\\x0d\\x5b-\\x5d\\x80-\\xff]';
        $atom = '[^\\x00-\\x20\\x22\\x28\\x29\\x2c\\x2e\\x3a-\\x3c'.
            '\\x3e\\x40\\x5b-\\x5d\\x7f-\\xff]+';
        $quoted_pair = '\\x5c\\x00-\\x7f';
        $domain_literal = "\\x5b($dtext|$quoted_pair)*\\x5d";
        $quoted_string = "\\x22($qtext|$quoted_pair)*\\x22";
        $domain_ref = $atom;
        $sub_domain = "($domain_ref|$domain_literal)";
        $word = "($atom|$quoted_string)";
        $domain = "$sub_domain(\\x2e$sub_domain)*";
        $local_part = "$word(\\x2e$word)*";
        $addr_spec = "$local_part\\x40$domain";

        return preg_match("!^$addr_spec$!", $this->getEmail()) ? true : false;
    }

    /**
     * Check to see if this password is a valid length
     *
     * @return bool True if the password is a valid length, false otherwise
     */
    public function hasValidPassword(){
        return (strlen($this->getPassword()) > 4);
    }

    /**
     * Check to see if the username is a valid length
     *
     * @return bool True if the username is a valid length, false otherwise
     */
    public function hasValidUsername(){
        return ((strlen($this->getUsername()) > 3) && (strlen($this->getUsername()) < 21));
    }

    /**
     * Check to make sure the username is only alphanumeric characters
     * (underscores and whitespace are also allowed)
     *
     * @return bool True if the username is only alphanumeric characters, false otherwise
     */
    public function hasOnlyAlphanumericCharacters(){
        return preg_match('/^[a-zA-Z0-9_ ]+$/', $this->getUsername()) ? true : false;
    }



    public function getJoined(){

        return $this->joined;
    }

    public function setJoined($date){
        $this->joined = $date;
    }

    /**
     * Change the username of this User
     *
     * @param string $newUsername The new username
     * @return bool True if the username has been changed, False otherwise
     */
    public function changeUsername($newUsername){

        /* change the username to the new one, but save the old one */
        $oldUsername = $this->getUsername();
        $this->setUsername($newUsername);

        /* only allow the username change if the user is registered and the username is unique */
        if($this->isRegistered() && !$this->hasDuplicateUsername()){

            $query = $this->getDBConnection()->prepare("UPDATE users SET username=? where email=?");
            $query->bind_param("ss", $newUsername, $this->getEmail());

            /* make sure the username has been updated in the database*/
            if($this->db->isExecuted($query)){
                return true;
            }
        }

        /* if we reach here the username has not been updated, reset it to the old one */
        $this->setUsername($oldUsername);
        return false;
    }

    /**
     * Change the password for this user
     *
     * @param string $newPassword
     * @return bool True if the password is changed in our DB, False otherwise
     */
    public function changePassword($newPassword){

        /* only change the password if this user has already registered in our database */
        if($this->isRegistered()){
            $this->setPassword($newPassword);
            $query = $this->getDBConnection()->prepare("UPDATE users SET password=? where email=?");
            $query->bind_param("ss", $this->getEncryptedPassword(), $this->getEmail());

            /* make sure the password has been updated */
            if($this->db->isExecuted($query))
                return true;
        }

        return false;
    }

    /**
     * Generate a new password for this user and reset it
     *
     * @return bool|string The new password or false if it couldn't be changed
     */
    public function resetPassword(){

        $newPassword = HelperFunc::generateRandomPassword();

        if($this->changePassword($newPassword))
            return $this->getPassword();

        return false;
    }

    public function suggestCompetition()
    {
    }

    public function addVideo()
    {
    }

    public function deleteVideo()
    {
    }

    public function getSubmissions()
    {
    }

    public function addCredits()
    {
    }

    public function viewCredits()
    {
    }

    public function watchVideo()
    {
    }

    public function voteLike()
    {
    }

    public function voteDislike()
    {
    }

    public function addComment()
    {
    } //series and video
    public function deleteComment()
    {
    } //series and video
    public function subscribeToSeries()
    {
    }

    public function unsubscribeFromSeries()
    {
    }

    public function editVidTitle()
    {
    }

    public function editVidDescr()
    {
    }
}

?>