<?php
namespace Employee\Form\Factory;

use Employee\Form\EmployeeForm;
use Employee\Model\EmployeeModel;
use Interop\Container\ContainerInterface;
use Midnet\Model\Uuid;
use Zend\ServiceManager\Factory\FactoryInterface;

class EmployeeFormFactory implements FactoryInterface
{
    public function __invoke(ContainerInterface $container, $requestedName, array $options = null)
    {
        $uuid = new Uuid();
        $form = new EmployeeForm($uuid->value);
        $adapter = $container->get('employee-model-primary-adapter');
        
        $model = new EmployeeModel($adapter);
        
        $form->setInputFilter($model->getInputFilter());
        $form->setDbAdapter($adapter);
        $form->initialize();
        return $form;
    }
}