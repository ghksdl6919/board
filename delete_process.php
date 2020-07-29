<script>
    // 메세지 출력 후 list로 이동
    function prtMsg(argMsg,argUrl) {
        alert(argMsg);
        location.href=argUrl;
    }
    // 해당 게시글로 복귀
    function goView() {
        let form = document.getElementById('form');
        form.submit();
    }
</script>
<?php
require_once('db_conf.php');
require_once('db_connect.php');
require_once('boardInfo.php');

function delete() {
    // post로 받은 board_id와 password 무결성 검사
    if (!isset($_POST['password']) || !isset($_POST['contentID'])) {
        echo "<script> prtMsg('시스템 에러 발생!','list.php') </script>";
    }

    switch ($_POST['mode']) {
        // 게시글 삭제 모드
        case 'deleteContent':
            // 삭제 하고자 하는 게시물의 비밀번호 찾기
            $sql = "select user_password from mybulletin where board_id={$_POST['contentID']};";

            $result = boardInfo::getQuery($sql, "NON_DML");

            // db의 비밀번호 저장
            $record = $result->fetch_array()[0];

            // 입력한 비밀번호와 db 비밀번호가 일치하는지 검사
            if (password_verify($_POST['password'], $record)) {
                // post로 받은 board_id에 해당하는 게시글 삭제 쿼리
                $deleteSql = "delete from mybulletin where board_id={$_POST[contentID]};";
                $countSql = "select count(*) from comment where board_num = {$_POST[contentID]};";

                $count = boardInfo::getQuery($countSql,"NON_DML");
                if($count > 0) {
                    $commentDeleteSql = "delete from comment where board_num={$_POST[contentID]};";
                    boardInfo::getQuery($commentDeleteSql,"DML");
                }

                if(boardInfo::DEBUGMODE)
                    echo "SQL : ".$deleteSql."<BR>";

                // 쿼리 전달
                boardInfo::getQuery($deleteSql, "DML");

                // 성공 메세지 출력 후 list.php 이동
                echo "<script> prtMsg('성공적으로 삭제 되었습니다.','list.php'); </script>";

            } else
                // 비밀번호 미일치 시 메세지 출력 후 list.php로 이동
                echo "<script> prtMsg('비밀번호가 틀렸습니다!','list.php'); </script>";

            break;

        // 댓글 삭제 모드
        case 'deleteComment':
            // 댓글의 비밀번호 db에서 가져오기
            $sql = "select user_password from comment where comment_id={$_POST['contentID']};";
            if(boardInfo::DEBUGMODE)
                echo "passwordSql : ".$sql."<BR>";

            // 쿼리 전송
            $result = boardInfo::getQuery($sql, "NON_DML");

            // 비밀번호 값 저장
            $record = $result->fetch_array()[0];

            // 해당 게시글 복귀를 위한 form 생성
            echo "<form id='form' action='view.php' method='get'>";
            echo "<input type='hidden' name='view_id' value='{$_POST['argId']}'>";
            echo "<input type='hidden' name='mode' value='notCount'>";
            echo "</form>";

            // 비밀번호 검사 - 일치 시 댓글 삭제
            if (password_verify($_POST['password'], $record)) {
                // 댓글 삭제 쿼리 저장
                $deleteSql = "delete from comment where comment_id={$_POST['contentID']};";
                if(boardInfo::DEBUGMODE)
                    echo "deleteSql : ".$deleteSql."<BR>";

                // 쿼리 전송
                boardInfo::getQuery($deleteSql, "DML");

                // 해당 게시글로 복귀
                echo "<script> goView(); </script>";
            } else
                // 비밀번호 오답일 경우 메세지 출력 후 해당 게시글로 복귀
                echo "<script> alert('비밀번호가 틀렸습니다.'); goView(); </script>";
            break;
    }
}

delete();
?>