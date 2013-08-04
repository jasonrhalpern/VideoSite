<?php

class VideoPageBuilder{

    protected $videoId;
    protected $videoWindow;//array, not actually a video object
    protected $comments;
    protected $otherVideosInCompetitionSidebar;
    protected $db;
    const MAXIMUM_SIDEBAR_SIZE = 10;

    public function __construct($videoId){
        $this->videoId = $videoId;
        $this->db = new MySQL();
    }

    public function buildVideoPage(){
        $this->videoWindow = $this->loadVideoWindow();
        $this->comments = $this->loadComments();
        $this->otherVideosInCompetitionSidebar = $this->loadOtherVideosInCompetitionSidebar();
    }

    public function loadVideoWindow(){
        $query = $this->getDBConnection()->prepare("select video_details.video_id, competition_id, video_details.title,
                                                    video_details.description, user_id, video_details.created,
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
        $query->bind_result($videoId, $competitionId, $videoTitle, $videoDescription, $userId, $createdDate, $videoLength, $votes,
                            $username, $competitionTitle, $competitionDescription, $competitionType, $category, $competitionStartDate,
                            $competitionEndDate);
        $videoWindow = array();
        if($query->fetch()){
            $videoWindow["videoId"] = $videoId;
            $videoWindow["competitionId"] = $competitionId;
            $videoWindow["videoTitle"] = $videoTitle;
            $videoWindow["videoDescription"] = $videoDescription;
            $videoWindow["userId"] = $userId;
            $videoWindow["createdDate"] = $createdDate;
            $videoWindow["videoLength"] = $videoLength;
            $videoWindow["votes"] = $votes;
            $videoWindow["username"] = $username;
            $videoWindow["competitionTitle"] = $competitionTitle;
            $videoWindow["competitionDescription"] = $competitionDescription;
            $videoWindow["competitionType"] = $competitionType;
            $videoWindow["category"] = $category;
            $videoWindow["competitionStartDate"] = $competitionStartDate;
            $videoWindow["competitionEndDate"] = $competitionEndDate;
        }

        return $videoWindow;
    }

    public function loadComments(){
        $query = $this->getDBConnection()->prepare("select * from comments where production_id = ? order by posted desc");
        $query->bind_param("i", $this->videoId);
        $query->execute();
        $query->store_result();
        $query->bind_result($comment_id, $production_id, $username, $comment_text, $posted);
        $comments = array();
        $count = 0;
        while($query->fetch()){
            $comments[$count]["comment_id"] = $comment_id;
            $comments[$count]["production_id"] = $production_id;
            $comments[$count]["username"] = $username;
            $comments[$count]["comment_text"] = $comment_text;
            $comments[$count]["posted"] = $posted;
            $count++;
        }

        return $comments;
    }

    public function loadOtherVideosInCompetitionSidebar(){
        $allParticipants = $this->getAllParticipantsInCompetition();
        $numberOfParticipants = count($allParticipants);
        $maxIndex = $numberOfParticipants - 1;

        $participantsIndex = 0;
        if($numberOfParticipants > self::MAXIMUM_SIDEBAR_SIZE){
            $participantsIndex = rand(0, $maxIndex);
        }
        $startingIndex = $participantsIndex;

        $otherVideosInCompetition = array();
        $i = 0;
        while ($i < self::MAXIMUM_SIDEBAR_SIZE) {

            if($participantsIndex > $maxIndex){
                $participantsIndex = 0;
                if($participantsIndex == $startingIndex){
                    break;
                }
            }

            if($allParticipants[$participantsIndex]["id"] != $this->videoId){
                $otherVideosInCompetition[$i] = $allParticipants[$participantsIndex];
                $i++;
            }
            $participantsIndex++;
            if($participantsIndex == $startingIndex){
                break;
            }
        }

        return $otherVideosInCompetition;
    }

    private function getAllParticipantsInCompetition(){
        $query = $this->getDBConnection()->prepare("select v.video_id, v.title, v.description, v.created,
                                                    v.views, v.video_length, v.likes, u.username
                                                    from competition comp, competition_entries ce, video v, users u
                                                    where comp.id = ? and comp.id = ce.competition_id
                                                    and ce.video_id = v.video_id
                                                    and v.created_by = u.user_id");
        $query->bind_param("i", $this->videoWindow["competitionId"]);
        $query->execute();
        $query->store_result();
        $query->bind_result($videoId,  $title, $description, $created, $views, $length, $likes, $username);
        $count = 0;
        $participants = array();
        while($query->fetch()){
            $participants[$count]["id"] = $videoId;
            $participants[$count]["title"] = $title;
            $participants[$count]["description"] = $description;
            $participants[$count]["created"] = $created;
            $participants[$count]["user"] = $username;
            $participants[$count]["views"] = $views;
            $participants[$count]["length"] = $length;
            $participants[$count]["votes"] = $likes;
            //do whatever here
            $count++;
        }

        return $participants;
    }

    public function getVideoId(){
        return $this->videoId;
    }

    public function getVideoWindow(){
        return $this->videoWindow;
    }

    public function getComments(){
        return $this->comments;
    }

    public function getOtherVideosInCompetitionSidebar(){
        return $this->otherVideosInCompetitionSidebar;
    }

    /**
     * @return mysqli The database handle
     */
    public function getDBConnection(){
        return $this->db->getDBConnection();
    }
}

?>