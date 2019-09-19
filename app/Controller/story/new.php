<?php

use Slim\Http\Request;
use Slim\Http\Response;
use Model\Dao\User;


// ストーリー新規作成ページコントローラ
$app->get('/story/new/', function (Request $request, Response $response) {

    //GETされた内容を取得します。
    $data = $request->getQueryParams();

    // Render index view
    return $this->view->render($response, 'story/new.twig', $data);

});

// 新規タイトル作成コントローラ
// とりあえず会員登録処理コントローラのコピーのまま放置
$app->post('/story/new/', function (Request $request, Response $response) {

    //POSTされた内容を取得します
    $data = $request->getParsedBody();

    //ユーザーDAOをインスタンス化
    $user = new User($this->db);

    //入力されたメールアドレスの会員が登録済みかどうかをチェックします
    if ($user->select(array("email" => $data["email"]), "", "", 1, false)) {

        //入力項目がマッチしない場合エラーを出す
        $data["error"] = "このメールアドレスは既に会員登録済みです";

        // 入力フォームを再度表示します
        return $this->view->render($response, 'register/register.twig', $data);
    }

    //DB登録に必要ない情報は削除します
    unset($data["password_re"]);

    //DBに登録をする。戻り値は自動発番されたIDが返ってきます
    $id = $user->insert($data);

    // 登録完了ページを表示します。
    return $this->view->render($response, 'story/index.twig', $data);

});
