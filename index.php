<?php

//google url https://accounts.google.com/AuthSubRequest?scope=https%3A%2F%2Fpicasaweb.google.com%2Fdata&amp;session=1&amp;secure=0&amp;next=http%3A%2F%2Faddsharesale.com%2Fpicasa%2Fcatalog_upload.php
error_reporting(0);
require_once 'Zend/Loader.php';
/**
 * @see Zend_Gdata
 */
Zend_Loader::loadClass('Zend_Gdata');

/**
 * @see Zend_Gdata_AuthSub
 */
Zend_Loader::loadClass('Zend_Gdata_AuthSub');

/**
 * @see Zend_Gdata_Photos
 */
Zend_Loader::loadClass('Zend_Gdata_Photos');

Zend_Loader::loadClass('Zend_Gdata_Mk');

/**
 * @see Zend_Gdata_Photos_UserQuery
 */
Zend_Loader::loadClass('Zend_Gdata_Photos_UserQuery');

/**
 * @see Zend_Gdata_Photos_AlbumQuery
 */
Zend_Loader::loadClass('Zend_Gdata_Photos_AlbumQuery');

/**
 * @see Zend_Gdata_Photos_PhotoQuery
 */
Zend_Loader::loadClass('Zend_Gdata_Photos_PhotoQuery');

/**
 * @see Zend_Gdata_App_Extension_Category
 */
Zend_Loader::loadClass('Zend_Gdata_App_Extension_Category');

session_start();

//this is a directory path of images that you want to upload on picasa And g+
$image_dir = $_SERVER['DOCUMENT_ROOT'] . "/picasa/image";


 if($_GET['token']==''){
            echo '<a href="https://accounts.google.com/AuthSubRequest?next=http%3A%2F%2Flocalhost%2Fpicasa%2Findex.php&scope=http%3A%2F%2Fpicasaweb.google.com%2Fdata&secure&session=1"> Authenticate Goolge account</a>';
            exit;
}
$client=Zend_Gdata_Mk::getAuthSubHttpClient();
$service = new Zend_Gdata_Photos($client);
$all = Zend_Gdata_Mk::GetAllAlbum($service);
$album_name = 'MK';         //Album name

if (in_array($album_name, $all) == true) {
    $album_tital = "Album updated from MK"; //Album titel
    $albumId = Zend_Gdata_Mk::GetDublicateAlbumId($all);

    for ($i = 0; $i < count($albumId[$album_name]); $i++) {

       
        if ($dh = opendir($image_dir)) {
            while (($file = readdir($dh)) !== false) {
                if ($file != '.' && $file != '..') {
                    $username = "default";
                    $nameimg = $image_dir . '/' . $file;
                    $file_new='New_'.$file;
                    $newpath= $image_dir . '/' . $file_new;
                    $filename = Zend_Gdata_Mk::AddWatermark($nameimg, $file_new, $image_dir);
                    $photoName = $file;
                    $photoCaption = $file . ' By MK';   // Photo tag
                    $photoTags = "MK," . $album_name;
                    $fd = $service->newMediaFileSource($filename);
                    $fd->setContentType("image/jpeg");

                    // Create a PhotoEntry
                    $photoEntry = $service->newPhotoEntry();

                    $photoEntry->setMediaSource($fd);
                    $photoEntry->setTitle($service->newTitle($photoName));
                    $photoEntry->setSummary($service->newSummary($photoCaption));

                    // add some tags
                    $keywords = new Zend_Gdata_Media_Extension_MediaKeywords();
                    $keywords->setText($photoTags);
                    $photoEntry->mediaGroup = new Zend_Gdata_Media_Extension_MediaGroup();
                    $photoEntry->mediaGroup->keywords = $keywords;

                    // We use the AlbumQuery class to generate the URL for the album
                    $albumQuery = $service->newAlbumQuery();
                    $albumQuery->setUser($username);
                    $albumQuery->setAlbumId($albumId[$album_name][$i]);

                    // We insert the photo, and the server returns the entry representing
                    // that photo after it is uploaded


                    $insertedEntry = $service->insertPhotoEntry($photoEntry, $albumQuery->getQueryUrl());
                }
                unlink($newpath);
            }
            closedir($dh);
            
        }
    }
} else {
    $album_tital = "Newly uloaded from MK";
    $entry = new Zend_Gdata_Photos_AlbumEntry();
    $entry->setTitle($service->newTitle($album_name));
    $entry->setSummary($service->newSummary($album_tital));
    $createdEntry = $service->insertAlbumEntry($entry);
    $all = Zend_Gdata_Mk::GetAllAlbum($service);
    $albumId = array_search($album_name, $all);
    if ($dh = opendir($image_dir)) {
        while (($file = readdir($dh)) !== false) {
            if ($file != '.' && $file != '..') {
                $username = "default";
                $nameimg = $image_dir . '/' . $file;
                $file_new='New_'.$file;
                $newpath= $image_dir . '/' . $file_new;
                $filename = Zend_Gdata_Mk::AddWatermark($nameimg, $file_new, $image_dir);
                $photoName = $file;
                $photoCaption = $file . ' By MK';
                $photoTags = "MK," . $album_name;
                $fd = $service->newMediaFileSource($filename);
                $fd->setContentType("image/jpeg");

                // Create a PhotoEntry
                $photoEntry = $service->newPhotoEntry();

                $photoEntry->setMediaSource($fd);
                $photoEntry->setTitle($service->newTitle($photoName));
                $photoEntry->setSummary($service->newSummary($photoCaption));

                // add some tags
                $keywords = new Zend_Gdata_Media_Extension_MediaKeywords();
                $keywords->setText($photoTags);
                $photoEntry->mediaGroup = new Zend_Gdata_Media_Extension_MediaGroup();
                $photoEntry->mediaGroup->keywords = $keywords;

                // We use the AlbumQuery class to generate the URL for the album
                $albumQuery = $service->newAlbumQuery();
                $albumQuery->setUser($username);
                $albumQuery->setAlbumId($albumId);

                // We insert the photo, and the server returns the entry representing
                // that photo after it is uploaded


                $insertedEntry = $service->insertPhotoEntry($photoEntry, $albumQuery->getQueryUrl());
            }
            unlink($newpath);
        }
        
        closedir($dh);
    }
}


?>
   
