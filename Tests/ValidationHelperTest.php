<?php
/**
 * @author Jason Halpern
 */
require_once dirname(__FILE__) . '/../Classes/ValidationHelper.php';
require_once dirname(__FILE__) . '/../Classes/User.php';

class ValidationHelperTest extends PHPUnit_Framework_TestCase{

    public function testInputExists(){

        $required = array('login', 'password', 'email');
        $input['login'] = "test";
        $input['password'] = "test";
        $errors = ValidationHelper::inputExists($required, $input);
        $this->assertContains("You must enter a email", $errors);
        $this->assertNotContains("You must enter a login", $errors);
        $this->assertNotContains("You must enter a password", $errors);

        $input['email'] = "test";
        $input['login'] = "";
        $input['password'] = "test";
        $errors = ValidationHelper::inputExists($required, $input);
        $this->assertNotContains("You must enter a email", $errors);
        $this->assertContains("You must enter a login", $errors);
        $this->assertNotContains("We could not add your video to the competition at this time,
                                    please try again later", $errors);

        $input['email'] = "";
        $input['login'] = "";
        $input['password'] = "";
        $errors = ValidationHelper::inputExists($required, $input);
        $this->assertContains("You must enter a email", $errors);
        $this->assertContains("You must enter a login", $errors);
        $this->assertContains("We could not add your video to the competition at this time,
                                please try again later", $errors);

        $input['email'] = "test";
        $input['login'] = "test";
        $input['password'] = "";
        $errors = ValidationHelper::inputExists($required, $input);
        $this->assertNotContains("You must enter a email", $errors);
        $this->assertNotContains("You must enter a login", $errors);
        $this->assertContains("You must enter a password", $errors);


    }
}
