<?php

class Project
{
    private $project;
    private $image;
    private $ground;

    public function __construct($data, $mode = 'create')
    {
        if ($mode == 'create') {
            $this->project = Leaf\Form::sanitizeInput($data["Project_name"]);
            $this->image = new Image($data["Workspace_image"]);
        }

        if ($mode == 'save') {
            $this->project = $data['name'];
            $this->ground = imagecreatefrompng(IMAGE_PATH .  $data['image']);
        }
    }

    public function init()
    {
        try {
            $uploader = $this->image->upload();
            return [
                'name' => $this->project,
                'link' => $uploader['link'],
                'size' => $uploader['size']
            ];
        } catch (Exception $e) {
            response()->json([
                'error' => $e,
            ], 500);
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