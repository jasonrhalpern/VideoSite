<?php
/**
 * This handles loading the information that we need for the sections on the homepage
 *
 * @author Jason Halpern
 * @since 4/5/2013
 */
class HomepageBuilder{

    protected $currentCompetitions;
    protected $upcomingCompetitions;
    protected $recentWinners;
    protected $db;

    public function __construct(){
        $this->db = new MySQL();
    }

    public function buildHomepage(){
        $this->currentCompetitions = $this->loadCurrentCompetitions();
        $this->upcomingCompetitions = $this->loadUpcomingCompetitions();
        $this->recentWinners = $this->loadRecentWinners();
    }

    /**
     * Load the competitions that are currently ongoing
     *
     * @return array of competitions
     */
    public function loadCurrentCompetitions(){
        $query = $this->getDBConnection()->prepare("select * from competition
                                                  where start_date <= CURDATE() and end_date >= CURDATE()
                                                  order by end_date asc");
        $query->execute();
        $query->store_result();
        $query->bind_result($id, $title, $description, $start_date, $end_date, $entry_fee, $comp_type, $category);

        $count = 0;
        $currentCompetitions = array();
        while($query->fetch()){
            //WHAT ABOUT # PARTICIPANTS??
            $currentCompetitions[$count]["id"] = $id;
            $currentCompetitions[$count]["title"] = $title;
            $currentCompetitions[$count]["description"] = $description;
            $currentCompetitions[$count]["start_date"] = $start_date;
            $currentCompetitions[$count]["end_date"] = $end_date;
            $currentCompetitions[$count]["entry_fee"] = $entry_fee;
            $currentCompetitions[$count]["type"] = $comp_type;
            $currentCompetitions[$count]["category"] = $category;
            $currentCompetitions[$count]["participants"] = $this->db->getNumParticipants($id);
            $count++;
        }

        return $currentCompetitions;
    }

    /**
     * Load the competitions that will be starting soon
     *
     * @return array of competitions
     */
    public function loadUpcomingCompetitions(){
        $query = $this->getDBConnection()->prepare("select * from competition where start_date > CURDATE()
                                                  order by start_date asc");
        $query->execute();
        $query->store_result();
        $query->bind_result($id, $title, $description, $start_date, $end_date, $entry_fee, $comp_type, $category);
        $count = 0;
        $upcomingCompetitions = array();
        while($query->fetch()){
            //push the competitions into array
            $upcomingCompetitions[$count]["id"] = $id;
            $upcomingCompetitions[$count]["title"] = $title;
            $upcomingCompetitions[$count]["description"] = $description;
            $upcomingCompetitions[$count]["start_date"] = $start_date;
            $upcomingCompetitions[$count]["end_date"] = $end_date;
            $upcomingCompetitions[$count]["entry_fee"] = $entry_fee;
            $upcomingCompetitions[$count]["type"] = $comp_type;
            $upcomingCompetitions[$count]["category"] = $category;
            $count++;
        }

        return $upcomingCompetitions;
    }

    /**
     * Load the winners of recent competitions
     *
     * @return array with the details of the winning videos
     */
    public function loadRecentWinners(){
        $query = $this->getDBConnection()->prepare("select comp.id, comp.title, comp.category,
                                                    v.video_id, v.title, v.likes, v.created_by, u.username
                                                    from competition comp, video v, competition_winner cw, users u
                                                    where comp.id = cw.competition_id
                                                    and cw.video_id = v.video_id
                                                    and v.created_by = u.user_id
                                                    order by comp.end_date desc");
        $query->execute();
        $query->store_result();
        $query->bind_result($competitionId, $competitionTitle, $competitionCategory, $videoId, $videoTitle,
                            $videoLikes, $videoCreatedBy, $username);
        $count = 0;
        $recentWinners = array();
        while($query->fetch()){
            //push the winners into array
            $recentWinners[$count]["competitionId"] = $competitionId;
            $recentWinners[$count]["competitionTitle"] = $competitionTitle;
            $recentWinners[$count]["competitionCategory"] = $competitionCategory;
            $recentWinners[$count]["videoId"] = $videoId;
            $recentWinners[$count]["videoTitle"] = $videoTitle;
            $recentWinners[$count]["videoLikes"] = $videoLikes;
            $recentWinners[$count]["videoCreatedBy"] = $videoCreatedBy;
            $recentWinners[$count]["username"] = $username;
            $count++;
        }

        return $recentWinners;
    }

    public function getCurrentCompetitions(){
        return $this->currentCompetitions;
    }

    public function setCurrentCompetitions($currentCompetitions){
        $this->currentCompetitions = $currentCompetitions;
    }

    public function getUpcomingCompetitions(){
        return $this->upcomingCompetitions;
    }

    public function setUpcomingCompetitions($upcomingCompetitions){
        $this->upcomingCompetitions = $upcomingCompetitions;
    }

    public function getRecentWinners(){
        return $this->recentWinners;
    }

    public function setRecentWinners($recentWinners){
        $this->recentWinners = $recentWinners;
    }

    /**
     * @return mysqli The database handle
     */
    public function getDBConnection(){
        return $this->db->getDBConnection();
    }
}
