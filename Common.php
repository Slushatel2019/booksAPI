<?php
function response($input)
{
    #error_reporting(0);
    if (is_array($input[0])) {
        foreach ($input as $array) {
            $resp = ['data' => $array['data'], 'message' => $array['message'], 'status' => $array['status']];
            echo json_encode($resp);
        }
        exit;
    }
    $resp = ['data' => $input['data'], 'message' => $input['message'], 'status' => $input['status']];
    echo json_encode($resp);
    exit;
}
function get($value, $default = null)
{
    return isset($value) ? $value : $default;
}
