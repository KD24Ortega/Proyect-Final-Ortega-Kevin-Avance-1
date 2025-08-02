<?php
declare(strict_types=1);

// Carga el autoloader de Composer
require __DIR__ . '/vendor/autoload.php';

use App\Repositories\AuthorRepository;
use App\Repositories\BookRepository;
use App\Repositories\ArticleRepository;

$authorRepo = new AuthorRepository();
$authors   = $authorRepo->findAll();

echo "=== Autores ===\n";
foreach ($authors as $a) {
    printf("[%d] %s %s (Correo: \"%s\")\n", $a->getId(), $a->getFirstName(), $a->getLastName(), $a->getEmail());
}

$bookRepo = new BookRepository();
$books    = $bookRepo->findAll();

echo "\n=== Libros ===\n";
foreach ($books as $b) {
    printf("%s — \"%s\" - \"(Author: %s %s)\"\n", $b->getIsbn(), $b->getTitle(), $b->getAuthor()->getFirstName(), $b->getAuthor()->getLastName());
}


$articleRepo = new ArticleRepository();
$articles = $articleRepo->findAll();

echo "\n=== Artículos ===\n";
foreach ($articles as $a) {
    printf("%s — \"%s\" - \"%s\" - (Author: %s %s)\"\n", $a->getDoi(), $a->getAbstract(), $a->getTitle(), $a->getAuthor()->getFirstName(), $a->getAuthor()->getLastName());
}