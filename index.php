<!DOCTYPE html>
<html>
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title></title>
  <link rel="stylesheet" href="//code.jquery.com/ui/1.12.1/themes/base/jquery-ui.css">
  <script src="https://code.jquery.com/jquery-1.12.4.js"></script>
  <script src="https://code.jquery.com/ui/1.12.1/jquery-ui.js"></script>
  <script src="https://code.jquery.com/jquery-3.3.1.js" integrity="sha256-2Kok7MbOyxpgUVvAk/HJ2jigOSYS2auK4Pfzbm7uH60=" crossorigin="anonymous"></script>
  <script src="//www.gstatic.com/firebasejs/4.5.0/firebase.js"></script>
<script>
$(document).ready( function () {
  function toDB(user, email, db, method){
    (db) ? '' : db = "friends";
    let userdb;
    $.ajax({
      type: 'POST',
      url: 'fb.php',
      data: {"user": user, "email": email, "db": db, "method": method},
      success: function(response){console.log(response.responseText)},
      error: function(response){userdb = response.responseText;console.log(userdb)},
      dataType: "application/json"
    });
    console.log(user, email, db, method);
  };

  $('.auth input[type="submit"]').on("click", async function(){
   try{
    let login = await $.get('https://proto-489c4-default-rtdb.europe-west1.firebasedatabase.app/users/' + $('#name').val() + '.json');
    if(login){
      if(login.email == $('#email').val()){
        console.log('пароль верный');
        document.cookie = "user=" + login.name;
        window.location.reload();
      } else {
        console.log('пароль не верный');
      }
    } else {
      console.log('не найден, регистрируем');
      document.cookie = "user=" + $('#name').val();
      toDB($('#name').val(),$('#email').val(),'users',true);
      toDB($('#name').val(),'{"name":"'+$('#name').val()+'"}');
      setTimeout(() => window.location.reload(), 500);
    }
   } catch(e) {
    console.log('юзер не найден и что-то пошло не так')
   }
  });
  
  $('#user input[type="button"]').on("click", function(){ // кнопка выхода
    document.cookie = "user=";
    setTimeout(() => window.location.reload(), 500);
  });

  $('#user input[type="submit"]').on("click", async function(){ // кнопка убрать с главной страницы
    let onmain = await $.get('https://proto-489c4-default-rtdb.europe-west1.firebasedatabase.app/users/' + $('#user h2').text() + '/onmain.json');
    toDB($('#user h2').text(),$('#user div').text(),'users',!onmain);
    setTimeout(() => window.location.reload(), 500);
  });

  $('.users-card input[type="button"]').on("click", function(){ // кнопки в карточках друзей
    let type = this.className;
    console.log(type);
    let username = $('#user h2').text();
    // let user = document.cookie.split('=');
    let friend = this.name;
    
async function removeFriend( user, friend){ // удалить друга в своём списке друзей
  let newfr = {}
  let friends = await $.get('https://proto-489c4-default-rtdb.europe-west1.firebasedatabase.app/friends/' + user + '.json');
  Object.keys(await friends).forEach(function(key){
    console.log(key)
    if(key != friend){ newfr[key] = friends[key];}
    console.log(newfr)
  });
  toDB(user, JSON.stringify(newfr));
}

async function addFriend( user, friend, type){ // добавить друга в свой список друзей
  let newfr = {}
  let friends = await $.get('https://proto-489c4-default-rtdb.europe-west1.firebasedatabase.app/friends/' + user + '.json');
  Object.keys(friends).forEach(key => newfr[key] = friends[key]);
  newfr[friend] = type;
  toDB(user, JSON.stringify(newfr));
}

async function changeFriend( user, friend){ // изменить статус у друга обомне
  let newfr = {}
  let friends = await $.get('https://proto-489c4-default-rtdb.europe-west1.firebasedatabase.app/friends/' + user + '.json');
  Object.keys(friends).forEach(function(key){
    if(key == friend){ newfr[key] = !friends[key];} else {newfr[key] = friends[key];}
  });
  toDB(user, JSON.stringify(newfr));
}

switch(type){
  case 'yellow':
    removeFriend(username,friend);
    break;
  case 'green':
    addFriend(username,friend, true);
    changeFriend(friend,username);
    break;
  case 'blue':
    addFriend(username,friend, false);
    break;
  case 'red':
    removeFriend(username,friend);
    changeFriend(friend,username);
    break;
  case 'gray':
    removeFriend(friend,username);
    break;
}

setTimeout(() => window.location.reload(), 500);

  });


  // preg_match('/^([А-Яа-я]{3})$/u', $item);

  $('#name, #email').focusout(function(){
    let nameTest = /^([A-Z]{1}[a-z]{1,})$/u;
    let emailTest = /^([a-z]{2,})@([a-z]{2,}\.)([a-z]{2,})$/u;
    if( nameTest.test($('#name').val()) && emailTest.test($('#email').val()) ){
      $('input[type="submit"]').prop("disabled", false);
      $('.auth').removeClass("err");
    } else {$('input[type="submit"]').prop("disabled", true);}
  });
}); 
</script>
<style>
  .users{max-width: 600px;padding-top: 100px;position: relative;border-bottom: 1px solid #000;}
  .users-card{display: inline-block;float: left;width: 160px;margin: 5px;padding: 14px;border: 1px solid #000;height: 100px;}
  .users-card input{border: 1px solid #000;width: 100%;}
  .blue{background: blue;color: #fff;}
  .green{background: green;color: #fff;}
  .red{background: red;color: #fff;}
  .yellow{background: yellow;color: #000;}
  .gray{background: gray;color: #000;}
  #user{position: absolute;top: 0; height: 100px;width: 100%;}
  #user div{text-align: right;font-size: 32px;margin-top: -53px;}
  #user input[type="button"]{float: right;}
</style>

</head>

<body result="<?=$result?>">
<?php
  if( !$_COOKIE["user"] ){$_COOKIE["user"] = '';
?>
<div class="auth">
    Name формата "Ааа", Email формата "аа@аа.аа" только латиница,</br>проверка при выходе с поля</br>
    <h3>Авторизация/Регистрация</h3>
    <input id="name" type="text" placeholder="Name"/>
    <input id="email" type="text" placeholder="Email" />
    <input type="submit" value="Sign in" disabled/>
</div>
<?}else{
  
function db($url){
  $array = json_decode(json_decode(json_encode(file_get_contents($url)),TRUE), TRUE);
  return $array;
}
$users = db('https://proto-489c4-default-rtdb.europe-west1.firebasedatabase.app/users.json');
$friends = db('https://proto-489c4-default-rtdb.europe-west1.firebasedatabase.app/friends.json');
$userfr = [];
$friend = [];
$fruser = [];
foreach($friends as $key => $item){
  if($item["name"] == $_COOKIE["user"]){
    foreach($item as $key => $i){
      if($i === false){
        array_push($userfr, $key);
      }
      if($i === true){
        array_push($friend, $key);
      }
    }
    
  }
  if($item[strval($_COOKIE["user"])] === false){array_push($fruser, $item["name"]);}
}
/* print_r($userfr);
print_r($friend);
print_r($fruser); */
?>


<div class="users">
<?
  foreach($users as $key => $item){
    if($item["name"] == $_COOKIE["user"]){?>
      <div id="user">
        <h2><?=$item["name"]?></h2>
        <div><?=$item["email"]?></div>
        <input type="submit" value="<? if($item["onmain"]){echo 'Убрать с главной';} else {echo 'Добавить на главную';}?>"/>
        <input type="button" value="Выйти" />
      </div>
  <?} else if( $item["onmain"] ) { ?>
      <div class="users-card">
        <? print_r($key); ?>
    </br>
          </br>
        <?
          $notfriend = true;
          foreach($userfr as $u){
            if($u == $key){
        ?>
          <input type="button" class="yellow" name="<?=$key;?>" value="Отменить заявку" />
        <?  
            $notfriend = false;
            }
          }

          foreach($friend as $f){
            if($f == $key){
            print_r($item["email"]);
        ?>
          </br>
          <input type="button" class="red" name="<?=$key;?>" value="Прекратить дружбу" />
        <?  
            $notfriend = false;
            }
          }
          
          foreach($fruser as $f){
            if($f == $key){
        ?>
          <input type="button" class="green" name="<?=$key;?>" value="Подтвердить заявку" />
          </br>
          </br>
          <input type="button" class="gray" name="<?=$key;?>" value="Отклонить заявку" />
        <?  
            $notfriend = false;
            }
          }
          if($notfriend){
        ?>
          <input type="button" class="blue" name="<?=$key;?>" value="Подать заявку" />
        <?
          }
        ?>
      </div>
<?  }
  }
}
?>
</div>

</body>
</html>