<?php
require_once __DIR__ . "/config.php";

$article_id = filter_input(INPUT_GET, "article_id", FILTER_VALIDATE_INT);

if (!$article_id) {
    header("Location: board.php");
    exit;
}

$conn = mysqli_connect($db_host, $db_user, $db_pass, $db_name);

if (!$conn) {
    die("DB 연결 실패: " . mysqli_connect_error());
}

mysqli_set_charset($conn, "utf8mb4");

// 게시글 + 게시판 정보
$stmt = mysqli_prepare($conn,
    "SELECT a.article_id, a.board_id, a.title, a.content, a.author,
            a.created_at, a.updated_at, b.board_name
     FROM article a
     JOIN board b ON b.board_id = a.board_id
     WHERE a.article_id = ?");
mysqli_stmt_bind_param($stmt, "i", $article_id);
mysqli_stmt_execute($stmt);
$article = mysqli_fetch_assoc(mysqli_stmt_get_result($stmt));
mysqli_stmt_close($stmt);
mysqli_close($conn);

if (!$article) {
    die("존재하지 않는 게시글입니다.");
}
?>
<!DOCTYPE html>
<html lang="ko">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title><?php echo htmlspecialchars($article["title"]); ?></title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <script defer src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</head>
<body class="bg-light">
    <div class="container py-5">
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="board.php">게시판 목록</a></li>
                <li class="breadcrumb-item"><a href="article_list.php?board_id=<?php echo urlencode($article["board_id"]); ?>"><?php echo htmlspecialchars($article["board_name"]); ?></a></li>
                <li class="breadcrumb-item active" aria-current="page"><?php echo htmlspecialchars($article["article_id"]); ?></li>
            </ol>
        </nav>
        <div class="card shadow-sm">
            <div class="card-header bg-white py-3">
                <h1 class="h3 mb-2"><?php echo htmlspecialchars($article["title"]); ?></h1>
                <div class="text-muted small">
                    <span class="me-3">작성자: <?php echo htmlspecialchars($article["author"]); ?></span>
                    <span class="me-3">작성일: <?php echo htmlspecialchars($article["created_at"]); ?></span>
                    <?php if ($article["updated_at"] !== $article["created_at"]): ?>
                    <span>수정일: <?php echo htmlspecialchars($article["updated_at"]); ?></span>
                    <?php endif; ?>
                </div>
            </div>
            <div class="card-body" style="min-height: 200px;">
                <?php echo nl2br(htmlspecialchars($article["content"] ?? "")); ?>
            </div>
            <div class="card-footer bg-white text-end">
                <a href="article_update.php?article_id=<?php echo urlencode($article["article_id"]); ?>" class="btn btn-primary">수정</a>
                <a href="article_list.php?board_id=<?php echo urlencode($article["board_id"]); ?>" class="btn btn-outline-secondary">목록</a>
                <form method="post" action="article_delete.php" class="d-inline"
                      onsubmit="return confirm('이 게시글을 삭제하시겠습니까?');">
                    <input type="hidden" name="article_id" value="<?php echo htmlspecialchars($article["article_id"]); ?>">
                    <button type="submit" class="btn btn-danger">삭제</button>
                </form>
            </div>
        </div>
    </div>
</body>
</html>
