<?php
namespace Admin\Controller;

use Zend\Mvc\Controller\AbstractActionController;
use Zend\View\Model\ViewModel;
use Admin\Model\User;
use Admin\Form\UserForm;

class UserController extends AbstractActionController
{
    protected $userTable;
    protected $propertyTable;
    protected $brandTable;
    protected $roleTable;

    public function indexAction()
    {
        $propertyReturn = $this->getPropertyTable()->fetchAll();
        $properties = array();
        foreach($propertyReturn as $p){
            $properties[$p->id] = $p->abbreviation;
        }

        $roleReturn = $this->getRoleTable()->fetchAll();
        $roles = array();
        foreach($roleReturn as $r){
            $roles[$r->id] = $r->role;
        }

        $paginator = $this->getUserTable()->getActiveUsers(true);
        $paginator->setCurrentPageNumber((int)$this->params()->fromQuery('page', 1));
        $paginator->setItemCountPerPage(10);

        return new ViewModel(array(
            'properties' => $properties,
            'paginator' => $paginator,
            'roles' => $roles
        ));

    }

    public function addAction()
    {
        $form = new UserForm();
        $form->get('submit')->setValue('Add');

        $properties = $this->getPropertyTable()->getActiveProperties();
        $pOptions = array();
        foreach($properties as $p){
            $pOptions[$p->id] = $p->abbreviation;
        }
        $roles = $this->getRoleTable()->fetchAll();
        $rOptions = array();
        foreach($roles as $role){
            $rOptions[$role->id] = $role->role;
        }

        $form->add(array(
            'type' => 'Zend\Form\Element\Select',
            'name' => 'property_id',
            'options' => array(
                'label' => 'Property',
                'value_options' => $pOptions,
            )));

        $form->add(array(
            'type' => 'Zend\Form\Element\Select',
            'name' => 'role_id',
            'options' => array(
                'label' => 'Role',
                'value_options' => $rOptions,
            )));

        $request = $this->getRequest();

        if ($request->isPost()) {
            $user = new User();
            $form->setInputFilter($user->getInputFilter());
            $form->setData($request->getPost());

            if ($form->isValid()) {
                $user->exchangeArray($form->getData());
                $password = $user->password;
                $vPassword = $form->getInputFilter()->getValue('vPassword');
                if(!$password === $vPassword){
                    echo 'Passwords do not match.';
                    return $this->redirect()->toRoute('user');
                }

                $this->getUserTable()->saveUser($user);

                return $this->redirect()->toRoute('user');
            }
        }
        return array('form' => $form);
    }

    public function editAction()
    {
        $id = (int) $this->params()->fromRoute('id', 0);
        if (!$id) {
            return $this->redirect()->toRoute('user', array(
                'action' => 'add'
            ));
        }
        $user = $this->getUserTable()->getUser($id);

        $form  = new UserForm();
        $form->bind($user);
        $form->get('submit')->setAttribute('value', 'Save');
        $properties = $this->getPropertyTable()->getActiveProperties();
        $pOptions = array();

        foreach($properties as $p){
            $pOptions[$p->id] = $p->abbreviation;
        }
        $roles = $this->getRoleTable()->fetchAll();
        $rOptions = array();
        foreach($roles as $role){
            $rOptions[$role->id] = $role->role;
        }

        $form->add(array(
            'type' => 'Zend\Form\Element\Select',
            'name' => 'property_id',
            'options' => array(
                'label' => 'Property',
                'value_options' => $pOptions,
            ),
            'attributes' => array(
                'value' => $user->property_id,
            ),
            ));

        $form->add(array(
            'type' => 'Zend\Form\Element\Select',
            'name' => 'role_id',
            'options' => array(
                'label' => 'Role',
                'value_options' => $rOptions,
            ),
            'attributes' => array(
                'value' => $user->role_id,
            ),
            ));


        $request = $this->getRequest();
        if ($request->isPost()) {
            $form->setInputFilter($user->getInputFilter());
            $form->setData($request->getPost());

            if ($form->isValid()) {
                $this->getUserTable()->editUser($form->getData());

                return $this->redirect()->toRoute('user');
            }
        }

        return array(
            'id' => $id,
            'form' => $form,
            'user' => $user
        );
    }

