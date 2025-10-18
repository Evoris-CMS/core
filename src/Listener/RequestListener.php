<?php

namespace Evoris\Core\Listener;

use Evoris\Core\Projection\PageProjection;
use Evoris\Core\Service\PageRegistry;
use Evoris\Core\Service\PageService;
use Symfony\Component\EventDispatcher\Attribute\AsEventListener;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Event\RequestEvent;
use Symfony\Component\Serializer\SerializerInterface;
use Twig\Environment;

class RequestListener
{
    public function __construct(
        private readonly PageProjection $pageProjection,
        private readonly PageRegistry $pageRegistry,
        private readonly SerializerInterface $serializer,
        private readonly Environment $twig
    )
    {
    }

    #[AsEventListener(priority: 64)]
    public function onKernelRequest(
        RequestEvent $event,
    ): void
    {
        if($event->isMainRequest()) {
            return;
        }

        $page = $this->pageProjection->getPageByPath($event->getRequest()->getHost(), $event->getRequest()->getPathInfo());

        if(!$page) {
            return;
        }

        $class = $this->pageRegistry->get($page['type']);

        $content = $this->serializer->deserialize($page['content'], $class['class'], 'json');

        $html = $this->twig->render($class['template'], ['page' => $content]);

        $event->setResponse(new Response($html, 200, []));
    }
}