<?php
namespace Application\Controller\Factory;

use Application\Controller\IndexController;
use Interop\Container\ContainerInterface;
use Zend\ServiceManager\Factory\FactoryInterface;

class IndexControllerFactory implements FactoryInterface
{
    public function __invoke(ContainerInterface $container, $requestedName, array $options = null)
    {
        $controller = new IndexController();
        $adapter = $container->get('training-model-primary-adapter');
        $controller->setDbAdapter($adapter);
        return $controller;
    }
}