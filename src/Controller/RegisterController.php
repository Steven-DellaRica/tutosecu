<?php

namespace App\Controller;

use App\Entity\User;
use App\Form\RegisterType;
use App\Service\UtilsService;
use App\Service\MessagerieService;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Annotation\Route;

class RegisterController extends AbstractController
{
    private UserRepository $userRepository;
    private $em;
    private MessagerieService $messagerie;
    public function __construct(UserRepository $userRepository, EntityManagerInterface $em, MessagerieService $messagerie)
    {
        $this->userRepository = $userRepository;
        $this->em = $em;
        $this->messagerie = $messagerie;
    }

    #[Route('/register', name: 'app_register')]
    public function addUser(Request $request, UserPasswordHasherInterface $userPasswordHasher): Response
    {
        $msg = 'Hello';
        $user = new User();
        $form = ($this->createForm(RegisterType::class, $user));
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            if ($this->userRepository->findOneBy(['email' => $user->getEmail()])) {
                $msg = "L'utilisateur existe déjà.";
            } else {
                $pass = UtilsService::cleanInput($request->request->all('register')['password']['first']);
                $hash = $userPasswordHasher->hashPassword($user, $pass);
                $user->setPassword($hash);
                $user->setRoles(['ROLE_USER']);
                $user->setActivated(false);

                $user->setName(UtilsService::cleanInput($request->request->all('register')['name']));
                $user->setFirstname(UtilsService::cleanInput($request->request->all('register')['firstname']));
                $user->setEmail(UtilsService::cleanInput($request->request->all('register')['email']));

                $this->em->persist($user);
                $this->em->flush();

                $object = "Valider votre compte";
                $content = "<h1>Félicitations, votre inscription a été réussie.Pour activer le compte cliquer sur le lien ci-dessous :</h1>
                <a href='https://localhost:8000/register/activate/".$user->getId()."'>Activer</a>";

                $this->messagerie->sendMail($object, $content, $user->getEmail());

                
                
                $msg = 'Le compte a été ajouté en BDD';
            }
        }
        return $this->render('register/index.html.twig', [
            'form' => $form->createView(),
            'msg' => $msg,
        ]);
    }

    #[Route('/register/activate/{id}', name: 'app_activate')]
    public function activateUser(int $id ){
        $cleanId = UtilsService::cleanInput($id);
        $user = $this->userRepository->find($cleanId);
        if ($user){
            $user->setActivated(true);
            $this->em->persist($user);
            $this->em->flush();
            return $this->redirectToRoute('app_login');
        } else {
            return $this->redirectToRoute('app_register');
        }
    }

    #[Route('/register/update/{id}', name:'app_update')]
    public function updateUser(int $id, Request $request, UserPasswordHasherInterface $userPasswordHasher){
        $cleanId = UtilsService::cleanInput($id);
        $user = $this->userRepository->find($cleanId);
        $msg = '';
        $form = $this->createForm(RegisterType::class, $user);
        $form->handleRequest($request);
        if($user){
            if($form->isSubmitted() && $form->isValid()){
                $pass = UtilsService::cleanInput($request->request->all('register')['password']['first']);
                $hash = $userPasswordHasher->hashPassword($user, $pass);
                $user->setPassword($hash);

                $user->setName(UtilsService::cleanInput($request->request->all('register')['name']));
                $user->setFirstname(UtilsService::cleanInput($request->request->all('register')['firstname']));
                $user->setEmail(UtilsService::cleanInput($request->request->all('register')['email']));

                $this->em->flush();

                $msg = 'Le compte à été mis à jour';
            }
        }else{

            $msg = 'Le compte n\'existe pas';
        }
        return $this->render('register/index.html.twig', [
            'form' => $form->createView(),
            'msg' => $msg,
        ]);
    }
}
