<?php
require_once('db_conf.php');
require_once('db_connect.php');
require_once('boardInfo.php');

if(boardInfo::pagination)
    // 페이지 정보를 pagingInfo에 저장
    $pagingInfo = getPageValue();

// 페이지 정보를 얻는 함수
function getPageValue() {
    if(boardInfo::pagination) {
        $currentPageNum = 0; // 현 페이지 번호
        $totalPageNum = 0; // 총 페이지 개수
        $pagingStartNum = 0; // 현 페이지 기준, 화면 출력 시작 글 번호 계산
        $pagingEndNum = 0; // 현 페이지 기준, 화면 출력 종료 글 번호 계산
        $totalRowNum = 0; // db 테이블 내 저장된 총 게시글 수

        // 현재 페이지 번호를 post로 받아 저장
        if (isset($_POST['page_num']) && is_numeric($_POST['page_num']))
            $currentPageNum = $_POST['page_num'];
        else
            // 처음 시작 시 0부터 시작
            $currentPageNum = 0;

        $sql = "select count(*) from mybulletin ";

        if(isset($_POST['keyword']) && isset($_POST['keyword_text'])) {
            $sql .= " where ";
            $sql = searchContent($sql);
        }

        if(boardInfo::DEBUGMODE)
            echo "sql : ".$sql."<BR>";

        // 쿼리 전송
        $result = boardInfo::getQuery($sql, "NON_DML");

        // 총 게시글 개수
        $totalRowNum = $result->fetch_array()[0];

        // 총 페이지 개수 = 총 게시글 개수 / 페이지 당 게시글 수
        $totalPageNum = ceil($totalRowNum / boardInfo::pageContentNum);

        // 현재 페이지 번호가 0보다 작거나 총 페이지 수보다 크거나 같으면 0으로 설정
        if ($currentPageNum < 0 || $currentPageNum >= $totalPageNum)
            $currentPageNum = 0;

        // 페이지 시작 글 번호 = 현재 페이지 번호 X 페이지 당 게시글 수
        $pagingStartNum = $currentPageNum * boardInfo::pageContentNum;

        // 페이지 끝 글 번호 = 페이지 당 게시글 수
        $pagingEndNum = boardInfo::pageContentNum;

        if(boardInfo::DEBUGMODE) {
            echo "currentPageNum : ".$currentPageNum."<BR>";
            echo "pagingStartNum : ".$pagingStartNum."<BR>";
            echo "pagingEndNum : ".$pagingEndNum."<BR>";
            echo "totalRowNum : ".$totalRowNum."<BR>";
            echo "totalPageNum : ".$totalPageNum."<BR>";
        }

        // 배열로 페이지 정보 반환
        return [
            "currentPageNum" => $currentPageNum,
            "pagingStartNum" => $pagingStartNum,
            "pagingEndNum" => $pagingEndNum,
            "totalRowNum" => $totalRowNum,
            "totalPageNum" => $totalPageNum
        ];
    }

    return null;
}

// 검색 시 활성화 되는 글 목록 페이지 이동 버튼 구현
function goListPage() {
    if(isset($_POST['keyword']) && isset($_POST['keyword_text']))
        echo "<button onclick=\"location.href='list.php'\">글목록</button>";
    else
        return;
}

// 검색 결과가 페이지별로 나뉠 때 post 검색 값을 유지하기 위한 함수
function searchBlockMove() {
    if(isset($_POST['keyword']) && isset($_POST['keyword_text'])) {
        echo "<input type='hidden' value='".$_POST['keyword']."' name='keyword'>";
        echo "<input type='hidden' value='".$_POST['keyword_text']."' name='keyword_text'>";
    }
    else
        return;
}

// 검색 옵션에 따라 쿼리문 설정
function searchContent($argSql) {
    switch($_POST['keyword']) {
        // 제목
        case 'title':
            $argSql .= " title";
            break;
        // 내용
        case 'content':
            $argSql .= " contents";
            break;
        // 작성자
        case 'user_name':
            $argSql .= " user_name";
            break;
        // 제목 + 내용
        case 'title_content':
            $argSql .= " title ";
            break;
    }
    $argSql .=  " like '%{$_POST['keyword_text']}%' ";

    if($_POST['keyword'] == 'title_content')
        $argSql .=  " or contents like '%{$_POST['keyword_text']}%' ";

    return $argSql;
}

