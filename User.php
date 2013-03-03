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

    public function isRegistered(){
        return $this->db->userExists($this);
    }

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

    public function hasDuplicateEmail(){
        $query = $this->getDBConnection()->prepare("select * from users where email = ?");
        $query->bind_param("s", $this->getEmail());

        return $this->db->dataExists($query);
    }

    public function hasDuplicateUsername(){
        $query = $this->getDBConnection()->prepare("select * from users where username = ?");
        $query->bind_param("s", $this->getUsername());

        return $this->db->dataExists($query);

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