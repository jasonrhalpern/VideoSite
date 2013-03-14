<?php
/**
 * @author Jason Halpern
 */

class Video
{

    protected $posted; //date
    protected $tags; //array
    protected $comments; //array
    protected $title;
    protected $views; //integer
    protected $description;
    protected $submitter;
    protected $length;
    protected $likes;
    protected $dislikes;
    protected $seriesId;
    protected $seasonNumber;
    protected $episodeNumber;

    public function __construct()
    {
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

    public function getLikes()
    {
    }

    public function getDislikes()
    {
    }

    public function getLength()
    {
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

    public function getSubmitter()
    {
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

}


?>