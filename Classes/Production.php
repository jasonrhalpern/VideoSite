<?php
/**
 * The abstract Production class includes details that are common to all
 * types of Productions (i.e. Series, Competitions, etc).
 *
 * @author Jason Halpern
 * @since 4/5/2013
 */

require_once dirname(__FILE__) . '/../Classes/MySQL.php';
require_once dirname(__FILE__) . '/../Classes/Transcoder.php';

abstract class Production{

    protected $id;
    protected $title;
    protected $description;
    protected $videos; //array of Video objects
    protected $tags; //array
    protected $db;
    protected $fileStorage;
    protected $transcoder;

    public function __construct($title, $description){
        $this->title = $title;
        $this->description = $description;
        $this->db = new MySQL();
        $this->fileStorage = new S3();
        $this->transcoder = new Transcoder();
    }

    public function addVideo()
    {
        //connect to S3, add video to S3
        //connect to DB, add details to DB
    }

    public function deleteVideo()
    {
        //connect to S3, remove video to S3
        //connect to DB, remove details to DB
    }

    /**
     * @return string The description for the series
     */
    public function getDescription()
    {
        return $this->description;
    }

    /**
     * Set the description for the series
     *
     * @param string $newDescr The description of the series
     */
    public function setDescription($newDescr)
    {
        $this->description = $newDescr;
    }

    /**
     * @return string The title of the series
     */
    public function getTitle()
    {
        return $this->title;
    }

    /**
     * Set the title for the series
     *
     * @param string $newTitle The title of the series
     */
    public function setTitle($newTitle)
    {
        $this->title = $newTitle;
    }

    /**
     * @return int The id of the series
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Set the id for the series
     *
     * @param int $id The id of the series
     */
    public function setId($id){
        $this->id = $id;
    }

    public function showVideos()
    {
    }

    public function getUnapprovedVideos()
    {
    }

    /**
     * @return array The tags for the series
     */
    public function getTags()
    {
        return $this->tags;
    }

    /**
     * Set the tags for the series
     *
     * @param array $newTags The tags for the series
     */
    public function setTags($newTags)
    {
        $this->tags = $newTags;
    }

    public function addTag()
    {
    }

    public function removeTag()
    {
    }

    /**
     * @return mysqli The database handle
     */
    public function getDBConnection(){
        return $this->db->getDBConnection();
    }
}


?>