<?php

require 'Slim/Slim.php';

\Slim\Slim::registerAutoloader();

$app = new \Slim\Slim();

// GET route

// POST route
$app->post('/ping', 'getPing');

$app->post('/register', function () use($app) {
    $data = $app->request->getBody();
    $json = json_decode($data);

    $user_id = $json->user_id;
    $user_name = $json->nama;
    $ip_domisili = $json->ip_domisili;

    if (quorumCheck() > 1) {
        registerUser($user_id, $user_name, $ip_domisili);
    }
    
});

$app->post('/getSaldo', function () use($app) {
    //$user_id = $app->request->post('user_id');

    $data = $app->request->getBody();
    $json = json_decode($data);

    $user_id = $json->user_id;
    if (quorumCheck() > 1) {
        getSaldo($user_id);
    }
});

$app->post('/transfer', function () use($app) {
    $data = $app->request->getBody();
    $json = json_decode($data);

    $user_id = $json->user_id;
    $nilai = $json->nilai;

    if (quorumCheck() > 1) {
        transfer($user_id, $nilai);
    }
});

$app->post('/callTransfer', function () use($app) {
    $data = $app->request->getBody();
    $json = json_decode($data);

    $user_id = $json->user_id;
    $nilai = $json->nilai;
    $ip_tujuan = $json->ip_tujuan;

    if (quorumCheck() > 1) {
        callTransfer($user_id, $nilai, $ip_tujuan);
    }
});

$app->post('/getTotalSaldo', function () use($app) {
    $data = $app->request->getBody();
    $json = json_decode($data);

    $user_id = $json->user_id;

    if (quorumCheck() > 1) {
        getTotalSaldo($user_id);
    }
});

// Necessary Function
function quorumCheck() {
    $array = array("152.118.33.76", "152.118.33.95", "152.118.33.96", "152.118.33.97");
    $count_ping = 0;
    $ping_payload = "";
    $e = "error";

    foreach($array as $value) {
        $url_ping = "http://" . $value . "/ewallet/ping";
        $ch = curl_init();
        curl_setopt_array($ch, array(
            CURLOPT_SSL_VERIFYPEER => FALSE,
            CURLOPT_POSTFIELDS => $ping_payload,
            CURLOPT_HTTPHEADER => array('Content-Type:application/json'),
            CURLOPT_RETURNTRANSFER => TRUE,
            CURLOPT_URL => $url_ping,
        ));

        $response = curl_exec($ch);
        $json = json_decode($response);

        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        if($httpCode == 200) {
             $pong = $json->pong;
            $count_ping = $count_ping + $pong;
        }
    }
    return $count_ping;

}

function getPing() {
    $app = \Slim\Slim::getInstance();
    $app_info = array();

    $pong = 1;

    $app_info = array('pong' => $pong); 
    $app->response()->headers->set('Content-Type', 'application/json');
    echo json_encode($app_info);
}

function registerUser($user_id, $user_name, $ip_domisili) {
    $app = \Slim\Slim::getInstance();
    $app_info = array();
    
    $saldo_temp = 0;
    $data_inserted = 1;
    $data_exists = 0;
    
	$db = connectDatabase();

    $check_query = mysqli_query($db, "SELECT * FROM User WHERE user_id = '$user_id'");

    if(mysqli_fetch_row($check_query)) {
        $app_info = array('success' => $data_exists);
        $app->response()->headers->set('Content-Type', 'application/json');
        echo json_encode($app_info);

    }
    else {
        $insert_query = "INSERT INTO User (user_id, user_name, ip_domisili, nilai_saldo) VALUES ('$user_id', '$user_name','$ip_domisili','$saldo_temp')";
        mysqli_query($db, $insert_query);
        mysqli_close($db);

        $app_info = array('success' => $data_inserted); 
        $app->response()->headers->set('Content-Type', 'application/json');
        echo json_encode($app_info);
    }   
}

