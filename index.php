<?php
require __DIR__ . "/vendor/autoload.php";

require_once("config.php");
require_once("classes/Generate.php");
require_once("classes/Image.php");
require_once("classes/Project.php");
require_once("classes/Watermark.php");

Leaf\Http\Headers::accessControl("Allow-Origin", "*", 200);
Leaf\Http\Headers::accessControl("Allow-Headers", "*", 200);


app()->post("/project/save", function () {
    $data = request()->body();
    $project = new Project($data['project'], 'save');
    foreach ($data['layers'] as $layer) {
        $watermark = new Watermark($layer);
        $project->merge($watermark);
    }
    response()->json($project->save());
});

app()->post("/resource/upload", function () {
    $response = array();
    $data = request()->body();
    foreach ($data as $item) {
        $image = new Image($item);
        $response[] = $image->uploadImage();
    }
    sleep(3);
    response()->json($response);
});

app()->post("/create/project", function () {
    $project = new Project(request()->body());
    sleep(3);
    response()->json($project->init());
});

app()->get("/uploads/{image}", function ($image) {
    Generate::image('./image/' . $image);
});

app()->run();
?>
