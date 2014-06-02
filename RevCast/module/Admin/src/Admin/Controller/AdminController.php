<?php
namespace Admin\Controller;

use Zend\Mvc\Controller\AbstractActionController;
use Zend\View\Model\ViewModel;

class AdminController extends AbstractActionController
{
    public function indexAction()
    {
    	$actions = array(
    		'brand' => array(
    			'title' => 'Brand Administration',
    			'url' => '/brand',
    		),
    		'property' => array(
    			'title' => 'Property Administration',
    			'url' => '/property',
    		),
    		'role' => array(
    			'title' => 'Role Administration',
    			'url' => '/role',
    		),
    		'user' => array(
    			'title' => 'User Administration',
    			'url' => '/user',
    		),
    	);
    	return new ViewModel(array(
    		'actions' => $actions,
    	));
    }
}