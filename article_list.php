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
$stmt = mysqli_prepare($conn, "SELECT board_name, description FROM board WHERE board_id = ?");
mysqli_stmt_bind_param($stmt, "i", $board_id);
mysqli_stmt_execute($stmt);
$board = mysqli_fetch_assoc(mysqli_stmt_get_result($stmt));
mysqli_stmt_close($stmt);

if (!$board) {
    die("존재하지 않는 게시판입니다.");
}

// 게시글 목록
$stmt = mysqli_prepare($conn,
    "SELECT article_id, title, author, created_at
     FROM article
     WHERE board_id = ?
     ORDER BY article_id DESC");
mysqli_stmt_bind_param($stmt, "i", $board_id);
mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);
?>
<!DOCTYPE html>
<html lang="ko">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title><?php echo htmlspecialchars($board["board_name"]); ?></title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <script defer src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</head>
<body class="bg-light">
    <div class="container py-5">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <div>
                <h1 class="mb-1"><?php echo htmlspecialchars($board["board_name"]); ?></h1>
                <p class="text-muted mb-0"><?php echo htmlspecialchars($board["description"]); ?></p>
            </div>
            <div>
                <a href="article_insert.php?board_id=<?php echo urlencode($board_id); ?>" class="btn btn-primary">글쓰기</a>
                <a href="board.php" class="btn btn-outline-secondary">게시판 목록</a>
            </div>
        </div>
        <div class="card shadow-sm">
            <div class="table-responsive">
                <table class="table table-striped table-hover align-middle mb-0">
                    <thead class="table-dark text-center">
                        <tr>
                            <th>번호</th>
                            <th>제목</th>
                            <th>작성자</th>
                            <th>작성일</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (mysqli_num_rows($result) === 0): ?>
                        <tr>
                            <td colspan="4" class="text-center text-muted py-4">등록된 게시글이 없습니다.</td>
                        </tr>
                        <?php endif; ?>
                        <?php while ($row = mysqli_fetch_assoc($result)): ?>
                        <tr role="button" onclick="location.href='article_view.php?article_id=<?php echo urlencode($row["article_id"]); ?>'">
                            <td class="text-center"><?php echo htmlspecialchars($row["article_id"]); ?></td>
                            <td class="fw-semibold"><?php echo htmlspecialchars($row["title"]); ?></td>
                            <td class="text-center"><?php echo htmlspecialchars($row["author"]); ?></td>
                            <td class="text-center"><?php echo htmlspecialchars($row["created_at"]); ?></td>
                        </tr>
                        <?php endwhile; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</body>
</html>
<?php
mysqli_stmt_close($stmt);
mysqli_close($conn);
?>
