<?php
/**
 * MySQL class to handle database related operations, implements Database.php
 *
 * This class handles a lot of the function related to database operations,
 * which includes connecting/disconnecting from the database,
 * inserting/deleting users, series, video metadata, etc.
 *
 * @author Jason Halpern
 * @since 4/5/2013
 */

require_once('/home/simawatkinto/AppConfig.php');
require_once('Database.php');

class MySQL implements Database{

    /**
     * @var mysqli object - Our handle to interact with the database
     */
    protected  $dbh;

    public function __construct(){
        $this->connect();
    }

    public function __destruct() {
        $this->dbh->close();
    }

    public function connect(){
        $this->dbh = new mysqli(AppConfig::getConnection(), AppConfig::getUsername(),
                                AppConfig::getPassword(), AppConfig::getTable());
    }

    public function getDBConnection(){
        return $this->dbh;
    }

    /**
     * @return bool True if connected to the database, False otherwise
     */
    public function isConnected(){

        if($this->dbh){
            return true;
        }
        else{
            return false;
        }
    }

    /**
     * Close the connection to the database
     */
    public function disconnect(){
        $this->dbh->close();
    }

    /**
     * Check if our database query returned any results
     *
     * @param mysqli $query The query we are executing
     * @return bool True if results have been found, False otherwise
     */
    public function dataExists($query){
        $query->execute();
        $query->store_result();
        $dataExists = $query->num_rows;
        $query->close();

        /* check if this data already exists in the database */
        if($dataExists > 0){
            return true;
        }
        else{
            return false;
        }
    }

    /**
     * Check if the query was successfully executed
     *
     * @param mysqli $query The query that is being executed
     * @return bool True if the query was executed, False otherwise
     */
    public function isExecuted($query){

        $query->execute();
        $query->store_result();
        $querySuccess = $query->affected_rows;
        $query->close();

        /* check if successful */
        if($querySuccess !== -1){
            return true;
        }
        else{
            return false;
        }
    }


    /**
     * Add the user to the database
     *
     * @param User $newUser The user we want to insert into the database
     * @return bool True if the user has been inserted into the database, False otherwise
     */
    public function insertUser($newUser){

        $temp_id = 0;

        $query = $this->dbh->prepare("insert into users values(?, ?, ?, ?, ?, ?)");
        $query->bind_param("isssss", $temp_id, $newUser->getUsername(), $newUser->getName(), $newUser->getEmail(),
                                     $newUser->getEncryptedPassword(), $newUser->getJoined());

        return $this->isExecuted($query);

    }

    /**
     * Delete a user from the database
     *
     * @param User $user The user we want to delete from the database
     * @return bool True if the user has been deleted from the database, False otherwise
     */
    public function deleteUser($user){

        $query = $this->dbh->prepare("delete from users where username=?");
        $query->bind_param("s", $user->getUsername());

        return $this->isExecuted($query);
    }

    /**
     * Check if a user exists with the given email and password
     *
     * @param User $user The user we want to check and see if they exist in the DB
     * @return bool True if the user exists in the database, False otherwise
     */
    public function userExists($user){
        $query = $this->dbh->prepare("select * from users where email = ? and password = ?");
        $query->bind_param("ss", $user->getEmail(), $user->getEncryptedPassword());

        return $this->dataExists($query);
    }

    /**
     * Load all of the user details from the database
     *
     * @param User $user The user whose details we are loading
     * @param mysqli $query The query that is being executed
     */
    public static function getUserInfo($user, $query){

        $query->execute();
        $query->bind_result($id, $username, $name, $email, $password, $joined);
        if($query->fetch()){
            $user->setId($id);
            $user->setUsername($username);
            $user->setName($name);
            $user->setJoined($joined);
        }
    }

    /**
     * Insert a new series into the database
     *
     * @param Series $series The series that we want to add to the database
     * @return bool True if the series has been added to the DB, false otherwise
     */
    public function insertSeries($series){

        /* check if a user is trying to create a series with the same name as one that already exists */
        if($this->seriesExists($series))
            return false;

        $temp_id = 0;

        $query = $this->dbh->prepare("insert into series values(?, ?, ?, ?, ?, ?, ?)");
        $query->bind_param("iisisss", $temp_id, $series->getCreatorId(), $series->getCreatedDate(),
                    $series->getSeasonNum(), $series->getTitle(), $series->getDescription(), $series->getCategory());

        return $this->isExecuted($query);
    }

