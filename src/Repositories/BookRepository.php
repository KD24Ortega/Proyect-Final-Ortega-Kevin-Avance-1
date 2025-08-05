<?php

declare(strict_types=1);

namespace App\Repositories;

use App\Entities\Author;
use App\Entities\Book;
use App\Config\Database;
use App\Interfaces\RepositoryInterface;
use PDO;


class BookRepository implements RepositoryInterface{
    private PDO $db;

    private AuthorRepository $authorRepo;

    public function create(object $entity): bool {
        if (!$entity instanceof Book) {
            throw new \InvalidArgumentException("Expected instance of Book");
        }

        $stmt = $this->db->prepare("CALL sp_create_book(
        
            :title, 
            :description, 
            :publication_date, 
            :author_id, 
            :isbn, 
            :genre, 
            :edition
        )" );
        $ok = $stmt->execute(
            [
                "title" => $entity->getTitle(),
                "description" => $entity->getDescription(),
                "publication_date" => $entity->getPublicationDate()->format('Y-m-d'),
                "author_id" => $entity->getAuthor()->getId(),
                "isbn" => $entity->getIsbn(),
                "genre" => $entity->getGenre(),
                "edition" => $entity->getEdition()
            ]
        );

        if ($ok) {
            $stmt->fetch(PDO::FETCH_ASSOC);
        }
        $stmt->closeCursor();
        return $ok;
    }
    public function findById(int $id): ?object {
        $stmt = $this->db->prepare("CALL sp_find_book(:id)");
        $stmt->execute(["id" => $id]);
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        $stmt->closeCursor();

        return $row ? $this->hydrate($row) : null;
    }

    public function update(object $entity): bool {
        if (!$entity instanceof Book) {
            throw new \InvalidArgumentException("Expected instance of Book");
        }
        $stmt = $this->db->prepare("CALL sp_update_book (
            :id,
            :title, 
            :description, 
            :publication_date, 
            :author_id, 
            :isbn, 
            :genre, 
            :edition
        )" );
        $ok = $stmt->execute(
            [
                "id" => $entity->getId(),
                "title" => $entity->getTitle(),
                "description" => $entity->getDescription(),
                "publication_date" => $entity->getPublicationDate()->format('Y-m-d'),
                "author_id" => $entity->getAuthor()->getId(),
                "isbn" => $entity->getIsbn(),
                "genre" => $entity->getGenre(),
                "edition" => $entity->getEdition()
            ]
        );

        if ($ok) {
            $stmt->fetch(PDO::FETCH_ASSOC);
        }
        $stmt->closeCursor();
        return $ok;

    }
    public function delete(int $id): bool {
        $stmt = $this->db->prepare("CALL sp_delete_book(:id)");
        $ok = $stmt->execute(["id" => $id]);
        $stmt->closeCursor();
        return $ok;
    }
    public function __construct() {
        $this->db = Database::getConnection();
        $this->authorRepo = new AuthorRepository();
    }

    public function findAll(): array {
        $stmt = $this->db->query("CALL sp_book_list();");
        $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);
        $stmt->closeCursor();

        $out = [];

        foreach ($rows as $row) {
            $out[] = $this->hydrate($row);
        }
        return $out;
    }

    private function hydrate(array $row): Book
    {
        // 1) Coge sólo el book + publication
        $bookId   = (int)$row['publication_id'];
        $authorId = (int)$row['author_id'];

        // 2) Pide al repo de autores el objeto completo
        $author = $this->authorRepo->findById($authorId);
        if (!$author) {
            throw new \RuntimeException("Autor con ID $authorId no encontrado");
        }

        // 3) Crea el Book con un Author ya válido
        return new Book(
            $bookId,
            $row['title'],
            $row['description'],
            new \DateTime($row['publication_date']),
            $author,
            $row['isbn'],
            $row['genre'],
            (int)$row['edition']
        );
    }

}
/*
    private function hydrate(array $row): Book {
        $author = new Author(
            (int)$row ['id'],
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

        return new Book(
            (int)$row['publication_id'],
            $row['title'],
            $row['description'],
            new \DateTime($row['publication_date']),
            $author,
            $row['isbn'],
            $row['genre'],
            (int)$row['edition']
        );
    }
}
    */