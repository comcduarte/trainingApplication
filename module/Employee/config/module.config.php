<?php 

use Employee\Controller\EmployeeConfigController;
use Employee\Controller\EmployeeController;
use Employee\Controller\Factory\EmployeeControllerFactory;
use Employee\Form\EmployeeForm;
use Employee\Form\Factory\EmployeeFormFactory;
use Employee\Service\Factory\EmployeeModelPrimaryAdapterFactory;
use Zend\Router\Http\Literal;
use Zend\Router\Http\Segment;
use Employee\Controller\Factory\EmployeeConfigControllerFactory;

return [
    'router' => [
        'routes' => [
            'employee' => [
                'type' => Literal::class,
                'priority' => 1,
                'options' => [
                    'route' => '/employee',
                    'defaults' => [
                        'action' => 'index',
                        'controller' => EmployeeController::class,
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
                                'controller' => EmployeeConfigController::class,
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
                                'controller' => EmployeeController::class,
                            ],
                        ],
                    ],
                ],
            ],
        ],
    ],
    'controllers' => [
        'factories' => [
            EmployeeController::class => EmployeeControllerFactory::class,
            EmployeeConfigController::class => EmployeeConfigControllerFactory::class,
        ],
    ],
    'form_elements' => [
        'factories' => [
            EmployeeForm::class => EmployeeFormFactory::class,
        ],
    ],
    'navigation' => [
        'default' => [
            [
                'label' => 'Employee',
                'route' => 'home',
                'class' => 'dropdown',
                'pages' => [
                    [
                        'label' => 'Employee Maintenance',
                        'route' => 'employee/default',
                        'class' => 'dropdown-submenu',
                        'pages' => [
                            [
                                'label' => 'Add Employee',
                                'route' => 'employee/default',
                                'action' => 'create',
                            ],
                            [
                                'label' => 'List Employees',
                                'route' => 'employee/default',
                                'action' => 'index',
                            ],
                            [
                                'label' => 'Settings',
                                'route' => 'employee/config',
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
            'employee-model-primary-adapter-config' => 'model-primary-adapter-config',
        ],
        'factories' => [
            'employee-model-primary-adapter' => EmployeeModelPrimaryAdapterFactory::class,
        ],
    ],
    'view_manager' => [
        'template_path_stack' => [
            __DIR__ . '/../view',
        ],
    ],
];