<?php
if(!isset($_GET["url"])){die;}
$domain = parse_url($_GET["url"], PHP_URL_HOST);
      if($domain!="freepik.com"){
        $url = str_replace($domain, "freepik.com", $_GET["url"]);
      }
      else
      {
        $url = $_GET["url"];
      }
$server = 1;
$useragent = "Mozilla/5.0 (Linux; Android 12; SM-S906N Build/QP1A.190711.020; wv) AppleWebKit/537.36 (KHTML, like Gecko) Version/4.0 Chrome/80.0.3987.119 Mobile Safari/537.36"; 

$url = strtok($url, "#");
function GetBetween($content,$start,$end)
    {
      $r = explode($start, $content);
      if (isset($r[1])){
        $r = explode($end, $r[1]);
        return $r[0];
      }
      return '';
}

$url= $_GET["url"];
$start = '_';
$end = '.htm';
$stockid = GetBetween($url,$start,$end);
$tryurl = "https://www.freepik.com/xhr/download-url/".$stockid;

$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, $url);
curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
curl_setopt($ch, CURLOPT_VERBOSE, true);
curl_setopt($ch, CURLOPT_COOKIEJAR, 'C:/wamp/www/freepik.com_cookies.txt');
curl_setopt($ch, CURLOPT_COOKIEFILE, 'C:/wamp/www/freepik.com_cookies.txt');
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);  

$headers = [
  'Referer: https://www.freepik.com/home',
  'user-agent: '.$useragent,
];

curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);                                                                                            
$result = curl_exec($ch);

$start1 = "CSRF_TOKEN = '";
$end1 = "';";
$token = GetBetween($result,$start1,$end1);

$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, $tryurl);
curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
curl_setopt($ch, CURLOPT_VERBOSE, true);
curl_setopt($ch, CURLOPT_COOKIEJAR, 'C:/wamp/www/freepik.com_cookies.txt');
curl_setopt($ch, CURLOPT_COOKIEFILE, 'C:/wamp/www/freepik.com_cookies.txt');
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);  
$headers = [
  'Referer: '.$url,
  'user-agent: '.$useragent,
  'x-requested-with: XMLHttpRequest',
  'x-csrf-token: ' . $token,
];

curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);                                                                                            
$result = curl_exec($ch);
curl_close($ch);

$json = json_decode($result);

if($json->success){
  $download_url = $json->url;

  $filename = basename(trim(strtok($download_url, '?')));
  $ext = pathinfo($filename, PATHINFO_EXTENSION);

  $rsArray = array('status' => true, 'server' => $server, 'url' => $download_url, 'filename' => $filename, 'extension' => $ext);
  echo json_encode($rsArray);
  die;
  
}
else
{
$rsArray = array('status' => false, 'server' => $server, 'message' => $json);
  echo json_encode($rsArray);
  die;
}
?>
