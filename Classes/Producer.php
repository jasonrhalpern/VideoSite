<?php
/**
 * A Producer is able to create new series, add new videos to that series, update
 * series information, etc. This class extends User because a Producer is basically
 * a user with the additional power of managing productions.
 *
 * @author Jason Halpern
 * @since 4/5/2013
 */

require_once('User.php');

class Producer extends User{

    /**
     * @var array $productions An array of the IDs of the series this person produces
     */
    protected $productions;

    public function __construct($user){

        parent::__construct($user->getName(), $user->getEmail(), $user->getUsername(),
                            $user->getId(), $user->getPassword());
    }

    /**
     * Create a new season for this series
     *
     * @param Series $series The series for which we are creating a new season
     * @return bool True if a new season has been created, False otherwise
     */
    public function createNewSeason($series){

        return $series->addNewSeason();
    }

    /**
     * Create a new series
     *
     * @param Series $series The details of the new series that is being created
     * @return bool True if the series has been created, False otherwise
     */
    public function createSeries($series){

        /*
         * insert series into our database and create a folder for the series episodes
         * and create a folder for season 1 episodes
         */
        if($this->db->insertSeries($series)){
            if($this->fileStorage->createSeriesFolder($series))
                if($this->fileStorage->createSeasonFolder($series))
                    //upload pilot episode??
                    return true;
        }

        return false;
    }

    public function addVidToSeries()
    {
    }

    public function submitPilot()
    {
    }

    public function editSeriesTitle()
    {
    }

    public function editSeriesDirector()
    {
    }

    /**
     * Change the description for a series
     *
     * @param Series $series The series that we are editing
     * @param string $newDescription The new description for the series
     * @return bool True if the description has been updated, False otherwise
     */
    public function editSeriesDescr($series, $newDescription){

        $query = $this->getDBConnection()->prepare("update series set description = ? where series_id = ?");
        $query->bind_param("si", $newDescription, $series->getId());

        return $this->db->isExecuted($query);
    }

    public function editSeriesWriters()
    {
    }

    public function editSeriesActors()
    {
    }

    public function editSeriesProducers()
    {
    }

}

?>