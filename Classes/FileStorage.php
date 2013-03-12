<?php
/**
 * @author Jason Halpern
 */
interface FileStorage{

    public function createSeriesFolder($series);
    public function deleteSeriesFolder($series);

    public function createSeasonFolder($series);
    public function deleteSeasonFolder($series, $seasonNumber);

    public function addVideoFile($series);
    public function deleteVideoFile();

    public function seriesFolderExists($series);
    public function seasonFolderExists($series);
}
