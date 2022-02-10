<?
 $method = $_REQUEST['method'];
 $user = $_REQUEST['user'];
 $email = $_REQUEST['email'];
 $db = $_REQUEST['db'];
 if($db == 'users') {
  $toDB = '{"'.$user.'": { "name":"'.$user.'","email":"'.$email.'","onmain":'.$method.'} }';
 } else {
  $toDB = '{"'.$user.'": '.$email.'}';
 }
 $url = 'https://proto-489c4-default-rtdb.europe-west1.firebasedatabase.app/'.$db.'.json';
 $server_key ='AAAAnLiyA0U:APA91bE5IEzXO_OLOPgKIgfS9zTz7qpKjM0_1Fhmolk32PDgV3-qxhUwAd5OoGX8M_zGErbec26RMLWd8jbll383YJsLZDjpg1eaFDpVHotmuC8MgaTphgUmMwDasBNEj2tA92UMrXc_';
 $headers = array(
  'Content-Type:application/json',
  'Authorization:key='.$server_key
 );
 $ch = curl_init();
curl_setopt($ch, CURLOPT_URL, $url);
/* curl_setopt($ch, CURLOPT_POST, true);
curl_setopt($ch, CURLOPT_POSTFIELDS, $toDB); */
curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'PATCH');
curl_setopt($ch, CURLOPT_HTTPHEADER, $headers); //CURLOPT_DNS_SHUFFLE_ADDRESSES
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_POSTFIELDS, $toDB);
$response = curl_exec($ch);
curl_close($ch);

echo $toDB;
?>