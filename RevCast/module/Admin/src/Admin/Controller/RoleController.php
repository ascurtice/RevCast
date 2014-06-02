<?php
namespace Admin\Controller;

use Zend\Mvc\Controller\AbstractActionController;
use Zend\View\Model\ViewModel;
use Admin\Model\Role;
use Admin\Form\RoleForm;

class RoleController extends AbstractActionController
{
    protected $roleTable;

    public function indexAction()
    {
        $allRoles = $this->getRoleTable()->fetchAll();
        $roles = array();
        foreach($allRoles as $role){
            $edit = false;
            if ($role->id > 2){
                $edit = true;
            }
            array_push($roles, array($role, $edit));
        }


        return new ViewModel(array(
            'roles' => $roles,
        ));
    }

    public function addAction()
    {
        $form = new RoleForm();
        $form->get('submit')->setValue('Add');

        $request = $this->getRequest();

        if ($request->isPost()) {
            $role = new Role();
            $form->setInputFilter($role->getInputFilter());
            $form->setData($request->getPost());

            if ($form->isValid()) {
                $role->exchangeArray($form->getData());
                $this->getRoleTable()->saveRole($role);

                return $this->redirect()->toRoute('role');
            }
        }
        return array('form' => $form);
    }

    public function editAction()
    {
        $id = (int) $this->params()->fromRoute('id', 0);
        if (!$id) {
            return $this->redirect()->toRoute('role', array(
                'action' => 'add'
            ));
        }
        $role = $this->getRoleTable()->getRole($id);

        $form  = new RoleForm();
        $form->bind($role);
        $form->get('submit')->setAttribute('value', 'Save');

        $request = $this->getRequest();
        if ($request->isPost()) {
            $form->setInputFilter($role->getInputFilter());
            $form->setData($request->getPost());

            if ($form->isValid()) {
                $this->getRoleTable()->saveRole($form->getData());

                return $this->redirect()->toRoute('role');
            }
        }

        return array(
            'id' => $id,
            'form' => $form,
        );
    }

    public function getRoleTable()
    {
        if (!$this->roleTable) {
            $sm = $this->getServiceLocator();
            $this->roleTable = $sm->get('Admin\Model\RoleTable');
        }
        return $this->roleTable;
    }

}