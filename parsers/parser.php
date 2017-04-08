<?php 

require '../classes/Users.php';
require '../classes/Message.php';
require '../classes/Dialog.php';
require '../classes/Group.php';

if(isset($_GET['action']))
  switch ( $_GET['action'] )
{
  case 'star':
  pars::dialogStar($_GET['access_token'],$_GET['id'],$_GET['important']);
  break;
  case 'delete':
  pars::del($_GET['access_token'],$_GET['id']);
  break;
  case 'restore':
  pars::restoreMessage($_GET['access_token'],$_GET['id']);
  break;
  case 'answer':
  pars::markAsAnsweredDialog($_GET['access_token'],$_GET['id'],$_GET['answered']);
  break;
  case 'send':
  pars::sendMessage($_GET['access_token'],$_GET['id'],$_GET['message'],$_GET['attachment']);
  break;
  case 'getAttachments':
  pars::getAttachments($_GET['access_token'],$_GET['id'],$_GET['media_type']);
  break;
  case 'load':
  foreach (pars::GetDialogHistory($_GET['access_token'],$_GET['id']) as $value) {
    pars::constructMessage($value);
  }
  break;
}

class pars {

  public static function cURL($url) {
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
    curl_setopt($ch, CURLOPT_HEADER, false);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
    $response = curl_exec($ch);
    curl_close($ch);
    return $response;
  }

  public static function getAttachments($access_token,$id,$media_type){
    $params = array(
      'peer_id'=>$id,
      'v' => '5.63',
      'media_type'=>$media_type,
      'access_token' => $access_token
      );  
    $url = 'https://api.vk.com/method/messages.getHistoryAttachments?' . http_build_query($params);
    $items = json_decode(pars::cURL($url))->response->items;   

    foreach ($items as $item) {
      // print_r($item->attachment);
      $rezult = pars::GetFile($item->attachment);
      echo $rezult;
    }

                   // print_r($items) ;
  }



    //получение полной инфы о юзере
  public static function GetUserInfo($user_id, $access_token) {
    $user = new Users();
    $params = array(
      'user_ids' => $user_id,
      'fields' => 'photo_200,photo_50,sex, bdate, city, country, home_town,relatives,relation,contacts,online',
      'name_case' => 'Nom',
      'v' => '5.63',
      'access_token' => $access_token
      );
    $url = 'https://api.vk.com/method/users.get?' . http_build_query($params);
    $response = pars::cURL($url);
    foreach (json_decode($response)->response[0] as $key => $value) {
      if (is_object($value)) {
        $user->{$key} = (array) $value;
      } else {
        $user->{$key} = $value;
      }
    }
    $user->relation = Users::$rel[$user->relation];
    return $user;
  }

  static function GetNameById($user) {
    if ($user->id < 0) {
      return "<span>" . $user->name . ";</span>";
    } else {
      $params = array(
        'user_ids' => $user->id,
        'name_case' => 'Nom',
        'v' => '5.63'
        );
      $url = 'https://api.vk.com/method/users.get?' . http_build_query($params);
      $response = json_decode(pars::cURL($url));
      $response = $response->response[0];
      return '<a href="http://web-2/examples/main.php?id=' . $user->id . '">' . $response->first_name . " " . $response->last_name . ";</a> ";
    }
  }
    //получение фото и имени юсера
  public static function GetUser($user_id) {
    $user = new Users();
    $params = array(
      'user_ids' => $user_id,
      'fields' => 'photo_50',
      'name_case' => 'Nom',
      'v' => '5.63',
      );
    $url = 'https://api.vk.com/method/users.get?' . http_build_query($params);
    $response = json_decode(pars::cURL($url))->response[0];
    $user->last_name = $response->last_name;
    $user->first_name = $response->first_name;
    $user->photo_50 = $response->photo_50;

    return $user;
  }

  public static function GetGroup($group_id,$access_token) {
    $group = new Group();
    $params = array(
      'user_ids' => $user_id,
      'fields' => 'photo_50',
      'name_case' => 'Nom',
      'v' => '5.63',
      'access_token' => $access_token
      );
    $url = 'https://api.vk.com/method/groups.getById?' . http_build_query($params);
    $response = json_decode(pars::cURL($url))->response[0];
    $group->name = $response->name;
    $group->photo_50 = $response->photo_50;
    $group->screen_name = $response->screen_name;
    return $group;
  }

