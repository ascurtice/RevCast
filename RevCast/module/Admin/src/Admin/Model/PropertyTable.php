<?php
namespace Admin\Model;

use Zend\Db\TableGateway\TableGateway;
use Zend\Db\Sql\Predicate\Like;
use Zend\Db\Sql\Select;

class PropertyTable
{
    protected $tableGateway;

    public function __construct(TableGateway $tableGateway)
    {
        $this->tableGateway = $tableGateway;
    }

    public function fetchAll()
    {
        $resultSet = $this->tableGateway->select();
        return $resultSet;
    }

    public function getProperty($id)
    {
        $id  = (int) $id;
        $rowset = $this->tableGateway->select(array('id' => $id));
        $row = $rowset->current();
        if (!$row) {
            throw new \Exception("Could not find row $id");
        }
        return $row;
    }

    public function getPropertiesByBrand($bid){
        $resultSet = $this->tableGateway->select(array('brand_id' => $bid));
        return $resultSet;
    }

    public function saveProperty(Property $property)
    {
        $active = 1;
        $data = array(
            'brand_id' => $property->brand_id,
            'location' => $property->location,
            'abbreviation' => $property->abbreviation,
            'active' => $active,
        );

        $id = (int)$property->id;
        if ($id == 0) {
            $this->tableGateway->insert($data);
        } else {
            if ($this->getProperty($id)) {
                $this->tableGateway->update($data, array('id' => $id));
            } else {
                throw new \Exception('Form id does not exist');
            }
        }
    }

    public function getPropertybyAbb($p){
        $rowset = $this->tableGateway->select(array('abbreviation' => $p));
        $row = $rowset->current();
        
        return $row;
    }

    public function getActiveProperties(){
        $resultset = $this->tableGateway->select(array('active' => 1));

        return $resultset;
    }

    public function disableProperty($id)
    {
        $active = 0;
        $data = array(
            'active' => $active,
        );

        if ($this->getProperty($id)) {
            $this->tableGateway->update($data, array('id' => $id));
        } else {
            throw new \Exception('Form id does not exist');
        }
    }

    public function disablePropertiesbyBrand($bid){
        $active = 0;
        $data = array(
            'active' => $active,
        );

        $this->tableGateway->update($data, array('brand_id' => $bid));
    }

    public function enablePropertiesbyBrand($bid){
        $active = 1;
        $data = array(
            'active' => $active,
        );

        $this->tableGateway->update($data, array('brand_id' => $bid));
    }

    public function enableProperty($id)
    {
        $active = 1;
        $data = array(
            'active' => $active,
        );

        if ($this->getProperty($id)) {
            $this->tableGateway->update($data, array('id' => $id));
        } else {
            throw new \Exception('Form id does not exist');
        }
    }
}