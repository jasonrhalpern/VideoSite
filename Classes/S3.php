<?php
/**
 * This class includes the functions related to storing files and retrieving
 * data from Amazon's S3 service. The S3 service is used to store videos,
 * thumbnails, pictures and other series and user related files.
 *
 * @author Jason Halpern
 */

require_once('/home/simawatkinto/AppConfig.php');

/* Include the Amazon Web Services SDK for PHP */
require_once dirname(__FILE__) . '/../AWS_SDK_PHP/aws.phar';
require_once dirname(__FILE__) . '/../Classes/FileStorage.php';

use Aws\S3\S3Client;

class S3 implements FileStorage{

    /**
     * @var Aws\S3\S3Client $s3Client Our portal into our S3 service
     */
    protected  $s3Client;

    public function __construct(){

        $this->s3Client = S3Client::factory(array(
                                'key'    => AppConfig::getAwsKey(),
                                'secret' => AppConfig::getAwsSecret()
                            ));
    }

    /**
     * Create a bucket to hold the files for a particular series
     *
     * @param Series $series The series we are creating a bucket for
     * @return bool True if the bucket has been created, False otherwise
     */
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

    /**
     * Delete a bucket that holds the files for a particular series. All
     * files in the bucket will also be removed.
     *
     * @param Series $series The series we are deleting a bucket for
     * @return bool True if the bucket has been deleted, False otherwise
     */
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

    /**
     * Check to see if a bucket exists for a particular series
     *
     * @param Series $series The series we are looking up
     * @return bool True if the bucket exists for the series, False otherwise
     */
    public function seriesFolderExists($series){

        $bucketName = $series->getFullSeriesPath();

        return $this->s3Client->doesBucketExist($bucketName);
    }

    public function seasonFolderExists($series){

    }
}
