<?php
namespace Training\Controller\Factory;

use Interop\Container\ContainerInterface;
use Training\Controller\TrainingConfigController;
use Zend\ServiceManager\Factory\FactoryInterface;

class TrainingConfigControllerFactory implements FactoryInterface
{
    public function __invoke(ContainerInterface $container, $requestedName, array $options = null)
    {
        $controller = new TrainingConfigController();
        $adapter = $container->get('training-model-primary-adapter');
        $controller->setDbAdapter($adapter);
        return $controller;
    }
}