<?php

namespace Evoris\Core\Controller;

use Evoris\Core\Aggregate\Webspace;
use Evoris\Core\Command\AddPage;
use Evoris\Core\Id\PageId;
use Evoris\Core\Id\WebspaceId;
use Evoris\Core\Page\Page;
use Patchlevel\EventSourcing\CommandBus\CommandBus;
use Patchlevel\EventSourcing\Repository\Repository;
use Patchlevel\EventSourcing\Repository\RepositoryManager;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Attribute\AsController;
use Symfony\Component\Routing\Attribute\Route;
use Twig\Environment;

#[AsController]
class PageController
{
    private readonly Repository $evorisWebspaceRepository;
    public function __construct(
        private readonly Environment $twig,
        RepositoryManager $repositoryManager,
        private readonly CommandBus $commandBus,
    )
    {
        $this->evorisWebspaceRepository = $repositoryManager->get(Webspace::class);
    }

    #[Route(path: '/webspace/{webspaceId}/pages', name: 'list_pages', methods: ['GET'] )]
    public function listAction(Request $request): Response
    {
        $webspaceId = WebspaceId::fromString($request->attributes->get('webspaceId'));
        $webspace = $this->evorisWebspaceRepository->load($webspaceId);

        // Currently listing only root-level pages as provided by existing logic
        $pages = array_filter($webspace->pages(), fn($page) => $page->parent() === null);

        $html = $this->twig->render('page/list.html.twig', [
            'webspaceId' => $webspaceId->toString(),
            'host' => $webspace->host(),
            'pages' => $pages,
        ]);

        return new Response($html);
    }

    #[Route(path: '/webspace/{webspaceId}/pages/create', name: 'create_page', methods: ['GET', 'POST'] )]
    public function createAction(Request $request): Response
    {
        $webspaceId = WebspaceId::fromString($request->attributes->get('webspaceId'));
        $webspace = $this->evorisWebspaceRepository->load($webspaceId);

        $title = trim((string) $request->request->get('title', ''));
        $slug = trim((string) $request->request->get('slug', ''));
        $content = (string) $request->request->get('content', '');

        $errors = [];
        $success = false;

        if ($request->isMethod('POST')) {
            if ($title === '') {
                $errors[] = 'Bitte geben Sie einen Titel ein.';
            }
            if ($slug === '') {
                $errors[] = 'Bitte geben Sie einen Slug ein.';
            }
            if ($content === '') {
                $errors[] = 'Bitte geben Sie einen Inhalt ein.';
            }

            if (empty($errors)) {
                $page = new Page($content);

                $this->commandBus->dispatch(
                    new AddPage(
                    $webspaceId,
                    null,
                    $webspace->host(),
                    $slug,
                    $title,
                    $page)
                );

                $success = true;
                // clear form on success
                $title = '';
                $slug = '';
                $content = '';
            }
        }

        $html = $this->twig->render('page/create.html.twig', [
            'webspaceId' => $webspaceId->toString(),
            'host' => $webspace->host(),
            'title' => $title,
            'slug' => $slug,
            'content' => $content,
            'errors' => $errors,
            'success' => $success,
        ]);

        return new Response($html);
    }
}