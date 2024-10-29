<?php

include('vendor/autoload.php');

global $translator;

$GLOBALS['user_id'] = 4;
$GLOBALS['username'] = 'Theodore Admin';
$GLOBALS['profile_id'] = 4;
$GLOBALS['entity_id'] = 1;
$GLOBALS['entity_treepath'] = '00001';
$GLOBALS['entity_recursive'] = true;

$lang = new \App\Translation();
$translator = $lang->loadLanguage('en_GB');
