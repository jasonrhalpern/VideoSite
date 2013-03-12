<?php

    require_once('/home/simawatkinto/AppConfig.php');

    /* Include the Amazon Web Services SDK for PHP */
    require_once 'AWS-SDK-PHP/aws.phar';

    use Aws\S3\S3Client;

    /* Instantiate the S3 client with our AWS credentials and desired AWS region */
    $client = S3Client::factory(array(
        'key'    => AppConfig::getAwsKey(),
        'secret' => AppConfig::getAwsSecret(),
    ));

    /* create a new bucket within assets.gookeyz.com */
    $bucket = 'assets.gookeyz.com/my-bucketdeuces7/';
    /* create a regular bucket at the root of my S3 domain */
    $buck = 'my-bucketdeuces7';

    $result = $client->createBucket(array(
        'Bucket' => $bucket
    ));

?>