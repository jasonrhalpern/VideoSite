<?php
/**
 * This class holds all details related to a comment
 *
 * @author Jason Halpern
 * @since 4/5/2013
 */
class Comment{

    protected $commentId;
    protected $productionId;
    protected $username;
    protected $text;
    protected $posted; //datetime

    public function __construct($productionId, $username, $text){

        $this->productionId = $productionId;
        $this->username = $username;
        $this->text = $text;
        $this->posted = DateHelper::currentDateAndTime();
    }

    public function getCommentId(){
        return $this->commentId;
    }

    public function setCommentId($commentId){
        $this->commentId = $commentId;
    }

    public function getProductionId(){
        return $this->productionId;
    }

    public function setProductionId($productionId){
        $this->productionId = $productionId;
    }

    public function getUsername(){
        return $this->username;
    }

    public function setUsername($username){
        $this->username = $username;
    }

    public function getText(){
        return $this->text;
    }

    public function setText($text){
        $this->text = $text;
    }

    public function getPosted(){
        return $this->posted;
    }

    public function setPosted($posted){
        $this->posted = $posted;
    }

}
