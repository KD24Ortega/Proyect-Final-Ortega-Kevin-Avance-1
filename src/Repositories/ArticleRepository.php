<?php

declare(strict_types=1);

namespace App\Repositories;

use App\Entities\Author;
use App\Entities\Article;
use App\Config\Database;
use App\Interfaces\RepositoryInterface;
use PDO;

class ArticleRepository implements RepositoryInterface {
    private PDO $db;
    private AuthorRepository $authorRepo;

    public function __construct() {
        $this->db = Database::getConnection();
        $this->authorRepo = new AuthorRepository();
    }

    public function create(object $entity): bool {
        if (!$entity instanceof Article) {
            throw new \InvalidArgumentException("Expected instance of Article");
        }

        $stmt = $this->db->prepare("CALL sp_create_article(
            :title, 
            :description, 
            :publication_date, 
            :author_id, 
            :doi, 
            :abstract, 
            :keywords, 
            :indexation, 
            :magazine, 
            :area
        )");
        
        $ok = $stmt->execute([
            "title" => $entity->getTitle(),
            "description" => $entity->getDescription(),
            "publication_date" => $entity->getPublicationDate(),
            "author_id" => $entity->getAuthor()->getId(),
            "doi" => $entity->getDoi(),
            "abstract" => $entity->getAbstract(),
            "keywords" => $entity->getKeywords(),
            "indexation" => $entity->getIndexation(),
            "magazine" => $entity->getMagazine(),
            "area" => $entity->getArea()
        ]);

        if ($ok) {
            $stmt->fetch(PDO::FETCH_ASSOC);
        }
        $stmt->closeCursor();
        return $ok;
    }
    public function findById(int $id): ?object {
        $stmt = $this->db->prepare("CALL sp_find_article(:id)");
        $stmt->execute(["id" => $id]);
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        $stmt->closeCursor();

        return $row ? $this->hydrate($row) : null;
    }
    public function update(object $entity): bool {
        if (!$entity instanceof Article) {
            throw new \InvalidArgumentException("Expected instance of Article");
        }

        $stmt = $this->db->prepare("CALL sp_update_article (
            :id,
            :title, 
            :description, 
            :publication_date, 
            :author_id, 
            :doi, 
            :abstract, 
            :keywords, 
            :indexation, 
            :magazine, 
            :area
        )");

        $ok = $stmt->execute([
            "id" => $entity->getId(),
            "title" => $entity->getTitle(),
            "description" => $entity->getDescription(),
            "publication_date" => $entity->getPublicationDate(),
            "author_id" => $entity->getAuthor()->getId(),
            "doi" => $entity->getDoi(),
            "abstract" => $entity->getAbstract(),
            "keywords" => $entity->getKeywords(),
            "indexation" => $entity->getIndexation(),
            "magazine" => $entity->getMagazine(),
            "area" => $entity->getArea()
        ]);

        if ($ok) {
            $stmt->fetch(PDO::FETCH_ASSOC);
        }
        $stmt->closeCursor();
        return $ok;
    }
    public function delete(int $id): bool {
        $stmt = $this->db->prepare("CALL sp_delete_article(:id)");
        $ok = $stmt->execute(["id" => $id]);
        $stmt->closeCursor();
        return $ok;
    }
    public function findAll(): array {
        $stmt = $this->db->query("CALL sp_article_list();");
        $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);
        $stmt->closeCursor();

        $out = [];

        foreach ($rows as $row) {
            $out[] = $this->hydrate($row);
        }
        return $out;
    }
    private function hydrate(array $row): Article {
        $articleId = (int)$row['publication_id'];
        $authorId = (int)$row['author_id'];
        $author = $this->authorRepo->findById($authorId);
         if (!$author) {
            throw new \RuntimeException("Author with ID {$row['author_id']} not found");
        }


        return new Article(
            $articleId,
            $row['title'],
            $row['description'],
            new \DateTime($row['publication_date']),
            $author,
            $row['doi'],
            $row['abstract'],
            $row['keywords'],
            $row['indexation'],
            $row['magazine'],
            $row['area']
        );

    }
}
    /*
    private function hydrate(array $row): Article {
        $author = new Author(
            (int)$row ['publication_id'],
            $row['first_name'],
            $row['last_name'],
            $row['username'],
            $row['email'],
            'temporal',
            $row['orcid'],
            $row['afiliation']
        );

        //Reemplazar hash sin regenerar
        $ref = new \ReflectionClass($author);
        $prop=$ref->getProperty('password');
        $prop->setAccessible(true);
        $prop->setValue($author, $row['password']);

        return new Article(
            (int)$row['publication_id'],
            $row['title'],
            $row['description'],
            new \DateTime($row['publication_date']),
            $author,
            $row['doi'],
            $row['abstract'],
            $row['keywords'],
            $row['indexation'],
            $row['magazine'],
            $row['area']
        );
    }
}
*/