<?php
/**
 * This class includes functions to generate Date and Time information.
 * The functions will be useful for generating date and time information when
 * we need to insert or update that information in the database or other
 * data store.
 *
 * @author Jason Halpern
 * @since 4/5/2013
 */

class DateHelper{

    /**
     * Generate the current date
     *
     * @return Date today's date
     */
    public static function currentDate(){

        date_default_timezone_set('America/New_York');
        $date = date("Y-m-d");

        return $date;
    }

    /**
     * Generate the current date and the current time
     *
     * @return Datetime today's date and the current time
     */
    public static function currentDateAndTime(){

        date_default_timezone_set('America/New_York');
        $dateTime = date("Y-m-d H:i:s");

        return $dateTime;
    }
}

?>