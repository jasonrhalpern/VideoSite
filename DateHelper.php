<?php
    /**
     * @author Jason Halpern
     */

class DateHelper{

    /* todays date */
    public static function currentDate(){

        date_default_timezone_set('America/New_York');
        $date = date("Y-m-d");

        return $date;
    }

    /* todays date and current time */
    public static function currentDateAndTime(){

        date_default_timezone_set('America/New_York');
        $date = date("Y-m-d H:i:s");

        return $date;
    }
}

?>