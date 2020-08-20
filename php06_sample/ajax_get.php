<?php
include("functions.php");

// var_dump($_GET);
// exit();

// ...DB接続の処理を記述
$search_word = $_GET["serchword"]; // GETのデータ受け取り

$pdo = connect_to_db();

$sql = "SELECT * FROM todo_table WHERE todo LIKE '%{$search_word}%'";

$stmt = $pdo->prepare($sql);
$status = $stmt->execute();


if ($status == false) {
  // SQL実行に失敗した場合はここでエラーを出力し，以降の処理を中止する
  $error = $stmt->errorInfo();
  echo json_encode(["error_msg" => "{$error[2]}"]);
  exit();
} else {
  // 正常にSQLが実行された場合は入力ページファイルに移動し，入力ページの処理を実行する
  // fetchAll()関数でSQLで取得したレコードを配列で取得できる
  $result = $stmt->fetchAll(PDO::FETCH_ASSOC);
  echo json_encode($result); // JSON形式にして出力
  exit();
}
