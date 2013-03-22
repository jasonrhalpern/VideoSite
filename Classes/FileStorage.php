<?php
/**
 * This interface includes all the functions that our file storage system
 * will need to support. Our file storage (i.e. Amazon's S3) will be where
 * we store videos, thumbnails, pictures and other files.
 *
 * @author Jason Halpern
 */
interface FileStorage{

    public function createSeriesFolder($series);
    public function deleteSeriesFolder($series);

    public function createSeasonFolder($series);
    public function deleteSeasonFolder($series, $seasonNumber);

    public function uploadVideo($fileName, $key, $folderName);
    public function deleteVideo($folderName, $key);

    public function uploadImage($fileName, $key, $folderName);
    public function deleteImage($folderName, $key);

    public function seriesFolderExists($series);
    public function seasonFolderExists($series);
}
