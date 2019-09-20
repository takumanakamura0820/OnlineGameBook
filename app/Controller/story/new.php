<?php

use Slim\Http\Request;
use Slim\Http\Response;
use Model\Dao\Story;


// ストーリー新規作成ページコントローラ
$app->get('/story/new/', function (Request $request, Response $response) {

    //GETされた内容を取得します。
    // $data = $request->getQueryParams();

    $data = [
        "user" => [
            "name" => $_SESSION['username'],
        ],
    ];

    // Render index view
    return $this->view->render($response, 'story/new.twig', $data);

});

// 新規タイトル作成コントローラ
// とりあえず会員登録処理コントローラのコピーのまま放置
$app->post('/story/new/', function (Request $request, Response $response) {

    //POSTされた内容を取得します
    $data = $request->getParsedBody();
    $story = new Story($this->db);

    if (isset($_SESSION['user_info'])) {
        // ログイン時 => ストーリを新規作成
        $param = [
            "title" => $data["title"],
            "user_id" => (int)$_SESSION["user_info"]["id"],
            "theme1" => $data["theme1"],
            "theme2" => $data["theme2"],
            "picture" => $data["picture"],
            "discription" => $data["discription"],
            "next_id" => 1
        ];

        // 新規ストーリー作成
        $id = $story->insert($param);

        return $response->withRedirect('/story/' . $id . '/1/edit');

    } else {
        // ログインしてください的な処理
        return $response->withRedirect('/login/');
    };

});