function getSaldo($user_id) {
    $app = \Slim\Slim::getInstance();
    $app_info = array();

    $field= "nilai_saldo";
	$db = connectDatabase();

    $select_query = "SELECT $field FROM User WHERE user_id = '$user_id'" ;
    $result = mysqli_query($db, $select_query);
    $row = mysqli_fetch_array($result);

    $status_result = -1;
    
    if ($row[$field] != null) {
        $status_result = (int) $row[$field];
    }
    $app_info = array('nilai_saldo' => $status_result); 
    $app->response()->headers->set('Content-Type', 'application/json');
    echo json_encode($app_info);
    

    mysqli_close($db);
}

function getTotalSaldo($user_id) {
    $app = \Slim\Slim::getInstance();
    $app_info = array(); 
    $total_saldo = 0;
    $local_ip = "152.118.33.76";
    $field = "ip_domisili";
    $db = connectDatabase();

    $getsaldo_payload = json_encode(array("user_id"=> $user_id));

    $select_query = "SELECT $field FROM User WHERE user_id = '$user_id'" ;
    $result = mysqli_query($db, $select_query);
    $row = mysqli_fetch_array($result);

    // array of branch
    $array = array("152.118.33.76", "152.118.33.95", "152.118.33.96","152.118.33.97");

    // case 0: if data empty
    if ($row[$field] == null) {
        $total_saldo = -1;

    }

    // case 1: user_id from our branch, looping getSaldo from each branch
    else if ($row[$field] == $local_ip) {
        foreach($array as $value) {
            $url_getsaldo = "http://" . $value . "/ewallet/getSaldo";
            $ch = curl_init();
            curl_setopt_array($ch, array(
                CURLOPT_SSL_VERIFYPEER => FALSE,
                CURLOPT_POSTFIELDS => $getsaldo_payload,
                CURLOPT_HTTPHEADER => array('Content-Type:application/json'),
                CURLOPT_RETURNTRANSFER => TRUE,
                CURLOPT_URL => $url_getsaldo,
            ));

            $response = curl_exec($ch);
            $json = json_decode($response);

            $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
            if($httpCode == 200) {
                // saldo value from getSaldo
                $nilai_saldo = $json->nilai_saldo;
                $total_saldo = $total_saldo + $nilai_saldo;
            }
        }
    }
    // case 2: user_id not from our branch, call getTotalSaldo() in their origin
    else {
        $url_getTotalsaldo = "http://" . $row[$field] . "/ewallet/getTotalSaldo";
        $ch = curl_init();
        curl_setopt_array($ch, array(
            CURLOPT_SSL_VERIFYPEER => FALSE,
            CURLOPT_POSTFIELDS => $getsaldo_payload,
            CURLOPT_HTTPHEADER => array('Content-Type:application/json'),
            CURLOPT_RETURNTRANSFER => TRUE,
            CURLOPT_URL => $url_getTotalsaldo,
        ));

        $response = curl_exec($ch);
        $json = json_decode($response);
        $nilai_saldo = $json->nilai_saldo;
        $total_saldo = $nilai_saldo;
    }

    $app_info = array('nilai_saldo' => $total_saldo); 
    $app->response()->headers->set('Content-Type', 'application/json');
    echo json_encode($app_info);
}

// Transfer method in receiver server
function transfer($user_id, $nilai) {
    $app = \Slim\Slim::getInstance();
    $app_info = array();

	$db = connectDatabase();

    $field= "nilai_saldo";
    // Select saldo user_id
    $select_query = "SELECT $field FROM User WHERE user_id = '$user_id'" ;
    $result = mysqli_query($db, $select_query);
    $row = mysqli_fetch_array($result);

    $init_saldo = (int)$row[$field];
    $new_saldo = $init_saldo + $nilai;
    $update_query = "UPDATE User SET nilai_saldo = '$new_saldo' WHERE user_id = '$user_id'" ;
    $updated = mysqli_query($db, $update_query);

    $app_info = array('status_transfer' => 0); 
    $app->response()->headers->set('Content-Type', 'application/json');
    echo json_encode($app_info);
    

    mysqli_close($db);
}

