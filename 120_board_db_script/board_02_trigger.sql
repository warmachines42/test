-- ============================================================
-- Board System
-- sudo  mysql -u root -p mydb < 120_board_db_script/board_02_trigger.sql
-- ============================================================

USE mydb ;

DROP TRIGGER IF EXISTS article_after_insert;
DROP TRIGGER IF EXISTS article_after_update;
DROP TRIGGER IF EXISTS article_after_delete;

-- ============================================================
-- Trigger
-- ============================================================

DELIMITER //


-- ============================================================
-- INSERT
-- ============================================================

CREATE TRIGGER article_after_insert
AFTER INSERT ON article
FOR EACH ROW
BEGIN

    -- 변경 이력 저장
    INSERT INTO article_hist (
        article_id,
        board_id,
        title,
        content,
        author,
        created_at,
        updated_at,
        hist_type
    )
    VALUES (
        NEW.article_id,
        NEW.board_id,
        NEW.title,
        NEW.content,
        NEW.author,
        NEW.created_at,
        NEW.updated_at,
        'I'
    );

    -- 게시글 수 증가
    UPDATE board
    SET article_count = article_count + 1
    WHERE board_id = NEW.board_id;

END//


-- ============================================================
-- UPDATE
-- ============================================================

CREATE TRIGGER article_after_update
AFTER UPDATE ON article
FOR EACH ROW
BEGIN

    -- 변경 이력 저장
    INSERT INTO article_hist (
        article_id,
        board_id,
        title,
        content,
        author,
        created_at,
        updated_at,
        hist_type
    )
    VALUES (
        NEW.article_id,
        NEW.board_id,
        NEW.title,
        NEW.content,
        NEW.author,
        NEW.created_at,
        NEW.updated_at,
        'U'
    );

    -- 게시판이 변경된 경우
    IF OLD.board_id <> NEW.board_id THEN

        -- 기존 게시판 게시글 수 감소
        UPDATE board
        SET article_count = article_count - 1
        WHERE board_id = OLD.board_id;

        -- 새로운 게시판 게시글 수 증가
        UPDATE board
        SET article_count = article_count + 1
        WHERE board_id = NEW.board_id;

    END IF;

END//


-- ============================================================
-- DELETE
-- ============================================================

CREATE TRIGGER article_after_delete
AFTER DELETE ON article
FOR EACH ROW
BEGIN

    -- 삭제 이력 저장
    INSERT INTO article_hist (
        article_id,
        board_id,
        title,
        content,
        author,
        created_at,
        updated_at,
        hist_type
    )
    VALUES (
        OLD.article_id,
        OLD.board_id,
        OLD.title,
        OLD.content,
        OLD.author,
        OLD.created_at,
        OLD.updated_at,
        'D'
    );

    -- 게시글 수 감소
    UPDATE board
    SET article_count = article_count - 1
    WHERE board_id = OLD.board_id;

END//


DELIMITER ;
