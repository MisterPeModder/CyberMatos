<?php

namespace App\Controller;

use App\Component\JsonErrorResponse;
use App\Entity\User;
use App\Repository\UserRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Serializer\SerializerInterface;
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
}
