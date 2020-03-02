<?php

require_once filter_input(INPUT_SERVER, 'DOCUMENT_ROOT') . '/' . 'inc.php';

use app\Api;
use app\http\filesController;

//пределяем метод запроса
$api = new Api();
$controller = new filesController;
switch ($api->method) {
    case 'GET':
        //отдаем один или несколько файлов

        if (!$controller->checkFilenamesRequest()) {
            $api->setResponseError('no filenames in request');
            $api->sendResponse();
            die;
        } else if (!$controller->checkFilenames($BD)) {

            $api->setResponseError('no such file(s) on server');
            $api->sendResponse();
            die;
        } else {
            foreach ($controller->filenames as $filename) {
                $controller->filename = $controller->storge_path . $filename;
                $file_array[] = $controller->XML2JSONFile();
            }
            $api->setResponseFiles($file_array);
            $api->sendResponse();
        }

        break;
    case 'POST':
        //парсим файл и если нужно сохраняем его
        if (!$controller->checkFileRequest($_FILES)) {
            $api->setResponseError('no file in request');
            $api->sendResponse();
            die();
        } else if (!$controller->checkFileStructure($_FILES)) {
            $api->setResponseError('file structure fail');
            $api->sendResponse();
            die();
        } else if (!$controller->saveFile($_FILES)) {
            $api->setResponseError('file save error');
            $api->sendResponse();
            die();
        } else {
            //читаем файл в json и отдаем безусловно файл уже проверен и на структуру и на паттерн добавляем файл в БД
            $controller->insertFilename($BD);
            $file_array = $controller->XML2JSONFile();
            $api->setResponseFiles([$file_array]);
//            $api->setResponseData('filename',$controller->filename1);
            $api->sendResponse();
        }

        break;

    default:
        $api->setResponseError('WRONG METHOD');
        $api->sendResponse();
        die();
        break;
}



