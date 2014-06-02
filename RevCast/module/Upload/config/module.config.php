<?php
return array(
    'controllers' => array(
        'invokables' => array(
            'Upload\Controller\Upload' => 'Upload\Controller\UploadController',
            'Upload\Controller\MT' => 'Upload\Controller\MTController',
            'Upload\Controller\IHG' => 'Upload\Controller\IHGController',
            'Upload\Controller\HI' => 'Upload\Controller\HIController',
            'Upload\Controller\HG' => 'Upload\Controller\HGController',
        ),
    ),

    // The following section is new and should be added to your file
    'router' => array(
        'routes' => array(
            'upload' => array(
                'type'    => 'segment',
                'options' => array(
                    'route'    => '/upload[/:action][/:id]',
                    'constraints' => array(
                        'action' => '[a-zA-Z][a-zA-Z0-9_-]*',
                        'id'     => '[0-9]+',
                    ),
                    'defaults' => array(
                        'controller' => 'Upload\Controller\Upload',
                        'action'     => 'index',
                    ),
                ),
            ),
            'mt' => array(
                'type'    => 'segment',
                'options' => array(
                    'route'    => '/mt[/:action][/:id]',
                    'constraints' => array(
                        'action' => '[a-zA-Z][a-zA-Z0-9_-]*',
                        'id'     => '[0-9]+',
                    ),
                    'defaults' => array(
                        'controller' => 'Upload\Controller\MT',
                        'action'     => 'index',
                    ),
                ),
            ),
            'ihg' => array(
                'type'    => 'segment',
                'options' => array(
                    'route'    => '/ihg[/:action][/:id]',
                    'constraints' => array(
                        'action' => '[a-zA-Z][a-zA-Z0-9_-]*',
                        'id'     => '[0-9]+',
                    ),
                    'defaults' => array(
                        'controller' => 'Upload\Controller\IHG',
                        'action'     => 'index',
                    ),
                ),
            ),
            'hi' => array(
                'type'    => 'segment',
                'options' => array(
                    'route'    => '/hi[/:action][/:id]',
                    'constraints' => array(
                        'action' => '[a-zA-Z][a-zA-Z0-9_-]*',
                        'id'     => '[0-9]+',
                    ),
                    'defaults' => array(
                        'controller' => 'Upload\Controller\HI',
                        'action'     => 'index',
                    ),
                ),
            ),
            'hg' => array(
                'type'    => 'segment',
                'options' => array(
                    'route'    => '/hg[/:action][/:id]',
                    'constraints' => array(
                        'action' => '[a-zA-Z][a-zA-Z0-9_-]*',
                        'id'     => '[0-9]+',
                    ),
                    'defaults' => array(
                        'controller' => 'Upload\Controller\HG',
                        'action'     => 'index',
                    ),
                ),
            ),
        ),
    ),

    'view_manager' => array(
        'template_path_stack' => array(
            'upload' => __DIR__ . '/../view',
        ),
    ),
);