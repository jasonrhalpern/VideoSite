<?php

    require_once('/home/simawatkinto/AppConfig.php');

    /* Include the SDK using the Composer autoloader */
    require_once 'AWS-SDK-PHP/aws.phar';

    use Aws\S3\S3Client;

    /* Instantiate the S3 client with our AWS credentials and desired AWS region */
    $client = S3Client::factory(array(
        'key'    => AppConfig::getAwsKey(),
        'secret' => AppConfig::getAwsSecret(),
    ));

    $bucket = 'my-bucketdeuces';

    $result = $client->createBucket(array(
        'Bucket' => $bucket
    ));

?>