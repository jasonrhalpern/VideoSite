<?php
/**
 * This class includes the functions related to storing files and retrieving
 * data from Amazon's S3 service. The S3 service is used to store videos,
 * thumbnails, pictures and other series and user related files.
 *
 * @author Jason Halpern
 * @since 4/5/2013
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

    public function getS3Client(){
        return $this->s3Client;
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

        $bucketName = $this->getFullSeriesPath($series);

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

        $bucketName = $this->getFullSeriesPath($series);

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

        $seriesFolder = $this->getFullSeriesPath($series);
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

        $seriesFolder = $this->getFullSeriesPath($series);
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

        $bucketName = $this->getFullSeriesPath($series);

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

        $seriesFolder = $this->getFullSeriesPath($series);
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
     * Delete an episode from a series. This deletes the original video file, the
     * standard definition video file, the high definition video file and the
     * thumbnails for the videos.
     *
     * @param Series $series The series
     * @param int $seasonNumber The season of the series
     * @param int $episodeNumber The episode in the season
     */
    public function deleteEpisode($series, $seasonNumber, $episodeNumber){
        $seasonFolder = $this->getSeasonFolderPath($series, $seasonNumber);
        $this->deleteVideo($seasonFolder, $episodeNumber);
        $this->deleteVideo($seasonFolder, $episodeNumber . '_HD.mp4');
        $this->deleteVideo($seasonFolder, $episodeNumber . '_SD.mp4');

        $thumbnailFolder = substr($this->getThumbnailFolder($series, $seasonNumber, $episodeNumber), 0, -1);
        $this->deleteImage(AppConfig::getS3Root() . $thumbnailFolder, '00001.png');
        $this->deleteImage(AppConfig::getS3Root() . $thumbnailFolder, '00002.png');
        $this->deleteImage(AppConfig::getS3Root() . $thumbnailFolder, '00003.png');
        $this->deleteImage(AppConfig::getS3Root() . $thumbnailFolder, '00004.png');

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

    /**
     * Get the path of the folder that holds the main image for a series
     *
     * @param Series $series The series
     */
    public function getSeriesImagePath($series){
        return $this->getFullSeriesPath($series) . 'series_image';
    }

    /**
     * Return the name of the folder that stores all videos for this series.
     * Whitespace is replaced by underscores because S3 buckets cannot have whitespace.
     * This is just the folder name for a series, not the full path to the series folder.
     *
     * @param Series $series The series
     * @return string The bucket name for this series
     */
    public function getSeriesFolderName($series){
        /* replace the whitespace with underscores to get the right folder */
        return str_replace(' ', '_', $series->getTitle());
    }

    /**
     * This is the full path for a bucket of a series. It is different from just
     * the bucket name in that it also includes the root S3 bucket. The full bucket
     * path is needed to correctly add/remove files and folders from S3.
     *
     * @param Series $series The series
     * @return string The full path to the bucket for this series.
     */
    public function getFullSeriesPath($series){
        return AppConfig::getS3Root() . $this->getSeriesFolderName($series) . '/';
    }

    /**
     * This function returns the path to a folder for a specific season of the series.
     *
     * @param Series $series The series
     * @param int $seasonNumber
     */
    public function getSeasonFolderPath($series, $seasonNumber){
        $seriesFolder = $this->getFullSeriesPath($series);
        $seasonFolder = 'season_' . $seasonNumber;

        $bucketName = $seriesFolder . $seasonFolder;

        return $bucketName;
    }

    /**
     * Get the episode key for the original video file. This is the full path to that file.
     *
     * @param Series $series
     * @param int $seasonNum The season number
     * @param int $episodeNum The episode number
     * @return string The key to access the original file for the episode
     */
    public function getEpisodeKey($series, $seasonNum, $episodeNum){
        return $this->getSeriesFolderName($series) . '/' . 'season_' . $seasonNum . '/' . $episodeNum;
    }

    /**
     * Get the episode key for the standard definition video file. This is the full path to that file.
     *
     * @param Series $series The series
     * @param int $seasonNum The season number
     * @param int $episodeNum The episode number
     * @return string The key to access the SD episode
     */
    public function getSDEpisodeKey($series, $seasonNum, $episodeNum){
        return $this->getEpisodeKey($series, $seasonNum, $episodeNum) . '_SD.mp4';
    }

    /**
     * Get the episode key for the high definition video file. This is the full path to that file.
     *
     * @param Series $series The series
     * @param int $seasonNum The season number
     * @param int $episodeNum The episode number
     * @return string The key to access the HD episode
     */
    public function getHDEpisodeKey($series, $seasonNum, $episodeNum){
        return $this->getEpisodeKey($series, $seasonNum, $episodeNum) . '_HD.mp4';
    }

    /**
     * Get the thumbnail folder for a specific episode. This is the full path to that folder.
     *
     * @param Series $series The series
     * @param int $seasonNum The season number
     * @param int $episodeNum The episode number
     */
    public function getThumbnailFolder($series, $seasonNum, $episodeNum){
        return $this->getEpisodeKey($series, $seasonNum, $episodeNum) . '_thumbnails/';
    }

    /**
     * Wait until a file exists before doing anything else. This hangs until it has been
     * verified that the object exists in S3
     *
     * @param string $folder The folder in which the file will exist
     * @param string $key The name of the file
     */
    public function waitUntilFileExists($folder, $fileName){

        $this->s3Client->waitUntilObjectExists(array(
            'Key' => $fileName,
            'Bucket' => $folder
        ));

        return true;
    }


}
