
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

DROP PROCEDURE IF EXISTS sp_create_article()

DELIMITER ;