    /**
     * Delete a series from the database
     *
     * @param Series $series The series we want to delete from the database
     * @return bool True if the series has been deleted, False otherwise
     */
    public function deleteSeries($series){
        $query = $this->dbh->prepare("delete from series where title=?");
        $query->bind_param("s", $series->getTitle());

        return $this->isExecuted($query);
    }

    /**
     * Check if a series with this title already exists in the database
     *
     * @param Series $series The series we are looking up in the database
     * @return bool True if a series with this title exists, False otherwise
     */
    public function seriesExists($series){
        $query = $this->dbh->prepare("select * from series where title = ?");
        $query->bind_param("s", $series->getTitle());

        return $this->dataExists($query);
    }

    /**
     * Execute a query about a Series and then load all the details of the series that
     * match the query
     *
     * @param Series $series The series whose details we are loading
     * @param mysqli $query The query we are executing
     */
    public static function getSeriesInfo($series, $query){
        $query->execute();
        $query->bind_result($id, $creatorId, $createdDate, $seasonNumber, $title, $description, $category);
        if($query->fetch()){
            $series->setId($id);
            $series->setCreatorId($creatorId);
            $series->setCreatedDate($createdDate);
            $series->setSeasonNum($seasonNumber);
            $series->setTitle($title);
            $series->setDescription($description);
            $series->setCategory($category);
        }

    }

    /**
     * Insert a new video into the database
     *
     * @param Video $video The video that we want to add to the database
     * @return bool True if the video has been added to the DB, false otherwise
     */
    public function insertVideo($video){
        $temp_id = 0;

        $query = $this->dbh->prepare("insert into video values(?, ?, ?, ?, ?, ?, ?, ?)");
        $query->bind_param("isssiiii", $temp_id, $video->getTitle(), $video->getDescription(),
                                        $video->getPostedDate(), $video->getSubmitter(), $video->getViews(),
                                        $video->getLength(), $video->getLikes());

        return $this->isExecuted($query);
    }

    /**
     * Delete a video from the database
     *
     * @param Video $video The video we want to delete from the database
     * @return bool True if the video has been deleted, False otherwise
     */
    public function deleteVideo($video){
        $query = $this->dbh->prepare("delete from video where video_id=?");
        $query->bind_param("i", $video->getVideoId());

        return $this->isExecuted($query);
    }

    /**
     * Execute a query about a video and then load all the details of the video that
     * match the query
     *
     * @param Video $video The video whose details we are loading
     * @param mysqli $query The query we are executing
     */
    public static function getVideoInfo($video, $query){
        $query->execute();
        $query->bind_result($id, $title, $description, $createdDate, $createdBy, $views, $length, $likes);
        if($query->fetch()){
            $video->setVideoId($id);
            $video->setTitle($title);
            $video->setDescription($description);
            $video->setPostedDate($createdDate);
            $video->setSubmitter($createdBy);
            $video->setViews($views);
            $video->setLength($length);
            $video->setLikes($likes);
        }

    }

    /**
     * Insert a new episode into the database
     *
     * @param Episode $episode The episode that we want to add to the database
     * @return bool True if the episode has been added to the DB, false otherwise
     */
    public function insertEpisode($episode){

        /* Need to add the video details to the database first */
        if(!$this->insertVideo($episode->getVideo()))
            return false;

        /*
         *  Need to get the ID of the video before adding the other details to our episode table.
         *  We do this by getting the last video submitted by this producer, which is the video
         *  we are looking for.
         */
        $videoId = $this->mostRecentVideoId($episode->getVideo()->getSubmitter());
        $episode->getVideo()->setVideoId($videoId);

        /* Now insert the details into the episode table */
        $query = $this->dbh->prepare("insert into episode values(?, ?, ?, ?)");
        $query->bind_param("iiii", $episode->getVideo()->getVideoId(), $episode->getSeriesId(),
                                    $episode->getSeasonNumber(), $episode->getEpisodeNumber());

        return $this->isExecuted($query);
    }

