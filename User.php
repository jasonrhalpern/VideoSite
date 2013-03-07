<?php
/**
 * @author Jason Halpern
 */

require_once('Person.php');
require_once('DateHelper.php');

class User extends Person{

    protected $joined; //date

    public function __construct($name, $email, $username, $id, $password){

        parent::__construct($name, $email, $username, $id, $password);
        $this->joined = DateHelper::currentDate();
    }

    /* check if this user exists in the database */
    public function isRegistered(){
        return $this->db->userExists($this);
    }

    public function register(){

        if($this->hasDuplicateEmail())
            return false;

        if($this->hasDuplicateUsername())
            return false;

        if(!$this->hasValidEmail())
            return false;

        return $this->db->insertUser($this);

    }

    /* check if there is a user with the email and password provided */
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

    /* load all of the user details from the database */
    public function loadDetails(){
        $query = $this->getDBConnection()->prepare("select * from users where email = ? and password = ?");
        $query->bind_param("ss", $this->getEmail(), $this->getEncryptedPassword());
        $query->execute();
        $query->bind_result($id, $username, $name, $email, $password, $joined);
        if($query->fetch()){
            $this->setId($id);
            $this->setUsername($username);
            $this->setName($name);
            $this->setJoined($joined);
        }
    }

    /* check if this user is trying to register with an email already in the system */
    public function hasDuplicateEmail(){
        $query = $this->getDBConnection()->prepare("select * from users where email = ?");
        $query->bind_param("s", $this->getEmail());

        return $this->db->dataExists($query);
    }

    /* check if this user is trying to register with a username already in the system */
    public function hasDuplicateUsername(){
        $query = $this->getDBConnection()->prepare("select * from users where username = ?");
        $query->bind_param("s", $this->getUsername());

        return $this->db->dataExists($query);

    }

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

    public function getJoined(){

        return $this->joined;
    }

    public function setJoined($date){
        $this->joined = $date;
    }

    public function changeUsername()
    {
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