<?php

    require_once('/home/simawatkinto/AppConfig.php');
    require_once 'Classes/S3.php';
require_once 'Classes/Series.php';

    /* Include the Amazon Web Services SDK for PHP */
    require_once 'AWS_SDK_PHP/aws.phar';

    use Aws\S3\S3Client;

    /* Instantiate the S3 client with our AWS credentials and desired AWS region */
    $client = S3Client::factory(array(
        'key'    => AppConfig::getAwsKey(),
        'secret' => AppConfig::getAwsSecret()
    ));

    /* create a new bucket within assets.gookeyz.com */
    $bucket = 'assets.gookeyz.com/1/';
    /* create a regular bucket at the root of my S3 domain */
    $buck = 'my-bucketdeuces7';

    $result = $client->createBucket(array(
        'Bucket' => $bucket
    ));

    $exists = $client->doesBucketExist($bucket);

    if($exists){
        echo 'true';
    }
    else{
        echo 'false';
    }

   // $exists = $client->deleteBucket(array(
     //   'Bucket' => $bucket
    //));

    $exists = $client->doesBucketExist(AppConfig::getS3Root() + '1/');

    if($exists){
        echo 'true';
    }
    else{
        echo 'false';
    }

    /* create a regular bucket at the root of my S3 domain */
    $buck = 'my-bucketdeuces7';


    $valid = $client->isValidBucketName($buck);

    if($valid){
        echo 'true';
    }
    else{
        echo 'false';
    }

    $s3 = new S3();
    if($s3)
          echo 'S3 exists';


    $series = new Series(1, 'The Crazies Are Out Bakonka', 'An in depth look into an insane asylum',
    'Documentary', 1);

if($client->isValidBucketName('assets.gookeyz.com'))
       echo 'Bucket name is good';

$client->createBucket(array(
    'Bucket' => $series->getFullSeriesPath()
));




?>