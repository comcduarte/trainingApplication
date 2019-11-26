<?php 

use Midnet\View\Helper\Functions;
use Training\Controller\TrainingConfigController;
use Training\Controller\TrainingController;
use Training\Controller\Factory\TrainingConfigControllerFactory;
use Training\Controller\Factory\TrainingControllerFactory;
use Training\Form\TrainingForm;
use Training\Form\Factory\EmployeeClassesFormFactory;
use Training\Form\Factory\TrainingFormFactory;
use Training\Service\Factory\TrainingModelPrimaryAdapterFactory;
use Zend\Router\Http\Literal;
use Zend\Router\Http\Segment;
use Zend\ServiceManager\Factory\InvokableFactory;

return [
    'router' => [
        'routes' => [
            'training' => [
                'type' => Literal::class,
                'priority' => 1,
                'options' => [
                    'route' => '/training',
                    'defaults' => [
                        'action' => 'index',
                        'controller' => TrainingController::class,
                    ],
                ],
                'may_terminate' => TRUE,
                'child_routes' => [
                    'config' => [
                        'type' => Segment::class,
                        'priority' => 100,
                        'options' => [
                            'route' => '/config[/:action]',
                            'defaults' => [
                                'action' => 'index',
                                'controller' => TrainingConfigController::class,
                            ],
                        ],
                    ],
                    'default' => [
                        'type' => Segment::class,
                        'priority' => -100,
                        'options' => [
                            'route' => '/[:action[/:uuid]]',
                            'defaults' => [
                                'action' => 'index',
                                'controller' => TrainingController::class,
                            ],
                        ],
                    ],
                ],
            ],
        ],
    ],
    'controllers' => [
        'factories' => [
            TrainingController::class => TrainingControllerFactory::class,
            TrainingConfigController::class => TrainingConfigControllerFactory::class,
        ],
    ],
    'form_elements' => [
        'factories' => [
            TrainingForm::class => TrainingFormFactory::class,
            EmployeeClassForm::class => EmployeeClassesFormFactory::class,
        ],
    ],
    'navigation' => [
        'default' => [
            [
                'label' => 'Training',
                'route' => 'home',
                'class' => 'dropdown',
                'pages' => [
                    [
                        'label' => 'Training Maintenance',
                        'route' => 'training/default',
                        'class' => 'dropdown-submenu',
                        'pages' => [
                            [
                                'label' => 'Add Training',
                                'route' => 'training/default',
                                'action' => 'create',
                            ],
                            [
                                'label' => 'List Training',
                                'route' => 'training/default',
                                'action' => 'index',
                            ],
                            [
                                'label' => 'Settings',
                                'route' => 'training/config',
                                'action' => 'index',
                            ],
                        ],
                    ],
                ],
            ],
        ],
    ],
    'service_manager' => [
        'aliases' => [
            'training-model-primary-adapter-config' => 'model-primary-adapter-config',
        ],
        'factories' => [
            'training-model-primary-adapter' => TrainingModelPrimaryAdapterFactory::class,
        ],
    ],
    'view_helpers' => [
        'aliases' => [
            'functions' => Functions::class,
        ],
        'factories' => [
            Functions::class => InvokableFactory::class,
        ],
    ],
    'view_manager' => [
        'template_map' => [
            'training/config' => __DIR__ . '/../view/training/config/index.phtml',
            'related_files' => __DIR__ . '/../view/training/partials/related_files.phtml',
            'training/reports/' => __DIR__ . '/../view/training/reports/',
        ],
        'template_path_stack' => [
            __DIR__ . '/../view',
        ],
    ],
];