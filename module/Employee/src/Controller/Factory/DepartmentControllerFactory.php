<?php
namespace Employee\Controller\Factory;

use Employee\Controller\DepartmentController;
use Employee\Form\DepartmentForm;
use Employee\Model\DepartmentModel;
use Interop\Container\ContainerInterface;
use Midnet\Model\Uuid;
use Zend\ServiceManager\Factory\FactoryInterface;

class DepartmentControllerFactory implements FactoryInterface
{
    public function __invoke(ContainerInterface $container, $requestedName, array $options = null)
    {
        $controller = new DepartmentController();
        $uuid = new Uuid();
        $date = new \DateTime('now',new \DateTimeZone('EDT'));
        $today = $date->format('Y-m-d H:i:s');
        
        $adapter = $container->get('employee-model-primary-adapter');
        $controller->setDbAdapter($adapter);
        
        $model = new DepartmentModel($adapter);
        $model->UUID = $uuid->value;
        $model->DATE_CREATED = $today;
        $model->STATUS = $model::ACTIVE_STATUS;
        
        $controller->setModel($model);
        
        $form = new DepartmentForm();
        $form->setDbAdapter($adapter);
        $form->initialize();
        $controller->setForm($form);
        return $controller;
    }
}