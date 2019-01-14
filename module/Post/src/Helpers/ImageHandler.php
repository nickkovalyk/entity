<?php

namespace Post\Helpers;
use Post\Helpers\Gifresizer;
use Imagecraft\ImageBuilder;

class ImageHandler
{
    public static function resizeImage($tmpName, $w, $h, $directory, $crop = false)
    {

        list($width, $height) = getimagesize($tmpName);
        $r = $width / $height;
        if ($crop) {
            if ($width > $height) {
                $width = ceil($width-($width*abs($r-$w/$h)));
            } else {
                $height = ceil($height-($height*abs($r-$w/$h)));
            }
            $newWidth = $w;
            $newHeight = $h;
        } else {
            if ($w/$h > $r) {
                $newWidth = $h*$r;
                $newHeight = $h;
            } else {
                $newHeight = $w/$r;
                $newWidth = $w;
            }
        }



        $mimetype = explode('/', mime_content_type($tmpName))[1];
        $newFileName = uniqid();
        switch($mimetype) {
            case('jpeg'): {
                $src = imagecreatefromjpeg($tmpName);
                break;
            }
            case('jpg'): {
                $src = imagecreatefromjpeg($tmpName);
                break;
            }
            case('png'): {
                $src = imagecreatefrompng($tmpName);
                break;
            }
            case('gif'): {
                $options = ['engine' => 'php_gd', 'locale' => 'zh_TW'];
                $builder = new ImageBuilder($options);

                $layer = $builder->addBackgroundLayer();
                $layer->filename($tmpName);
                $layer->resize($newWidth, $newHeight);
                $image = $builder->save();
                if ($image->isValid()) {
                    file_put_contents("$directory/$newFileName.gif", $image->getContents());
                } else {
                    throw new \Exception($image->getMessage().PHP_EOL);
                }

            return "$directory/$newFileName.gif";
            break;

            }
        }

        $fullpath = "$directory/$newFileName.jpg";
        $dst = imagecreatetruecolor($newWidth, $newHeight);
        imagecopyresampled($dst, $src, 0, 0, 0, 0, $newWidth, $newHeight, $width, $height);
        imagejpeg($dst, $fullpath);
        return $fullpath;
    }

}