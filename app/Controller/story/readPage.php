<?php

use Slim\Http\Request;
use Slim\Http\Response;
// use Model\Dao\User;


// 閲覧ページ
$app->get('/story/{story_id}/{page_id}/', function (Request $request, Response $response) {

    //GETされた内容を取得します。
    $data = $request->getQueryParams();

    // Render index view
    return $this->view->render($response, 'story/readPage.twig', $data);

});
