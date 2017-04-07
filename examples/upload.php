<?php 
    require '../parsers/parser.php';

uploadFile($_GET['access_token']);

function uploadFile($access_token)
{


$params = array(
        'v' => '5.63',
        'access_token' => $access_token
        );
// $img_real_path = realpath(dirname(__FILE__)."/..".$_POST['img']);
$curl_file =  curl_file_create($_GET['type'],$_GET['name']);
$url = 'https://api.vk.com/method/photos.getMessagesUploadServer?' . http_build_query($params);
$upload_url = json_decode(pars::cURL($url))->response->upload_url;
// echo $img_real_path;

$ch=curl_init();
curl_setopt_array($ch, array(
CURLOPT_RETURNTRANSFER => 1,
CURLOPT_URL => $upload_url,
CURLOPT_POST => 1,
CURLOPT_POSTFIELDS => array("photo" => $curl_file)
));
$response = json_decode(curl_exec($ch), true);

$params = array(
        'photo' =>$response['photo'],
        'server'=>$response['server'],
        'hash'=>$response['hash'],
        'v' => '5.63',
        'access_token' => $access_token
        ); 
$url = 'https://api.vk.com/method/photos.saveMessagesPhoto?' . http_build_query($params);
$rezult = json_decode(pars::cURL($url));

// // Выводим информацию о загруженном файле:
//     echo "<h3>Информация о загруженном на сервер файле: </h3>";
     print_r($response);
     echo $_SERVER['DOCUMENT_ROOT'];
     // echo $access_token;
     echo "<p><b>Оригинальное имя загруженного файла: ".$_GET['name']."</b></p>";
     echo "<p><b>Mime-тип загруженного файла: ".$_GET['type']."</b></p>";
     echo "<p><b>Размер загруженного файла в байтах: ".$_FILES['uploadfile']['size']."</b></p>";
     echo "<p><b>Временное имя файла: ".$_FILES['uploadfile']['tmp_name']."</b></p>";
}
