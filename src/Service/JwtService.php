<?php

namespace App\Service;

use App\Repository\UserRepository;
use App\Service\UtilsService;
use Firebase\JWT\JWT;
use Firebase\JWT\Key;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class JwtService
{
    private string $token;
    private UserRepository $userRepo;
    private UserPasswordHasherInterface $hash;

    public function __construct(string $token, UserRepository $userRepo, UserPasswordHasherInterface $hash)
    {
        $this->userRepo = $userRepo;
        $this->hash = $hash;
        $this->token = $token;
    }

    public function checkUser(string $email, string $password)
    {
        $email = UtilsService::cleanInput($email);
        $password = UtilsService::cleanInput($password);

        $user = $this->userRepo->findOneBy(['email' => $email]);

        if ($user) {
            if ($this->hash->isPasswordValid($user, $password)) {
                return true;
            } else {
                return false;
            }
        } else {
            return false;
        }
    }
    public function genToken($email, $secretKey, $repo){
        //construction du JWT
        require_once('../vendor/autoload.php');
        //variables pour le token
        $issuedAt = new \DateTimeImmutable();
        $expire = $issuedAt->modify('+60minutes')->getTimestamp();
        $serverName = "your.domain.name";
        $userName = $this->userRepo->findOneBy(["email"=> $email])->getName();
        $userFirstName = $this->userRepo->findOneBy(["email"=> $email])->getFirstname();
        $id = $this->userRepo->findOneBy(["email"=> $email])->getId();
        //Contenu du token
        $data = [
            'iat' => $issuedAt->getTimestamp(),         //Timestamp génération du token
            'iss' => $serverName,                       //Serveur
            'nbf' => $issuedAt->getTimestamp(),         //Timestamp empécher date antérieur
            'exp' => $expire,                           //Timestamp expiration du token
            'username' => $userName,
            'userFirstName' => $userFirstName,
            'userId' => $id,
        ];

        //retourne le JWT Token encode
        $token = JWT::encode(
            $data,
            $this->token,
            'HS512'
        );
        return $token;
    }
}
