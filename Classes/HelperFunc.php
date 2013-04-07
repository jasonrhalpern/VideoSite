<?php
/**
 * This includes random functions that we will need throughout our system.
 *
 * @author Jason Halpern
 * @since 4/5/2013
 */
class HelperFunc{

    /**
     * Generates a random password using a word and a number
     *
     * @return string A new password
     */
    public static function generateRandomPassword(){
        /*
         * Array of random words to generate new passwords.
         * A number is added to the end of the word below so the password remains unique.
         */
        $words = array('mother', 'brother', 'seattle', 'newark', 'lists', 'piano', 'trumpet', 'kitchen',
            'freezer', 'couch', 'plant','smoke', 'beer', 'music', 'silly', 'goose', 'horse', 'books',
            'heart', 'kidney', 'brain', 'grass', 'iron', 'leather', 'sneaker', 'flex', 'steel', 'bucket',
            'ice', 'swim', 'run','catch', 'fall','weather', 'cloudy','table', 'harmony','tense', 'radio',
            'lagoon', 'stream', 'water', 'listen', 'hear', 'frost', 'bugs', 'henry', 'sally', 'molly', 'school',
            'taxi', 'king','queen','july','june','birth', 'less', 'clue', 'wood','suede','midnight','spent',
            'abra','candle','ken','suzie');

        $wordCount = count($words);
        /* add random number to the end of the random word from the array above */
        $newPassword = ($words[mt_rand(0, $wordCount-1)].mt_rand(0,9999));
        $newPassword = strtolower($newPassword);

        return $newPassword;
    }

    /**
     * WARNING: NEVER USE THIS. EVER.
     */
    public static function loadVideoByDetails($title, $description){

        $video = new Video(null, null, null);
        $query = $video->getDBConnection()->prepare("select * from video where title = ? and description = ?");
        $query->bind_param("ss", $title, $description);

        $video->getVideoInfo($query);

        if(is_null($video->getTitle()))
            return false;

        return $video;
    }

}
