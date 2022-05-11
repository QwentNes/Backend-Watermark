<?php
require __DIR__ . "/vendor/autoload.php";

require_once("config.php");
require_once("classes/Generate.php");
require_once("classes/Image.php");
require_once("classes/Project.php");
require_once("classes/Watermark.php");

Leaf\Http\Headers::accessControl("Allow-Origin", "*");
Leaf\Http\Headers::accessControl("Allow-Headers", "*");


app()->post("/project/save", function () {
    $data = request()->body();
    if(isset($data['project']) && isset($data['layers'])){
        $project = new Project($data['project'], 'save');
        foreach ($data['layers'] as $layer) {
            $watermark = new Watermark($layer);
            $project->merge($watermark);
        }
        response()->json($project->save());
        return;
    }
    response()->json([
        'error' => 'bad request',
    ], 500);
});

app()->post("/create/project", function () {
    $project = new Project(request()->body());
    sleep(3);
    response()->json($project->init());
});

app()->post("/resource/upload", function () {
    $response = array();
    $data = request()->body();
    foreach ($data as $item) {
        $image = new Image($item);
        $response[] = $image->upload();
    }
    sleep(3);
    response()->json($response);
});

app()->get("/uploads/{image}", function ($image) {
    Generate::image('./image/' . $image);
});

app()->run();
?>
