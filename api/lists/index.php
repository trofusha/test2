<?php

require_once filter_input(INPUT_SERVER, 'DOCUMENT_ROOT') . '/' . 'inc.php';

use app\Api;
use app\http\filesController;

$api = new Api();
$controller = new filesController;
switch ($api->method) {
    case 'GET':
        if(!$controller->getFilenames($BD)){
            $api->setResponseError('query filenames error');
            $api->sendResponse();
            die();
        }else{
            $api->setResponseData('filenames',$controller->filenames);
            $api->sendResponse();
        }
        break;
    default:
        $api->setResponseError('WRONG METHOD');
        $api->sendResponse();
        die();
}