    //получение  диалогов
  static function GetDialogs($access_token) {
    $dialogs =array();
    $params = array(
      'offset' => 0,
      'count' => 200,
      'v' => '5.63',
      'access_token' => $access_token
      );     
    if(isset($_GET["flag"]))
      switch ($_GET["flag"]) {
        case '1':
        $params['important']='1';
        break;
        case '2':
        $params['unanswered']='1';
        break;
        case '3':
        $params['unread']='1';
        break;
      }
      $url = 'https://api.vk.com/method/messages.getDialogs?' . http_build_query($params);    
      $items = json_decode(pars::cURL($url))->response->items;
      foreach ($items as $value) {
        $dialog = new Dialog;
        $user = pars::GetUser($value->message->user_id);
        $dialog->user_id = $value->message->user_id;
        $dialog->photo_50 = $user->photo_50;
        $dialog->last_name = $user->last_name;
        $dialog->first_name = $user->first_name;
        $dialog->body = $value->message->body;
        if(isset( $value->attachments))
          $dialog->body = pars::GetTypeFile($value->attachments);
        if(isset($value->unread))
        {
          $dialog->unread="unread";
          $dialog->unreadCol=$value->unread;
        }
        array_push($dialogs, $dialog);
      }
     xml::generateValidXmlFromObj((object)$items,"dialogs","dialog");
     file_put_contents('date/jsonFile.json',json_encode($items));
      return $dialogs;
    }

    function constructMessage($value){
      echo '<tr class ="'.$value->read_state.'" message_id="'.$value->message_id.'" onclick="allotMessage(this)"><td style="width:1;">
      <div class="icon pull-left"><span class="glyphicon glyphicon-ok-circle"></span></div>
      <img class="img-circle" src="' . $value->photo_50 . '"/></td><td><a href="http://web-2/examples/main.php?id=' . $value->user_id . '">' . $value->first_name . '</a>'.'
      <br><div class="bodyMessage"><p>' . $value->body . '</p>'.$value->attachments.'</div></td>';
    }

    static function GetDialogHistory($access_token,$user_id) {
      $dialogHistory =array();
      $params = array(
        'offset' => 0,
        'count' => 10,
        'v' => '5.63',
        'user_id' => $user_id,
        'access_token' => $access_token
        );
      if(isset($_GET['message_id']))
      {
        $params['start_message_id']=$_GET['message_id'];
        $params['offset']=1;
      }
      $url = 'https://api.vk.com/method/messages.getHistory?' . http_build_query($params);
      $items = json_decode(pars::cURL($url))->response->items;
      foreach ($items as $value) {
        $message = new Message();
        if(($value->from_id)>0)
        {
          $user = pars::GetUser($value->from_id);
          $message->photo_50 = $user->photo_50;
          $message->last_name = $user->last_name;
          $message->first_name = $user->first_name;
        }
        else
        {
          $group = pars::GetGroup($value->from_id,$access_token);
          $message->photo_50 = $group->photo_50;
          $message->last_name = "";
          $message->first_name = $group->name;
        }
        $message->message_id = $value->id;
        $message->user_id = $value->from_id;
        $message->body = $value->body;
        if(isset( $value->attachments))
          foreach ($value->attachments  as $item) {
            $str = pars::GetFile($item);
            $message->attachments = $message->attachments.$str;
          }
          if($value->read_state == 0)
            $message->read_state = "unread";
          array_push($dialogHistory, $message);
        }
        // $items  = (object)$items;
        xml::generateValidXmlFromObj((object)$items,"messages","message");
        file_put_contents('date/jsonFile.json',json_encode($items));
        return $dialogHistory;
      }
        static function GetFile($attachments){//получение содержимого фйла в сообщении
          $str="";
          
          switch ($attachments->type) {
            case 'photo':
            $str='<a href="'.$attachments->photo->photo_604.'"><img src="'.$attachments->photo->photo_130.'">'.'</a>';
            break;
            case 'doc':
            $str='<br><a href="'.$attachments->doc->url.'">'.$attachments->doc->title.'</a>';
            break;
            default:
            $str="какая то фигня";
            break;
          }
          
          return $str;
        }

        static function GetTypeFile($attachments){//получение типа  фйла в сообщении
          switch ($attachments[0]->type) {
            case 'photo':
            return 'Изображение';
            break;
            case 'doc':
            return 'Документ';
            break;
            default:
            return "какая то фигня";
            break;
          }
        }

        static function dialogStar($access_token,$user_id,$important){//пометить/убрать как важный
          $params = array(
            'important'=>$important,
            'peer_id' => $user_id,
            'v' => '5.63',
            'access_token' => $access_token
            );     

          $url = 'https://api.vk.com/method/messages.markAsImportantDialog?' . http_build_query($params);    
          $rezult = json_decode(pars::cURL($url))->response;
          echo $rezult;
        }


        static function del($access_token,$id){//удалить диалог/сообщение
          $params = array(
            'v' => '5.63',
            'access_token' => $access_token
            ); 
          $url;    
          switch ($_GET["type"]) {
            case 'dialog':
            $params['peer_id']=$id;
            $url = 'https://api.vk.com/method/messages.deleteDialog?' . http_build_query($params);
            $rezult = json_decode(pars::cURL($url))->response;
            break;
            case 'message':
            $params['message_ids']=$id;
            $url = 'https://api.vk.com/method/messages.delete?' . http_build_query($params);
            $rezult = json_decode(pars::cURL($url))->response->$id;
            break;
          }           
          echo $rezult;
        }

