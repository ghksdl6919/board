<?php
function db_connect() {
    // db 접속
    $db_conn = new mysqli(db_info::DB_URL,db_info::USER_ID,db_info::PASSWD,db_info::DB_NAME);

    // db 접속 실패 시 에러 메세지 출력 및 프로그램 종료
    if($db_conn -> connect_errno) {
        echo "system error";
        exit(-1); // 프로그램 종료
    }

    return $db_conn;
}
?>