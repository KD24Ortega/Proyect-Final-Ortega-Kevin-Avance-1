<?php

declare(strict_types=1);

namespace App\Repositories;

use App\Interfaces\RepositoryInterface;
use App\Config\Database;
use App\Entities\Author;
use PDO;

class AuthorRepository implements RepositoryInterface
{
    private PDO $db;

    public function __construct()
    {
        $this->db = Database::getConnection();
    }
    
    public function findAll(): array {
        $stmt = $this->db->query("SELECT * FROM author");//salida sql
        $list = [];
        while($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            $list[] = $this->hydrate($row); //row -> fila sql
        }
        return $list;
    }

    public function create(object $entity): bool {
        if (!$entity instanceof Author) {
            throw new \InvalidArgumentException("Expected instance of Author");
        }

        $sql = "INSERT INTO author 
                (first_name, last_name, username, email, password, orcid, afiliation) 
                VALUES (:first_name, :last_name, :username, :email, :password, :orcid, :afiliation)";
        $stmt = $this->db->prepare($sql);

        return $stmt->execute([
            "first_name"    => $entity->getFirstName(),
            "last_name"     => $entity->getLastName(),
            "username"      => $entity->getUsername(),
            "email"         => $entity->getEmail(),
            "password"      => $entity->getPassword(),
            "orcid"         => $entity->getOrcid(),
            "afiliation"    => $entity->getAfiliation()
        ]);
    }

    public function update(object $entity): bool {
        if (!$entity instanceof Author) {
            throw new \InvalidArgumentException("Expected instance of Author");
        }

        $sql = "UPDATE author SET 
                first_name = :first_name, 
                last_name = :last_name, 
                username = :username, 
                email = :email, 
                password = :password, 
                orcid = :orcid, 
                afiliation = :afiliation 
                WHERE id = :id";
        $stmt = $this->db->prepare($sql);

        return $stmt->execute([
            "id"           => $entity->getId(),
            "first_name"   => $entity->getFirstName(),
            "last_name"    => $entity->getLastName(),
            "username"     => $entity->getUsername(),
            "email"        => $entity->getEmail(),
            "password"     => $entity->getPassword(),
            "orcid"        => $entity->getOrcid(),
            "afiliation"   => $entity->getAfiliation()
        ]);
    }

    public function delete(int $id): bool {
        $sql = "DELETE FROM author WHERE id = :id";
        $stmt = $this->db->prepare($sql);
        return $stmt->execute(["id" => $id]);
    }

    //convierte filas sql a Author
    private function hydrate(array $row): Author {
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

        return $author;
    }

    public function findById(int $id): ?object {
        $sql = "SELECT * FROM author WHERE id = :id";
        $stmt = $this->db->prepare($sql);
        $stmt->execute(["id" => $id]);
        $row = $stmt->fetch(PDO::FETCH_ASSOC);

        return $row ? $this->hydrate($row) : null;
    }


}