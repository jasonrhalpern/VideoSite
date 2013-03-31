<?php
/**
 * All functions that will need to be supported by our transcoder.
 * The transcoder converts videos between different formats.
 *
 * @author Jason Halpern
 */
interface Transcoder {

    public function transcode($source, $destination, $videoPreset);
}
