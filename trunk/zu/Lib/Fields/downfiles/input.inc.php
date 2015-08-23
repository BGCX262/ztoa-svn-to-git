<?php

function downfiles($field, $value) {
    $files = $_POST[$field . '_fileurl'];
    $files_alt = $_POST[$field . '_filename'];
    if (defined("IN_ADMIN") && IN_ADMIN) {
        $groupid = $_POST[$field . '_groupid'];
        $point = $_POST[$field . '_point'];
    } else {
        $groupid = array();
        $point = array();
    }
    $array = $temp = array();
    if (!empty($files)) {
        foreach ($files as $key => $file) {
            $temp['fileurl'] = $file;
            $temp['filename'] = $files_alt[$key];
            $temp['groupid'] = $groupid[$key]?$groupid[$key]:0;
            $temp['point'] = $point[$key]?$point[$key]:0;
            $array[$key] = $temp;
        }
    }
    $array = serialize($array);
    return $array;
}

?>