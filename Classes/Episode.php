<?php
/**
 * The Episode class encapsulates the specific details about an episode
 * that are not already included in the specific Video class. The series id,
 * season number and episode number are additional information about a
 * specific video in a series, but since an Episode is not a Video, we used
 * composition instead of inheritance.
 *
 * @author Jason Halpern
 * @since 4/5/2013
 */
class Episode{

    protected $video; //Video object
    protected $seriesId;
    protected $seasonNumber;
    protected $episodeNumber;

    public function __construct($videoObject, $seriesId,
                                $seasonNumber, $episodeNumber){

        $this->video = $videoObject;
        $this->seriesId = $seriesId;
        $this->seasonNumber = $seasonNumber;
        $this->episodeNumber = $episodeNumber;
    }

    /**
     * Create a episode object from the ID. We can do this by using the
     * ID to fetch the rest of the details from the database.
     *
     * @param int $videoId The id of the video representing the episode that we are getting the details about
     * @return bool|Episode The episode object with all the series details, False if we can't find it
     */
    public static function loadEpisodeById($videoId){

        /* get the details about this episode, first we get the details about the video
           that represents this episode */
        $video = Video::loadVideoById($videoId);
        if($video === false)
            return false;

        $episode = new Episode($video, null, null, null);

        $query = $episode->video->getDBConnection()->prepare("select * from episode where video_id = ?");
        $query->bind_param("i", $video->getVideoId());

        $episode->getEpisodeInfo($query);

        if(is_null($episode->getSeriesId()))
            return false;

        return $episode;
    }

    /**
     * Execute a query about a Episode and then load all the details of the episode that
     * match the query
     *
     * @param mysqli $query The query we are executing
     */
    public function getEpisodeInfo($query){
        $query->execute();
        $query->bind_result($videoId, $seriesId, $seasonNumber, $episodeNumber);
        if($query->fetch()){
            $this->setSeriesId($seriesId);
            $this->setSeasonNumber($seasonNumber);
            $this->setEpisodeNumber($episodeNumber);
        }

    }

    public function getSeriesId(){
        return $this->seriesId;
    }

    public function setSeriesId($seriesId){
        $this->seriesId = $seriesId;
    }

    public function getSeasonNumber(){
        return $this->seasonNumber;
    }

    public function setSeasonNumber($seasonNum){
        $this->seasonNumber = $seasonNum;
    }

    public function getEpisodeNumber(){
        return $this->episodeNumber;
    }

    public function setEpisodeNumber($episodeNum){
        $this->episodeNumber = $episodeNum;
    }

    public function getVideoId(){
        return $this->video->getVideoId();
    }

    public function getSubmitter(){
        return $this->video->getSubmitter();
    }

    public function getVideo(){
        return $this->video;
    }

    public function setVideo($video){
        $this->video = $video;
    }
}
