<?php

class Watermark
{
    private $id;
    private $mode;
    private $name;
    public $opacity;
    private $zIndex;
    public $size = array();
    public $position = array();
    public $ground;

    public function __construct($data)
    {
        $this->id = $data['id'];
        $this->mode = $data['mode'];
        $this->name = $data['image'];
        $this->opacity = $data['opacity'];
        $this->size = $data['size'];
        $this->position = $data['position'];
        $this->zIndex = $data['zIndex'];
        $this->createGround();
    }

    private function createGround()
    {
        try {
            $this->ground = imagecreatefrompng(IMAGE_PATH . $this->name);
            $this->resizeMark();
            $this->acceptPresets();
        } catch (Exception $e) {
            response()->json([
                'error' => $e,
            ], 500);
            die();
        }
    }

    private function resizeMark()
    {
        list($w_i, $h_i, $type) = getimagesize(IMAGE_PATH . $this->name);
        $w_o = $this->size['width'];
        $h_o = $this->size['height'];
        $img_o = imagecreatetruecolor((int)$w_o, (int)$h_o);
        imagealphablending($img_o, false);
        imagesavealpha($img_o, true);
        imagecopyresampled($img_o, $this->ground, 0, 0, 0, 0, $w_o, $h_o, $w_i, $h_i);
        $this->ground = $img_o;
    }

    private function acceptPresets()
    {
        $presets = ["black" => IMG_FILTER_GRAYSCALE, "blur" => IMG_FILTER_GAUSSIAN_BLUR, "contrast" => [IMG_FILTER_CONTRAST, 50], "invert" => IMG_FILTER_NEGATE];
        if ($this->mode != 'normal') {
            $preset = $presets[$this->mode];
            !is_array($preset) && imagefilter($this->ground, $preset);
            is_array($preset) && imagefilter($this->ground, $preset[0], $preset[1]);
        }
        imagefilter($this->ground, IMG_FILTER_COLORIZE, 0, 0, 0, ceil((1 - $this->opacity) * 127));
    }
}