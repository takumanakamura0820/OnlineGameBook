<?php

use Slim\Http\Request;
use Slim\Http\Response;
use Model\Dao\Page;
use Model\Dao\Story;
use Model\Dao\Selection;


// 編集ページ(Get Request)
$app->get('/story/{story_id}/{page_id}/edit', function (Request $request, Response $response, array $args) {


    //GETされた内容を取得します。
    $data = $request->getQueryParams();

    // $data = array_merge($data, [
    //     "user" => [
    //         "name" => $_SESSION['username'],
    //     ],
    //     "page_id" => $args["page_id"],
    // ]);

    $data = [
        "user" => [
            "name" => $_SESSION['username'],
        ],
        "page_id" => $args["page_id"],
    ];

    // Render index view
    return $this->view->render($response, 'story/edit.twig', $data);

});

// $app->get('/story', function (Request $request, Response $response) {
//
//     //GETされた内容を取得します。
//     $data = $request->getQueryParams();
//
//     // Render index view
//     return $this->view->render($response, 'story/edit.twig', $data);
//
// });


// 編集ページ(Post Request)
$app->post('/story/{story_id}/{page_id}/edit', function (Request $request, Response $response, array $args) {

    //POSTされた内容を取得します
    $data = $request->getParsedBody();
    $page = new Page($this->db);
    $selection = new Selection($this->db);

    $param = [
        "story_id" => $args["story_id"],
        "page_id" => $args["page_id"],
        "picture" => $data["picture"],
        "content" => $data["content"]
    ];

    if ($page->select([
        "story_id" => $args["story_id"],
        "page_id" => $args["page_id"],
    ]) === false ) {  # 新規作成
        $page->insert($param);
    } else {  # 編集
        $page->update($param);
    };

    $ids = $selection->select([
        "story_id" => $args["story_id"],
        "page_id" => $args["page_id"]
    ], $fetch_all = true);

    foreach ($ids as $id) {
        $selection->delete(intval($id));
    };

    $count = 1;
    while(true) {
        $key = "selection" . strval($count);
        if (isset($data[$key])) {
            $param = [
                "story_id" => $args["story_id"],
                "page_id" => $args["page_id"],
                "content" => $data[$key],
                "ahead" => intval($data[$key . "ahead"])
            ];

            $selection->insert($param);
            $count += 1;
        } else {
            break;
        };
    }

    // intval($args["story_id"])

    $story = new Story($this->db);
    $current_story = $story->select($param = [
        "id" => 1
    ]);

    $param = [
        "user" => [
            "name" => $_SESSION['username'],
        ],
        "page_id" => $id
    ];

    $story->update(["next_id" => $id + 1]);

    return $this->view->render($response, 'story/edit.twig', $param);

});
