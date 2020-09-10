<?php
namespace Employee\Controller\Factory;

use Employee\Controller\EmployeeController;
use Employee\Form\EmployeeForm;
use Employee\Model\EmployeeModel;
use Interop\Container\ContainerInterface;
use Midnet\Model\Uuid;
use Zend\ServiceManager\Factory\FactoryInterface;

class EmployeeControllerFactory implements FactoryInterface
{
    public function __invoke(ContainerInterface $container, $requestedName, array $options = null)
    {
        $controller = new EmployeeController();
        $uuid = new Uuid();
        $date = new \DateTime('now',new \DateTimeZone('EDT'));
        $today = $date->format('Y-m-d H:i:s');
        
        $adapter = $container->get('employee-model-primary-adapter');
        $controller->setDbAdapter($adapter);
        
        $model = new EmployeeModel($adapter);
        $model->UUID = $uuid->value;
        $model->DATE_CREATED = $today;
        $model->STATUS = $model::ACTIVE_STATUS;
        
        $controller->setModel($model);
        
        $form = $container->get('FormElementManager')->get(EmployeeForm::class);
        $controller->setForm($form);
        return $controller;
    }
}