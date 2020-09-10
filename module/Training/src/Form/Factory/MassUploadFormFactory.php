<?php
namespace Training\Form\Factory;

use Interop\Container\ContainerInterface;
use Zend\ServiceManager\Factory\FactoryInterface;
use Training\Form\MassUploadForm;

class MassUploadFormFactory implements FactoryInterface
{
    public function __invoke(ContainerInterface $container, $requestedName, array $options = null)
    {
        $adapter = $container->get('training-model-primary-adapter');
        
        $form = new MassUploadForm();
        $form->setDbAdapter($adapter);
        $form->initialize();
        
        return $form;
    }
}