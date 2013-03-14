<?php
/**
 * @author Jason Halpern
 */

require_once('Production.php');
require_once('DateHelper.php');

class Series extends Production
{
    protected $createdDate; //date
    protected $creatorId; //id of user that created the series
    protected $category; //comedy, drama, etc.
    protected $seasonNum; //number of seasons
    protected $crew; //crew is a SeriesCrew object

    public function __construct($creatorId, $title, $description, $category, $seasonNumber){

        parent::__construct($title, $description);
        $this->createdDate = DateHelper::currentDate();
        $this->creatorId = $creatorId;
        $this->category = $category;
        $this->seasonNum = $seasonNumber;
    }

    public static function loadSeriesById($seriesId){
        $series = new Series(null, null, null, null, null);
        $query = $series->getDBConnection()->prepare("select * from series where series_id = ?");
        $query->bind_param("s", $seriesId);

        $series->getSeriesInfo($query);

        if(is_null($series->getTitle()))
            return false;

        return $series;
    }

    public static function loadSeriesByTitle($seriesTitle){
        $series = new Series(null, null, null, null, null);
        $query = $series->getDBConnection()->prepare("select * from series where title = ?");
        $query->bind_param("s", $seriesTitle);

        $series->getSeriesInfo($query);

        if(is_null($series->getId()))
            return false;

        return $series;
    }

    public function getSeriesInfo($query){
        $query->execute();
        $query->bind_result($id, $creatorId, $createdDate, $seasonNumber, $title, $description, $category);
        if($query->fetch()){
            $this->setId($id);
            $this->setCreatorId($creatorId);
            $this->setCreatedDate($createdDate);
            $this->setSeasonNum($seasonNumber);
            $this->setTitle($title);
            $this->setDescription($description);
            $this->setCategory($category);
        }

    }

    public function getCreatedDate(){
        return $this->createdDate;
    }

    public function setCreatedDate($date){
        $this->createdDate = $date;
    }

    public function getCreatorId(){
        return $this->creatorId;
    }

    public function setCreatorId($id){
        $this->creatorId = $id;
    }

    public function getCategory(){
        return $this->category;
    }

    public function setCategory($category){
        $this->category = $category;
    }

    public function getSeasonNum(){
        return $this->seasonNum;
    }

    public function setSeasonNum($number){
        $this->seasonNum = $number;
    }

    /* folder that stores all videos for this series */
    public function getFolderName(){
        /* replace the whitespace with underscores to get the right folder */
        return str_replace(' ', '_', $this->getTitle());
    }

    /* This is the full path for a bucket of a series */
    public function getFullSeriesPath(){
        return AppConfig::getS3Root() . $this->getFolderName() . '/';
    }

    public function getCrew(){

    }

    /* increase the season number for a series */
    public function addNewSeason(){
        /* increment the season number for this series in the database */
        $newSeasonNum = $this->getSeasonNum() + 1;
        $query = $this->getDBConnection()->prepare("update series set seasons = ? where series_id = ?");
        $query->bind_param("ii", $newSeasonNum, $this->getId());

        return $this->db->isExecuted($query);
    }

    public function isProducer($producer){

        return $producer->getId() === $this->getCreatorId();
    }

    public function getFullSeries()
    {
    }

    public function submitPilot()
    {
    }

    public function showSeason()
    {
    }
}


?>