<?php

declare(strict_types=1);
namespace App\Entities;

abstract class Publication {
    protected int $id;
    protected string $title;
    protected string $description;
    protected \DateTime $publicationDate;
    protected Author $author;

    public function __construct(
        int $id,
        string $title,
        string $description,
        \DateTime $publicationDate,
        Author $author
    ) {
        $this->id = $id;
        $this->title = $title;
        $this->description = $description;
        $this->publicationDate = $publicationDate;
        $this->author = $author;
    }

    public function getId(): int {
        return $this->id;
    }

    public function getTitle(): string {
        return $this->title;
    }

    public function getDescription(): string {
        return $this->description;
    }

    public function getPublicationDate(): \DateTime {
        return $this->publicationDate;
    }

    public function getAuthor(): Author {
        return $this->author;
    }

    public function setTitle(string $title): void {
        $this->title = $title;
    }

    public function setDescription(string $description): void {
        $this->description = $description;
    }

    public function setPublicationDate(\DateTime $publicationDate): void {
        $this->publicationDate = $publicationDate;
    }

    public function setAuthor(Author $author): void {
        $this->author = $author;
    }

    public function setId(int $id): void {
        $this->id = $id;
    }

    

}