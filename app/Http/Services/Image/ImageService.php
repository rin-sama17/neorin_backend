<?php

namespace App\Http\Services\Image;

use Illuminate\Support\Facades\Config;
use Intervention\Image\Drivers\Gd\Driver;
use Intervention\Image\ImageManager;

class ImageService extends ImageToolsService
{

    public function save($image)
    {
        $this->setImage($image);
        $this->provider();
        $manager = new ImageManager(new Driver());
        $result = $manager->read($image->getRealPath())->save(public_path($this->getImageAdress()), null,$this->getImageFormat());
        return $result ? $this->getImageAdress() :false;
    }

    public function fitAndSave($image ,$width,$height)
    {
        $this->setImage($image);
        $this->provider();
        $manager = new ImageManager(new Driver());
        $result = $manager->read($image->getRealPath())->resizeDown($width , $height)->save(public_path($this->getImageAdress()), null,$this->getImageFormat());
        return $result ? $this->getImageAdress() :false;
    }

    public function createIndexAndSave($image)
    {
        $imageSizes = Config::get('image.index-image-size');

        $this->setImage($image);
        $this->getImageDirectory() ?? $this->setImageDirectory(date('Y') . DIRECTORY_SEPARATOR , date("m") . DIRECTORY_SEPARATOR . date("d"));
        $this->setImageDirectory($this->getImageDirectory() . DIRECTORY_SEPARATOR . time());

        $this->getImageName() ?? $this->setImageName(time());
        $imageName= $this->getImageName();

        $indexArray =[];
        foreach($imageSizes as $sizeAlias=> $imageSize)
        {
            $currentImageName = $imageName . '_' . $sizeAlias;
            $this->setImageName($currentImageName);
            $this->provider();

            $manager = new ImageManager(new Driver());
            $result = $manager->read($image->getRealPath())->resizeDown($imageSize['wdith'] , $imageSize['height'])->save(public_path($this->getImageAdress()), null,$this->getImageFormat());
            if($result)
            {
                $indexArray[$sizeAlias] = $this->getImageAdress();
            }else {
                return false;
            }
        }

        $images['indexArray'] = $indexArray;
        $images['directory'] = $this->getfinalImageDirectory();
        $image['currentImage'] = Config::get('image.default-current-index-image');

        return $images;
    }


    public function deleteImage($imagePath)
    {
        if(file_exists($imagePath)){
            unlink($imagePath);
        }
    }

    public function deleteIndex($images){
        $directory = public_path($images['directory']);
        $this->deleteDirectoryAndFiles($directory);
    }

    public function deleteDirectoryAndFiles($directory)
    {
        if(!is_dir($directory)){
            return false;
        }

        $files = glob($directory . DIRECTORY_SEPARATOR . "*" , GLOB_MARK);
        foreach ($files as $file) {

            if(is_dir($file)){
                $this->deleteDirectoryAndFiles($file);
            }else{
                unlink($file);
            }

        }
        $result = rmdir($directory);
        return $result;
    }
}
