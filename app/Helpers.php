<?php

/**
 * generic functions
 *
 * @author Giovane Pessoa
 */

function viewsPath() {
    return BASE_PATH . DIRECTORY_SEPARATOR . 'app' . DIRECTORY_SEPARATOR . 'Views' . DIRECTORY_SEPARATOR;
}

function alert($type = 1, $message, $showTitle = true) {
    $style = null;

    switch ($type) {
        case 1:
            $style = 'success';
            break;
        case 2:
            $style = 'info';
            break;
        case 3:
            $style = 'warning';
            break;
        case 4:
            $style = 'danger';
            break;
        case 5:
            $style = 'primary';
            break;
        case 6:
            $style = 'secondary';
            break;
        case 7:
            $style = 'light';
            break;
        case 8:
            $style = 'dark';
            break;
    }

    if ($showTitle) {
        return "<div role=\"alert\" class=\"alert alert-{$style}\"><span><strong class=\"mr-2\">Atenção!</strong>{$message}</span></div>";
    } else {
        return "<div role=\"alert\" class=\"alert alert-{$style}\"><span>{$message}</span></div>";
    }
}

function getFirstName($name) {
    $pos = strpos($name, ' ');

    $firstName = substr($name, 0, $pos);
    $firstName = $firstName ? $firstName : $name;

    return $firstName;
}

function dateInterval($period = '-1M', $format = 'Y-m-d') {
    $date = new DateTime();

    if (strstr($period, '-')) {
        $period = str_replace('-', '', $period);
        $date = $date->sub(new DateInterval("P{$period}"));
    } else {
        $date = $date->add(new DateInterval("P{$period}"));
    }

    return $date->format($format);
}

function storage($item, $value) {
    $strStorage = null;

    $strStorage = $strStorage . '<script>';
    $strStorage = $strStorage . "localStorage.setItem(\"{$item}\",\"{$value}\");";
    $strStorage = $strStorage . '</script>';

    echo $strStorage;
}

function setBodyClass() {

    $lenght = strlen($_SERVER['REQUEST_URI']) - strlen(substr($_SERVER['REQUEST_URI'], 0, strripos($_SERVER['REQUEST_URI'], '/')));

    $url = substr($_SERVER['REQUEST_URI'], strripos($_SERVER['REQUEST_URI'], '/') + 1, $lenght);

    return 'body-' . $url;
}

function getURL() {
    $pos = strpos(substr($_SERVER['REQUEST_URI'], 1, strlen($_SERVER['REQUEST_URI'])), '/');

    $lenght = strlen($_SERVER['REQUEST_URI']);

    $url = substr($_SERVER['REQUEST_URI'], $pos + 2, $lenght);

    return $url;
}

function getValueArray($array, $key, $value, $search) {
    foreach ($array as $item) {
        if ($item[$key] == $value) {
            return $item[$search];
        }
    }
}