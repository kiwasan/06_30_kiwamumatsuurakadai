<?php
session_start();
include("functions.php");
check_session_id();

if (
  !isset($_POST['todo']) || $_POST['todo'] == '' ||
  !isset($_POST['deadline']) || $_POST['deadline'] == ''
) {
  // 項目が入力されていない場合はここでエラーを出力し，以降の処理を中止する
  echo json_encode(["error_msg" => "no input"]);
  exit();
}

// 受け取ったデータを変数に入れる
$todo = $_POST['todo'];
$deadline = $_POST['deadline'];

// ここからファイルアップロード&DB登録の処理を追加しよう！！！
// ファイルが追加されていない or エラー発生の場合を分ける. // 送信されたファイルは$_FILES['...'];で受け取る!
// コード
if (isset($_FILES['upfile']) && $_FILES['upfile']['error'] == 0) {
  // 送信が正常に行われたときの処理 ...
  $uploadedFileName = $_FILES['upfile']['name'];  // アップロードしたファイル名を取得.
  $tempPathName = $_FILES['upfile']['tmp_name'];  // 一時保管しているtmpフォルダの場所の取得.
  $fileDirectoryPath = 'upload/';  // アップロード先のパスの設定(サンプルではuploadフォルダ←自分でで決めて作成!)
  $extension = pathinfo($uploadedFileName, PATHINFO_EXTENSION);  // ファイルの拡張子の種類を取得.
  $uniqueName = date('YmdHis') . md5(session_id()) . "." . $extension;  // ファイルごとにユニークな名前を作成.(最後に拡張子を追加)
  $fileNameToSave = $fileDirectoryPath . $uniqueName;  // ファイルの保存場所をファイル名に追加.

  if (is_uploaded_file($tempPathName)) {
    if (move_uploaded_file($tempPathName, $fileNameToSave)) {
      chmod($fileNameToSave, 0644);  // 権限の変更
      //$img = '<img src="' . $fileNameToSave . '" >';  // imgタグを設定←今回は画像を表示しない
    } else {
      exit('Error:アップロードできませんでした');  // 画像の保存に失敗
    }
  } else {
    exit('Error:画像ありません');  // tmpフォルダにデータがない
  }
} else {
  // 送られていない，エラーが発生，などの場合
  //exit('Error:画像が送信されていません');
}


//DB接続
$pdo = connect_to_db();

// 他のデータと一緒にDBへ登録!
// INSERT文にimageカラムを追加!
$sql = 'INSERT INTO todo_table(id, todo, deadline, image, created_at, updated_at) VALUES(NULL, :todo, :deadline, :image, sysdate(), sysdate())';

// SQL準備&実行
$stmt = $pdo->prepare($sql);
$stmt->bindValue(':todo', $todo, PDO::PARAM_STR);
$stmt->bindValue(':deadline', $deadline, PDO::PARAM_STR);
$stmt->bindValue(':image', $fileNameToSave, PDO::PARAM_STR);
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
