<?php
namespace Admin;

use Admin\Model\Brand;
use Admin\Model\BrandTable;
use Admin\Model\Property;
use Admin\Model\PropertyTable;
use Admin\Model\User;
use Admin\Model\UserTable;
use Admin\Model\Role;
use Admin\Model\RoleTable;
use Admin\Model\Marketcodes;
use Admin\Model\MarketcodesTable;
use Zend\Db\ResultSet\ResultSet;
use Zend\Db\TableGateway\TableGateway;

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
                'Admin\Model\BrandTable' =>  function($sm) {
                    $tableGateway = $sm->get('BrandTableGateway');
                    $table = new BrandTable($tableGateway);
                    return $table;
                },
                'BrandTableGateway' => function ($sm) {
                    $dbAdapter = $sm->get('Zend\Db\Adapter\Adapter');
                    $resultSetPrototype = new ResultSet();
                    $resultSetPrototype->setArrayObjectPrototype(new Brand());
                    return new TableGateway('brand', $dbAdapter, null, $resultSetPrototype);
                },
                'Admin\Model\PropertyTable' =>  function($sm) {
                    $tableGateway = $sm->get('PropertyTableGateway');
                    $table = new PropertyTable($tableGateway);
                    return $table;
                },
                'PropertyTableGateway' => function ($sm) {
                    $dbAdapter = $sm->get('Zend\Db\Adapter\Adapter');
                    $resultSetPrototype = new ResultSet();
                    $resultSetPrototype->setArrayObjectPrototype(new Property());
                    return new TableGateway('property', $dbAdapter, null, $resultSetPrototype);
                },
                'Admin\Model\RoleTable' =>  function($sm) {
                    $tableGateway = $sm->get('RoleTableGateway');
                    $table = new RoleTable($tableGateway);
                    return $table;
                },
                'RoleTableGateway' => function ($sm) {
                    $dbAdapter = $sm->get('Zend\Db\Adapter\Adapter');
                    $resultSetPrototype = new ResultSet();
                    $resultSetPrototype->setArrayObjectPrototype(new Role());
                    return new TableGateway('role', $dbAdapter, null, $resultSetPrototype);
                },
                'Admin\Model\UserTable' =>  function($sm) {
                    $tableGateway = $sm->get('UserTableGateway');
                    $table = new UserTable($tableGateway);
                    return $table;
                },
                'UserTableGateway' => function ($sm) {
                    $dbAdapter = $sm->get('Zend\Db\Adapter\Adapter');
                    $resultSetPrototype = new ResultSet();
                    $resultSetPrototype->setArrayObjectPrototype(new User());
                    return new TableGateway('user', $dbAdapter, null, $resultSetPrototype);
                },
                'Admin\Model\MarketcodesTable' =>  function($sm) {
                    $tableGateway = $sm->get('MarketcodesTableGateway');
                    $table = new MarketcodesTable($tableGateway);
                    return $table;
                },
                'MarketcodesTableGateway' => function ($sm) {
                    $dbAdapter = $sm->get('Zend\Db\Adapter\Adapter');
                    $resultSetPrototype = new ResultSet();
                    $resultSetPrototype->setArrayObjectPrototype(new Marketcodes());
                    return new TableGateway('marketcodes', $dbAdapter, null, $resultSetPrototype);
                },
            ),
        );
    }
}