<?php
/**
 * This handles loading the information that we need for the sections on the competition page
 *
 * @author Jason Halpern
 * @since 7/6/2013
 */

class CompetitionPageBuilder{

    protected $competitionId;
    protected $competition;
    protected $state; //closed, current or upcoming
    protected $winner;
    protected $runnerUp;
    protected $finalists;
    protected $otherParticipants;
    protected $db;

    public function __construct($competitionId){
        $this->competitionId = $competitionId;
        $this->db = new MySQL();
    }

    public function buildCompetitionPage(){
        $this->competition = $this->loadCompetition();
        $this->determineState();
        $this->winner = $this->loadWinner();
        $this->runnerUp = $this->loadRunnerUp();
        $this->finalists = $this->loadFinalists();
        $this->otherParticipants = $this->loadOtherParticipants();
    }

    /**
     * Load the competition details for this page
     *
     * @return array with the competition details
     */
    public function loadCompetition(){
        $query = $this->getDBConnection()->prepare("select * from competition where id = ?");
        $query->bind_param("i", $this->competitionId);
        $query->execute();
        $query->store_result();
        $query->bind_result($id, $title, $description, $startDate, $endDate, $entryFee, $compType, $category);

        $competition = array();
        if($query->fetch()){
            $competition["id"] = $id;
            $competition["title"] = $title;
            $competition["description"] = $description;
            $competition["startDate"] = $startDate;
            $competition["endDate"] = $endDate;
            $competition["entryFee"] = $entryFee;
            $competition["type"] = $compType;
            $competition["category"] = $category;
            $competition["participants"] = $this->db->getNumParticipants($id);
        }

        return $competition;
    }

    public function loadWinner(){
        $query = $this->getDBConnection()->prepare("select v.video_id, v.title, v.description, v.created,
                                                    v.views, v.video_length, v.likes, u.username
                                                    from competition_winner cw, video v, users u
                                                    where cw.competition_id = ? and cw.video_id = v.video_id
                                                    and v.created_by = u.user_id");
        $query->bind_param("i", $this->competitionId);
        $query->execute();
        $query->store_result();
        $query->bind_result($videoId,  $title, $description, $created, $views, $length, $likes, $username);

        $winner = array();
        if($query->fetch()){
            $winner["id"] = $videoId;
            $winner["title"] = $title;
            $winner["description"] = $description;
            $winner["created"] = $created;
            $winner["user"] = $username;
            $winner["views"] = $views;
            $winner["length"] = $length;
            $winner["likes"] = $likes;
        }

        return $winner;
    }

    public function loadRunnerUp(){
        $query = $this->getDBConnection()->prepare("select v.video_id, v.title, v.description, v.created,
                                                    v.views, v.video_length, v.likes, u.username
                                                    from competition_runner_up cru, video v, users u
                                                    where cru.competition_id = ? and cru.video_id = v.video_id
                                                    and v.created_by = u.user_id");
        $query->bind_param("i", $this->competitionId);
        $query->execute();
        $query->store_result();
        $query->bind_result($videoId,  $title, $description, $created, $views, $length, $likes, $username);

        $runnerUp = array();
        if($query->fetch()){
            $runnerUp["id"] = $videoId;
            $runnerUp["title"] = $title;
            $runnerUp["description"] = $description;
            $runnerUp["created"] = $created;
            $runnerUp["user"] = $username;
            $runnerUp["views"] = $views;
            $runnerUp["length"] = $length;
            $runnerUp["likes"] = $likes;
        }

        return $runnerUp;

    }

    public function loadFinalists(){

    }

    public function loadOtherParticipants(){

    }

    public function determineState(){

    }

    public function getCompetitionId(){
        return $this->competitionId;
    }

    public function setCompetitionId($competitionId){
        $this->competitionId = $competitionId;
    }

    public function getCompetition(){
        return $this->competition;
    }

    public function setCompetition($competition){
        $this->competition = $competition;
    }

    public function getState(){
        return $this->state;
    }

    public function setState($state){
        $this->state = $state;
    }

    public function getWinner(){
        return $this->winner;
    }

    public function setWinner($winner){
        $this->winner = $winner;
    }

    public function getRunnerUp(){
        return $this->runnerUp;
    }

    public function setRunnerUp($runnerUp){
        $this->runnerUp = $runnerUp;
    }

    public function getFinalists(){
        return $this->finalists;
    }

    public function setFinalists($finalists){
        $this->finalists = $finalists;
    }

    public function getOtherParticipants(){
        return $this->otherParticipants;
    }

    public function setOtherParticipants($otherParticipants){
        $this->otherParticipants = $otherParticipants;
    }

    /**
     * @return mysqli The database handle
     */
    public function getDBConnection(){
        return $this->db->getDBConnection();
    }

}