// Transfer method in caller server
function callTransfer($user_id, $nilai, $ip_tujuan) {
    $app = \Slim\Slim::getInstance();
    $app_info = array();
    
	$db = connectDatabase();

    $field= "nilai_saldo";
    $name = "user_name";
    $domisili = "ip_domisili";
    // Select saldo user_id
    $select_query = "SELECT * FROM User WHERE user_id = '$user_id'" ;
    $result = mysqli_query($db, $select_query);
    $row = mysqli_fetch_array($result);
    $init_saldo = (int)$row[$field];
    $init_name = $row[$name];
    $init_ip = $row[$domisili];

    // neccessary payload
    $register_payload = json_encode(array("user_id"=> $user_id, "nama"=> $init_name, "ip_domisili"=> $init_ip)); 
    $payload = json_encode(array("user_id"=> $user_id, "nilai"=> $nilai));
    $getsaldo_payload = json_encode(array("user_id"=> $user_id)); 

    //check getSaldo Condition
    $url_getsaldo = "http://" . $ip_tujuan . "/ewallet/getSaldo";
    $ch = curl_init();
        curl_setopt_array($ch, array(
            CURLOPT_SSL_VERIFYPEER => FALSE,
            CURLOPT_POSTFIELDS => $getsaldo_payload,
            CURLOPT_HTTPHEADER => array('Content-Type:application/json'),
            CURLOPT_RETURNTRANSFER => TRUE,
            CURLOPT_URL => $url_getsaldo,
        ));

    $response = curl_exec($ch);
    $json = json_decode($response);

    // saldo value from getSaldo output in receiver-side
    $nilai_saldo = $json->nilai_saldo;

    // user doesn't exists in receiver-side
    if ($nilai_saldo == -1) {
        // do register
        $url_register = "http://" . $ip_tujuan . "/ewallet/register";
        $ch = curl_init();
        curl_setopt_array($ch, array(
            CURLOPT_SSL_VERIFYPEER => FALSE,
            CURLOPT_POSTFIELDS => $register_payload,
            CURLOPT_HTTPHEADER => array('Content-Type:application/json'),
            CURLOPT_RETURNTRANSFER => TRUE,
            CURLOPT_URL => $url_register,
        ));

        $response = curl_exec($ch);

        $app_info = array('status_transfer' => -1); 
        $app->response()->headers->set('Content-Type', 'application/json');
        echo json_encode($app_info);
    }
    else if ($init_saldo > $nilai) {
        $url = "http://" . $ip_tujuan . "/ewallet/transfer";
        $ch = curl_init();
        curl_setopt_array($ch, array(
            CURLOPT_SSL_VERIFYPEER => FALSE,
            CURLOPT_POSTFIELDS => $payload,
            CURLOPT_HTTPHEADER => array('Content-Type:application/json'),
            CURLOPT_RETURNTRANSFER => TRUE,
            CURLOPT_URL => $url,
        ));

        $response = curl_exec($ch);

        $latest_saldo = $init_saldo - $nilai;
        $update_query = "UPDATE User SET nilai_saldo = '$latest_saldo' WHERE user_id = '$user_id'" ;
        $updated = mysqli_query($db, $update_query);
        
        $app_info = array('status_transfer' => 0); 
        $app->response()->headers->set('Content-Type', 'application/json');
        echo json_encode($app_info);
    }
    else {
        $app_info = array('status_transfer' => -1); 
        $app->response()->headers->set('Content-Type', 'application/json');
        echo json_encode($app_info);
    }

    mysqli_close($db);
}

function connectDatabase() {
    $server = 'localhost';
	$user = 'root';
	$pass = 'gemagema';
	$database = 'ewallet_db';
    $conn = mysqli_connect($server, $user, $pass, $database);
	return $conn;
}

$app->run();

?>