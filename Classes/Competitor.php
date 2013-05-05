<?php
/**
 * @author Jason Halpern
 * @since 4/5/2013
 */

require_once('User.php');

class Competitor extends User{

    public function __construct($user){

        parent::__construct($user->getName(), $user->getEmail(), $user->getUsername(),
            $user->getId(), $user->getPassword());
    }

    public function getSubmissions(){
    }

    /**
     * Add a video as a new entry to an ongoing competition
     *
     * @param Competition $competition The competition that the video is being added to
     * @param Video $videoObject The video object representing the video for this entry
     * @param string $fileName The file we are uploading to our file storage.
     * @return bool True if the video has been added to the competition, False otherwise
     */
    public function addVideoToCompetition($competition, $videoObject, $fileName){
        $competition->addNewEntry($videoObject, $fileName, $this->getId());
    }

}

?>