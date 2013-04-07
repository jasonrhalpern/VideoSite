<?php
/**
 * The database interface includes all the functions that will need to be
 * supported by our database, whether its MySQL, Oracle, etc. The database
 * will include information about users, series, videos, etc.
 *
 * @author Jason Halpern
 * @since 4/5/2013
 */

interface Database
{
    public function connect();
    public function disconnect();

    public function getDBConnection();
    public function isConnected();

    public function dataExists($query);
    public function isExecuted($query);

    public function insertUser($user);
    public function deleteUser($user);
    public function userExists($user);

    public function insertVideo($video);
    public function deleteVideo($video);

    public function insertEpisode($episode);
    public function deleteEpisode($episode);

    public function insertSeason($seriesId, $seasonNum, $description);
    public function deleteSeason($seriesId, $seasonNum);

    public function insertSeries($series);
    public function deleteSeries($series);
    public function seriesExists($series);

}

?>