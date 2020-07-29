<script>
    // 게시글 보기 페이지로 이동 함수
    function goView() {
        let form = document.getElementById('form');
        form.submit();
    }
</script>
<?php
require_once('db_conf.php');
require_once('db_connect.php');
require_once('boardInfo.php');

// post로 전달 받은 값 체크
function checkPOST($argList) {
    foreach($argList as $key=>$value) {
        if($key == 'view_id')
            continue;

        // 댓글 입력 칸 공란 여부 검사 - 공란 있을 시 메세지 출력 후 해당 게시글로 다시 이동
        if($value == null || trim($value) == ""){
            echo "<form id='form' action='view.php' method='get'>";
            echo "<input type='hidden' name='view_id' value='".$argList['view_id']."'>";
            echo "</form>";
            echo "<script> alert('댓글 입력 칸 중에 공란이 있습니다!'); goView(); </script>";
            exit(-1);
        }

        // 전달받은 값들 html태그 수정
        $argList[$key] = htmlspecialchars($value,ENT_QUOTES);
        // 비밀번호 암호화
        $argList['password'] = password_hash($argList['password'],PASSWORD_DEFAULT);
    }
    return $argList;
}

function insertComment() {
    $commentInfo = checkPOST($_POST);

    // 댓글 삽입 쿼리문 저장
    $sql = "insert into comment values(0,{$commentInfo[view_id]},'{$commentInfo[writer]}','{$commentInfo[password]}','{$commentInfo[comment]}',now());";

    boardInfo::getQuery($sql, "DML");

    // DEBUG
    if (boardInfo::DEBUGMODE) {
        echo "댓글 번호 : {$commentInfo[view_id]} <BR>";
        echo "댓글 작성자 : {$commentInfo[writer]} <BR>";
        echo "댓글 비밀번호 : {$commentInfo[password]} <BR>";
        echo "댓글 내용 : {$commentInfo[comment]} <BR>";
        echo "sqlQuery : {$sql}";
    }

    // 다시 해당 게시글로 복귀
    echo "<form id='form' action='view.php' method='get'>";
    echo "<input type='hidden' name='view_id' value='" . $commentInfo['view_id'] . "'>";
    echo "<input type='hidden' name='mode' value='notCount'>";
    echo "</form>";
    echo "<script> goView(); </script>";
}

insertComment();
?>