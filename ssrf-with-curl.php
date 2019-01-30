<?php
$url = $_GET['url'];
$url1 = parse_url($url, PHP_URL_HOST); // get hostname from url.
if(!filter_var($url1, FILTER_VALIDATE_IP)){
/*
the function dns_get_record() request all dns address like if a hacker would place 2 ip's on one domain both will be extracted. this prevents SSRF with 2 dns addresses.( gethostbyname only takes 1 dns adres and could be bypassed with 2 dns addresses ).
*/
$ips = dns_get_record($url1, DNS_A);
    
if(!empty($ips)){ // fast patch to prevent http://0 from being accepted.
foreach ($ips as &$value) {
    if(!ipCheck($value['ip'])){
        $is_allowed = false;
        break;
    }
}
    
}else{
$is_allowed = false;
}    
    
}else{
$ips = $url1;
if(!ipCheck($ips)){
    $is_allowed = false;
}
}
if(!isset($is_allowed)){    

header('Content-Type:text/plain'); // change this in the format you like to return the content.

$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, $url);
curl_setopt($ch, CURLOPT_HEADER, 0);
// //CURLOPT_FOLLOWLOCATION 0 prevents ssrf by redirecting it to a internal ip.
curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 0);
curl_setopt($ch, CURLOPT_PROTOCOLS, CURLPROTO_HTTP, CURLPROTO_HTTPS);
echo curl_exec($ch);
curl_close($ch);

}else{
    echo '<h1>forbidden</h1>';
}
function ipCheck($ip){
// checks if it is a internal ip adres.
return filter_var($ip, FILTER_VALIDATE_IP, FILTER_FLAG_NO_PRIV_RANGE | FILTER_FLAG_NO_RES_RANGE);    
}
?>
