<?php

namespace App\Controller;

use App\Entity\Article;
use App\Repository\ArticleRepository;
use App\Repository\UserRepository;
use App\Service\UtilsService;
use App\Service\JwtService;
use DateTime;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Serializer\SerializerInterface;

class ApiArticleController extends AbstractController
{
    private ArticleRepository $articleRepository;
    private $em;
    private SerializerInterface $serializer;

    public function __construct(ArticleRepository $articleRepository, EntityManagerInterface $em, SerializerInterface $serializer)
    {
        $this->articleRepository = $articleRepository;
        $this->em = $em;
        $this->serializer = $serializer;
    }

    #[Route('/api/article', name: 'app_api_article')]
    public function index(): Response
    {
        return $this->render('api_article/index.html.twig', [
            'controller_name' => 'ApiArticleController',
        ]);
    }

    #[Route('/api/article/all', name: 'app_api_article_all')]
    public function getAllArticle(): Response
    {
        return $this->json(['test' => 'exemple'], 200, ['Content-Type' => 'application/json', 'Access-Control-Allow-Origin' => '*'], ['groups' => 'articles']);
    }

    #[Route('/api/article/id/{id}', name: 'app_api_article_api')]
    public function getArticleById(int $id): Response
    {
        $id = UtilsService::cleanInput($id);
        $article = $this->articleRepository->find($id);

        if ($article) {
            return $this->json($article, 200, ['Content-Type' => 'application/json', 'Access-Control-Allow-Origin' => '*'], ['groups' => 'articles']);
        } else {
            return $this->json(['error : ' => 'Aucun article'], 206, ['Content-Type' => 'application/json', 'Access-Control-Allow-Origin' => '*']);
        }
    }

    #[Route('/api/article/new', name:'app_api_article_new', methods: ['POST'])]
    public function addArticle(Request $request, UserRepository $userRepository): Response
    {
        $content = $request->getContent();
        
        $content = $this->serializer->decode($content, 'json');

        $article = new Article();

        $article->setTitle(UtilsService::cleanInput($content['title']));
        $article->setContent(UtilsService::cleanInput($content['content']));
        $article->setDate(new \DateTimeImmutable(UtilsService::cleanInput($content['date'])));
        $article->setAuthor($userRepository->findOneBy(['email'=>UtilsService::cleanInput($content['author']['email'])]));

        $this->em->persist($article);
        $this->em->flush();

        return $this->json($request, 200, ['Content-Type'=> 'application/json','Access-Control-Allow-Origin'=>'*']);
    }

    #[Route('/api/article/testAuth', name:'app_api_article_test')]
    public function checkAuth(Request $request, JwtService $jwtService){
        $email = $request->get('email');
        $password = $request->get('password');

        $checkUser = $jwtService->checkUser($email, $password);

        dd($checkUser);
    }
}
