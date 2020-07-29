<!DOCTYPE html>
<html>

<head>
    <meta charset="UTF-8">
    <title>글쓰기</title>
    <link href="CSS/write.css" rel="stylesheet">
</head>

<body>
<form action="write_process.php" method="POST" autocomplete="off">
    <fieldset>
        <legend>글쓰기</legend>
        <label>제목</label><input id="title" type="text" name="title"><br>
        <label>작성자</label><input id="writer" type="text" name="writer"><br>
        <label>비밀번호</label><input id="password" type="text" name="password"><br>
        <textarea id="content" name="content"></textarea>
        <input type="submit" value="글쓰기" style="width: 600px;">
    </fieldset>
</form>
</body>

</html>

