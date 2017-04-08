    <script src="https://code.jquery.com/jquery-3.2.1.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/tablefilter/2.5.0/tablefilter.js"></script>
    <script src="Content/jquery.tablesorter.js"></script>
    <script src="Content/jquery.uitablefilter.js" type="text/javascript"></script>
    <link href="Content/bootstrap.css" rel="stylesheet" type="text/css"/>
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css"
    integrity="sha384-BVYiiSIFeK1dGmJRAkycuHAHRg32OmUcww7on3RYdg4Va+PmSTsz/K68vbdEjh4u" crossorigin="anonymous">
    <link href="Content/style.css" rel="stylesheet" type="text/css"/>
    <script src="Content/bootstrap.js"></script>    
    <script src="Content/scriptAll.js"></script>
    <script src="Content/script1.js"></script>


    <?php
    require 'config.php';
    require '../parsers/parser.php';
    session_start();
    if (isset($_GET['user_id'])) {
      $user_id = "'".$_GET['user_id']."'";
      $user = pars::GetUser($_GET['user_id']);
      pars::markAsRead($_SESSION['token']->access_token_88212219,$_GET['user_id']);
      $dialogHistory = pars::GetDialogHistory($_SESSION['token']->access_token_88212219,$_GET['user_id']);
    } 
    else {
      header('Location: http://web-2/examples/main.php');
    }
    ?>


    <body>
      <div class="container">
        <div class="row">
          <div class="col-md-8">
            <div class="panel">
              <!-- <div class="panel-title text-center">  -->
               <!-- </div> -->
              <div class="row panel-body"> 
               <?php // print_r($dialogHistory);
               //echo $dialogHistory;
               ;?>
               <div class="col-md-10" id="div1" >
                <div class="col-md-12 allot">
                  <div class="col-md-4 ">
                    <div class="col-md-8 ">
                      <p class="myP text-right" id="countMessage"></p>
                    </div>
                    <div onclick='allotDel()' class="col-md-2">
                      <span class="glyphicon glyphicon-remove"></span>
                    </div>
                    
                    
                  </div>
                  <div class="col-md-offset-4 col-md-4">
                    <button type="button"  onclick="delMessage()" class="btn btn-default">Удалить</button>
                  </div>
                </div>
                <div class="col-md-12 search">
                  <div class="col-md-10">
                    <input id="filter" type="text" placeholder="Поиск" class="form-control"/>
                  </div>
                  <div class="col-md-2">
                    <button type="button"  onclick="defaultActive()" class="btn btn-default">Отмена</button>
                  </div>
                </div>
                <div class="col-md-12 default">
                  <div class="col-md-4">
                    <a type="button" href="main.php"  class="btn btn-default">Назад</a>
                  </div>
                  <div class="col-md-4">
                   <h4><?php echo $user->first_name." ".$user->last_name ?>  </h4>
                 </div>     
                 <div class="col-md-4">                       
                  <div class="btn-group  pull-right">
                    <button type="button"   onclick="answerDialog($(this).children('span'),<?php echo $user_id?>)" class="btn btn-default">
                      <span class="glyphicon glyphicon-ok"></span>
                    </button>
                    <button type="button"   onclick="star($(this).children('span'),<?php echo $user_id?>)" class="btn btn-default">
                      <span class="glyphicon glyphicon-star"></span>
                    </button>
                     <!-- <button type="button"   onclick="loadMessage(-1)" class="btn btn-default">
                      <span class="glyphicon glyphicon-refresh"></span>
                    </button> -->
                    <div class="btn-group">
                      <button type="button" class="btn btn-default dropdown-toggle" data-toggle="dropdown">
                        <span class="glyphicon glyphicon-chevron-down"></span>
                      </button>
                      <ul class="dropdown-menu">
                        <li>
                          <a  href="#"  onclick="">
                            <span class="glyphicon glyphicon-th-large" data-toggle="modal" data-target="#myModal">Показать вложения</span>
                          </a>
                        </li>
                        <li>
                          <a href="#"   onclick="searchActive()" >
                            <span class="glyphicon glyphicon-search">Поиск по истории сообщений</span>
                          </a>
                        </li>
                        <li>
                          <a  href="#"  onclick="delDialog(<?php echo $user_id?>)">
                            <span class="glyphicon glyphicon-trash">Удалить историю сообщений</span>
                          </a>
                        </li>
                      </ul>
                    </div>
                  </div>
                </div>
              </div>
            </div>
            <div class="col-md-2">
              <img class="img-circle" src="<?php echo $dialogHistory[0]->photo_50 ?>" style="float:rigth;">
            </div>
          </div>
          <div  class="row panel-body"  id="history">
            <table class="table tablesorter" id="myTable" style="margin-top: 10px;">
              <tbody>
               <?php                                
               foreach ($dialogHistory as $value) {
                pars::constructMessage($value);
              }
              ?>
            </tbody>
          </table>
        </div>
        

        <div class="row panel-body">
          <div class="col-md-12" style="padding-bottom: 10px;">
            <textarea id="textArea" placeholder="Введите сообщение" class="form-control" rows="3"></textarea>
          </div>
          <div class="col-md-12">
              <div class="col-md-9">
              <form id="form1" action="upload.php?user_id=<?php echo $_GET['user_id'] ?>"  method=post enctype=multipart/form-data>
                <input type=file name=uploadfile[] accept="image/*" onchange="$('#form1').submit()" multiple>
              </form> 
              </div>
              <div class="col-md-3">
                <button type="button" onclick="sendMessage()" class="btn btn-default">Отправить</button>
              </div>
              
          </div>
          <div class="col-md-12" id="attachment">
          </div>
            </div>
          </div>
        </div>
    
      <div class="col-md-2">
        <div class="btn-group-vertical" style="padding:20px;">
          <button type="button" onclick="buttonClick('')" class="btn btn-default">Все сообщения</button>
          <button type="button" onclick="buttonClick('1')" class="btn btn-default">Важные</button>
          <button type="button" onclick="buttonClick('2')" class="btn btn-default">Неотвеченные</button>
          <button type="button" onclick="buttonClick('3')" class="btn btn-default">Непрочитанные</button>
        </div>
      </div>
    </div>
  </div>

  <div class="modal fade" id="myModal">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
        <button type="button" onclick="getAttachments('photo')" class="btn btn-default" >Фотографии</button>
        <!-- <button type="button" onclick="getAttachments('video')" class="btn btn-default" >Видео</button> -->
        <button type="button" onclick="getAttachments('doc')" class="btn btn-default" >Документы</button>
        <!-- <button type="button" onclick="getAttachments('audio')" class="btn btn-default" >Музыка</button> -->
      </div>
      <div class="modal-body">
        
      </div>
     <!--  <div class="modal-footer">
        <button type="button" class="btn btn-default" data-dismiss="modal">Закрыть</button>
        <button type="button" class="btn btn-primary">Сохранить изменения</button>
      </div> -->
    </div><!-- /.modal-content -->
  </div><!-- /.modal-dialog -->
</div><!-- /.modal -->
  <span id="token" token = <?php echo $_SESSION['token']->access_token_88212219 ?> ></span>
  <span id="user_id" user_id = <?php echo $_GET['user_id'] ?> ></span>
</body>
</html>

<?php 
    if(isset($_SESSION['attachment']))
    {
      echo "<script type='text/javascript'>
          uploadFile('".$_SESSION['attachment']."','".$_SESSION['htmlCode']."')
      </script>";
      unset($_SESSION['attachment']);
      unset($_SESSION['htmlCode']);
    }

?>