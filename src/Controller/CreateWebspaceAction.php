<?php

namespace Evoris\Core\Controller;

use Evoris\Core\Aggregate\Webspace;
use Evoris\Core\Id\WebspaceId;
use Patchlevel\EventSourcing\Repository\Repository;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Attribute\AsController;
use Symfony\Component\Routing\Attribute\Route;
use Twig\Environment;

#[AsController]
#[Route(path: '/webspace', name: 'create_webspace', methods: ['GET', 'POST'])]
class CreateWebspaceAction
{
    /** @param Repository<Webspace> $webspaceRepository */
    public function __construct(
        private readonly Environment $twig,
        private readonly Repository $leafWebspaceRepository,
    ) {
    }

    public function __invoke(Request $request): Response
    {
        $hostname = '';
        $errors = [];
        $success = false;

        if ($request->isMethod('POST')) {
            $hostname = trim((string) $request->request->get('hostname', ''));
            if ($hostname === '') {
                $errors[] = 'Bitte geben Sie einen Hostname ein.';
            } else {
                $id = WebspaceId::generate();
                $webspace = Webspace::create($id, $hostname);

                $this->leafWebspaceRepository->save($webspace);

                $success = true;
            }
        }

        $html = $this->twig->render('webspace/create.html.twig', [
            'hostname' => $hostname,
            'errors' => $errors,
            'success' => $success,
        ]);

        return new Response($html);
    }
}