    /**
     * This gets the video ID of the last video that this user added.
     *
     * @param $userId
     * @return int|bool The video ID if the video exists, false otherwise
     */
    public function mostRecentVideoId($userId){

        $query = $this->dbh->prepare("select * from video where created_by = ? order by video_id DESC LIMIT 1");
        $query->bind_param("i", $userId);
        $query->execute();
        $query->bind_result($id, $title, $desc, $created, $createdBy, $views, $length, $likes);
        if($query->fetch())
            return $id;
        else
            return false;
    }

    /**
     * Delete an episode from the database
     *
     * @param Episode $episode The episode we want to delete from the database
     * @return bool True if the episode has been deleted, False otherwise
     */
    public function deleteEpisode($episode){

        /* delete the data from the video table first */
        if(!$this->deleteVideo($episode->getVideo()))
            return false;

        $query = $this->dbh->prepare("delete from episode where video_id = ?");
        $query->bind_param("i", $episode->getVideo()->getVideoId());

        return $this->isExecuted($query);
    }

    /**
     * Execute a query about a Episode and then load all the details of the episode that
     * match the query
     *
     * @param Episode $episode The episode whose details we are loading
     * @param mysqli $query The query we are executing
     */
    public static function getEpisodeInfo($episode, $query){
        $query->execute();
        $query->bind_result($videoId, $seriesId, $seasonNumber, $episodeNumber);
        if($query->fetch()){
            $episode->setSeriesId($seriesId);
            $episode->setSeasonNumber($seasonNumber);
            $episode->setEpisodeNumber($episodeNumber);
        }

    }

    /**
     * Returns the number of rows found from executing the query.
     *
     * @param mysqli $query The query we are executing, must use MySQL's COUNT() function.
     * @return int|bool The number of rows, false if the query failed to execute
     */
    public function getNumberOfRows($query){

        $query->execute();
        $query->bind_result($count);
        if($query->fetch()){
            return $count;
        }

        return false;
    }


    /**
     * Insert a new season into the database
     *
     * @param int $seriesId The id of the series that we are adding a season to
     * @param int $seasonNum The number of the new season
     * @param string $description The description for the new season
     * @return bool True if the season has been added to the DB, false otherwise
     */
    public function insertSeason($seriesId, $seasonNum, $description){

        $query = $this->dbh->prepare("insert into season values(?, ?, ?)");
        $query->bind_param("iis", $seriesId, $seasonNum, $description);

        return $this->isExecuted($query);
    }

    /**
     * Delete a season from the database
     *
     * @param int $seriesId The id of the series that we are deleting a season from
     * @param int $seasonNum The number of the season that we are deleting
     * @return bool True if the season has been deleted from the DB, false otherwise
     */
    public function deleteSeason($seriesId, $seasonNum){
        $query = $this->dbh->prepare("delete from season where series_id = ? and season_num = ?");
        $query->bind_param("ii", $seriesId, $seasonNum);

        return $this->isExecuted($query);
    }

    /**
     * Check if a specific season exists for a given series
     *
     * @param int $seriesId The id of the series
     * @param int $seasonNum The season number
     * @return bool True if the season exists for this series, false otherwise
     */
    public function seasonExists($seriesId, $seasonNum){
        $query = $this->dbh->prepare("select * from season where series_id = ? and season_num = ?");
        $query->bind_param("ii", $seriesId, $seasonNum);

        return $this->dataExists($query);
    }

    /**
     * Insert a new competition into the database
     *
     * @param Competition $competition The competition we want to add to the database
     * @return bool True if the competition has been added, false otherwise
     */
    public function insertCompetition($competition){
        $temp_id = 0;

        $query = $this->dbh->prepare("insert into competition values(?, ?, ?, ?, ?, ?, ?, ?)");
        $query->bind_param("issssdss", $temp_id, $competition->getTitle(), $competition->getDescription(),
            $competition->getStartDate(), $competition->getEndDate(), $competition->getEntryFee(),
            $competition->getType(), $competition->getCategory());

        return $this->isExecuted($query);
    }

    /**
     * Delete a competition from the database
     *
     * @param int $competitionId The ID of the competition we want to delete
     * @return bool True if the competition has been deleted, false otherwise
     */
    public function deleteCompetition($competitionId){
        $query = $this->dbh->prepare("delete from competition where id = ?");
        $query->bind_param("i", $competitionId);

        return $this->isExecuted($query);
    }

