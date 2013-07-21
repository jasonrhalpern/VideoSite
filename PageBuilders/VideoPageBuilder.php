<?php

class VideoPageBuilder{

    protected $videoId;
    protected $videoPage;//array, not actually a video object
    protected $comments;
    protected $otherVideosInCompetition;
    protected $db;

    public function __construct($videoId){
        $this->videoId = $videoId;
        $this->db = new MySQL();
    }

    public function buildCompetitionPage(){
        $this->videoPage = $this->loadVideoPage();
        $this->comments = $this->loadComments();
        $this->otherVideosInCompetition = $this->loadOtherVideosInCompetition($competitionId);
    }

    public function loadVideoPage(){
        $query = $this->getDBConnection()->prepare("select video_details.video_id, competition_id, video_details.title,
                                                    video_details.description, user_id, video_details.created, video_details.created_by,
                                                    video_details.video_length, video_details.likes,
                                                    username, comp.title, comp.description, comp.comp_type, comp.category,
                                                    comp.start_date, comp.end_date
                                                    from (select v.video_id, title, description, created, created_by, views
                                                    video_length, likes, competition_id from video v inner join competition_entries ce
                                                    on ce.video_id = v.video_id where v.video_id = ?) as video_details,
                                                    competition comp, users u
                                                    where comp.id = video_details.competition_id
                                                    and video_details.created_by = u.user_id");
        $query->bind_param("i", $this->videoId);
        $query->execute();
        $query->store_result();
        $query->bind_result();
        $video = array();
        if($query->fetch()){

        }
    }

    public function loadComments(){

    }

    public function loadOtherVideosInCompetition($competitionId){

    }
}

?>