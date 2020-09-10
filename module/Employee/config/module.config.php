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
use Employee\Controller\DepartmentController;
use Employee\Controller\Factory\DepartmentControllerFactory;

return [
    'router' => [
        'routes' => [
            'department' => [
                'type' => Literal::class,
                'priority' => 1,
                'options' => [
                    'route' => '/department',
                    'defaults' => [
                        'action' => 'index',
                        'controller' => DepartmentController::class,
                    ]
                ],
                'may_terminate' => FALSE,
                'child_routes' => [
                    'default' => [
                        'type' => Segment::class,
                        'priority' => -100,
                        'options' => [
                            'route' => '/[:action[/:uuid]]',
                            'defaults' => [
                                'action' => 'index',
                                'controller' => DepartmentController::class,
                            ],
                        ],
                    ],
                ],
            ],
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
    'acl' => [
        'guest' => [
        ],
        'member' => [
            'employee/default' => ['index','create','update','delete','find'],
            'employee/config' => ['index','clear','create', 'reconciledirectories','importemployees'],
            'department/default' => ['index','create','update','delete'],
        ],
    ],
    'controller_plugins' => [
        'factories' => [
            \User\Controller\Plugin\CurrentUser::class => \User\Controller\Plugin\Factory\CurrentUserFactory::class,
        ],
        'aliases' => [
            'currentUser' => \User\Controller\Plugin\CurrentUser::class,
        ],
        
    ],
    'controllers' => [
        'aliases' => [
            'department' => DepartmentController::class,
            'employee' => EmployeeController::class,
        ],
        'factories' => [
            DepartmentController::class => DepartmentControllerFactory::class,
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
                        'label' => 'Department Maintenance',
                        'route' => 'department/default',
                        'class' => 'dropdown-submenu',
                        'pages' => [
                            [
                                'label' => 'Add Department',
                                'route' => 'department/default',
                                'action' => 'create',
                                'controller' => 'department',
                            ],
                            
                            [
                                'label' => 'List Departments',
                                'route' => 'department/default',
                                'action' => 'index',
                                'controller' => 'department',
                            ],
                        ],
                    ],
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
        'template_map' => [
            'employee/config' => __DIR__ . '/../view/employee/config/index.phtml',
        ],
        'template_path_stack' => [
            __DIR__ . '/../view',
        ],
    ],
];