<script>
    // 메세지 출력 후 list로 이동
    function prtMsg(argStr) {
        alert(argStr);
        location.href="list.php";
    }
</script>

<?php
require_once('db_conf.php');
require_once('db_connect.php');
require_once('boardInfo.php');

// post로 받은 값들 htmlspecialChar로 치환
function htmlCharSetting($argList) {
    foreach($argList as $key=>$value) {
        $argList[$key] = htmlspecialchars($value,ENT_QUOTES);
    }
    return $argList;
}

// db에 등록된 게시글의 비밀번호 가져오기
function getDBPassword() {

    $userInfo = $_POST['ID'];

    // board_id에 해당하는 게시글의 비밀번호 찾기
    $sql = "select user_password from mybulletin where board_id='{$userInfo}';";
    if(boardInfo::DEBUGMODE)
        echo $sql."<BR>";

    // 쿼리 전송
    $result = boardInfo::getQuery($sql, "NON_DML");

    return $result;
}

// 비밀번호 확인
function passwordCheck() {
    $result = getDBPassword();
    $userInfo = htmlCharSetting($_POST);

    // 비밀 번호가 공란일 경우 메세지 출력 후 list.php로 이동
    if (trim($_POST['password']) == "" || $_POST['password'] == null)
        echo "<script> prtMsg('비밀번호가 공란 입니다!'); </script>";


    // db에서 가져온 비밀번호 저장
    $db_passWd = $result->fetch_array()[0];

    // 입력한 비밀번호와 db 비밀번호가 일치하는지 검사
    if (password_verify($userInfo['password'], $db_passWd)) {
        // 일치 시 게시글 수정하는 쿼리문 저장
        $ModiSql = "update mybulletin set title='{$userInfo['title']}', user_name='{$userInfo['writer']}', contents='{$userInfo['content']}' where board_id='{$userInfo['ID']}';";

        if(boardInfo::DEBUGMODE)
            echo $ModiSql."<BR>";

        // 쿼리 전달
        boardInfo::getQuery($ModiSql, "DML");

        // 메세지 출력 후 list.php로 이동
        echo "<script> prtMsg('성공적으로 수정 되었습니다.'); </script>";

    } else
        echo "<script> prtMsg('비밀번호가 일치하지 않습니다.'); </script>";
}

passwordCheck();

?>