    public function passwordAction(){
        $id = (int) $this->params()->fromRoute('id', 0);

        $user = $this -> getUserTable() -> getUser($id);

        $form  = new UserForm();
        $form->get('submit')->setAttribute('value', 'Save');
        $form->bind($user);
        
        $request = $this->getRequest();
        if ($request->isPost()) {
            $form->setInputFilter($user->getInputFilter());
            $form->setData($request->getPost());

            if ($form->isValid()) {

                $password = $form -> getInputFilter() -> getValue('password');
                $vPassword = $form->getInputFilter()->getValue('vPassword');

                if($password == $vPassword){
                    $this->getUserTable()->updatePassword($form->getData());
                    return $this->redirect()->toRoute('user');
                } else {
                    echo 'Passwords do not match.';
                }
                
            }
        }

        return array(
            'id' => $id,
            'form' => $form,
            'user' => $user
        );
    }

    public function disableAction()
    {
        $id = (int) $this->params()->fromRoute('id', 0);
        if (!$id) {
            return $this->redirect()->toRoute('user');
        }

        $request = $this->getRequest();
        if ($request->isPost()) {
            $del = $request->getPost('del', 'No');

            if ($del == 'Yes') {
                $id = (int) $request->getPost('id');
                
                //disable brand
                $this->getUserTable()->disableUser($id);

            }
            return $this->redirect()->toRoute('user');
        }

        return array(
            'id'    => $id,
            'user' => $this->getUserTable()->getUser($id)
        );
    }

    public function enableAction(){
        $id = (int) $this->params()->fromRoute('id', 0);
        if (!$id) {
            return $this->redirect()->toRoute('user', array(
                'action' => 'add'
            ));
        }
        $user = $this->getUserTable()->getUser($id);

        $form  = new UserForm();
        $form->bind($user);
        $form->get('submit')->setAttribute('value', 'Save');
        $properties = $this->getPropertyTable()->getActiveProperties();
        $pOptions = array();

        foreach($properties as $p){
            $pOptions[$p->id] = $p->abbreviation;
        }
        $roles = $this->getRoleTable()->fetchAll();
        $rOptions = array();
        foreach($roles as $role){
            $rOptions[$role->id] = $role->role;
        }

        $form->add(array(
            'type' => 'Zend\Form\Element\Select',
            'name' => 'property_id',
            'options' => array(
                'label' => 'Property',
                'value_options' => $pOptions,
            ),
            'attributes' => array(
                'value' => $user->property_id,
            ),
            ));

        $form->add(array(
            'type' => 'Zend\Form\Element\Select',
            'name' => 'role_id',
            'options' => array(
                'label' => 'Role',
                'value_options' => $rOptions,
            ),
            'attributes' => array(
                'value' => $user->role_id,
            ),
            ));

        $request = $this->getRequest();
        if ($request->isPost()) {
            $form->setInputFilter($user->getInputFilter());
            $form->setData($request->getPost());

            if ($form->isValid()) {
                $this->getUserTable()->enableUser($form->getData());

                return $this->redirect()->toRoute('user');
            }
        }

        return array(
            'id' => $id,
            'user' => $user,
            'form' => $form,
        );
    }

    public function inactiveAction(){
        $propertyReturn = $this->getPropertyTable()->fetchAll();
        $properties = array();
        foreach($propertyReturn as $p){
            $properties[$p->id] = $p->abbreviation;
        }

        $roleReturn = $this->getRoleTable()->fetchAll();
        $roles = array();
        foreach($roleReturn as $r){
            $roles[$r->id] = $r->role;
        }

        $paginator = $this->getUserTable()->getInactiveUsers(true);
        $paginator->setCurrentPageNumber((int)$this->params()->fromQuery('page', 1));
        $paginator->setItemCountPerPage(10);

        return new ViewModel(array(
            'properties' => $properties,
            'paginator' => $paginator,
            'roles' => $roles
        ));

    }

    public function getBrandTable()
    {
        if (!$this->brandTable) {
            $sm = $this->getServiceLocator();
            $this->brandTable = $sm->get('Admin\Model\BrandTable');
        }
        return $this->brandTable;
    }

    public function getPropertyTable()
    {
        if(!$this->propertyTable){
            $sm = $this->getServiceLocator();
            $this->propertyTable = $sm->get('Admin\Model\PropertyTable');
        }
        return $this->propertyTable;
    }

    public function getUserTable()
    {
        if(!$this->userTable){
            $sm = $this->getServiceLocator();
            $this->userTable = $sm->get('Admin\Model\UserTable');
        }
        return $this->userTable;
    }

    public function getRoleTable()
    {
        if(!$this->roleTable){
            $sm = $this->getServiceLocator();
            $this->roleTable = $sm->get('Admin\Model\RoleTable');
        }
        return $this->roleTable;
    }
}