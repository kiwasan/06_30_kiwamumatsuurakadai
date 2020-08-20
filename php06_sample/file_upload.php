<?php

// ファイルが追加されていない or エラー発生の場合を分ける. // 送信されたファイルは$_FILES['...'];で受け取る!
// コード
if (isset($_FILES['upfile']) && $_FILES['upfile']['error'] == 0) {
  // 送信が正常に行われたときの処理 ...
  // アップロードしたファイル名を取得.
  $uploadedFileName = $_FILES['upfile']['name'];
  // 一時保管しているtmpフォルダの場所の取得.
  $tempPathName = $_FILES['upfile']['tmp_name'];
  // アップロード先のパスの設定(サンプルではuploadフォルダ←自分でで決めて作成!)
  $fileDirectoryPath = 'upload/';
  // ファイルの拡張子の種類を取得.
  $extension = pathinfo($uploadedFileName, PATHINFO_EXTENSION);
  // ファイルごとにユニークな名前を作成.(最後に拡張子を追加)
  $uniqueName = date('YmdHis') . md5(session_id()) . "." . $extension;
  // ファイルの保存場所をファイル名に追加.
  $fileNameToSave = $fileDirectoryPath . $uniqueName;

  if (is_uploaded_file($tempPathName)) {
    if (move_uploaded_file($tempPathName, $fileNameToSave)) {
      chmod($fileNameToSave, 0644);  // 権限の変更
      $img = '<img src="' . $fileNameToSave . '" >';  // imgタグを設定  

    } else {
      // 送られていない，エラーが発生，などの場合
      exit('Error:画像が送信されていません');
    }
  } else {
    exit('Error:アップロードできませんでした');  // 画像の保存に失敗
  }
} else {
  exit('Error:画像ありません');  // tmpフォルダにデータがない
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>file_upload</title>
</head>

<body>
  <?= $img ?>
</body>

</html>