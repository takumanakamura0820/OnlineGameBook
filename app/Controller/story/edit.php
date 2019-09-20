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

    $data = [
        "user" => [
            "name" => $_SESSION['username'],
        ],
        "page_id" => $args["page_id"],
    ];

    // Render index view
    return $this->view->render($response, 'story/edit.twig', $data);

});

// 新規ページ作成
$app->get('/story/{story_id}/new', function (Request $request, Response $response, array $args) {

	//現在のDBの状況を取得
    $story = new Story($this->db);
	$currentStory=$story->select(
		array("id"=>$args["story_id"]),"","",1,false
	);
    return $response->withRedirect('/story/' . $args["story_id"] . '/' . $currentStory["next_id"] . '/edit');
});


// 編集ページ(Post Request)
$app->post('/story/{story_id}/{page_id}/edit', function (Request $request, Response $response, array $args) {


    //POSTされた内容を取得します
    $data = $request->getParsedBody();

	//DB操作に用いるインスタンスの取得
    $page = new Page($this->db);
    $selection = new Selection($this->db);
    $story = new Story($this->db);

	//現在のDBの状況を取得
	$currentStory=$story->select(
		array("id"=>$args["story_id"]),"","",1,false
	);
    $current_page = $page->select([
        "story_id" => $args["story_id"],
        "page_id" => $args["page_id"],
    ]);

	//ページテーブル用のデータ作成
    $param = [
        "story_id" => $args["story_id"],
        "page_id" => $args["page_id"],
        "picture" => $data["picture"],
        "content" => $data["content"]
    ];

	//ページの作成・更新
    if ($current_page === false) {	# 新規作成
        $page->insert($param);
    } else {	#更新
        $page->update($param, ["story_id", "page_id"]);
    }

	//既存のselectionのdelete
    $delete_selections = $selection->select([
        "story_id" => $args["story_id"],
        "page_id" => $args["page_id"]
    ], "", "", 5, true);
    foreach ($delete_selections as $select) {
        $selection->delete(intval($select["id"]));
    };

	//selectionのinsert
    $count = 1;
    while(true) {
        $key = "selection" . strval($count);
        if (!empty($data[$key])) {
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

	//必要な場合はDB上のnext_idを編集
	if (isset($currentStory["next_id"])){
		if((int)$args["page_id"]===(int)$currentStory["next_id"]){
			$story->update(array(
					"id"=>$args["story_id"],
					"next_id"=>($args["page_id"]+1)
				)
			);
		}
	};

    if (isset($data["exit"])) {
        return $response->withRedirect('/');
    } else {
        return $response->withRedirect('/story/' . $args["story_id"] . '/' . ($args["page_id"]+1) . '/edit');
    };
});
