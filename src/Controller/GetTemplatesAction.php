<?php

namespace CMS\Core\Controller;

use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Attribute\AsController;
use Symfony\Component\Routing\Attribute\Route;

#[AsController]
#[Route(path: '/templates', name: 'get_templates', methods: ['GET'])]
class GetTemplatesAction
{
    public function __construct()
    {
    }

    public function __invoke(): Response
    {
        return new Response();
    }
}