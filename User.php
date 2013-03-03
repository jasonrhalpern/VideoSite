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

    public function login(){
        return $this->db->userExists($this);
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