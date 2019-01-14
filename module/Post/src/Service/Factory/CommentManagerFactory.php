<?php
namespace Post\Service\Factory;

use Interop\Container\ContainerInterface;
use Zend\ServiceManager\Factory\FactoryInterface;
use Post\Service\CommentManager;

/**
 * This is the factory for PostManager. Its purpose is to instantiate the
 * service.
 */
class CommentManagerFactory implements FactoryInterface
{
    public function __invoke(ContainerInterface $container,
                             $requestedName, array $options = null)
    {
        $entityManager = $container->get('doctrine.entitymanager.orm_default');

        // Instantiate the service and inject dependencies
        $config = $container->get('config')['post'];

        return new CommentManager($entityManager, $config);
    }
}