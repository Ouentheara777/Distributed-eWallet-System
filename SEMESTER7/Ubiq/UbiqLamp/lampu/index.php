<?php

// Require the Slim Framework
 require 'Slim/Slim.php';

\Slim\Slim::registerAutoloader();


// Instantiate a Slim application
 
$app = new \Slim\Slim();

// Define the Slim application routes

// GET route
$app->get('/', function() use ($app) {
    $id_device = $app->request()->get('id_device');
    getStatusLampu($id_device);
});

$app->get('/klien/viewImage/:image_name', function ($image_name) {
    viewImage($image_name);
});

// POST route
$app->post('/', function() use ($app) {
    $id_device = $app->request()->get('id_device');
    $arg_input = $app->request()->get('arg1');
    setStatusLampu($id_device, $arg_input);
});

$app->post('/server/postImage', 'uploadImage');


function connectDatabase() {
    $server = 'localhost';
	$user = 'root';
	$pass = 'gemagema';
	$database = 'lampu_ubiq';
    $conn = mysqli_connect($server, $user, $pass, $database);
	return $conn;
}

function getStatusLampu($id_device) {
    $app = \Slim\Slim::getInstance();
    $app_info = array();

    $field= "status";
    $state_general = false;

	$db = connectDatabase();
    $select_query = "SELECT $field FROM LampuRecord WHERE id = '$id_device'" ;
    $result = mysqli_query($db, $select_query);
    $row = mysqli_fetch_array($result);

    $current_state = $row[$field];

    if ($row[$field] != null) {
        $state_general = true;
    }

    $data_info = array('id_device' => $id_device, "current_status" => $current_state);
    $app_info = array('status_lampu' => $state_general, "data" => $data_info); 
    $app->response()->headers->set('Content-Type', 'application/json');
    echo json_encode($app_info);
    

    mysqli_close($db);

}

function setStatusLampu($id_device , $arg) {
    $app = \Slim\Slim::getInstance();
    $app_info = array();

    $field= "status";
    $state_general = false;

	$db = connectDatabase();

    // query for checking last status of lamp
    $select_query = "SELECT $field FROM LampuRecord WHERE id = '$id_device'" ;
    $result = mysqli_query($db, $select_query);
    $row = mysqli_fetch_array($result);

    $last_state = $row[$field];

    // set lamp state
    $update_query = "UPDATE LampuRecord SET $field = '$arg' WHERE id = '$id_device'" ;
    $updated = mysqli_query($db, $update_query);

    // checking current state of lamp
    $current_state_result = mysqli_query($db, $select_query);
    $row_current = mysqli_fetch_array($current_state_result);

    $current_state = $row_current[$field];

    if ($row[$field] != null) {
        $state_general = true;
    }

    $data_info = array('id_device' => $id_device, "last_status" => $last_state , "current_status" => $current_state);
    $app_info = array('status_lampu' => $state_general, "data" => $data_info); 
    $app->response()->headers->set('Content-Type', 'application/json');
    echo json_encode($app_info);
    

    mysqli_close($db);

}

function getImage($image_name){
    $app = \Slim\Slim::getInstance();
    $app_info = array();
    $images_location = 'images/'. $image_name;
    $size = filesize($images_location);
    $size = $size / 1024;

    $base64 = base64_encode(file_get_contents($images_location));
    $the_location = realpath($images_location);
    $the_size = floor($size). " KB";

    $app_info = array("isi_berkas" => $base64 , "lokasi_berkas" => $the_location, "ukuran_berkas" => $the_size); 

    $app->response()->headers->set('Content-Type', 'application/json');
    echo json_encode($app_info);
}

function viewImage($image_name){
    $app = \Slim\Slim::getInstance();
    $url = "http://152.118.33.76/tugas4/server/getImage/".$image_name;
    $ch = curl_init();
    curl_setopt_array($ch, array(
        CURLOPT_SSL_VERIFYPEER => FALSE,
        CURLOPT_RETURNTRANSFER => TRUE,
        CURLOPT_URL => $url,
    ));

    $response = curl_exec($ch);
    $the_json = json_decode($response);
    $images_location = $the_json->lokasi_berkas;
    $images_location = str_replace("\\", "/", $images_location);
    $image_base64 = $the_json->isi_berkas;
    $image_size = $the_json->ukuran_berkas;
    
    $app->view()->setTemplatesDirectory('./view');
    $app->render('index.php', array('image'=>$image_base64, 'image_name'=> $image_name, 'images_location'=>$images_location, 'image_size'=>$image_size));
}

function uploadImage(){
    $app = \Slim\Slim::getInstance();
    $app_info = array();
    $app->response()->header("Content-Type","application/json");
    $the_data = $app->request->getBody();
    $the_json = json_decode($the_data);

    $the_name = $the_json->nama_gambar;
    $temp = explode(".", $the_name);
    $ext = $temp[1];
    $base64 = $the_json->base64;
    $the_location = 'images/'.$the_name;

    $data = base64_decode($base64);
    file_put_contents($the_location, $data);

    $app_info = array("status" => "200" , "message" => "Gambar suksess diupload"); 
    echo json_encode($app_info);
}



// Run the Slim application

$app->run();

?>