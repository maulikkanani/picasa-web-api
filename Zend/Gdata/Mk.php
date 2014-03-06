<?php
session_start();
class Zend_Gdata_Mk
{
    const COMPANY_URI= "https://lh5.googleusercontent.com/-iE-3Cz-L_Xc/UxifjIX0WpI/AAAAAAAAD8k/fWZ9VRxF8Bs/h100/maulikkanai.png";
    
    public static function getAuthSubHttpClient()
    {
	global $_SESSION, $_GET;
       
        if (!isset($_SESSION['sessionToken']) && isset($_GET['token'])) {
            $_SESSION['sessionToken'] =
                Zend_Gdata_AuthSub::getAuthSubSessionToken($_GET['token']);
        }
        $client = Zend_Gdata_AuthSub::getHttpClient($_SESSION['sessionToken']);
        return $client;
    }

    public static function GetDublicateAlbumId($my_arr, $clean = false) {
        if ($clean) {
            return array_unique($my_arr);
        }
        $dups = array();
        foreach ($my_arr as $key => $val) {
            if (isset($new_arr[$val])) {
                $new_arr[$val] = $key;
            } else {
                if (isset($dups[$val])) {
                    $dups[$val][] = $key;
                } else {
                    $dups[$val] = array($key);
                }
            }
        }
        return $dups;
    }

    public static function GetAllAlbum($service) {
        $albums_id = array();
        $albums_name = array();
        $results = $service->getUserFeed();
        while ($results != null) {
            $final_arr = array();
            foreach ($results as $entry) {
                $albums_id = $entry->gphotoId->text; //$entry->getId();//$entry->getGphotoId()->getText();
                $albums_name = $entry->title->text;
                $final_arr[$albums_id] = $albums_name;
            }
            try {
                $results = $results->getNextFeed();
            } catch (Exception $e) {
                $results = null;
            }
        }
        return $final_arr;
    }

    public static function GetCompanyLogo($dir,$org_Path)
    { 
     if($org_Path==''){   
        $org_Path=self::COMPANY_URI;
     }
     $savePath=$dir."/company.png";
     $in=    fopen($org_Path, "rb");
     $out=   fopen($savePath, "wb");
     while ($chunk = fread($in,8192))
     {
      fwrite($out, $chunk, 8192);
     }
     fclose($in);
     fclose($out);
    }


    public static function AddWatermark($imagename, $iname,$dir) {
        self::GetCompanyLogo($dir,'');
        // Load the stamp and the photo to apply the watermark to
        $im = imagecreatefromjpeg($imagename);
        $water = $dir."/company.png";
        $stamp = imagecreatefrompng($water);

        // Set the margins for the stamp and get the height/width of the stamp image
        $marge_right = 10;
        $marge_bottom = 10;
        $sx = imagesx($stamp);
        $sy = imagesy($stamp);

        // Copy the stamp image onto our photo using the margin offsets and the photo 
        // width to calculate positioning of the stamp. 
        imagecopy($im, $stamp, imagesx($im) - $sx - $marge_right, imagesy($im) - $sy - $marge_bottom, 0, 0, imagesx($stamp), imagesy($stamp));

        $save = $dir."/" . $iname;

        // Output and free memory
        //header('Content-type: image/jpeg');
        imagejpeg($im, $save, 90);
        unlink($water);
        return $save;
        
    }
}
?>