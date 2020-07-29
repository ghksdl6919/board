<?php
require_once('boardInfo.php');
// view_id 무결성 검사
if(!isset($_POST['view_id']) || !is_numeric($_POST['view_id']) || $_POST['view_id'] < 0) {
    echo "삭제 하고자 하는 게시글을 찾을 수 없습니다.";
    exit(-1);
}
include(boardInfo::HTMLNAME_DELETE);
?>
