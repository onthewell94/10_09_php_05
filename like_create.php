<?php
// 関数ファイルの読み込み
session_start();
include("functions.php");
check_session_id();

// GETデータ取得
$user_id = $_GET['user_id'];
$todo_id = $_GET['todo_id'];

// DB接続
$pdo = connect_to_db();

// SQL作成
$sql = 'INSERT INTO like_table(id, user_id, todo_id, created_at)
VALUES(NULL, :user_id, :todo_id, sysdate())';
$stmt = $pdo->prepare($sql);
$stmt->bindValue(':user_id', $user_id, PDO::PARAM_INT);
$stmt->bindValue(':todo_id', $todo_id, PDO::PARAM_INT);

// SQL実行
$status = $stmt->execute();

// エラー処理
if ($status == false) {
} else {
header('Location:todo_read.php');
// var_dump($like_count[0]); // データの件数を確認しよう！
// exit();
}

// いいね状態のチェック（COUNTで件数を取得できる！）
$sql = 'SELECT COUNT(*) FROM like_table
WHERE user_id=:user_id AND todo_id=:todo_id';
$stmt = $pdo->prepare($sql);
$stmt->bindValue(':user_id', $user_id, PDO::PARAM_INT);
$stmt->bindValue(':todo_id', $todo_id, PDO::PARAM_INT);
$status = $stmt->execute();
if ($status == false) {
// エラー処理
} else {
$like_count = $stmt->fetch();
// var_dump($like_count[0]); // データの件数を確認しよう！
// exit();
}

// いいねしていれば削除，していなければ追加のSQLを作成
if ($like_count[0] != 0) {
$sql = 'DELETE FROM like_table
WHERE user_id=:user_id AND todo_id=:todo_id';
} else {
$sql = 'INSERT INTO like_table(id, user_id, todo_id, created_at)
VALUES(NULL, :user_id, :todo_id, sysdate())';
}

// SQL準備&実行
$stmt = $pdo->prepare($sql);
$stmt->bindValue(':todo', $todo, PDO::PARAM_STR);
$stmt->bindValue(':deadline', $deadline, PDO::PARAM_STR);
$status = $stmt->execute();

// データ登録処理後
if ($status == false) {
  // SQL実行に失敗した場合はここでエラーを出力し，以降の処理を中止する
  $error = $stmt->errorInfo();
  echo json_encode(["error_msg" => "{$error[2]}"]);
  exit();
} else {
  // 正常にSQLが実行された場合は入力ページファイルに移動し，入力ページの処理を実行する
  header("Location:todo_input.php");
  exit();
}