    /**
     * Execute a query about a Competition and then load all the details of the competition that
     * match the query
     *
     * @param Competition $competition The competition whose details we are loading
     * @param mysqli $query The query we are executing
     */
    public static function getCompetitionInfo($competition, $query){
        $query->execute();
        $query->bind_result($id, $title, $description, $startDate,
                            $endDate, $entryFee, $type, $category);
        if($query->fetch()){
            $competition->setId($id);
            $competition->setTitle($title);
            $competition->setDescription($description);
            $competition->setStartDate($startDate);
            $competition->setEndDate($endDate);
            $competition->setEntryFee($entryFee);
            $competition->setType($type);
            $competition->setCategory($category);
        }

    }

    /**
     * WARNING: DON'T use this, only for testing purposes.
     * This gets the competition ID of the last competition that was added to the database.
     *
     * @return int|bool The competition ID if the competition exists, false otherwise
     */
    public function mostRecentCompetitionId(){

        $query = $this->dbh->prepare("select * from competition order by id DESC LIMIT 1");
        $query->execute();
        $query->bind_result($id, $title, $description, $startDate, $endDate, $fee, $type, $category);
        if($query->fetch())
            return $id;
        else
            return false;
    }

    /**
     * Add a new entry to the competition
     *
     * @param int $competitionId The competition we are adding an entry to
     * @param int $videoId The video we are adding to the competition
     * @return bool True if the entry has been added, False otherwise
     */
    public function insertCompetitionEntry($competitionId, $videoId){

        $query = $this->dbh->prepare("insert into competition_entries values(?, ?)");
        $query->bind_param("ii", $competitionId, $videoId);

        return $this->isExecuted($query);
    }

    /**
     * Delete a new entry from the competition
     *
     * @param int $competitionId The competition we are deleting an entry from
     * @param int $videoId The video we are unregistering from the competition
     * @return bool True if the entry has been added, False otherwise
     */
    public function deleteCompetitionEntry($competitionId, $videoId){

        $query = $this->dbh->prepare("delete from competition_entries where competition_id = ? and video_id = ?");
        $query->bind_param("ii", $competitionId, $videoId);

        return $this->isExecuted($query);
    }

    /**
     * Add a new comment to the database
     *
     * @param Comment $comment The comment we want to add to the database
     * @return bool True if the comment has been added to the database, false otherwise
     */
    public function insertComment($comment){
        $temp_id = 0;

        $query = $this->dbh->prepare("insert into comments values(?, ?, ?, ?, ?)");
        $query->bind_param("iisss", $temp_id, $comment->getProductionId(), $comment->getUsername(),
                                    $comment->getText(), $comment->getPosted());

        return $this->isExecuted($query);
    }

    /**
     * Delete a comment from the database
     *
     * @param int $commentId The ID of the comment we want to delete from the database
     * @return bool True if the comment has been deleted from the database, false otherwise
     */
    public function deleteComment($commentId){
        $query = $this->dbh->prepare("delete from comments where comment_id = ?");
        $query->bind_param("i", $commentId);

        return $this->isExecuted($query);
    }

    /**
     * This gets the comment ID of the last comment that this user added.
     *
     * @param $username
     * @return int|bool The comment ID if the comment exists, false otherwise
     */
    public function mostRecentCommentId($username){

        $query = $this->dbh->prepare("select * from comments where username = ? order by comment_id DESC LIMIT 1");
        $query->bind_param("s", $username);
        $query->execute();
        $query->bind_result($comment_id, $production_id, $user, $text, $posted);
        if($query->fetch())
            return $comment_id;
        else
            return false;
    }

    /**
     * Add a vote to an entry in a competition
     *
     * @param int $competitionId The competition that the entry is in
     * @param int $videoId The video that the vote is being added to
     * @param int $userId The ID of the user casting the vote
     * @return bool True if the vote was added to the database, false otherwise
     */
    public function addVote($competitionId, $videoId, $userId){

        $query = $this->dbh->prepare("insert into competition_votes values(?, ?, ?)");
        $query->bind_param("iii", $competitionId, $videoId, $userId);

        return $this->isExecuted($query);
    }

