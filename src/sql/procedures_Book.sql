DELIMITER $$

-- Lista todos los libros
DROP PROCEDURE IF EXISTS sp_book_list$$

CREATE PROCEDURE sp_book_list()
BEGIN
    SELECT
        b.isbn,
        b.genre,
        b.edition,
        b.publication_id,
        p.description,
        p.publication_date,
        p.title,
        -- Ahora incluimos TODOS los campos del author que tu hydrate() necesita:
        a.id         AS id,
        a.first_name,
        a.last_name,
        a.username,
        a.email,
        a.password,
        a.orcid,
        a.afiliation

    FROM book       AS b
    JOIN publication AS p ON b.publication_id = p.id
    JOIN author      AS a ON p.author_id      = a.id

    ORDER BY p.publication_date DESC;
END$$

-- Busca un libro por publication_id
DROP PROCEDURE IF EXISTS sp_find_book$$
CREATE PROCEDURE sp_find_book(IN p_id INT)
BEGIN
    SELECT
        b.isbn,
        b.genre,
        b.edition,
        b.publication_id,
        p.description,
        p.publication_date,
        p.title,
        p.author_id,
        a.first_name,
        a.last_name
    FROM book AS b
    JOIN publication AS p ON b.publication_id = p.id
    JOIN author AS a      ON p.author_id       = a.id
    WHERE b.publication_id = p_id
    ORDER BY p.publication_date DESC;
END$$

-- Crea un libro (transaccional)
DROP PROCEDURE IF EXISTS sp_create_book$$
CREATE PROCEDURE sp_create_book(
    IN p_title VARCHAR(100),
    IN p_description VARCHAR(100),
    IN p_date DATE,
    IN p_author_id INT,
    IN p_isbn VARCHAR(20),
    IN p_genre VARCHAR(50),
    IN p_edition INT
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
        'book'
    );

    SET @new_pub_id = LAST_INSERT_ID();

    INSERT INTO book (
        publication_id,
        isbn,
        genre,
        edition
    ) VALUES (
        @new_pub_id,
        p_isbn,
        p_genre,
        p_edition
    );

    COMMIT;

    SELECT @new_pub_id AS pub_id;
END$$

-- Elimina un libro (y su publicación) de forma segura
DROP PROCEDURE IF EXISTS sp_delete_book$$
CREATE PROCEDURE sp_delete_book(IN p_id INT)
BEGIN
    DECLARE EXIT HANDLER FOR SQLEXCEPTION
    BEGIN
        ROLLBACK;
    END;

    START TRANSACTION;

    DELETE FROM book        WHERE publication_id = p_id;
    DELETE FROM publication WHERE id             = p_id;

    COMMIT;

    SELECT 1 AS OK;
END$$

DROP PROCEDURE IF EXISTS sp_update_book$$
CREATE PROCEDURE sp_update_book(
    IN p_id            INT,
    IN p_title         VARCHAR(100),
    IN p_description   VARCHAR(100),
    IN p_date          DATE,
    IN p_author_id     INT,
    IN p_isbn          VARCHAR(20),
    IN p_genre         VARCHAR(50),
    IN p_edition       INT
)
BEGIN
    DECLARE EXIT HANDLER FOR SQLEXCEPTION
    BEGIN
        ROLLBACK;
    END;

    START TRANSACTION;

    -- Actualiza la tabla publication
    UPDATE publication
    SET
        title            = p_title,
        description      = p_description,
        publication_date = p_date,
        author_id        = p_author_id
    WHERE id = p_id;

    -- Actualiza la tabla book
    UPDATE book
    SET
        isbn    = p_isbn,
        genre   = p_genre,
        edition = p_edition
    WHERE publication_id = p_id;

    COMMIT;

    SELECT 1 AS OK;
END$$

DELIMITER ;
