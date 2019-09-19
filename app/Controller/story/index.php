<?php

use Slim\Http\Request;
use Slim\Http\Response;
use Model\Dao\User;


// ストーリー一覧ページコントローラ
$app->get('/story/', function (Request $request, Response $response) {

    //GETされた内容を取得します。
    $data = $request->getQueryParams();

    // Render index view
    return $this->view->render($response, 'story/index.twig', $data);
});

// 一覧ページなのでPOSTアクセスの想定なし