<?php
/**
 * @author Jason Halpern
 */

class Producer extends User{

    protected $productions; //array of IDs of this person's productions

    public function __construct($user){

        parent::__construct($user->getName(), $user->getEmail(), $user->getUsername,
                            $user->getId(), $user->getPassword());
    }

    public function createNewSeason()
    {
    }

    public function createSeries($series){

        //insert series in DB
        //create folder in S3
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

    public function editSeriesDescr()
    {
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