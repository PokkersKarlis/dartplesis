<?php

namespace App\Controller\Api;

use App\Entity\User;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Attribute\Route;

final class ApiController extends AbstractController
{
    public function __construct(
        private readonly UserRepository $userRepository,
        private readonly EntityManagerInterface $em,
        private readonly UserPasswordHasherInterface $passwordHasher,
    ) {
    }
    /**
     * Router runs before the firewall on kernel.request, so this route must exist.
     * json_login validates JSON credentials and authenticates the user; this action
     * then returns the session user (default success handler does not set a response).
     */
    #[Route('/api/login', name: 'api_login_check', methods: ['POST'])]
    public function loginCheck(): JsonResponse
    {
        /** @var User|null $user */
        $user = $this->getUser();
        if (!$user instanceof User) {
            return $this->json(['message' => 'Invalid credentials.'], 401);
        }

        return $this->json([
            'user' => [
                'id' => $user->getId(),
                'email' => $user->getEmail(),
                'roles' => $user->getRoles(),
            ],
        ]);
    }

    #[Route('/api/welcome', name: 'api_welcome', methods: ['GET'])]
    public function welcome(): JsonResponse
    {
        return $this->json([
            'message' => 'Welcome to the administration panel API.',
        ]);
    }

    #[Route('/api/me', name: 'api_me', methods: ['GET'])]
    public function me(): JsonResponse
    {
        /** @var User|null $user */
        $user = $this->getUser();

        if ($user === null) {
            return $this->json(['user' => null], 200);
        }

        return $this->json([
            'user' => [
                'id' => $user->getId(),
                'email' => $user->getEmail(),
                'roles' => $user->getRoles(),
            ],
        ]);
    }

    #[Route('/api/dashboard', name: 'api_dashboard', methods: ['GET'])]
    public function dashboard(): JsonResponse
    {
        return $this->json([
            'message' => 'Dashboard: authenticated access granted.',
        ]);
    }

    #[Route('/api/admin/panel', name: 'api_admin_panel', methods: ['GET'])]
    public function adminPanel(): JsonResponse
    {
        return $this->json([
            'message' => 'Admin panel: administrator access granted.',
        ]);
    }

    #[Route('/api/admin/users', name: 'api_admin_users', methods: ['GET'])]
    public function adminUsers(): JsonResponse
    {
        $users = $this->userRepository->findBy([], ['email' => 'ASC']);

        return $this->json([
            'users' => array_map(fn(User $u) => [
                'id'    => $u->getId(),
                'email' => $u->getEmail(),
                'roles' => $u->getRoles(),
            ], $users),
        ]);
    }

    #[Route('/api/admin/change-password', name: 'api_admin_change_password', methods: ['POST'])]
    public function changePassword(Request $request): JsonResponse
    {
        /** @var User|null $user */
        $user = $this->getUser();
        if (!$user instanceof User) {
            return $this->json(['message' => 'Not authenticated.'], 401);
        }

        $data = json_decode($request->getContent(), true);
        $currentPassword = (string) ($data['currentPassword'] ?? '');
        $newPassword     = (string) ($data['newPassword'] ?? '');

        if (!$this->passwordHasher->isPasswordValid($user, $currentPassword)) {
            return $this->json(['message' => 'Current password is incorrect.'], 422);
        }

        if (strlen($newPassword) < 8) {
            return $this->json(['message' => 'New password must be at least 8 characters.'], 422);
        }

        $user->setPassword($this->passwordHasher->hashPassword($user, $newPassword));
        $this->em->flush();

        return $this->json(['message' => 'Password updated.']);
    }
}

