<?php
class amoCRM {

public $client_secret = 'auyAUqsu9qKFeYOsUT3QIu5DgH6apOX0TK0WblaRc5lURfEpP4DEkIGJCWgzxvcT';
public $client_id = 'abd9bfa6-9452-4d0f-b3e7-850e832a1175';
public $code = 'def502005b40d0c19541fddee79a6283f8bc30d5dc4fbc80828f8cc8c53590305c1a886ac501a4b9be4432d1f169f3817ddfa6fe31082c0ceeac8c05a99287716d909efd2e2307530599115efcbe12e9f79d7a8e972d137710e44a41f3cf8d6fdfda6443830e4552f046d2d7809dd009ea5fa96e29bf41ce0b980f90a00cacb492973e637111807c0abe22ffc5959bd58064d2899c919b99f37349af3f428f9ec5ad5746edbb5c7ca212431b46936b4938939674cd65333a24fa2a13515f57e0c4775cb631fcc6ce7c648443f60bbe980b97ff34510a2f296c34eed92bc7737f30dc8b798bdfe97d551b15276521a7a5eb3f39ec52ecf9203ddc3e33e965e209cd03f19b0a32aa8eb65e23973da557f02b08518f758371ce88e6b06b009ff597a7cd312694192beb954484b63aff5e3038f6ad1b610bbead24c183c095f9b47324675aba96f1fe605564fc13bc16222d6653740af8e1b8061a3b244c428ab53f5896ab56905f5b456d909b5b5d8c7a15749cc55d833a0e37a6f94e1fbe0e7db6a8612a7936ac054760c52f594e37268cdc59c364fb3128e0de1e0183c792350bc950d51e386187ce9056f93c650b04784dd253b6901a662b73cc8ef4ec8874b77614cad04ac9a400de16deb3e74452420ec4e9d2836aa9948dcbaedfa97044827c08331236f3531ae819b72267c5a324fd3315bdd103d00b';
public $token_file = 'tokens.txt';
public $redirect_url = 'https://s248934.h1n.ru/amoCRM/amo.php';
public $linkAMO;
public $data;
public $headers;
public $test;

public function curl($login) {
$curl = curl_init();
curl_setopt($curl,CURLOPT_RETURNTRANSFER, true);
curl_setopt($curl,CURLOPT_USERAGENT,'amoCRM-oAuth-client/1.0');
curl_setopt($curl,CURLOPT_URL, $login->linkAMO);
curl_setopt($curl,CURLOPT_HTTPHEADER, $login->headers);
curl_setopt($curl,CURLOPT_HEADER, false);
curl_setopt($curl,CURLOPT_CUSTOMREQUEST, 'POST');
curl_setopt($curl,CURLOPT_POSTFIELDS, json_encode($login->data));
curl_setopt($curl,CURLOPT_SSL_VERIFYPEER, 0);
curl_setopt($curl,CURLOPT_SSL_VERIFYHOST, 0);
$out = curl_exec($curl);
$code = curl_getinfo($curl, CURLINFO_HTTP_CODE);
curl_close($curl);
$code = (int)$code;

$errors = [
  301 => 'Moved permanently.',
  400 => 'Wrong structure of the array of transmitted data, or invalid identifiers of custom fields.',
  401 => 'Not Authorized. There is no account information on the server. You need to make a request to another server on the transmitted IP.',
  403 => 'The account is blocked, for repeatedly exceeding the number of requests per second.',
  404 => 'Not found.',
  500 => 'Internal server error.',
  502 => 'Bad gateway.',
  503 => 'Service unavailable.'
];

if ($code < 200 || $code > 204) die( "Error $code. " . (isset($errors[$code]) ? $errors[$code] : 'Undefined error') );


$response = json_decode($out, true);
if($response['access_token'] != null) {
    $arrParamsAmo = [
        "access_token"  => $response['access_token'],
        "refresh_token" => $response['refresh_token'],
        "token_type"    => $response['token_type'],
        "expires_in"    => $response['expires_in'],
        "endTokenTime"  => $response['expires_in'] + time(),
    ];

    $arrParamsAmo = json_encode($arrParamsAmo);

    $f = fopen($login->token_file, 'w');
    fwrite($f, $arrParamsAmo);
    fclose($f);
}
}

}
$login = new amoCRM();
$login->data = [];
$liveToken = file_get_contents($login->token_file);
$liveToken = json_decode($liveToken, true);

if (empty ($liveToken)) {
$login->headers=['Content-Type:application/json'];    
$login->linkAMO='https://denikder.amocrm.ru/oauth2/access_token';    
$login->data = [
  'client_id'     => $login->client_id,
  'client_secret' => $login->client_secret,
  'grant_type'    => 'authorization_code',
  'code'          => $login->code,
  'redirect_uri'  => $login->redirect_url,
];

$login->curl($login);

}else if (!empty ($liveToken) && $liveToken["endTokenTime"] - 60 < time()) {
$login->headers=['Content-Type:application/json'];     
$login->linkAMO='https://denikder.amocrm.ru/oauth2/access_token';      
$login->data = [
        'client_id'     => $login->client_id,
        'client_secret' => $login->client_secret,
        'grant_type'    => 'refresh_token',
        'refresh_token' => $liveToken["refresh_token"],
        'redirect_uri'  => $login->redirect_url,
    ];
$login->curl($login);    
}
$strdata = explode(',', $_POST['data']);


$name = $strdata[0];
$email = $strdata[1];
$phone = $strdata[2];
$company = 'Тестировщик';
$price = (int)$strdata[3];
$pipeline_id = 7408074;

$login->headers =  [
    'Content-Type: application/json',
    'Authorization: Bearer ' . $liveToken['access_token'],
];  
$login->linkAMO='https://denikder.amocrm.ru/api/v4/leads/complex';  
$login->data = [
    [
       "name" => $phone,
        "price" => $price,
        "pipeline_id" => (int) $pipeline_id,
        "_embedded" => [
            "metadata" => [
                "category" => "forms",
                "form_id" => 1,
                "form_name" => "Форма на сайте",
                "form_page" => '0',
                "form_sent_at" => strtotime(date("Y-m-d H:i:s")),
                "ip" => '1.2.3.4',
                "referer" => '0'
            ],
            "contacts" => [
                [
                    "first_name" => $name,
                    "custom_fields_values" => [
                        [
                            "field_code" => "EMAIL",
                            "values" => [
                                [
                                    "enum_code" => "WORK",
                                    "value" => $email
                                ]
                            ]
                        ],
                        [
                            "field_code" => "PHONE",
                            "values" => [
                                [
                                    "enum_code" => "WORK",
                                    "value" => $phone
                                ]
                            ]
                        ],
                    ]
                ]
            ],
            "companies" => [
                [
                    "name" => $company
                ]
            ]
        ],
        ]
];
$login->curl($login);
?>