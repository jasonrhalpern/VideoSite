<?php
/**
 * @author Jason Halpern
 */

class User extends Person
{

    protected $joined; //date

    public function __construct()
    {
    }

    public function getJoined()
    {
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