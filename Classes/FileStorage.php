<?php
/**
 * This interface includes all the functions that our file storage system
 * will need to support. Our file storage (i.e. Amazon's S3) will be where
 * we store videos, thumbnails, pictures and other files.
 *
 * @author Jason Halpern
 * @since 4/5/2013
 */
interface FileStorage{

    public function createFolder($folderName);
    public function deleteFolder($folderName);
    public function folderExists($folderName);

    public function createSeriesFolder($series);
    public function deleteSeriesFolder($series);
    public function seriesFolderExists($series);

    public function createSeasonFolder($series);
    public function deleteSeasonFolder($series, $seasonNumber);
    public function seasonFolderExists($series, $seasonNum);

    public function deleteEpisode($series, $seasonNumber, $episodeNumber);

    public function createCompetitionFolder($competition);
    public function deleteCompetitionFolder($competition);
    public function deleteCompetitionEntry($competition, $videoId);

    public function uploadVideo($fileName, $key, $folderName);
    public function deleteVideo($folderName, $key);

    public function uploadImage($fileName, $key, $folderName);
    public function deleteImage($folderName, $key);

    public function getSeriesImagePath($series);
    public function getSeriesFolderName($series);
    public function getFullSeriesPath($series);
    public function getFullCompetitionPath($competition);
    public function getSeasonFolderPath($series, $seasonNumber);
    public function getThumbnailFolder($series, $seasonNum, $episodeNum);

    public function getEpisodeKey($series, $seasonNum, $episodeNum);
    public function getSDEpisodeKey($series, $seasonNum, $episodeNum);
    public function getHDEpisodeKey($series, $seasonNum, $episodeNum);
    public function getSDCompetitionKey($competition, $videoId);
    public function getCompetitionKey($competition, $videoId);
    public function getCompetitionThumbnailFolder($competition, $videoId);

    public function waitUntilFileExists($folder, $fileName);

}
