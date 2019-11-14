<?php
$url = 'test/api/books/5';
$data = ['name' => 'Dedda', 'genre' => 'tae', 'pages' => '3'];


$data1 = json_encode($data);
$curl = curl_init();
curl_setopt($curl, CURLOPT_URL, $url);
#curl_setopt($curl, CURLOPT_FOLLOWLOCATION, true);
curl_setopt($curl, CURLOPT_HEADER, false);
curl_setopt($curl, CURLOPT_RETURNTRANSFER, false); // return to browser(0) or not(1)
curl_setopt($curl, CURLOPT_CUSTOMREQUEST, "GET");
curl_setopt($curl, CURLOPT_POSTFIELDS, $data1);
curl_setopt($curl, CURLOPT_USERPWD, 'andrew:123');
curl_setopt($curl, CURLOPT_HTTPAUTH, CURLAUTH_BASIC);
$json_response = curl_exec($curl);
var_dump($data1);
curl_close($curl);
