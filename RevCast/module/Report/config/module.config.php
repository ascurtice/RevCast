<?php
return array(
    'controllers' => array(
        'invokables' => array(
            'Report\Controller\Report' => 'Report\Controller\ReportController',
        ),
    ),

    // The following section is new and should be added to your file
    'router' => array(
        'routes' => array(
            'report' => array(
                'type'    => 'segment',
                'options' => array(
                    'route'    => '/report[/:action][/:id]',
                    'constraints' => array(
                        'action' => '[a-zA-Z][a-zA-Z0-9_-]*',
                        'id'     => '[0-9]+',
                    ),
                    'defaults' => array(
                        'controller' => 'Report\Controller\Report',
                        'action'     => 'index',
                    ),
                ),
            ),
            'generate' => array(
                'type'    => 'segment',
                'options' => array(
                    'route'    => '/generate[/:action][/:id]',
                    'constraints' => array(
                        'action' => '[a-zA-Z][a-zA-Z0-9_-]*',
                        'id'     => '[0-9]+',
                    ),
                    'defaults' => array(
                        'controller' => 'Report\Controller\Report',
                        'action'     => 'generate',
                    ),
                ),
            ),
        ),
    ),

    'view_manager' => array(
        'template_path_stack' => array(
            'report' => __DIR__ . '/../view',
        ),
    ),
);