// 게시글 출력 함수
function prtList($argPagingInfo) {
    if($argPagingInfo['totalRowNum'] == 0) {
        echo "검색 결과가 없습니다.";
        return;
    }

    // 댓글을 제외한 게시글 목록 역순으로 출력
    $sql = "select * from mybulletin order by board_id desc";

    // 검색 버튼 클릭 시 활성화
    if(isset($_POST['keyword']) && isset($_POST['keyword_text'])) {
        // 검색 단어가 공란일 경우 메세지 출력 후 list로 이동
        if(trim($_POST['keyword_text']) == "" && $_POST['keyword_text'] == null) {
            echo "<script> alert('검색할 단어를 입력해주세요!');";
            echo "location.href='list.php';</script>";
        }

        // 쿼리문 재설정
        $sql = "select * from mybulletin where ";

        $sql = searchContent($sql);

        $sql .=" order by board_id desc";
    }

    if (boardInfo::pagination)
        // 시작 게시글 번호부터 페이지 당 출력할 게시글 수만큼 출력
        $sql .= " limit {$argPagingInfo['pagingStartNum']},{$argPagingInfo['pagingEndNum']};";

    if(boardInfo::DEBUGMODE)
        echo "sql : ".$sql."<BR>";

    // 쿼리 전송
    $result = boardInfo::getQuery($sql,"NON_DML");

    // 게시글 출력
    while ($record = $result->fetch_array()) {
        echo "<tr>";
        // 제목 클릭 시 해당 게시글 조회 창으로 이동
        echo "<td>{$record[board_id]}</td>";
        echo "<td><a class='aTag' href='view.php?view_id={$record[board_id]}&mode=count'>{$record[title]}</a></td>";
        echo "<td>{$record[user_name]}</td>";
        echo "<td>{$record[hits]}</td>";
        // 날짜 년-월-일로 format
        $record[reg_date] = date_format(date_create($record['reg_date']), 'Y-m-d');
        echo "<td>{$record[reg_date]}</td>";
        echo "</tr>";
    }
}

// 페이지네이션 함수
function prtPagination($argPagingInfo) {
    // 게시글이 없을 때
    if($argPagingInfo['totalRowNum'] == 0)
        return;

    // 현 페이지 번호
    $currentPageNum = $argPagingInfo['currentPageNum'];
    // 총 페이지 갯수
    $totalPageNum = $argPagingInfo['totalPageNum'];

    // 페이지 계산용 변수
    $num = floor($currentPageNum/boardInfo::pageNum);

    echo "<div id='page_block' align='center'>";
    // 페이지 건너뛰기 구현
    // 현 페이지가 페이지 당 블록 갯수보다 클 경우 활성화
    if($currentPageNum >= boardInfo::pageNum) {
        // 점프 페이지 = ((현 페이지 번호 / 페이지 당 블록 갯수) - 1) X 페이지 당 블록 갯수
        $jumpPageNum = ($num - 1) * boardInfo::pageNum;
        // 건너 뛰기 버튼 생성
        echo "<form action='".$_SERVER[PHP_SELF]."' method='post' style='width: 50px; display: inline'>";
        echo "<input type='submit' value='<<'>";
        echo "<input type='hidden' value='$jumpPageNum' name='page_num'>";
        // 검색 페이지 블록 이동 기능
        searchBlockMove();
        echo "</form>";
        echo "&nbsp&nbsp";
    } else
        echo "<input type='submit' value='<<'>";

    // 페이지 번호 구현
    // 시작 페이지 = (현재 페이지 번호 / 페이지 당 블록 갯수) X 페이지 당 블록 갯수
    $startPageNum = $num * boardInfo::pageNum;
    // 끝 페이지 = 시작 페이지 + 페이지 당 블록 갯수가 총 페이지 갯수보다 작으면 시작 페이지 + 페이지 당 블록 갯수 저장
    $endPageNum = $startPageNum + boardInfo::pageNum < $totalPageNum ? $startPageNum + boardInfo::pageNum : $totalPageNum;

    if(boardInfo::DEBUGMODE) {
        echo "startPageNum : ".$startPageNum."<BR>";
        echo "endPageNum : ".$endPageNum."<BR>";
    }

    // 페이지 번호 출력
    for($i = $startPageNum; $i < $endPageNum; $i++,$startPageNum++) {
        echo "<form action='".$_SERVER[PHP_SELF]."' method='post' style='display: inline'>";
        // 현재 페이지 번호에 빨간 색 설정
        echo "<input type='submit' value='".($i+1)."'".($i == $currentPageNum ? " style='background-color: red'>" : null.">");
        // post로 page_num 값 전달
        echo "<input type='hidden' value='$i' name='page_num'>";
        // 검색 페이지 블록 이동 기능
        searchBlockMove();
        echo "</form>";
    }

    // 페이지 건너뛰기 구현
    // 현 페이지 기준 설정된 페이지 만큼 앞으로 이동할 페이지가 있을 경우 활성화
    // (현 페이지 번호 / 페이지 당 블록 갯수)가 ((전체 페이지 수 - 1) / 페이지 당 블록 갯수) 보다 작을 경우
    if($num < floor(($totalPageNum - 1)/boardInfo::pageNum)) {
        // 점프 페이지 = 현 페이지 번호 / 페이지 당 블록 갯수 + 1) X 페이지 당 블록 갯수
        $jumpPageNum = ($num + 1) * boardInfo::pageNum;
        // 건너 뛰기 버튼 생성
        echo "<form action='".$_SERVER[PHP_SELF]."' method='post' style='width: 50px; display: inline'>";
        echo "<input type='submit' value='>>'>";
        echo "<input type='hidden' value='$jumpPageNum' name='page_num'>";
        // 검색 페이지 블록 이동 기능
        searchBlockMove();
        echo "</form>";
    } else
        echo "<input type='submit' value='>>'>";

    echo "</div>";
}

// html 출력
include(boardInfo::HTMLNAME_LIST);
?>
