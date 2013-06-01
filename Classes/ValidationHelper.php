<?php
/**
 * @author Jason Halpern
 * @since 5/26/2013
 */
class ValidationHelper{

    /**
     * This function checks to make sure each variable in the required array parameter
     * has an actual value in the input array parameter.
     *
     * @param array $required The array of required variables
     * @param array $input The array of input variables
     * @return array The errors associated with the input, or an empty array if there are no errors
     */
    public static function inputExists($required, $input){

        $errors = array();

        foreach($required as $field) {
            if(!(isset($input[$field]) && strlen($input[$field]))){
                $errors[] = "You must enter a " . $field;
            }
        }
        return $errors;
    }
}

?>