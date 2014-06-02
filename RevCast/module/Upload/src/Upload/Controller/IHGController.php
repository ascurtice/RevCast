<?php
namespace Upload\Controller;

use Zend\Mvc\Controller\AbstractActionController;
use Upload\Form\UploadForm;
use Zend\Validator\File\Size;
use Zend\Session\Container;
use Zend\Config\Reader\Xml;
use Upload\Model\Property;
use Upload\Model\Lnr;
use Upload\Model\Reportkey;

class IHGController extends AbstractActionController
  {
    protected $propertyTable;
    protected $reportkeyTable;
    protected $lnrTable;

    public function indexAction()
    { 
      $session = new Container('userData');
      $uId = $session -> userId;

      $sessionUpload = new Container('upload');
      $filename = $sessionUpload->filename;
      $path = 'C:\Program Files (x86)\Zend\Apache2\htdocs\RevCast\data\\';

      if($filename){
        $fileNameExplode = explode('_', $filename);
        $monthName = substr($fileNameExplode[2], 0, 3);
        $month = date('m', strtotime($monthName));
        $year = date('Y', strtotime('20' . substr($fileNameExplode[2], 3, 2)));
        $fileDate = $year . '-' . $month . '-01';

        $property = $this -> getPropertyTable() -> getPropertybyAbb($fileNameExplode[0]);
        
        $reportKey = new Reportkey();
        $reportKey -> property_id = $property -> id; 
        $reportKey -> report_date = $fileDate; 
        $reportKey -> upload_by = $uId; 
        $reportKey -> report_type = 2; 

        $rKey = $this -> getReportkeyTable() -> saveReport($reportKey);
        
        $fileContents = file_get_contents($path . $filename);
        $fileContentsArray = preg_split('/\n/', $fileContents);
        foreach($fileContentsArray as $line){
            $split = preg_split('/\t/', $line);
            if((count($split) > 13) && ($split[2] == 'Market Group')){
                if(($split[3] == 'E') || ($split[3] == 'G') || ($split[3] == 'L') || ($split[3] == 'P')){
                    $lnr = new Lnr();
                    $lnr -> reportkey = $rKey;
                    $lnr -> date = $fileDate;
                    $lnr -> market_category = $split[2];
                    $lnr -> market_segment = $split[3];
                    $lnr -> market_prefix = $split[4];
                    $lnr -> rate_code = $split[5];
                    $lnr -> rate_program = $split[6];
                    $lnr -> rooms = $split[12];
                    $lnr -> adr = $split[14];
                    $lnr -> revenue = number_format($split[13], 2, '.', '');
                    $this -> getLnrTable() -> saveReport($lnr);
                }
            }
        }
      }
      return $this->redirect()->toRoute('report');
    }

    public function getPropertyTable()
    {
        if(!$this->propertyTable){
            $sm = $this->getServiceLocator();
            $this->propertyTable = $sm->get('Admin\Model\PropertyTable');
        }
        return $this->propertyTable;
    }

    public function getReportkeyTable()
     {
        if(!$this->reportkeyTable){
            $sm = $this->getServiceLocator();
            $this->reportkeyTable = $sm->get('Upload\Model\ReportkeyTable');
        }
        return $this->reportkeyTable;
    }

    public function getLnrTable()
    {
        if(!$this->lnrTable){
            $sm = $this->getServiceLocator();
            $this->lnrTable = $sm->get('Upload\Model\LnrTable');
        }
        return $this->lnrTable;
    }
  }