<?php

// 게시판 환경 설정
class boardInfo {
    const HTMLNAME_LIST = 'html/list.html';
    const HTMLNAME_VIEW = 'html/view.html';
    const HTMLNAME_MODIFY = 'html/modify.html';
    const HTMLNAME_DELETE = 'html/delete.html';

    // 페이지 당 게시글 수
    const pageContentNum = 8;

    // 페이지 블록 당 블록 수
    const pageNum = 3;
    const pagination = true;
    const DEBUGMODE = false;

    // 쿼리 전달 함수
    function getQuery($argSql, $mode) {
        $db_conn = db_connect();

        switch($mode) {
            case "DML" :
                if(!$db_conn->query($argSql)) {
                    echo "DML 쿼리 전송 실패!";
                    exit(-1);
                }
                break;
            case "NON_DML" :
                if (!($result = $db_conn->query($argSql))) {
                    echo "NON_DML 쿼리 전송 실패!";
                    exit(-1);
                }
                return $result;
                break;
        }
    }
}
?>