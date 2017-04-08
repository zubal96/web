<?php 
require '../parsers/parser.php';

session_start();
// echo $_SESSION['token']->access_token_88212219;
$i=0;
while( $i < count($_FILES['uploadfile']['name']))
{ 
  uploadFile();
  $i++;
}
 
// echo count($_FILES['uploadfile']['name']);
function uploadFile()
{
    global $i;
    $access_token = $_SESSION['token']->access_token_88212219;
    // echo $access_token;
    $params = array(
        'v' => '5.63',
        'access_token' => $access_token
        );

    $curl_file =  curl_file_create($_FILES['uploadfile']['tmp_name'][$i],$_FILES['uploadfile']['type'][$i],$_FILES['uploadfile']['name'][$i]);

    switch (substr($_FILES['uploadfile']['type'][$i], 0,strpos($_FILES['uploadfile']['type'][$i], '/'))) {
        case 'image':
        $url = 'https://api.vk.com/method/photos.getMessagesUploadServer?' . http_build_query($params);
        $typefile = "photo";
        break;
        case 'text':case'application':
        $params['group_id']='88212219';
        $url = 'https://api.vk.com/method/docs.getWallUploadServer?' . http_build_query($params);
        $typefile = "file";
        break;
    }

    $upload_url = json_decode(pars::cURL($url))->response->upload_url;
      // print_r($upload_url);

    $ch=curl_init();
    curl_setopt_array($ch, array(
        CURLOPT_RETURNTRANSFER => 1,
        CURLOPT_URL => $upload_url,
        CURLOPT_POST => 1,
        CURLOPT_POSTFIELDS => array($typefile => $curl_file)
        ));

    $response = json_decode(curl_exec($ch), true);
    switch (substr($_FILES['uploadfile']['type'][$i], 0,strpos($_FILES['uploadfile']['type'][$i], '/'))) {
       case 'image':
       $paramsSave = array(
        'photo' =>$response['photo'],
        'server'=>$response['server'],
        'hash'=>$response['hash'],
        'v' => '5.63',
        'access_token' => $access_token
        ); 
       $urlSave = 'https://api.vk.com/method/photos.saveMessagesPhoto?' . http_build_query($paramsSave);
       break;
       case 'text':case'application':
       $paramsSave = array(
        'file' =>$response['file'],
        'v' => '5.63',
        'access_token' => $access_token
        ); 
       $urlSave = 'https://api.vk.com/method/docs.save?' . http_build_query($paramsSave);
       break;
   }

   $rezult = json_decode(pars::cURL($urlSave))->response[0];
   $attachments = new stdClass();
   $attachments->type='photo';
   $attachments->photo=$rezult;
   
   $_SESSION['attachment'] = $_SESSION['attachment'].$attachments->type.$attachments->photo->owner_id."_".$attachments->photo->id.",";
   $_SESSION['htmlCode'] = $_SESSION['htmlCode'].(pars::GetFile($attachments));
   // $rezult->type;
   // echo $attachments->attachment."<br>";
  
   
   // print_r($attachments);

//       echo "<p><b>Оригинальное имя загруженного файла: ".$_FILES['uploadfile']['name'][$i]."</b></p>";
// echo "<p><b>Mime-тип загруженного файла: ".$_FILES['uploadfile']['type'][$i]."</b></p>";
// echo "<p><b>Размер загруженного файла в байтах: ".$_FILES['uploadfile']['size'][$i]."</b></p>";
// echo "<p><b>Временное имя файла: ".$_FILES['uploadfile']['tmp_name'][$i]."</b></p>";
}

header('Location:http://web-2/examples/dialog.php?user_id='.  $_GET['user_id']);
