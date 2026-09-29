<?php
require_once __DIR__ . "/config.php";

$conn = mysqli_connect($db_host, $db_user, $db_pass, $db_name);

if (!$conn) {
    die("DB 연결 실패: " . mysqli_connect_error());
}

mysqli_set_charset($conn, "utf8mb4");

$sql = "SELECT board_id, board_name, description, article_count, created_at
        FROM board
        ORDER BY board_id";

$result = mysqli_query($conn, $sql);

if (!$result) {
    die("쿼리 실행 실패: " . mysqli_error($conn));
}
?>
<!DOCTYPE html>
<html lang="ko">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>게시판 목록</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <script defer src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</head>
<body class="bg-light">
    <div class="container py-5">
        <h1 class="mb-4">게시판 목록</h1>
        <div class="card shadow-sm">
            <div class="table-responsive">
                <table class="table table-striped table-hover align-middle mb-0">
                    <thead class="table-dark text-center">
                        <tr>
                            <th>번호</th>
                            <th>게시판명</th>
                            <th>설명</th>
                            <th>글 수</th>
                            <th>생성일</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php while ($row = mysqli_fetch_assoc($result)): ?>
                        <tr role="button" onclick="location.href='article_list.php?board_id=<?php echo urlencode($row["board_id"]); ?>'">
                            <td class="text-center"><?php echo htmlspecialchars($row["board_id"]); ?></td>
                            <td class="fw-semibold"><?php echo htmlspecialchars($row["board_name"]); ?></td>
                            <td class="text-muted"><?php echo htmlspecialchars($row["description"]); ?></td>
                            <td class="text-end"><span class="badge text-bg-primary"><?php echo htmlspecialchars($row["article_count"]); ?></span></td>
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
mysqli_free_result($result);
mysqli_close($conn);
?>
