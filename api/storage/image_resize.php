<?php
function resize_image($filePath, $fileExt, $w = 700, $h = 700)
{
    try{
        list($width, $height) = getimagesize($filePath);
        $dst = imagecreatetruecolor($w, $h);

        switch ($fileExt) {
            case 'jpg':
            case 'jpeg':
                $src = imagecreatefromjpeg($filePath);
                break;
            case 'png':
                $src = imagecreatefrompng($filePath);
                imagecolortransparent($dst, imagecolorallocatealpha($dst, 0, 0, 0, 127));
                imagealphablending($dst, false);
                imagesavealpha($dst, true);
                break;
            default:
                throw new Exception('Unsupported image extension found: ' . $fileExt);
                break;
        }

        $result = imagecopyresampled($dst, $src, 0, 0, 0, 0, $w, $h, $width, $height);

        switch ($fileExt) {
            case 'jpg':
            case 'jpeg':
                imagejpeg($dst, $filePath);
                break;
            case 'png':
                imagepng($dst, $filePath);
                break;
        }

        return true;
    }catch(Exception $e){
        return false;
    }
}
?>