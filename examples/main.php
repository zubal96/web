<html>  

<script src="https://code.jquery.com/jquery-3.2.1.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/tablefilter/2.5.0/tablefilter.js"></script>
<script src="Content/jquery.tablesorter.js"></script>
<script src="Content/jquery.uitablefilter.js" type="text/javascript"></script>
<link href="Content/style.css" rel="stylesheet" type="text/css"/>  
<link href="Content/bootstrap.css" rel="stylesheet" type="text/css"/>
<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css"
integrity="sha384-BVYiiSIFeK1dGmJRAkycuHAHRg32OmUcww7on3RYdg4Va+PmSTsz/K68vbdEjh4u" crossorigin="anonymous">
<script src="Content/bootstrap.js"></script>
<script src="Content/scriptAll.js"></script>
<script src="Content/script.js"></script>

<?php
require 'config.php';
require '../parsers/parser.php';
session_start();
if (!isset($_SESSION['token'])) {
  if (isset($_SESSION['code'])) {
    $token = config::GetToken();
    $_SESSION['token'] = $token;
    $dialogs = pars::GetDialogs($_SESSION['token']->access_token_88212219);
    $_SESSION['dialogs'] = $dialogs;
  } else {
    header('Location:' . config::$get_access_code_url . http_build_query(config::$params_code));
  }
} else {
  $dialogs = pars::GetDialogs($_SESSION['token']->access_token_88212219);
  $_SESSION['dialogs'] = $dialogs;       
}

if (!isset($_SESSION['token_user'])) {
  if (isset($_SESSION['code_user'])) {
    $token_user = config::GetTokenUser();
    $_SESSION['token_user'] = $token_user;
    $user = pars::GetUserInfo($_SESSION['token_user']->user_id, $_SESSION['token_user']->access_token);
    $_SESSION['user'] = $user;
  } else {
    header('Location:' . $get_access_code_url . http_build_query($params_code_user));
  }
} else {
  if (isset($_GET["id"])) {
    $user = pars::GetUserInfo($_GET["id"], $_SESSION['token_user']->access_token);
  } else {
    $user = pars::GetUserInfo($_SESSION['token_user']->user_id, $_SESSION['token_user']->access_token);
    $_SESSION['user'] = $user;
  }
}
?>


<body>
  <div class="container-fluid">


   <nav class="navbar navbar-default navbar-fixed-top" style="background-color: #286090">
    <ul class="nav navbar-nav navbar-left">
      <li>
        <h1 style="color: #c4e3f3; margin-left: 20px;">AppTest</h1>
      </li>
    </ul>
    <ul class="nav navbar-nav navbar-right" style="margin-right: 5px;">
      <li class="dropdown">
        <a style="color: white" class="dropdown-toggle" data-toggle="dropdown" href="#"><?php echo '<img style="width:40px;height:40px" src="' . $_SESSION['user']->photo_50 . '" class="img-circle"/>' . $_SESSION['user']->first_name; ?>
          <span class="caret"></span></a>
          <ul class="dropdown-menu">
            <li><?php echo '<a href="http://web-2/examples/main.php?id='.$_SESSION['token_user']->user_id.'"><span class="glyphicon glyphicon-home"></span> Моя страница</a>'?></li>
            <li class="divider"></li>
            <li><a href="http://web-2/examples/session_destroy.php"><span class="glyphicon glyphicon-log-in"></span> Login</a></li>
          </ul>
        </li>

      </ul>
    </nav> 

    <div class="row" style="margin-top: 60px;">

      <div class="col-sm-2 sidebar">
        <?php echo '<img style="margin-top:20px;" class="img-thumbnail" src="' . $user->photo_200 . '"/>' ?>
      </div>
      <div class="col-sm-10">

       <div class="row">
        <h1 style="float: left">
          <?php echo $user->last_name . " " . $user->first_name ?>
          <h1 style="float:right"><?php
            if ($user->online === 1) {
              echo "Online";
            } else {
              echo 'Offline';
            }
            ?></h1>
          </h1>



        </div>
        <div class="panel">
          <div class="panel-body" style="width: 100%">
            <table class="table white_border_tr" style="border-color: white">
              <tbody>
                <tr>
                  <td>День рождения</td>
                  <td><?php echo $user->bdate; ?></td>
                </tr>
                <tr>
                  <td>Город</td>
                  <td><?php echo $user->city["title"]; ?></td>
                </tr>
                <tr>
                  <td>Семейное положение</td>
                  <td><?php echo $user->relation; ?></td>
                </tr>
                <tr>
                  <td>Родственники</td>
                  <td><?php
                    foreach ($user->relatives as $val) {
                      echo pars::GetNameById($val);
                    }
                    ?></td>
                  </tr>
                </tbody>
              </table>
              <hr>
              <h3>Основная информация</h3>

              <table class="table white_border_tr" style="border-color: white">
                <tbody>
                  <tr>
                    <td>Родной город</td>
                    <td><?php echo $user->home_town; ?></td>
                  </tr>
                  <tr>
                    <td>Домашний телефон</td>
                    <td><?php echo $user->home_phone ?></td>
                  </tr>
                  <tr>
                    <td>Сотовый телефон</td>
                    <td><?php echo $user->mobile_phone ?></td>
                  </tr>
                </tbody>
              </table>
            </div>
          </div>


          <div class="panel">
            <h3>Диалоги</h3>
            <div class="row">
              <div class ="col-md-6">
                <input id="filter" type="text" placeholder="Поиск" class="form-control"/>

                <table class="table tablesorter" id="myTable" style="margin-top: 10px;">
                  <tbody>
                    <?php
                                // echo $_SESSION['code_user'].'|'.$_SESSION['code'];
                                // echo $_SESSION['token']->access_token_88212219."<br>";
                    // print_r($dialogs) ;
                    foreach ($dialogs as $value) {
                      echo 
                      '
                      <tr class="'.$value->unread.'" user_id="'.$value->user_id . '">
                        <td style="width:1;"><img class="img-circle" src="' . $value->photo_50 . '" style="float:rigth;"></td>
                        <td>'.$value->first_name.' '.$value->last_name.'
                          <span onclick="star(this,'.$value->user_id.')" class="glyphicon glyphicon-star"></span><br>
                          <p>'. $value->body.'</p></td>
                          <td align="right">';
                          if($value->unreadCol>0) echo'<span class="badge">'.$value->unreadCol.'</span>';
                        echo  '<span onclick="delDialog('.$value->user_id.')" class="glyphicon glyphicon-remove"></span><br>
                               <span onclick="answerDialog(this,'.$value->user_id.')" class="glyphicon glyphicon-ok"></span>
                                </td></tr>';
                      }
                      ?>
                    </tbody>
                  </table>
                </div>
                <div class="col-md-2">
                  <div class="btn-group-vertical">
                    <button type="button" onclick="buttonClick('')" class="btn btn-default">Все сообщения</button>
                    <button type="button" onclick="buttonClick('1')" class="btn btn-default">Важные</button>
                    <button type="button" onclick="buttonClick('2')" class="btn btn-default">Неотвеченные</button>
                    <button type="button" onclick="buttonClick('3')" class="btn btn-default">Непрочитанные</button>
                  </div>
                </div>
              </div>
            </div>

          </div>
        </div>
      </div> 
      <span id="token" token ="<?php echo $_SESSION['token']->access_token_88212219 ?>" ></span>
    </body>
    </html>
