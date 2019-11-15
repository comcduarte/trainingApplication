<?php
namespace Training\Controller\Factory;

use Interop\Container\ContainerInterface;
use Midnet\Model\Uuid;
use Training\Controller\TrainingController;
use Training\Form\TrainingForm;
use Training\Model\TrainingModel;
use Zend\ServiceManager\Factory\FactoryInterface;

class TrainingControllerFactory implements FactoryInterface
{
    public function __invoke(ContainerInterface $container, $requestedName, array $options = null)
    {
        $controller = new TrainingController();
        $uuid = new Uuid();
        $date = new \DateTime('now',new \DateTimeZone('EDT'));
        $today = $date->format('Y-m-d H:i:s');
        
        $adapter = $container->get('training-model-primary-adapter');
        $controller->setDbAdapter($adapter);
        
        $model = new TrainingModel($adapter);
        $model->UUID = $uuid->value;
        $model->DATE_CREATED = $today;
        $model->STATUS = $model::ACTIVE_STATUS;
        
        $controller->setModel($model);
        
        $form = $container->get('FormElementManager')->get(TrainingForm::class);
        $controller->setForm($form);
        return $controller;
    }
}