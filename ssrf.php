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
echo 'allowed';    

//max_redirects 0 prevents ssrf by redirecting it to a internal ip.
$opts = array('http' =>
    array(
        'method' => 'GET',
        'max_redirects' => '0',
        'ignore_errors' => '1'
    )
);

$context = stream_context_create($opts);
$stream = fopen($url, 'r', false, $context);

header('Content-Type:text/plain'); // change this in the format you like to return the content.
echo stream_get_contents($stream);
fclose($stream);

}else{
    echo 'forbidden';
}

function ipCheck($ip){
// checks if it is a internal ip adres.
return filter_var($ip, FILTER_VALIDATE_IP, FILTER_FLAG_NO_PRIV_RANGE | FILTER_FLAG_NO_RES_RANGE);    

}
?>
