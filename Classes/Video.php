<?php
/**
 * The video class encapsulates all the information about a video file
 * that has been uploaded by a producer. This video can be an episode in
 * a series or it can be a part of another type of production.
 *
 * @author Jason Halpern
 * @since 4/5/2013
 */

require_once('DateHelper.php');

class Video{

    protected $videoId;
    protected $title;
    protected $description;
    protected $posted; //date
    protected $submitter; //user's ID
    protected $views; //integer
    protected $length;
    protected $likes;
    protected $tags; //array
    protected $comments; //array
    protected $db;


    public function __construct($title, $description, $submitterId){
        $this->title = $title;
        $this->description = $description;
        $this->posted = DateHelper::currentDate();
        $this->submitter = $submitterId;
        $this->views = 0;
        $this->length = 0;
        $this->likes = 0;
        $this->db = new MySQL();
    }

    /**
     * Create a video object from the ID. We can do this by using the
     * ID to fetch the rest of the details from the database.
     *
     * @param int $videoId The id of the video that we are getting the details about
     * @return bool|Video The video object with all the series details, False if we can't find it
     */
    public static function loadVideoById($videoId){

        $video = new Video(null, null, null);
        $query = $video->getDBConnection()->prepare("select * from video where video_id = ?");
        $query->bind_param("i", $videoId);

        $video->getVideoInfo($query);

        if(is_null($video->getTitle()))
            return false;

        return $video;
    }

    /**
     * Execute a query about a video and then load all the details of the video that
     * match the query
     *
     * @param mysqli $query The query we are executing
     */
    public function getVideoInfo($query){
        $query->execute();
        $query->bind_result($id, $title, $description, $createdDate, $createdBy, $views, $length, $likes);
        if($query->fetch()){
            $this->setVideoId($id);
            $this->setTitle($title);
            $this->setDescription($description);
            $this->setPostedDate($createdDate);
            $this->setSubmitter($createdBy);
            $this->setViews($views);
            $this->setLength($length);
            $this->setLikes($likes);
        }

    }

    public function joinCompetition()
    {
    }

    public function watchVideo()
    {
    }

    public function addLike()
    {
    }

    public function removeLike()
    {
    }

    public function addDislike()
    {
    }

    public function removeDislike()
    {
    }

    public function getLikes(){
        return $this->likes;
    }

    public function setLikes($likes){
        $this->likes = $likes;
    }

    public function getDislikes(){
    }

    public function getLength(){
        return $this->length;
    }

    public function setLength($length){
        $this->length = $length;
    }

    public function getVideoId(){
        return $this->videoId;
    }

    public function setVideoId($videoId){
        $this->videoId = $videoId;
    }

    public function getDescription()
    {
        return $this->description;
    }

    public function setDescription($newDescr)
    {
        $this->description = $newDescr;
    }

    public function getViews()
    {
        return $this->views;
    }

    public function setViews($views){
        $this->views = $views;
    }

    public function addView()
    {
    }

    public function getComment()
    {
    }

    public function addComment()
    {
    }

    public function deleteComment()
    {
    }

    public function uploadVideo()
    {
    }

    public function deleteVideo()
    {
    }

    public function getSubmitter(){
        return $this->submitter;
    }

    public function setSubmitter($submitter){
        $this->submitter = $submitter;
    }

    public function getTitle()
    {
        return $this->title;
    }

    public function setTitle($newTitle)
    {
        $this->title = $newTitle;
    }

    public function getPostedDate()
    {
        return $this->posted;
    }

    public function setPostedDate($date){
        $this->posted = $date;
    }

    //are tags needed for a specific video or just for productions?
    public function addTag()
    {
    }

    public function getTags()
    {
        return $this->tags;
    }

    public function removeTags()
    {
    }

    public function setTags($newTags)
    {
        $this->tags = $newTags;
    }

    /**
     * @return mysqli The database handle
     */
    public function getDBConnection(){
        return $this->db->getDBConnection();
    }

}


?>