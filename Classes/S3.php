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
use Aws\Common\Aws;
use Aws\Common\Enum\Size;
use Aws\Common\Exception\MultipartUploadException;
use Aws\S3\Model\MultipartUpload\UploadBuilder;
use Guzzle\Http\EntityBody;

class S3 implements FileStorage{

    /**
     * @var S3Client $s3Client Our portal into our S3 service
     */
    protected  $s3Client;

    public function __construct(){

        $this->s3Client = S3Client::factory(array(
                                'key'    => AppConfig::getAwsKey(),
                                'secret' => AppConfig::getAwsSecret()
                            ));
    }

    /**
     * Create a folder in S3
     *
     * @param string $folderName The full path of the folder to be created
     */
    public function createFolder($folderName){

        $this->s3Client->createBucket(array(
            'Bucket' => $folderName,
            'ACL' => 'public-read'
        ));
    }

    /**
     * Delete a folder in S3
     *
     * @param string $folderName The full path of the folder to be deleted
     */
    public function deleteFolder($folderName){

        $this->s3Client->deleteBucket(array(
            'Bucket' => $folderName
        ));
    }

    /**
     * Check if a folder exists in S3
     *
     * @param string $folderName Check if this folder exists
     */
    public function folderExists($folderName){

        if($this->s3Client->doesBucketExist($folderName))
            return true;

        return false;
    }

    /**
     * Create a bucket to hold the files for a particular series
     *
     * @param Series $series The series for which we are creating a bucket
     * @return bool True if the bucket has been created, False otherwise
     */
    public function createSeriesFolder($series){

        $bucketName = $series->getFullSeriesPath();

        /* create a bucket in s3 for the series */
        $this->createFolder($bucketName);

        /* bucket should have been created at this point */
        return $this->folderExists($bucketName);

    }

    /**
     * Delete a bucket that holds the files for a particular series. All
     * files in the bucket will also be removed.
     *
     * @param Series $series The series for which we are deleting a bucket
     * @return bool True if the bucket has been deleted, False otherwise
     */
    public function deleteSeriesFolder($series){

        $bucketName = $series->getFullSeriesPath();

        /* delete a bucket in s3 for the series */
        $this->deleteFolder($bucketName);

        /* bucket should no longer exist at this point */
        return !$this->folderExists($bucketName);
    }

    /**
     * Create a new folder to hold the video files for the new season
     *
     * @param string $series The series we are creating a new season for
     * @return bool True if a folder for was created, False otherwise
     */
    public function createSeasonFolder($series){

        $seriesFolder = $series->getFullSeriesPath();
        $seasonFolder = 'season_' . $series->getSeasonNum();

        $bucketName = $seriesFolder . $seasonFolder . '/';

        /* create a bucket in s3 for this season */
        $this->createFolder($bucketName);

        /* bucket should have been created at this point */
        return $this->folderExists($bucketName);
    }

    /**
     * Delete the folder that holds the video files for this season
     *
     * @param string $series The series we are deleting a season for
     * @param string $seasonNumber The specific season folder to delete
     * @return bool True if a folder for was deleted, False otherwise
     */
    public function deleteSeasonFolder($series, $seasonNumber){

        $seriesFolder = $series->getFullSeriesPath();
        $seasonFolder = 'season_' . $seasonNumber;

        $bucketName = $seriesFolder . $seasonFolder . '/';

        /* delete a bucket in s3 for this season */
        $this->deleteFolder($bucketName);

        /* bucket should no longer exist at this point */
        return !$this->folderExists($bucketName);
    }

    /**
     * Check to see if a bucket exists for a particular series
     *
     * @param Series $series The series we are looking up
     * @return bool True if the bucket exists for the series, False otherwise
     */
    public function seriesFolderExists($series){

        $bucketName = $series->getFullSeriesPath();

        return $this->folderExists($bucketName);
    }

    /**
     * Check to see if a bucket exists for a particular season of a series
     *
     * @param Series $series The series we are looking up
     * @param string $seasonNum The specific season we are looking up
     * @return bool True if the bucket exists for the series, False otherwise
     */
    public function seasonFolderExists($series, $seasonNum){

        $seriesFolder = $series->getFullSeriesPath();
        $seasonFolder = 'season_' . $seasonNum;

        $bucketName = $seriesFolder . $seasonFolder . '/';

        return $this->folderExists($bucketName);
    }

    /**
     * Upload a video to the S3 service. The video will be on our server and
     * we will then need to add it to the appropriate bucket in S3. The name of
     * the video will change when we upload it to S3.
     *
     * @param string $fileName The name of the video file we are uploading from
     * the local filesystem
     * @param string $key The desired name of the file as it will appear in S3
     * @param string $folderName The S3 bucket the video will be uploaded to
     * @return bool True if the video was uploaded, False otherwise
     */
    public function uploadVideo($fileName, $key, $folderName){

        $body = EntityBody::factory(fopen($fileName, 'r'));

        /* Create a transfer object from the builder */
        $transfer = UploadBuilder::newInstance()
            ->setClient($this->s3Client) // An S3 client
            ->setSource($body) // Can be a path, file handle, or EntityBody object
            ->setBucket($folderName) // Bucket
            ->setKey($key) // Desired object key
            ->setMinPartSize(10 * Size::MB) // Minimum part size to use (at least 5 MB)
            ->setHeaders(array(
                'ACL' => 'public-read',
                'ContentType' => 'video/quicktime',
                //'ContentType' => $_FILES['image']['type'],
            ))
            ->build();

        /* Perform the upload */
        try {
            $transfer->upload();
            //code here will execute after upload completes
        } catch (MultipartUploadException $e) {
            $transfer->abort();
        }

        return $this->s3Client->doesObjectExist($folderName, $key);
    }

    /**
     * Delete a video from S3
     *
     * @param string $folderName The folder that the video is stored within
     * @param string $key The name of the video
     * @return bool True if the file has been deleted, False otherwise
     */
    public function deleteVideo($folderName, $key){

        $this->s3Client->deleteObject(array(
            'Key' => $key,
            'Bucket' => $folderName
        ));

        return !$this->s3Client->doesObjectExist($folderName, $key);
    }

    /**
     * Upload an image to the S3 service. The image will be on our server and
     * we will then need to add it to the appropriate bucket in S3. The name of
     * the image will change when we upload it to S3.
     *
     * @param string $fileName The name of the image file we are uploading from
     * the local filesystem
     * @param string $key The desired name of the file as it will appear in S3
     * @param string $folderName The S3 bucket the image will be uploaded to
     * @return bool True if the image was uploaded, False otherwise
     */
    public function uploadImage($fileName, $key, $folderName){

        $image = file_get_contents($fileName);

        $this->s3Client->putObject(array(
            'Key' => $key,
            'ACL' => 'public-read',
            'Body'	=> $image,
            'ContentType' => 'image/jpeg',
            //'ContentType' => $_FILES['image']['type'],
            'Bucket' => $folderName
        ));

        return $this->s3Client->doesObjectExist($folderName, $key);
    }

    /**
     * Delete an image from S3
     *
     * @param string $folderName The folder that the image is stored within
     * @param string $key The name of the image
     * @return bool True if the file has been deleted, False otherwise
     */
    public function deleteImage($folderName, $key){

        $this->s3Client->deleteObject(array(
            'Key' => $key,
            'Bucket' => $folderName
        ));

        return !$this->s3Client->doesObjectExist($folderName, $key);
    }
}
