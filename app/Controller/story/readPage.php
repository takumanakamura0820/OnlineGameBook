<?php

use Slim\Http\Request;
use Slim\Http\Response;
// use Model\Dao\User;


// 閲覧ページ
$app->get('/story/{story_id}/{page_id}/', function (Request $request, Response $response,array $args) {

    //GETされた内容を取得します。今のところ使う予定なし。
    $data = $request->getQueryParams();

    // page_id = 0 -> Topページへ
    if ( $args["page_id"] === "-1" ) {
        return $response->withRedirect('/');
    }


	$user=new Model\Dao\User($this->db);
	$story=new Model\Dao\Story($this->db);
	$like=new Model\Dao\Fav($this->db);
	$page=new Model\Dao\Page($this->db);
	$page=$page->select(array("story_id"=>$args["story_id"],"page_id"=>$args["page_id"]),"","",1,false);
	$selection=new Model\Dao\Selection($this->db);
	$selection=$selection->select(array("story_id"=>$args["story_id"],"page_id"=>$args["page_id"]),"","",null,true);



	$story=$story->select(array("id"=>$args["story_id"]),"","",1,false);

	$data["story"]["story_id"]=(int)($story["id"]);


	$data["story"]["title"]=$story["title"];
	$data["story"]["page_title"]=$page["title"];
	$data["story"]["theme"][]=$story["theme1"];
	$data["story"]["theme"][]=$story["theme2"];

	$data["story"]["editor"]=$user->select(array("id"=>$story["user_id"]),"","",1,false)["name"];
    // dd($data["story"]);

	// $data["story"]=array_merge($data["story"],$page); // Error原因
	$data["story"] = $data["story"] + $page;

	foreach($selection as $v){
		$data["story"]["selection"][$v["content"]]="/story/".$args["story_id"]."/".$v["ahead"]."/";
	}
	$data["story"]["like"]=count($like->select(array("story_id"=>$args["story_id"]),"","",null,true));



	if(!empty($this->session->get('user_info'))){
		//ログインされている場合
		$data["user"]["name"]=$this->session->user_info["name"];
		$data["story"]["user_liked"]=count($like->select(array("story_id"=>$args["story_id"],"user_id"=>$this->session->get('user_info')["id"]),"","",null,true))>=1;
	} else {
		//ログインされていない場合
		$data["user"]["name"]=null;
		$data["story"]["user_liked"]=false;
	}
    // Render index view
    return $this->view->render($response, 'story/readPage.twig', $data);

});
