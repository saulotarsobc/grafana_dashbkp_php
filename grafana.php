<?php

// credeciais de acesso ao grafana
$server = 'http://192.168.1.2:3000';
$token = 'hckdblxkcasdckfhdbfdcfh98ryherhdiufhasddcjkasddfndclnfwel==';

date_default_timezone_set('America/Santarem');
$dirname = "grafana_bkp_" . date('d_m_y');
mkdir($dirname, 0700);

function criarJson($nome, $dados)
{
    $dirname = $GLOBALS['dirname'];
    $filename = "{$dirname}/{$nome}.json";
    $fp = fopen($filename, 'w');
    fwrite($fp, $dados);
}

function getUris()
{
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, "{$GLOBALS['server']}/api/search");
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
    curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'GET');
    $headers = ["Authorization: Bearer {$GLOBALS['token']}"];
    curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
    $result = curl_exec($ch);
    if (curl_errno($ch)) {
        echo 'Error:' . curl_error($ch);
    }
    curl_close($ch);
    return json_decode($result);
}

function getDashByUri($uid)
{
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, "{$GLOBALS['server']}/api/dashboards/uid/{$uid}");
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
    curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'GET');
    $headers = ["Authorization: Bearer {$GLOBALS['token']}"];
    curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
    if (curl_errno($ch)) {
        echo 'Error:' . curl_error($ch);
    }
    curl_close($ch);
    $result = (array) json_decode(curl_exec($ch));
    return $result['dashboard'];
}

foreach (getUris() as $key => $value) {
    $value = (array) $value;
    $dash = (array) (getDashByUri($value['uid']));
    $dash_json = json_encode($dash, JSON_PRETTY_PRINT);
    criarJson($dash['title'], $dash_json);
}
