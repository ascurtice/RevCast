<?php
namespace Report;

use Zend\Db\ResultSet\ResultSet;
use Zend\Db\TableGateway\TableGateway;
use Upload\Model\Reportkey;
use Upload\Model\ReportkeyTable;

class Module
{
    public function getAutoloaderConfig()
    {
        return array(
            'Zend\Loader\ClassMapAutoloader' => array(
                __DIR__ . '/autoload_classmap.php',
            ),
            'Zend\Loader\StandardAutoloader' => array(
                'namespaces' => array(
                    __NAMESPACE__ => __DIR__ . '/src/' . __NAMESPACE__,
                ),
            ),
        );
    }

    public function getConfig()
    {
        return include __DIR__ . '/config/module.config.php';
    }

    public function getServiceConfig()
    {
        return array(
            'factories' => array(
                'Upload\Model\ReportkeyTable' =>  function($sm) {
                    $tableGateway = $sm->get('ReportkeyTableGateway');
                    $table = new ReportkeyTable($tableGateway);
                    return $table;
                },
                'ReportkeyTableGateway' => function ($sm) {
                    $dbAdapter = $sm->get('Zend\Db\Adapter\Adapter');
                    $resultSetPrototype = new ResultSet();
                    $resultSetPrototype->setArrayObjectPrototype(new Reportkey());
                    return new TableGateway('reportkey', $dbAdapter, null, $resultSetPrototype);
                },
            ),
        );
    }
}