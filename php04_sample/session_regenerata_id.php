<?php
// sessionをスタートしてidを再生成しよう．
// 旧idと新idを表示しよう．
session_start();
$old_session_id = session_id();
session_regenerate_id(true);
$new_session_id = session_id();
var_dump($old_session_id);
var_dump($new_session_id);
exit();
