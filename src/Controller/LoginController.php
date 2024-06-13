<?php

namespace App\Controller;

use App\Entity\Users;
use Lexik\Bundle\JWTAuthenticationBundle\Services\JWTTokenManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Security\Core\Exception\BadCredentialsException;
use Symfony\Component\Validator\Validator\ValidatorInterface;
use Symfony\Component\HttpFoundation\JsonResponse;

class LoginController extends AbstractController
{
    private $jwtManager;
    private $passwordHasher;
    private $entityManager;
    private $validator;

    public function __construct(
        JWTTokenManagerInterface $jwtManager,
        UserPasswordHasherInterface $passwordHasher,
        EntityManagerInterface $entityManager,
        ValidatorInterface $validator

    ) {
        $this->jwtManager = $jwtManager;
        $this->passwordHasher = $passwordHasher;
        $this->entityManager = $entityManager;
        $this->validator = $validator;
    }

    #[Route('/api/login', name: 'api_login', methods: ['POST'])]
    public function login(Request $request): Response
    {
        $authorizationHeader = $request->headers->get('Authorization');

        if (!$authorizationHeader || strpos($authorizationHeader, 'Basic ') !== 0) {
            return $this->json(['message' => 'Missing or invalid Authorization header'], Response::HTTP_BAD_REQUEST);
        }

        $encodedCredentials = substr($authorizationHeader, 6);
        $decodedCredentials = base64_decode($encodedCredentials);
        list($username, $password) = explode(':', $decodedCredentials, 2);

        if (!$username || !$password) {
            return $this->json(['message' => 'Missing username or password'], Response::HTTP_BAD_REQUEST);
        }

        $user = $this->entityManager->getRepository(Users::class)->findOneBy(['username' => $username]);

        if (!$user || !$this->passwordHasher->isPasswordValid($user, $password)) {
            throw new BadCredentialsException('Invalid credentials');
        }

        // Validate user object
        $errors = $this->validator->validate($user);
        if (count($errors) > 0) {
            $errorsString = (string) $errors;
            return new JsonResponse(['message' => $errorsString], Response::HTTP_BAD_REQUEST);
        }

        if (!$this->passwordHasher->isPasswordValid($user, $password)) {
            return $this->json(['message' => 'Invalid credentials'], Response::HTTP_UNAUTHORIZED);
        }

        $token = $this->jwtManager->create($user);

        return $this->json(['token' => $token]);
    }
}
