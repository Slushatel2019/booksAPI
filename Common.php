<?php
function response($input)
{
    #error_reporting(0);
    $resp = ['data' => $input['data'], 'message' => $input['message'], 'status' => $input['status']];
    echo json_encode($resp);
    exit;
}
function get($value, $default = null)
{
    return isset($value) ? $value : $default;
}

