<?php
require_once('db_conf.php');
require_once('db_connect.php');
require_once('boardInfo.php');

// post로 전달 받은 board_id 무결성 검사
function postValidation() {
    if (isset($_POST['view_id']) && is_numeric($_POST['view_id']) && $_POST['view_id'] >= 0)
        $viewId = $_POST['view_id'];
    else {
        echo "시스템 오류 입니다";
        exit(-1);
    }
    return $viewId;
}

function getModifyQuery() {

    $viewId = postValidation();

    // post로 받은 id가 db의 board_id와 같은 게시글 찾기
    $sql = "select * from mybulletin where board_id='" . $viewId . "';";

    // 쿼리 전달
    $result = boardInfo::getQuery($sql,"NON_DML");

    // 레코드 저장
    $record = $result->fetch_array();

    return $record;
}

$record = getModifyQuery();
include(boardInfo::HTMLNAME_MODIFY);
?>


