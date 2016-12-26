<?php

namespace Krtv\Bundle\CsrfValidatorBundle\EventListener;

use Krtv\Bundle\CsrfValidatorBundle\ReaderManager\ReaderManagerInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpKernel\Event\FilterControllerEvent;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;
use Symfony\Component\HttpKernel\KernelEvents;

/**
 * Class AnnotationSubscriber
 * @package Krtv\Bundle\CsrfValidatorBundle\EventListener
 */
class AnnotationSubscriber implements EventSubscriberInterface
{
    /**
     * @var ReaderManagerInterface
     */
    protected $readerManager;

    /**
     * @param ReaderManagerInterface $readerManager
     */
    public function __construct(ReaderManagerInterface $readerManager)
    {
        $this->readerManager = $readerManager;
    }

    /**
     * Returns an array of event names this subscriber wants to listen to.
     *
     * The array keys are event names and the value can be:
     *
     *  * The method name to call (priority defaults to 0)
     *  * An array composed of the method name to call and the priority
     *  * An array of arrays composed of the method names to call and respective
     *    priorities, or 0 if unset
     *
     * For instance:
     *
     *  * array('eventName' => 'methodName')
     *  * array('eventName' => array('methodName', $priority))
     *  * array('eventName' => array(array('methodName1', $priority), array('methodName2'))
     *
     * @return array The event names to listen to
     *
     * @api
     */
    public static function getSubscribedEvents()
    {
        return [
            KernelEvents::CONTROLLER => 'onKernelController',
        ];
    }

    /**
     * @param \Symfony\Component\HttpKernel\Event\FilterControllerEvent $event
     * @throws \Symfony\Component\HttpKernel\Exception\BadRequestHttpException
     */
    public function onKernelController(FilterControllerEvent $event)
    {
        list($controller, $action) = $event->getController();

        $method = new \ReflectionMethod($controller, $action);

        $annotation = $this->readerManager->supports($method);
        if (!$annotation) {
            $class = new \ReflectionObject($controller);
            $annotation = $this->readerManager->supportsClass($class);
        }

        if ($annotation && !$this->readerManager->validate($annotation)) {
            throw new BadRequestHttpException('Token is invalid');
        }
    }
}
