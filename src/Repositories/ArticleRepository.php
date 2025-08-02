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

    public function create(object $entity): bool {}
    public function findById(int $id): ?object {}
    public function update(object $entity): bool {}
    public function delete(int $id): bool {}
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