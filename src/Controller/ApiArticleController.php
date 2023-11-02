<?php

namespace App\Controller;

use App\Repository\ArticleRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class ApiArticleController extends AbstractController
{
    private ArticleRepository $articleRepository;

    public function __construct(ArticleRepository $articleRepository){
        $this->articleRepository = $articleRepository;
    }

    #[Route('/api/article', name: 'app_api_article')]
    public function index(): Response
    {
        return $this->render('api_article/index.html.twig', [
            'controller_name' => 'ApiArticleController',
        ]);
    }

    #[Route('/api/article/all', name:'app_api_article_all')]
    public function getAllArticle(): Response{
        return $this->json(['test'=>'exemple'], 200, ['Content-Type'=>'application/json','Access-Control-Allow-Origin' =>'*'], ['groups'=>'articles']);
    }

    #[Route('/api/article/id/{id}', name:'app_api_article_api')]
        public function getArticleById(int $id): Response{
            $article = $this->articleRepository->find($id);

            if($article){
                return $this->json($article, 200, ['Content-Type'=>'application/json','Access-Control-Allow-Origin'=> '*'], ['groups'=> 'articles']);
            }
            else {
                return $this->json(['error : '=>'Aucun article'], 206, ['Content-Type'=>'application/json', 'Access-Control-Allow-Origin'=> '*']);
            }
        }
}
