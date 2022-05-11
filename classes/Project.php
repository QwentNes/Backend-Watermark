<?php

class Project extends Image
{
    private $project;

    public function __construct($data, $mode = 'create')
    {
        if ($mode == 'create') {
            $this->project = Leaf\Form::sanitizeInput($data["Project_name"]);
            parent::__construct($data["Workspace_image"]);
        }
        if ($mode == 'save') {
            $this->size = $data['size'];
            $this->project = $data['name'];
            $this->name = $data['image'];
            $this->ground = imagecreatefrompng(IMAGE_PATH . $this->name);
        }
    }

    public function init()
    {
        try {
            $uploader = $this->uploadImage();
            return [
                'name' => $this->project,
                'link' => $uploader['link'],
                'size' => $uploader['size']
            ];
        } catch (Exception $e) {
            Leaf\Http\Headers::status(500);
            die();
        }
    }

    public function merge(Watermark $watermark)
    {
        imagealphablending($this->ground, true);
        imagesavealpha($this->ground, true);
        imagecopy($this->ground, $watermark->ground, $watermark->position['left'], $watermark->position['top'], 0, 0, $watermark->size['width'], $watermark->size['height']);
    }

    public function save()
    {
        $path = Generate::FileName().'_final.png';
        imagepng($this->ground, IMAGE_PATH.$path, 0);
        return[
            "link" => '/uploads/'.$path,
        ];
    }
}