    /**
     * Delete a vote from an entry in a competition
     *
     * @param int $competitionId The competition that the entry is in
     * @param int $videoId The video that the vote is being deleted from
     * @param int $userId The ID of the user deleting the vote
     * @return bool True if the vote was deleted from the database, false otherwise
     */
    public function deleteVote($competitionId, $videoId, $userId){

        $query = $this->dbh->prepare("delete from competition_votes where competition_id = ? and
                                    video_id = ? and user_id = ?");
        $query->bind_param("iii", $competitionId, $videoId, $userId);

        return $this->isExecuted($query);
    }

    /**
     * Select a winner for a certain competition.
     *
     * @param $competitionId The id of the competition
     * @param $videoId The id of the video that is the winner
     * @return bool True if the winner has been added to the database, false otherwise
     */
    public function insertCompetitionWinner($competitionId, $videoId){

        $query = $this->dbh->prepare("insert into competition_winner values(?, ?)");
        $query->bind_param("ii", $competitionId, $videoId);

        return $this->isExecuted($query);
    }


    /**
     * Remove a winner for a certain competition.
     *
     * @param $competitionId The id of the competition
     * @return bool True if the winner has been removed from the database, false otherwise
     */
    public function deleteCompetitionWinner($competitionId){
        $query = $this->dbh->prepare("delete from competition_winner where competition_id = ?");
        $query->bind_param("i", $competitionId);

        return $this->isExecuted($query);
    }

    /**
     * Check if a winner already exists for a competition.
     *
     * @param $competitionId The id of the competition
     * @return bool True if a winner already exists, false otherwise
     */
    public function competitionWinnerExists($competitionId){

        $query = $this->dbh->prepare("select * from competition_winner where competition_id = ?");
        $query->bind_param("i", $competitionId);

        return $this->dataExists($query);
    }

    /**
     * Select a runner up for a certain competition.
     *
     * @param $competitionId The id of the competition
     * @param $videoId The id of the video that is the runner up
     * @return bool True if the runner up has been added to the database, false otherwise
     */
    public function insertCompetitionRunnerUp($competitionId, $videoId){
        $query = $this->dbh->prepare("insert into competition_runner_up values(?, ?)");
        $query->bind_param("ii", $competitionId, $videoId);

        return $this->isExecuted($query);
    }

    /**
     * Remove a runner up for a certain competition.
     *
     * @param $competitionId The id of the competition
     * @return bool True if the runner up has been removed from the database, false otherwise
     */
    public function deleteCompetitionRunnerUp($competitionId){
        $query = $this->dbh->prepare("delete from competition_runner_up where competition_id = ?");
        $query->bind_param("i", $competitionId);

        return $this->isExecuted($query);
    }

    /**
     * Check if a runner up already exists for a competition.
     *
     * @param $competitionId The id of the competition
     * @return bool True if a runner up already exists, false otherwise
     */
    public function competitionRunnerUpExists($competitionId){
        $query = $this->dbh->prepare("select * from competition_runner_up where competition_id = ?");
        $query->bind_param("i", $competitionId);

        return $this->dataExists($query);
    }

    /**
     * Get the winner of a certain competition
     *
     * @param $competitionId The id of the competition we want the winner for
     * @return int|bool The id of the video that is the winner, false if no winner exists
     */
    public function getCompetitionWinner($competitionId){
        $query = $this->dbh->prepare("select * from competition_winner where competition_id = ?");
        $query->bind_param("i", $competitionId);
        $query->execute();
        $query->bind_result($$competitionId, $videoId);
        if($query->fetch())
            return $videoId;
        else
            return false;
    }

    /**
     * Get the runner up of a certain competition
     *
     * @param $competitionId The id of the competition we want the runner up for
     * @return int|bool The id of the video that is the runner up, false if no runner up exists
     */
    public function getCompetitionRunnerUp($competitionId){
        $query = $this->dbh->prepare("select * from competition_runner_up where competition_id = ?");
        $query->bind_param("i", $competitionId);
        $query->execute();
        $query->bind_result($$competitionId, $videoId);
        if($query->fetch())
            return $videoId;
        else
            return false;
    }

    /**
     * Get the number of participants in a specific competition
     *
     * @param $competitionId The id of the competition
     * @return int|bool The number of participants in the competition, false if it doesn't exist
     */
    public function getNumParticipants($competitionId){
        $query = $this->dbh->prepare("select COUNT(*) from competition_entries where competition_id = ?");
        $query->bind_param("i", $competitionId);

        return $this->getNumberOfRows($query);
    }

}

?>