<?php
/**
 * All functions that will need to be supported by our transcoder.
 * The transcoder converts videos between different formats.
 *
 * @author Jason Halpern
 * @since 4/5/2013
 */
interface Transcoder {

    public function transcode($source, $destination, $videoPreset);
}
