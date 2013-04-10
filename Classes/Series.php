<?php
/**
 * The Series class includes all the information related to a particular series:
 * the category of the series, number of seasons in the series, the date it was
 * created, etc. It extends the Production class in Production.php.
 *
 * @author Jason Halpern
 * @since 4/5/2013
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

    /**
     * Create a series object from the ID. We can do this by using the
     * ID to fetch the rest of the details from the database.
     *
     * @param int $seriesId The id of the series we are getting the details about
     * @return bool|Series The series object with all the series details, False if we can't find it
     */
    public static function loadSeriesById($seriesId){
        $series = new Series(null, null, null, null, null);
        $query = $series->getDBConnection()->prepare("select * from series where series_id = ?");
        $query->bind_param("s", $seriesId);

        $series->getSeriesInfo($query);

        if(is_null($series->getTitle()))
            return false;

        return $series;
    }

    /**
     * Create a series object from the title. We can do this by using the
     * title to fetch the rest of the details from the database.
     *
     * @param string $seriesTitle The title of the series we are getting the details about
     * @return bool|Series The series object with all the series details, False if we can't find it
     */
    public static function loadSeriesByTitle($seriesTitle){
        $series = new Series(null, null, null, null, null);
        $query = $series->getDBConnection()->prepare("select * from series where title = ?");
        $query->bind_param("s", $seriesTitle);

        $series->getSeriesInfo($query);

        if(is_null($series->getId()))
            return false;

        return $series;
    }

    /**
     * Execute a query about a Series and then load all the details of the series that
     * match the query
     *
     * @param mysqli $query The query we are executing
     */
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

    /**
     * Return the number of seasons for this series
     *
     * @return int The number of seasons in the series
     */
    public function getSeasonNum(){
        return $this->seasonNum;
    }

    /**
     * Set the number of seasons for this series
     *
     * @param int $number Number of seasons
     */
    public function setSeasonNum($number){
        $this->seasonNum = $number;
    }

    /**
     * Return the name of the folder that stores all videos for this series.
     * Whitespace is replaced by underscores because S3 buckets cannot have whitespace.
     *
     * @return string The bucket name for this series
     */
    public function getFolderName(){
        /* replace the whitespace with underscores to get the right folder */
        return str_replace(' ', '_', $this->getTitle());
    }

    /**
     * This is the full path for a bucket of a series. It is different from just
     * the bucket name in that it also includes the root S3 bucket. The full bucket
     * path is needed to correctly add/remove files and folders from S3.
     *
     * @return string The full path to the bucket for this series.
     */
    public function getFullSeriesPath(){
        return AppConfig::getS3Root() . $this->getFolderName() . '/';
    }

    public function getCrew(){

    }

    /**
     * Create a new season for this series. Creating a new season involves updating
     * the season number in the database and creating a new folder in our filesystem
     * to hold the video files for the season.
     *
     * @param string $seasonDescription The description of the new season
     * @return bool True if a new season was created, False otherwise.
     */
    public function addNewSeason($seasonDescription){

        /* get the number of the new season */
        $newSeasonNum = $this->getSeasonNum() + 1;
        $this->setSeasonNum($newSeasonNum);

        /* add the new season to the database */
        if(!$this->db->insertSeason($this->getId(), $this->getSeasonNum(), $seasonDescription))
            return false;

        /* increment the season number for this series in the database */
        $query = $this->getDBConnection()->prepare("update series set seasons = ? where series_id = ?");
        $query->bind_param("ii", $this->getSeasonNum(), $this->getId());

        /* create a new folder to hold the video files for the new season */
        $isFolderCreated = $this->fileStorage->createSeasonFolder($this);

        return ($this->db->isExecuted($query) && $isFolderCreated);
    }

    /**
     * Check to see if a producer is actually the producer of this series. We do this
     * by checking to see if the producer's id matches the id of the series creator.
     *
     * @param Producer $producer
     * @return bool True if this is a producer for this series, False otherwise.
     */
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