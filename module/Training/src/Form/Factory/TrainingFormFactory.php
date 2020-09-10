<?php
namespace Training\Form\Factory;

use Interop\Container\ContainerInterface;
use Midnet\Model\Uuid;
use Training\Form\TrainingForm;
use Training\Model\TrainingModel;
use Zend\ServiceManager\Factory\FactoryInterface;

class TrainingFormFactory implements FactoryInterface
{
    public function __invoke(ContainerInterface $container, $requestedName, array $options = null)
    {
        $uuid = new Uuid();
        $form = new TrainingForm($uuid->value);
        $adapter = $container->get('training-model-primary-adapter');
        
        $model = new TrainingModel($adapter);
        
        $form->setInputFilter($model->getInputFilter());
        $form->setDbAdapter($adapter);
        $form->initialize();
        return $form;
    }
}