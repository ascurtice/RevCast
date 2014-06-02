<?php
namespace Upload\Model;

use Zend\Db\TableGateway\TableGateway;
use Zend\Db\Sql\Select; 

class ReportkeyTable
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

    public function getReport($id)
    {
        $id  = (int) $id;
        $rowset = $this->tableGateway->select(array('id' => $id));
        $row = $rowset->current();
        if (!$row) {
            throw new \Exception("Could not find row $id");
        }
        return $row;
    }

    public function getReportsbyUser($id){
        $resultSet = $this -> tableGateway -> select(array('upload_by' => $id));
        return $resultSet;
    }

    public function getReportkeys($propertyId, $reportDate){
        $resultSet = $this->tableGateway->select(array('property_id' => $propertyId, 'report_date' => $reportDate));
        return $resultSet;
    }


    public function saveReport(Reportkey $report)
    {
        $now = date('Y-m-d H:i:s');
        $data = array(
            'property_id' => $report->property_id,
            'report_date' => $report->report_date,
            'timestamp' => $now,
            'upload_by' => $report->upload_by,
            'report_type' => $report->report_type,
        );
        $id = (int)$report->id;
        if ($id == 0) {
            $this->tableGateway->insert($data);
            $id = $this->tableGateway->lastInsertValue;
            return $id; 
        } else {
            if ($this->getReport($id)) {
                $this->tableGateway->update($data, array('id' => $id));
            } else {
                throw new \Exception('Form id does not exist');
            }
        }
    }

    public function getLastInsertedValue(){
        $resultSet = $this -> tableGateway -> select();
        
        foreach($resultSet as $result){
            echo $result -> id;
        }

        \Zend\Debug\Debug::dump($resultSet); die;

        return ($count + 1);
    }
}