<?php

function copyfrom($field, $value) {
    $field_data = $field . '_data';
    if (isset($_POST[$field_data])) {
        $value .= '|' . $_POST[$field_data];
    }
    return $value;
}

?>