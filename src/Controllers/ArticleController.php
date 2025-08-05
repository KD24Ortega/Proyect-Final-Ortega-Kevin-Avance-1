<?php
declare(strict_types=1);

namespace App\Controllers;
use App\Repositories\ArticleRepository;
use App\Repositories\AuthorRepository;
use App\Entities\Article;
use App\Entities\Author;

class ArticleController {
    private ArticleRepository $articleRepository;
    private AuthorRepository $authorRepository;

    public function __construct() {
        $this->articleRepository = new ArticleRepository();
        $this->authorRepository = new AuthorRepository();
    }

    public function handle(): void {
        header('Content-Type: application/json');
        $method = $_SERVER['REQUEST_METHOD'];

        if ($method === 'GET') {
            if (isset($_GET['id'])) {
                $article = $this->articleRepository->findById((int)$_GET['id']);
                echo json_encode($article ? $this->articleToArray($article) : null);
            } else {
                $list = array_map([$this, 'articleToArray'], $this->articleRepository->findAll());
                echo json_encode($list);
            }
            return;
        }

        $payload = json_decode(file_get_contents('php://input'), true);

        if ($method === 'POST') {
            $author = $this->authorRepository->findById((int)$payload['author_id'] ?? 0);
            if (!$author) {
                http_response_code(400);
                echo json_encode(['error' => 'Author not found']);
                return;
            }
            $article = new Article(
                null,
                $payload['title'],
                $payload['description'],
                new \DateTime($payload['publication_date'] ?? 'now'),
                $author,
                $payload['doi'],
                $payload['abstract'],
                $payload['keywords'],
                $payload['indexation'],
                $payload['magazine'],
                $payload['area']
            );

            echo json_encode(['success' => $this->articleRepository->create($article)]);
            return;
        }
    }


    public function articleToArray(Article $article): array {
        return [
            'id' => $article->getId(),
            'title' => $article->getTitle(),
            'description' => $article->getDescription(),
            'publication_date' => $article->getPublicationDate(),
            'author' => [
                'id' => $article->getAuthor()->getId(),
                'firstname' => $article->getAuthor()->getFirstName(),
                'lastname' => $article->getAuthor()->getLastName()
            ],
            'doi' => $article->getDoi(),
            'abstract' => $article->getAbstract(),
            'keywords' => $article->getKeywords(),
            'indexation' => $article->getIndexation(),
            'magazine' => $article->getMagazine(),
            'area' => $article->getArea()
        ];
    }
}