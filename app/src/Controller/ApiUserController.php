<?php

namespace App\Controller;

use App\Component\JsonErrorResponse;
use App\Entity\AccessToken;
use App\Entity\User;
use App\Repository\AccessTokenRepository;
use App\Repository\UserRepository;
use App\Validator\ActuallyNotBlank;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Attribute\CurrentUser;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\Validator\Validator\ValidatorInterface;

#[Route('/api')]
class ApiUserController extends AbstractController
{
    #[Route('/register', name: 'register_user', methods: ['POST'], condition: "request.headers.get('Content-Type') === 'application/json'")]
    public function register(Request $request, UserPasswordHasherInterface $passwordHasher, ValidatorInterface $validator, SerializerInterface $serializer, UserRepository $userRepository): JsonResponse
    {
        try {
            $newUser = $serializer->deserialize($request->getContent(), User::class, 'json');
        } catch (\Exception $exception) {
            return new JsonErrorResponse($exception);
        }
        $errors = $validator->validate($newUser);

        if (count($errors) > 0) {
            return new JsonErrorResponse((string) $errors);
        }

        // hash the password (based on the security.yaml config for the $user class)
        $hashedPassword = $passwordHasher->hashPassword(
            $newUser,
            $newUser->getPassword()
        );
        $newUser->setPassword($hashedPassword);
        $userRepository->save($newUser, true);

        return new JsonResponse(null, 201);
    }

    /**
     * @throws \Exception
     */
    #[Route('/login', name: 'login', methods: ['POST'], condition: "request.headers.get('Content-Type') === 'application/json'")]
    public function login(Request $request, UserPasswordHasherInterface $passwordHasher, ValidatorInterface $validator, UserRepository $userRepository, AccessTokenRepository $accessTokenRepository): JsonResponse
    {
        try {
            $login = $request->get('login');
            $password = $request->get('password');
        } catch (\Exception $exception) {
            return new JsonErrorResponse($exception);
        }

        $errors = $validator->validate([
            'login' => $login,
            'password' => $password,
        ], new Assert\Collection([
            'login' => new ActuallyNotBlank(),
            'password' => new ActuallyNotBlank(),
        ]));

        if (count($errors) > 0) {
            return new JsonErrorResponse((string) $errors);
        }
        $user = $userRepository->findOneBy(['login' => $login]);

        if (null == $user || !$passwordHasher->isPasswordValid($user, $password)) {
            return new JsonErrorResponse('Invalid credentials', Response::HTTP_FORBIDDEN);
        }

        $accessToken = AccessToken::generate($user);
        $accessTokenRepository->save($accessToken, true);

        return new JsonResponse([
            'token' => $accessToken->getValue(),
        ], 200);
    }

    #[Route('/users', name: 'get_current_user', methods: ['GET'])]
    public function users(#[CurrentUser] ?User $user): JsonResponse
    {
        return new JsonResponse([
            'login' => $user->getLogin(),
            'email' => $user->getEmail(),
            'firstname' => $user->getFirstname(),
            'lastname' => $user->getLastname(),
        ], 200);
    }
}
