<?php
/**
 * @author Jason Halpern
 * @since 4/5/2013
 */

require_once('User.php');

class Competitor extends User{

    /**
     * @param User $user
     */
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
     * @return array string[]
     */
    public function addVideoToCompetition($competition, $videoObject, $fileName){

        $joinCompetition['valid'] = true;

        $title = $videoObject->getTitle();
        $description = $videoObject->getDescription();

        if(!(isset($title) && strlen($title))){
            $joinCompetition['errors'][] = "You must enter a title for the video";
        }

        if(!(isset($description) && strlen($description))){
            $joinCompetition['errors'][] = "You must enter a description for the video";
        }

        if(array_key_exists('errors', $joinCompetition)){
            $joinCompetition['valid'] = false;
        }

        if($joinCompetition['valid'] == true){
            if(!$competition->addNewEntry($videoObject, $fileName, $this->getId())){
                $joinCompetition['valid'] = false;
                $joinCompetition['errors'][] = 'We could not add your video to the competition at this time,
                                                please try again later';
            }
        }

        return $joinCompetition;
    }

}

?>