
DELIMITER $$
DROP PROCEDURE IF EXISTS sp_article_list$$

CREATE PROCEDURE sp_article_list()
BEGIN
    SELECT
        a.publication_id,
        p.title,
        p.description,
        p.publication_date,
        -- campos específicos de la tabla article
        a.doi,
        a.abstract,
        a.keywords,
        a.indexation,
        a.magazine,
        a.area,
        auth.id           AS author_id,
        auth.first_name,
        auth.last_name,
        auth.username,
        auth.email,
        auth.password,
        auth.orcid,
        auth.afiliation

    FROM article   AS a
    JOIN publication AS p ON a.publication_id = p.id
    JOIN author      AS auth ON p.author_id      = auth.id
    ORDER BY p.publication_date DESC;
END$$

DROP PROCEDURE IF EXISTS sp_create_article$$
CREATE PROCEDURE sp_create_article(
    IN p_title VARCHAR(100),
    IN p_description VARCHAR(100),
    IN p_date DATE,
    IN p_author_id INT,
    IN p_doi VARCHAR(50),
    IN p_abstract TEXT,
    IN p_keywords TEXT,
    IN p_indexation VARCHAR(50),
    IN p_magazine VARCHAR(100),
    IN p_area VARCHAR(100)
)
BEGIN
    DECLARE EXIT HANDLER FOR SQLEXCEPTION
    BEGIN
        ROLLBACK;
    END;
    START TRANSACTION;
    INSERT INTO publication (
        title,
        description,
        publication_date,
        author_id,
        type
    ) VALUES (
        p_title,
        p_description,
        p_date,
        p_author_id,
        'article'
    );
    SET @new_pub_id = LAST_INSERT_ID();

    INSERT INTO article (
        publication_id,
        doi,
        abstract,
        keywords,
        indexation,
        magazine,
        area
    ) VALUES (
        @new_pub_id,
        p_doi,
        p_abstract,
        p_keywords,
        p_indexation,
        p_magazine,
        p_area
    );
    COMMIT;

    SELECT @new_pub_id AS pub_id;
END$$

DROP PROCEDURE IF EXISTS sp_find_article$$
CREATE PROCEDURE sp_find_article(IN p_id INT)
BEGIN
    SELECT
        a.publication_id,
        p.title,
        p.description,
        p.publication_date,
        -- campos específicos de la tabla article
        a.doi,
        a.abstract,
        a.keywords,
        a.indexation,
        a.magazine,
        a.area,
        auth.id           AS author_id,
        auth.first_name,
        auth.last_name,
        auth.username,
        auth.email,
        auth.password,
        auth.orcid,
        auth.afiliation

    FROM article   AS a
    JOIN publication AS p ON a.publication_id = p.id
    JOIN author      AS auth ON p.author_id      = auth.id
    WHERE a.publication_id = p_id;
    ORDER BY p.publication_date DESC;
END$$

DROP PROCEDURE IF EXISTS sp_update_article$$
CREATE PROCEDURE sp_update_article(
    IN p_id INT,
    IN p_title VARCHAR(100),
    IN p_description VARCHAR(100),
    IN p_date DATE,
    IN p_author_id INT,
    IN p_doi VARCHAR(50),
    IN p_abstract TEXT,
    IN p_keywords TEXT,
    IN p_indexation VARCHAR(50),
    IN p_magazine VARCHAR(100),
    IN p_area VARCHAR(100)
)
BEGIN
    DECLARE EXIT HANDLER FOR SQLEXCEPTION
    BEGIN
        ROLLBACK;
    END;
    START TRANSACTION;

    UPDATE publication
    SET
        title = p_title,
        description = p_description,
        publication_date = p_date,
        author_id = p_author_id
    WHERE id = p_id;

    UPDATE article
    SET
        doi = p_doi,
        abstract = p_abstract,
        keywords = p_keywords,
        indexation = p_indexation,
        magazine = p_magazine,
        area = p_area
    WHERE publication_id = p_id;

    COMMIT;

    SELECT 1 AS OK;
END$$

DROP PROCEDURE IF EXISTS sp_delete_article$$
CREATE PROCEDURE sp_delete_article(IN p_id INT)
BEGIN
    DECLARE EXIT HANDLER FOR SQLEXCEPTION
    BEGIN
        ROLLBACK;
    END;

    START TRANSACTION;

    DELETE FROM article WHERE publication_id = p_id;
    DELETE FROM publication WHERE id = p_id;

    COMMIT;

    SELECT 1 AS OK;
END$$

DELIMITER ;
