<?php
require_once __DIR__ . "/config.php";

// 삭제는 POST 요청만 허용
if ($_SERVER["REQUEST_METHOD"] !== "POST") {
    header("Location: board.php");
    exit;
}

$article_id = filter_input(INPUT_POST, "article_id", FILTER_VALIDATE_INT);

if (!$article_id) {
    header("Location: board.php");
    exit;
}

$conn = mysqli_connect($db_host, $db_user, $db_pass, $db_name);

if (!$conn) {
    die("DB 연결 실패: " . mysqli_connect_error());
}

mysqli_set_charset($conn, "utf8mb4");

// 삭제 후 돌아갈 게시판
$stmt = mysqli_prepare($conn, "SELECT board_id FROM article WHERE article_id = ?");
mysqli_stmt_bind_param($stmt, "i", $article_id);
mysqli_stmt_execute($stmt);
$article = mysqli_fetch_assoc(mysqli_stmt_get_result($stmt));
mysqli_stmt_close($stmt);

if (!$article) {
    die("존재하지 않는 게시글입니다.");
}

// 게시글 삭제
$stmt = mysqli_prepare($conn, "DELETE FROM article WHERE article_id = ?");
mysqli_stmt_bind_param($stmt, "i", $article_id);

if (!mysqli_stmt_execute($stmt)) {
    die("삭제 실패: " . mysqli_stmt_error($stmt));
}

mysqli_stmt_close($stmt);
mysqli_close($conn);

header("Location: article_list.php?board_id=" . $article["board_id"]);
exit;
