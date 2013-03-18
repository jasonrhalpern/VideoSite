<?php
/**
 * @author Jason Halpern
 */

require_once('/home/simawatkinto/AppConfig.php');

/* Include the Amazon Web Services SDK for PHP */
require_once dirname(__FILE__) . '/../AWS_SDK_PHP/aws.phar';
require_once dirname(__FILE__) . '/../Classes/FileStorage.php';

use Aws\S3\S3Client;

class S3 implements FileStorage{

    protected  $s3Client;

    public function __construct(){

        $this->s3Client = S3Client::factory(array(
                                'key'    => AppConfig::getAwsKey(),
                                'secret' => AppConfig::getAwsSecret()
                            ));
    }

    public function createSeriesFolder($series){

        $bucketName = $series->getFullSeriesPath();

        /* create a bucket in s3 for the series */
        $this->s3Client->createBucket(array(
            'Bucket' => $bucketName,
            'ACL' => 'public-read'
        ));

        /* bucket should have been created at this point */
        if($this->s3Client->doesBucketExist($bucketName))
            return true;

        return false;

    }

    public function deleteSeriesFolder($series){

        $bucketName = $series->getFullSeriesPath();

        /* create a bucket in s3 for the series */
        $this->s3Client->deleteBucket(array(
            'Bucket' => $bucketName
        ));

        /* bucket should no longer exist at this point */
        if($this->s3Client->doesBucketExist($bucketName))
            return false;

        return true;
    }

    public function createSeasonFolder($series){

    }

    public function deleteSeasonFolder($series, $seasonNumber){

    }

    public function addVideoFile($series){

    }

    public function deleteVideoFile(){

    }

    public function seriesFolderExists($series){

        $bucketName = $series->getFullSeriesPath();

        return $this->s3Client->doesBucketExist($bucketName);
    }

    public function seasonFolderExists($series){

    }
}
