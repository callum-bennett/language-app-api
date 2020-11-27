<?php

namespace App\EventSubscriber;

use App\Controller\ApiController;
use Doctrine\Common\Annotations\Reader;
use Doctrine\ORM\EntityManagerInterface;
use ReflectionClass;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpKernel\Event\ResponseEvent;
use Symfony\Component\HttpKernel\KernelEvents;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;

class ApiV1ResponseNotificationsSubscriber implements EventSubscriberInterface
{

    private $annotationReader;

    private $tokenStorage;

    /**
     * @param EntityManagerInterface $em
     * @param Reader $annotationReader
     * @param TokenStorageInterface $tokenStorage
     */
    public function __construct(EntityManagerInterface $em, Reader $annotationReader, TokenStorageInterface $tokenStorage) {
        $this->em = $em;
        $this->annotationReader = $annotationReader;
        $this->tokenStorage = $tokenStorage;
    }

    public static function getSubscribedEvents()
    {
        return [
                KernelEvents::RESPONSE => 'onKernelResponse',
        ];
    }

    /**
     * @param ResponseEvent $event
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function onKernelResponse(ResponseEvent $event)
    {
        $request = $event->getRequest();
        $response = $event->getResponse();

        $reflectionClass = new ReflectionClass(ApiController::class);
        $classAnnotations = $this->annotationReader->getClassAnnotations($reflectionClass);
        $apiPath = $classAnnotations[0]->getPath();
        $isApiV1 = substr($request->getRequestUri(), 0, strlen($apiPath)) === $apiPath;

        if ($isApiV1 && $response->isSuccessful()) {
            $user = $this->tokenStorage->getToken()->getUser();
            $content = json_decode($response->getContent(), true);
            $content['notifications'] = $user->getNotifications();
            $response->setContent(json_encode($content));
        }

        return $response;
    }
}