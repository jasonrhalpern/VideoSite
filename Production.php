<?php
/**
 * @author Jason Halpern
 */

abstract class Production
{
    protected $id;
    protected $title;
    protected $description;
    protected $videos; //array
    protected $tags; //array
    protected $db;

    public function __construct($title, $description){
        $this->title = $title;
        $this->description = $description;
        $this->db = new MySQL();
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

    public function getDescription()
    {
        return $this->description;
    }

    public function setDescription($newDescr)
    {
        $this->description = $newDescr;
    }

    public function getTitle()
    {
        return $this->title;
    }

    public function setTitle($newTitle)
    {
        $this->title = $newTitle;
    }

    public function getId()
    {
        return $this->id;
    }

    public function showVideos()
    {
    }

    public function getUnapprovedVideos()
    {
    }

    public function getTags()
    {
        return $this->tags;
    }

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
}


?>