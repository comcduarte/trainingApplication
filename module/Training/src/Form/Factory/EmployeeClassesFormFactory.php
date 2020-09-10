<?php
namespace Training\Form\Factory;

use Interop\Container\ContainerInterface;
use Midnet\Model\Uuid;
use Training\Form\EmployeeClassesForm;

class EmployeeClassesFormFactory 
{
    public function __invoke(ContainerInterface $container)
    {
        $uuid = new Uuid();
        $adapter = $container->get('training-model-primary-adapter');
        
        $form = new EmployeeClassesForm($uuid->value);
        $form->setDbAdapter($adapter);
        $form->initialize();
        return $form;
    }
    
}