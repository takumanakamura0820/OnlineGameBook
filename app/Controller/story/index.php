<?php

use Slim\Http\Request;
use Slim\Http\Response;
use Model\Dao\User;
use Model\Dao\Story;


// ストーリー一覧ページコントローラ
$app->get('/story/', function (Request $request, Response $response) {

    //GETされた内容を取得します。
    $data = $request->getQueryParams();

	//DB通信用インスタンス作成
    $story = new Story($this->db);
	$user=new User($this->db);

	//ストーリー情報全権取得
	$stories=$story->select(array(),"","",null,true);

	$data["stories"]=array();
	foreach ($stories as $story){
		unset($story["created_at"]);
		unset($story["updated_at"]);
		unset($story["next_id"]);
		$story["author"]=$user->select(
			array("id"=>$story["user_id"]),"","",1,false
			)["name"];
		$story["is_mine"]=false;
		if(isset($_SESSION["user_info"]["id"])){
			$story["is_mine"]=($story["user_id"]==$_SESSION["user_info"]["id"]);
		}
		unset($story["user_id"]);
		// $data["stories"]=$story;
		array_push($data["stories"], $story);
	}
    // Render index view
    //
    return $this->view->render($response, 'story/index.twig', $data);
});

// 一覧ページなのでPOSTアクセスの想定なし
