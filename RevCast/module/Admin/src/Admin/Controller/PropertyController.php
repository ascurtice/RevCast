<?php
namespace Admin\Controller;

use Zend\Mvc\Controller\AbstractActionController;
use Zend\View\Model\ViewModel;
use Admin\Model\Property;
use Admin\Form\PropertyForm;

class PropertyController extends AbstractActionController
{
    protected $brandTable;
    protected $propertyTable;
    protected $userTable;

    public function indexAction()
    {
        $properties = $this->getPropertyTable()->fetchAll();
        $activeProperties = array();
        $inactiveProperties = array();
        $brands = $this->getBrandTable()->fetchAll();
        $brandArray = array();

        foreach($properties as $p){
            if ($p->active == 1){
                array_push($activeProperties, $p);
            } else {
                array_push($inactiveProperties, $p);
            }
        }

        foreach($brands as $b){
            $brandArray[$b->id] = $b->brand;
        }

        return new ViewModel(array(
            'activeProperties' => $activeProperties,
            'inactiveProperties' => $inactiveProperties,
            'brands' => $brandArray,
        ));
    }

    public function addAction()
    {
        $form = new PropertyForm();
        $form->get('submit')->setValue('Add');
        $brands = $this->getBrandTable()->getActiveBrands();
        $options = array();
        foreach($brands as $b){
            $options[$b->id] = $b->brand;
         }

        $form->add(array(
            'type' => 'Zend\Form\Element\Select',
            'name' => 'brand_id',
            'options' => array(
                'label' => 'Brand',
                'value_options' => $options,
            )));

        $request = $this->getRequest();

        if ($request->isPost()) {
            $property = new Property();
            $form->setInputFilter($property->getInputFilter());
            $form->setData($request->getPost());

            if ($form->isValid()) {
                $property->exchangeArray($form->getData());
                $this->getPropertyTable()->saveProperty($property);

                return $this->redirect()->toRoute('property');
            }
        }
        return array('form' => $form);
    }

    public function editAction()
    {
        $id = (int) $this->params()->fromRoute('id', 0);
        if (!$id) {
            return $this->redirect()->toRoute('property', array(
                'action' => 'add'
            ));
        }
        $property = $this->getPropertyTable()->getProperty($id);

        $form  = new PropertyForm();
        $form->bind($property);
        $form->get('submit')->setAttribute('value', 'Save');

        $brands = $this->getBrandTable()->getActiveBrands();
        $options = array();
        foreach($brands as $b){
            $options[$b->id] = $b->brand;
        }

        $form->add(array(
            'type' => 'Zend\Form\Element\Select',
            'name' => 'brand_id',
            'attributes' => array(
                'value' => $property->brand_id,
            ),
            'options' => array(
                'label' => 'Brand',
                'value_options' => $options,
        )));

        $request = $this->getRequest();
        if ($request->isPost()) {
            $form->setInputFilter($property->getInputFilter());
            $form->setData($request->getPost());

            if ($form->isValid()) {
                $this->getPropertyTable()->saveProperty($form->getData());

                return $this->redirect()->toRoute('property');
            }
        }

        return array(
            'id' => $id,
            'form' => $form,
        );
    }

    public function disableAction()
    {
        $id = (int) $this->params()->fromRoute('id', 0);
        if (!$id) {
            return $this->redirect()->toRoute('brand');
        }

        $request = $this->getRequest();
        if ($request->isPost()) {
            $del = $request->getPost('del', 'No');

            if ($del == 'Yes') {
                $id = (int) $request->getPost('id');
                $this->getPropertyTable()->disableProperty($id);
                $this -> getUserTable() -> disableUsersbyProperty($id);
            }
            return $this->redirect()->toRoute('property');
        }
        $property = $this->getPropertyTable()->getProperty($id);
        return array(
            'id'    => $id,
            'brand' => $this->getBrandTable()->getBrand($property->brand_id),
            'property' => $this->getPropertyTable()->getProperty($id)
        );
    }

    public function enableAction()
    {
        $id = (int) $this->params()->fromRoute('id', 0);
        if (!$id) {
            return $this->redirect()->toRoute('property');
        }
        $this->getPropertyTable()->enableProperty($id);
        return $this->redirect()->toRoute('property');
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
        if (!$this->propertyTable) {
            $sm = $this->getServiceLocator();
            $this->propertyTable = $sm->get('Admin\Model\PropertyTable');
        }
        return $this->propertyTable;
    }

    public function getUserTable(){
        if(!$this->userTable){
            $sm = $this->getServiceLocator();
            $this->userTable = $sm->get('Admin\Model\UserTable');
        }
        return $this->userTable;
    }
}