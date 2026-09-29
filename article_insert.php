<?php
require_once __DIR__ . "/config.php";

$board_id = filter_input(INPUT_GET, "board_id", FILTER_VALIDATE_INT);

if (!$board_id) {
    header("Location: board.php");
    exit;
}

$conn = mysqli_connect($db_host, $db_user, $db_pass, $db_name);

if (!$conn) {
    die("DB 연결 실패: " . mysqli_connect_error());
}

mysqli_set_charset($conn, "utf8mb4");

// 게시판 정보
$stmt = mysqli_prepare($conn, "SELECT board_name FROM board WHERE board_id = ?");
mysqli_stmt_bind_param($stmt, "i", $board_id);
mysqli_stmt_execute($stmt);
$board = mysqli_fetch_assoc(mysqli_stmt_get_result($stmt));
mysqli_stmt_close($stmt);

if (!$board) {
    die("존재하지 않는 게시판입니다.");
}

$title   = "";
$content = "";
$author  = "";
$error   = "";

// 게시글 등록
if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $title   = trim($_POST["title"] ?? "");
    $content = trim($_POST["content"] ?? "");
    $author  = trim($_POST["author"] ?? "");

    if ($title === "") {
        $error = "제목을 입력하세요.";
    } else {
        $stmt = mysqli_prepare($conn,
            "INSERT INTO article (board_id, title, content, author)
             VALUES (?, ?, ?, ?)");
        mysqli_stmt_bind_param($stmt, "isss", $board_id, $title, $content, $author);

        if (!mysqli_stmt_execute($stmt)) {
            die("등록 실패: " . mysqli_stmt_error($stmt));
        }

        $article_id = mysqli_insert_id($conn);
        mysqli_stmt_close($stmt);
        mysqli_close($conn);

        header("Location: article_view.php?article_id=" . $article_id);
        exit;
    }
}

mysqli_close($conn);
?>
<!DOCTYPE html>
<html lang="ko">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>글쓰기 - <?php echo htmlspecialchars($board["board_name"]); ?></title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <script defer src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</head>
<body class="bg-light">
    <div class="container py-5">
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="board.php">게시판 목록</a></li>
                <li class="breadcrumb-item"><a href="article_list.php?board_id=<?php echo urlencode($board_id); ?>"><?php echo htmlspecialchars($board["board_name"]); ?></a></li>
                <li class="breadcrumb-item active" aria-current="page">글쓰기</li>
            </ol>
        </nav>
        <?php if ($error !== ""): ?>
        <div class="alert alert-danger"><?php echo htmlspecialchars($error); ?></div>
        <?php endif; ?>
        <form method="post" class="card shadow-sm">
            <div class="card-body">
                <div class="mb-3">
                    <label for="title" class="form-label">제목</label>
                    <input type="text" id="title" name="title" class="form-control" maxlength="200" required
                           value="<?php echo htmlspecialchars($title); ?>">
                </div>
                <div class="mb-3">
                    <label for="author" class="form-label">작성자</label>
                    <input type="text" id="author" name="author" class="form-control" maxlength="50"
                           value="<?php echo htmlspecialchars($author); ?>">
                </div>
                <div>
                    <label for="content" class="form-label">내용</label>
                    <textarea id="content" name="content" class="form-control" rows="10"><?php echo htmlspecialchars($content); ?></textarea>
                </div>
            </div>
            <div class="card-footer bg-white text-end">
                <button type="submit" class="btn btn-primary">등록</button>
                <a href="article_list.php?board_id=<?php echo urlencode($board_id); ?>" class="btn btn-outline-secondary">취소</a>
            </div>
        </form>
    </div>
</body>
</html>
