<?php
namespace Employee\Controller\Factory;

use Employee\Controller\EmployeeConfigController;
use Interop\Container\ContainerInterface;
use Zend\ServiceManager\Factory\FactoryInterface;

class EmployeeConfigControllerFactory implements FactoryInterface
{
    public function __invoke(ContainerInterface $container, $requestedName, array $options = null)
    {
        $controller = new EmployeeConfigController();
        $adapter = $container->get('employee-model-primary-adapter');
        $controller->setDbAdapter($adapter);
        return $controller;
    }
}