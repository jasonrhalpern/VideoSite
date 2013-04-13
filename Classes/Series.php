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

        MySQL::getSeriesInfo($series, $query);

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

        MySQL::getSeriesInfo($series, $query);

        if(is_null($series->getId()))
            return false;

        return $series;
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
     * This is just the folder name for a series, not the full path to the series folder.
     *
     * @return string The bucket name for this series
     */
    public function getSeriesFolderName(){
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
        return AppConfig::getS3Root() . $this->getSeriesFolderName() . '/';
    }

    /**
     * This function returns the path to a folder for a specific season of the series.
     *
     * @param int $seasonNumber
     */
    public function getSeasonFolderPath($seasonNumber){
        $seriesFolder = $this->getFullSeriesPath();
        $seasonFolder = 'season_' . $seasonNumber;

        $bucketName = $seriesFolder . $seasonFolder;

        return $bucketName;
    }

    /**
     * Get the episode key for the original video.
     *
     * @param int $seasonNum The season number
     * @param int $episodeNum The episode number
     * @return string The key to access the original file for the episode
     */
    public function getEpisodeKey($seasonNum, $episodeNum){
        return $this->getSeriesFolderName() . '/' . 'season_' . $seasonNum . '/' . $episodeNum;
    }

    /**
     * Get the episode key for the standard definition video.
     *
     * @param int $seasonNum The season number
     * @param int $episodeNum The episode number
     * @return string The key to access the SD episode
     */
    public function getSDEpisodeKey($seasonNum, $episodeNum){
        return $this->getEpisodeKey($seasonNum, $episodeNum) . '_SD.mp4';
    }

    /**
     * Get the episode key for the high definition video.
     *
     * @param int $seasonNum The season number
     * @param int $episodeNum The episode number
     * @return string The key to access the HD episode
     */
    public function getHDEpisodeKey($seasonNum, $episodeNum){
        return $this->getEpisodeKey($seasonNum, $episodeNum) . '_HD.mp4';
    }

    /**
     * Get the thumbnail folder for a specific episode
     *
     * @param int $seasonNum The season number
     * @param int $episodeNum The episode number
     */
    public function getThumbnailFolder($seasonNum, $episodeNum){
        return $this->getEpisodeKey($seasonNum, $episodeNum);
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
     * Find the number of episodes that exist for a given season in this series
     *
     * @param int $seasonNumber The season number
     * @return int|bool The number of episodes in the season, false otherwise
     */
    public function getNumEpisodesInSeason($seasonNumber){

        $query = $this->getDBConnection()->prepare("select COUNT(*) from episode where
                                                    series_id = ? and season_num = ?");
        $query->bind_param("ii", $this->getId(), $seasonNumber);
        $query->execute();
        $query->bind_result($numberOfEpisodes);
        if($query->fetch()){
            return $numberOfEpisodes;
        }

        return false;
    }

    /**
     * Add a episode to a series.
     *
     * Adding an episode to a series has several steps. First that episode needs to be
     * created in the database, then we need to upload the file to the correct folder
     * for this series, then we need to transcode the file to the appropriate file formats.
     * The transcoder also handles the creation of thumbnails.
     *
     * @param Video $videoObject The video object representing the video for this episode
     * @param string $fileName The file we are uploading to our file storage.
     */
    public function addEpisode($videoObject, $fileName){

        /* Obtain the episode number for this new episode then insert the episode into the database */
        $episodeNumber = $this->getNumEpisodesInSeason($this->getSeasonNum()) + 1;
        $episode = new Episode($videoObject, $this->getId(), $this->getSeasonNum(), $episodeNumber);
        if(!$this->db->insertEpisode($episode))
            return false;

        /**
         *  Now we need to upload the actual video file representing the episode from our
         *  local filesystem to the remote file storage. The episode number is the name of
         *  the file and it is stored in the season folder within that series.
         */
        $seasonFolder = $this->getSeasonFolderPath($this->getSeasonNum());
        $this->fileStorage->uploadVideo($fileName, $episodeNumber, $seasonFolder);

        /* Transcode the file that we just uploaded */
        $originalFile = $this->getEpisodeKey($this->getSeasonNum(), $episodeNumber);
        /* create the standard definition (SD) video file */
        $standardDefinitionFile = $this->getSDEpisodeKey($this->getSeasonNum(), $episodeNumber);
        $this->transcoder->transcodeVideo($originalFile, $standardDefinitionFile,
                                            $this->getThumbnailFolder($this->getSeasonNum(), $episodeNumber),
                                            '1351620000000-000020'); //preset
        /* create the high definition (HD) video file */
        $highDefinitionFile = $this->getHDEpisodeKey($this->getSeasonNum(), $episodeNumber);
        $this->transcoder->transcodeVideo($originalFile, $highDefinitionFile,
                                            $this->getThumbnailFolder($this->getSeasonNum(), $episodeNumber),
                                            '1351620000000-000010'); //preset

        /*$this->fileStorage->waitUntilTranscodingCompletes($this->getSDEpisodeKey($this->getSeasonNum(),
                                                            $episodeNumber));
        $this->fileStorage->waitUntilTranscodingCompletes($this->getHDEpisodeKey($this->getSeasonNum(),
                                                            $episodeNumber));*/
    }

    public function addMainImage($image){

        /*if(!$this->fileStorage->folderExists()){

        }*/
        //see if series image already exists
        //if it does then delete it and add a new one
        //otherwise create folder for image and add image to folder
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