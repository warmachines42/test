-- ============================================================
-- Board System
-- sudo mysql -u root -p mydb < 120_board_db_script/board_01_create.sql
-- ============================================================

USE mydb ;

DROP TABLE IF EXISTS article_hist;
DROP TABLE IF EXISTS article;
DROP TABLE IF EXISTS board;


-- ============================================================
-- board
-- ============================================================

CREATE TABLE board (
    board_id      INT AUTO_INCREMENT PRIMARY KEY,
    board_name    VARCHAR(100) NOT NULL,
    description   VARCHAR(255),
    article_count INT NOT NULL DEFAULT 0,
    created_at    DATETIME DEFAULT CURRENT_TIMESTAMP
);


-- ============================================================
-- article
-- ============================================================

CREATE TABLE article (
    article_id INT AUTO_INCREMENT PRIMARY KEY,
    board_id   INT NOT NULL,
    title      VARCHAR(200) NOT NULL,
    content    TEXT,
    author     VARCHAR(50),
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    updated_at DATETIME DEFAULT CURRENT_TIMESTAMP
                         ON UPDATE CURRENT_TIMESTAMP,

    CONSTRAINT fk_article_board
        FOREIGN KEY (board_id)
        REFERENCES board(board_id)
);


-- ============================================================
-- article_hist
-- ============================================================

CREATE TABLE article_hist (
    hist_id    INT AUTO_INCREMENT PRIMARY KEY,
    article_id INT NOT NULL,
    board_id   INT NOT NULL,
    title      VARCHAR(200) NOT NULL,
    content    TEXT,
    author     VARCHAR(50),
    created_at DATETIME,
    updated_at DATETIME,
    hist_type  CHAR(1) NOT NULL,
    hist_at    DATETIME DEFAULT CURRENT_TIMESTAMP
);