<?php
    /**
     * @author Jason Halpern
     */

class AppConfig{

    private static $CONNECTION = 'gookeyzee.ckgrrjsrxixl.us-east-1.rds.amazonaws.com';
    private static $USERNAME = 'plownberga';
    private static $PASSWORD = 'hikal643vj';
    private static $TABLE = 'VideoBeans';
    private static $AWS_KEY = 'AKIAI3SR5BI4JS42YB4A';
    private static $AWS_SECRET = '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';
    private static $FACEBOOK_SECRET = '04c19c1b8f0e6f79725f1a401e2c26f9';
    private static $MEMCACHE_HOST = 'gookeyz-cache.r6hbmq.0001.use1.cache.amazonaws.com';

    public static function getConnection(){
        return self::$CONNECTION;
    }

    public static function getUsername(){
        return self::$USERNAME;
    }

    public static function getPassword(){
        return self::$PASSWORD;
    }

    public static function getTable(){
        return self::$TABLE;
    }

    public static function getAwsKey(){
        return self::$AWS_KEY;
    }

    public static function getAwsSecret(){
        return self::$AWS_SECRET;
    }

    public static function getFacebookSecret(){
        return self::$FACEBOOK_SECRET;
    }

    public static function getMemcacheHost(){
        return self::$MEMCACHE_HOST;
    }
}