            static function restoreMessage($access_token,$id){//восстановить сообщение
              $params = array(
                'message_id'=>$id,
                'v' => '5.63',
                'access_token' => $access_token
                );  
              $url = 'https://api.vk.com/method/messages.restore?' . http_build_query($params);
              $rezult = json_decode(pars::cURL($url))->response;        
              echo $rezult;
            }

            static function markAsRead($access_token,$id){//прочитать все сообщения
              $params = array(
                'peer_id'=>$id,
                'v' => '5.63',
                'access_token' => $access_token
                );  
              $url = 'https://api.vk.com/method/messages.markAsRead?' . http_build_query($params);
              $rezult = json_decode(pars::cURL($url))->response;        
                // echo $rezult;
            }

            static function markAsAnsweredDialog($access_token,$id,$answered){//пометить диалог как отвеченный
              $params = array(
                'peer_id'=>$id,
                'v' => '5.63',
                'answered'=>$answered,
                'access_token' => $access_token
                );  
              $url = 'https://api.vk.com/method/messages.markAsAnsweredDialog?' . http_build_query($params);
              $rezult = json_decode(pars::cURL($url))->response;        
                // echo $rezult." ".;
            }

        //     static function getById($access_token,$id){//получить сообщение по ID
        //       $params = array(
        //         'message_ids'=>$id,
        //         'v' => '5.63',
        //         'access_token' => $access_token
        //         );  
        //       $url = 'https://api.vk.com/method/messages.getById?' . http_build_query($params);
        //       $value = json_decode(pars::cURL($url))->response->items[0];   
        //       $message = new Message();
        //       if(($value->from_id)>0)
        // {
        //   $user = pars::GetUser($value->from_id);
        //   $message->photo_50 = $user->photo_50;
        //   $message->last_name = $user->last_name;
        //   $message->first_name = $user->first_name;
        // }
        // else
        // {
        //   $group = pars::GetGroup(,$access_token);
        //   $message->photo_50 = $group->photo_50;
        //   $message->last_name = "";
        //   $message->first_name = $group->name;
        // }

        //       $message->message_id = $value->id;
        //       $message->user_id = $value->from_id;
        //       $message->body = $value->body;
        //       if(isset( $value->attachments))
        //         $message->body = pars::GetFile($value->attachments);
        //       $message->read_state = "";
        //       if($value->read_state == 0)
        //         $message->read_state = "unread";     
        //       return $value->from_id;
        //     }

            static function sendMessage($access_token,$id,$body,$attachment){//отправить сообщение
              $params = array(
                'peer_id'=>$id,
                'v' => '5.63',
                'message'=>$body,
                'attachment'=>$attachment,
                'access_token' => $access_token
                );  
              $url = 'https://api.vk.com/method/messages.send?' . http_build_query($params);
              $id_message = json_decode(pars::cURL($url))->response;    
              $group = pars::GetGroup("-88212219",$access_token);
              $message = new Message();
              $message->photo_50 = $group->photo_50;
              $message->last_name = "";
              $message->first_name = $group->name;
              $message->body = $body;
              $message->message_id = $id_message;
              $message->user_id = $group->screen_name;
              $message->read_state = "unread";  
              $message ->attachments = '<div id="forAttachments"></div>';
              // if(is_int($id_message))         
              pars::constructMessage($message);
                  // print_r(pars::getById($access_token,$id_message));
                 // echo $id_message;
            }
          }

class xml{
public static function generateValidXmlFromObj(stdClass $obj, $node_block='nodes', $node_name='node') {
        $arr = get_object_vars($obj);
        // return $arr;
        $xml = self::generateValidXmlFromArray($arr, $node_block, $node_name);
        file_put_contents('date/xmlFile.xml',$xml);
    }

    public static function generateValidXmlFromArray($array, $node_block='nodes', $node_name='node') {
        $xml = '<?xml version="1.0" encoding="UTF-8" ?>';

        $xml .= '<' . $node_block . '>';
        $xml .= self::generateXmlFromArray($array, $node_name).' "\n" ';
        $xml .= '</' . $node_block . '>';

        return $xml;
    }

    private static function generateXmlFromArray($array, $node_name) {
        $xml = '';

        if (is_array($array) || is_object($array)) {
            foreach ($array as $key=>$value) {
                if (is_numeric($key)) {
                    $key = $node_name;
                }

                $xml .= '<' . $key . '>' . self::generateXmlFromArray($value, $node_name) . '</' . $key . '>';
            }
        } else {
            $xml = htmlspecialchars($array, ENT_QUOTES);
        }

        return $xml;
    }
}
  ?>