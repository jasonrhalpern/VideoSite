<?php
/**
 * This class holds all details related to a competition
 *
 * @author Jason Halpern
 * @since 4/5/2013
 */

require_once('Production.php');

class Competition extends Production{

    protected $startDate; //date
    protected $endDate; //date
    protected $entryFee; //double
    protected $type; //group or individual, ENUM
    protected $category; //music, food, fashion, etc, ENUM


    public function __construct($title, $description, $startDate, $endDate, $entryFee,
                                $type, $category){
        parent::__construct($title, $description);
        $this->startDate = $startDate;
        $this->endDate = $endDate;
        $this->entryFee = $entryFee;
        $this->type = $type;
        $this->category = $category;
    }

    /**
     * Create a competition object from the ID. We can do this by using the
     * ID to fetch the rest of the details from the database.
     *
     * @param int $competitionId The id of the competition we are getting the details about
     * @return bool|Competition The competition object with all the series details, False if we can't find it
     */
    public static function loadCompetitionById($competitionId){
        $competition = new Competition(null, null, null, null, null, null, null);
        $query = $competition->getDBConnection()->prepare("select * from competition where id = ?");
        $query->bind_param("i", $competitionId);

        MySQL::getCompetitionInfo($competition, $query);

        if(is_null($competition->getTitle()))
            return false;

        return $competition;
    }

    /**
     * Add a video as a new entry to this competition
     *
     * @param Video $videoObject The video object representing the video for this entry
     * @param string $fileName The file we are uploading from local to remote file storage.
     * @param string $userId The ID of the user adding the video to the competition
     * @return bool True if the video has been added to the competition, False otherwise
     */
    public function addNewEntry($videoObject, $fileName, $userId){

        /* insert the video object into the database */
        if(!$this->db->insertVideo($videoObject))
            return false;

        /* grab the ID of the video that was just added */
        $videoId = $this->db->mostRecentVideoId($userId);

        /* register this video as a participant in the competition */
        if(!$this->db->insertCompetitionEntry($this->getId(), $videoId))
            return false;

        /**
         *  Now we need to upload the actual video file representing the new entry from our
         *  local filesystem to the remote file storage.
         */
        $competitionFolder = $this->fileStorage->getFullCompetitionPath($this);
        $this->fileStorage->uploadVideo($fileName, (string)$videoId, $competitionFolder);

        /* Transcode the file that we just uploaded */
        $originalFile = $this->fileStorage->getCompetitionKey($this, $videoId);
        /* create the standard definition (SD) video file */
        $standardDefinitionFile = $this->fileStorage->getSDCompetitionKey($this, $videoId);
        $this->transcoder->transcodeVideo($originalFile, $standardDefinitionFile,
            $this->fileStorage->getCompetitionThumbnailFolder($this, $videoId), '1351620000000-000020');

        return true;
    }

    public function getTimeRemaining()
    {
    }

    public function getNumSubmissions()
    {
    }

    public function getStartDate(){
        return $this->startDate;
    }

    public function setStartDate($startDate){
        $this->startDate = $startDate;
    }

    public function getEndDate(){
        return $this->endDate;
    }

    public function setEndDate($endDate){
        $this->endDate = $endDate;
    }

    public function getEntryFee(){
        return $this->entryFee;
    }

    public function setEntryFee($entryFee){
        $this->entryFee = $entryFee;
    }

    public function getType(){
        return $this->type;
    }

    public function setType($type){
        $this->type = $type;
    }

    public function getCategory(){
        return $this->category;
    }

    public function setCategory($category){
        $this->category = $category;
    }

    public function start()
    {
    } //kick off a competition
    public function end()
    {
    } //end that competition


    public function setWinner()
    {
    }

    public function changeWinner()
    {
    }

    public function addRunnerUp()
    {
    }

    public function removeRunnerUp()
    {
    }
}

?>