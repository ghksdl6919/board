<script>
function prtResult (argStr, argUrl) {
alert(argStr);
location.href=argUrl;
}
</script>

<?php
// db 관련 파일
require_once("db_conf.php");
require_once("db_connect.php");
require_once("boardInfo.php");

// DEBUG
if(boardInfo::DEBUGMODE) {
    echo "작성자 : ".$_POST[writer]."<BR>";
    echo "글 제목 : ".$_POST[title]."<BR>";
    echo "비밀번호 : ".$_POST[password]."<BR>";
    echo "내용 : ".$_POST[content]."<BR>";
}

// post값 검사
function writeValidation($argPost) {
    // 공란 체크 후 html 태그 수정
    foreach ($argPost as $key => $value) {
        if (!isset($_POST[$key]) || trim($_POST[$key]) == "") {
            echo "<script> prtResult('입력 칸 중 공백이 있습니다!','list.php') </script>";
            exit(-1);
        }
        $argPost[$key] = htmlspecialchars($argPost[$key], ENT_QUOTES);
    }
    // 비밀번호 암호화
    $argPost['password'] = password_hash($argPost['password'], PASSWORD_DEFAULT);

    if(boardInfo::DEBUGMODE)
        echo "hash_password : ".$argPost[password]."<BR>";

    return $argPost;
}

// db에 게시글 저장
function getWriteQuery($argList) {

    $sql = "insert into mybulletin values(0,0,'{$argList[writer]}','{$argList[password]}','{$argList[title]}','{$argList[content]}',0,now());";

    if(boardInfo::DEBUGMODE)
        echo "sqlQuery : ".$sql."<BR>";

    boardInfo::getQuery($sql,"DML");

    // 전송 성공 시 알림 메세지 출력 후, list.php로 이동
    echo "<script> prtResult('게시글이 등록 되었습니다.','list.php') </script>";
}

// 무결성 검사 후 태그 제거와 비밀번호 암호화 - post값 중 공란이 있으면 메세지 출력 후 list이동
$write = writeValidation($_POST);

// 게시글 등록
getWriteQuery($write);
?>