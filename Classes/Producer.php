<?php
/**
 * @author Jason Halpern
 */

require_once('User.php');

class Producer extends User{

    protected $productions; //array of IDs of this producer's productions

    public function __construct($user){

        parent::__construct($user->getName(), $user->getEmail(), $user->getUsername(),
                            $user->getId(), $user->getPassword());
    }

    public function createNewSeason($series){

        return $series->addNewSeason();
    }

    public function createSeries($series){

        /* insert series into our database and create a folder for the series episodes */
        if($this->db->insertSeries($series)){
            if($this->fileStorage->createSeriesFolder($series))
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

    /* change the description for a series */
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