<?php
/**
 * @author Jason Halpern
 */

require_once('Production.php');

class Series extends Production
{
    protected $created; //date
    protected $creatorId; //id of user that created the series
    protected $category; //comedy, drama, etc.
    protected $seasonNum; //number of seasons
    protected $directors; //array
    protected $writers; //array
    protected $producers; //array
    protected $actors; //array

    public function __construct($creatorId, $title, $description, $category, $seasonNumber){

        parent::__construct($title, $description);
        $this->created = DateHelper::currentDate();
        $this->creatorId = $creatorId;
        $this->category = $category;
        $this->seasonNum = $seasonNumber;
    }

    public static function loadSeriesById($seriesId){

    }

    public function getCreated(){
        return $this->created;
    }

    public function setCreated($date){
        $this->created = $date;
    }

    public function getCreatorId(){
        return $this->creatorId;
    }

    public function setCreatorId($id){
        $this->creatorId = $id;
    }

    public function getDirectors()
    {
    }

    public function setDirectors()
    {
    }

    public function getProducers()
    {
    }

    public function setProducers()
    {
    }

    public function getWriters()
    {
    }

    public function setWriters()
    {
    }

    public function getActors()
    {
    }

    public function setActors()
    {
    }

    public function getCategory(){
        return $this->category;
    }

    public function setCategory($category){
        $this->category = $category;
    }

    public function createNewSeason()
    {
    }

    public function getFullSeries()
    {
    }

    public function getSeasonNum(){
        return $this->seasonNum;
    }

    public function setSeasonNum($number){
        $this->seasonNum = $number;
    }

    public function submitPilot()
    {
    }

    public function showSeason()
    {
    }
}


?>