<?php
/**
 * This interface includes all the functions that our file storage system
 * will need to support. Our file storage (i.e. Amazon's S3) will be where
 * we store videos, thumbnails, pictures and other files.
 *
 * @author Jason Halpern
 */
interface FileStorage{

    public function createFolder($folderName);
    public function deleteFolder($folderName);
    public function folderExists($folderName);

    public function uploadVideo($fileName, $key, $folderName);
    public function deleteVideo($folderName, $key);

    public function uploadImage($fileName, $key, $folderName);
    public function deleteImage($folderName, $key);

}
