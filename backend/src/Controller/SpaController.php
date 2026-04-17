<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

/**
 * Catch-all controller that returns the Quasar SPA shell for every route
 * that isn't handled by an API controller or served as a static file.
 *
 * The built SPA lives at  public/app/index.html  (output of `npm run build`
 * in the frontend folder with distDir set to ../backend/public/app).
 *
 * Priority -100 ensures every real API route takes precedence.
 */
final class SpaController extends AbstractController
{
    #[Route('/', name: 'spa_root', priority: -100)]
    #[Route(
        '/{path}',
        name: 'spa_fallback',
        requirements: ['path' => '^(?!api|uploads|_profiler|_wdt).*'],
        priority: -100,
    )]
    public function index(): Response
    {
        $indexPath = $this->getParameter('kernel.project_dir') . '/public/app/index.html';

        if (!file_exists($indexPath)) {
            return new Response(
                '<h2>Frontend not built yet.</h2><p>Run <code>npm run build</code> inside the <code>frontend/</code> folder.</p>',
                503,
                ['Content-Type' => 'text/html; charset=UTF-8'],
            );
        }

        return new Response(
            (string) file_get_contents($indexPath),
            200,
            ['Content-Type' => 'text/html; charset=UTF-8'],
        );
    }
}
