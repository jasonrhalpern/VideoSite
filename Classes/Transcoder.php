<?php
/**
 * The transcoder converts videos between different formats and also generates thumbnails.
 *
 * @author Jason Halpern
 * @since 4/5/2013
 */

require_once('/home/simawatkinto/AppConfig.php');
/* Include the Amazon Web Services SDK for PHP */
require_once dirname(__FILE__) . '/../AWS_SDK_PHP/aws.phar';

use Aws\S3\S3Client;
use Aws\Common\Aws;
use Guzzle\Http\EntityBody;
use Aws\ElasticTranscoder\ElasticTranscoderClient;

class Transcoder {

    /**
     * @var ElasticTranscoderClient $transcoderClient Our portal into our AWS transcoding service
     */
    protected  $transcoderClient;

    public function __construct(){

        /* elastic transcoder client */
        $this->transcoderClient = ElasticTranscoderClient::factory(array(
            'key'    => AppConfig::getAwsKey(),
            'secret' => AppConfig::getAwsSecret(),
            'region' => 'us-east-1'
        ));
    }

    /**
     * Transcode a video file to the given preset. The transcoder also generates thumbnails
     * and places them in the same folder as the video file. The preset is an ID that
     * represents the specific format we are transcoding to.
     *
     * @param string $sourceFile The source file
     * @param string $destinationFile The file we are creating with the transcoder
     * @param string $thumbnailFolder The folder in which the transcoder will place the thumbnails
     * @param string $videoPreset The preset to determine the format of the video
     */
    public function transcodeVideo($sourceFile, $destinationFile, $thumbnailFolder, $videoPreset){

        $input = array(
            'Key'    => $sourceFile,
            'FrameRate' => 'auto',
            'Resolution'  => 'auto',
            'AspectRatio' => 'auto',
            'Interlaced' => 'auto',
            'Container' => 'auto'
        );

        $output = array(
            'Key'    => $destinationFile,
            'ThumbnailPattern'  => $thumbnailFolder . '{count}',
            'Rotate'  => 'auto',
            'PresetId' => $videoPreset
        );

        $this->transcoderClient->createJob(array(
            'Input' => $input,
            'Output' => $output,
            'PipelineId' => AppConfig::getTranscoderId())
        );


    }
}
