<?php
namespace Post\Controller\Factory;

use Interop\Container\ContainerInterface;
use Zend\ServiceManager\Factory\FactoryInterface;
use Post\Service\CommentManager;
use Post\Controller\CommentController;
use Post\Helpers\AttachmentValidator;
/**
 * This is the factory for PostController. Its purpose is to instantiate the
 * controller.
 */
class CommentControllerFactory implements FactoryInterface
{
    public function __invoke(ContainerInterface $container,
                             $requestedName, array $options = null)
    {
        $entityManager = $container->get('doctrine.entitymanager.orm_default');
        $commentManager = $container->get(CommentManager::class);
        $attachmentHandler = new AttachmentValidator($container->get('config')['post']['attachment']);
        // Instantiate the controller and inject dependencies
        return new CommentController($entityManager, $commentManager, $attachmentHandler);
    }
}