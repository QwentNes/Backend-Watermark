<?php

class Image{
    protected $size = array();
    protected $type;
    protected $name;
    protected $uploadData;
    protected $currentPath;

   public function __construct($image)
   {
       $this->name = Generate::FileName();
       $this->uploadData = $image;
   }

   public function uploadImage(){
       Leaf\FS::uploadFile($this->uploadData, IMAGE_PATH);
       $oldPath = IMAGE_PATH.$this->uploadData['name'];
       $this->type = $this->getType($oldPath);
       $this->currentPath = $this->name.".".$this->type;
       Leaf\FS::renameFile($oldPath, IMAGE_PATH.$this->currentPath);
       $this->reType();
       $this->getSize();
       return ([
           "link" => $this->currentPath,
           "size" => $this->getSize(),
       ]);
   }

   protected function getType($path){
       return pathinfo($path, PATHINFO_EXTENSION);
   }

   protected function reType(){
       if($this->type != 'png'){
           $image = imagecreatefromjpeg(IMAGE_PATH.$this->currentPath);
           $newPath = $this->name.".png";
           imagepng($image, IMAGE_PATH.$newPath);
           Leaf\FS::deleteFile(IMAGE_PATH.'/'.$this->currentPath);
           $this->currentPath = $newPath;
       }
   }

   protected function getSize(){
       $size = getimagesize(IMAGE_PATH.$this->currentPath);
       return [
            "width" => $size[0],
            "height" => $size[1]
       ];
   }
}
