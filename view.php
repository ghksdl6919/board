<?php
require_once('db_conf.php');
require_once('db_connect.php');
require_once('boardInfo.php');

// 댓글 출력 함수
function prtComment($argId) {

    // board_pid 값이 해당 게시글의 board_id 값인 레코드 찾기
    $commentSql = "select * from comment where board_num={$argId};";
    if(boardInfo::DEBUGMODE)
        echo "commentSql : ".$commentSql."<BR>";

    // 쿼리 전송
    $commentResult = boardInfo::getQuery($commentSql, "NON_DML");

    // 댓글 출력
    while($comment = $commentResult->fetch_array()) {
        // 날짜 format
        $comment[reg_date] = date_format(date_create($comment['reg_date']), 'Y-m-d');

        // 댓글 템플릿 출력
        echo "<div style='border: 1px solid black;'>";
        echo "<p class='commentArea' style='margin-left: 10px; width: 400px;'>".$comment['user_name']."</p>";
        echo "<p class='commentArea' style='float: right;'>".$comment['reg_date']."</p>";
        echo "<p id='comment'>".$comment['contents']."</p>";
        echo "<form id='deleteBtn' action='delete.php' method='post'>";
        // 삭제할 댓글의 id 값 전송
        echo "<input type='hidden' name='view_id' value='".$comment['comment_id']."'>";
        // 해당 게시글 id 값 전송
        echo "<input type='hidden' name='argId' value='".$argId."'>";
        // 댓글 삭제 모드로 전달
        echo "<input type='hidden' value='deleteComment' name='deleteMode'>";
        echo "<input type='submit' value='삭제' style='margin: 0; width: 50px;'>";
        echo "</form></div>";
    }
}

// 댓글 입력, 삭제 시 조회수 증가 방지
function viewsCount($argId) {
    if (isset($_GET['mode']) && $_GET['mode'] == "notCount")
        return;
    // 조회수 증가
    else if (isset($_GET['mode']) && $_GET['mode'] == "count") {
        $hitSql = "update mybulletin set hits=hits+1 where board_id={$argId};";
        if(boardInfo::DEBUGMODE)
            echo "hitSql : ".$hitSql."<BR>";

        boardInfo::getQuery($hitSql, "DML");
    }
}

// 게시글 조회
function getView() {
    $viewId = $_GET['view_id'];
    viewsCount($viewId);

    // 게시글 아이디와 같은 레코드 선택
    $sql = "select * from mybulletin where board_id={$viewId};";
    if(boardInfo::DEBUGMODE)
        echo "viewSql : ".$sql."<BR>";

    $result = boardInfo::getQuery($sql, "NON_DML");

    $record = $result->fetch_array();

    // date 값 format
    $record[reg_date] = date_format(date_create($record['reg_date']), 'Y년 m월 d일 H시 i분 s초');

    return $record;
}

// 댓글 갯수 구하기
function commentCount($argId) {
    $commentCountSql = "select count(*) from comment where board_num={$argId};";
    if(boardInfo::DEBUGMODE)
        echo "commentCountSql : ".$commentCountSql."<BR>";

    $countResult = boardInfo::getQuery($commentCountSql,"NON_DML");
    $count = $countResult->fetch_array()[0];

    return $count;
}

$viewId = $_GET['view_id'];

$record = getView();
$count = commentCount($viewId);

// html 출력
include(boardInfo::HTMLNAME_VIEW);
?>
