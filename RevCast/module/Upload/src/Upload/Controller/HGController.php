<?php
namespace Upload\Controller;

use Zend\Mvc\Controller\AbstractActionController;
use Upload\Form\UploadForm;
use Zend\Validator\File\Size;
use Zend\Session\Container;
use Upload\Model\Property;
use Upload\Model\Lnr;
use Upload\Model\Reportkey;

require_once '\public\Spreadsheet\Excel\Reader\reader.php';

//ReportType = 4

    class HGController extends AbstractActionController
    {

      protected $propertyTable;
      protected $reportkeyTable;
      protected $lnrTable;

      public function indexAction()
      { 
       error_reporting(0);
        $session = new Container('userData');
        $uId = $session -> userId;

        //Get File Data
        $fileSession = new Container('upload');
        $filename = $fileSession->filename;
        $path = 'C:\Program Files (x86)\Zend\Apache2\htdocs\RevCast\data\\';
        $inputFileName = $path . $filename;

        ///create reportKey
        $reportKey = new Reportkey();
        $fileNameExplode = explode('_', $filename);
        $monthName = substr($fileNameExplode[2], 0, 3);
        $month = date('m', strtotime($monthName));
        $year = date('Y', strtotime('20' . substr($fileNameExplode[2], 3, 2)));
        $fileDate = $year . '-' . $month . '-01';
        
        $property = $this -> getPropertyTable() -> getPropertybyAbb($fileNameExplode[0]);
       
        $reportKey -> property_id = $property -> id;
        $reportKey -> report_date = $fileDate;
        $reportKey -> upload_by = $uId;
        $reportKey -> report_type = 4;
        
        $rKey = $this -> getReportkeyTable() -> saveReport($reportKey);
        
        $row = 1;
        if (($handle = fopen($inputFileName, "r")) !== FALSE) {
            while (($data = fgetcsv($handle, 1000, "\n")) !== FALSE) {
                $num = count($data);
                $row++;
                for ($c=0; $c < $num; $c++) {
                  $rowData = preg_split('/,/', $data[$c]);
                    $lnr = new Lnr();
                    $lnr -> reportkey = $rKey;
                    $lnr -> date = $fileDate;
                    $lnr -> market_category = "";
                    $lnr -> market_prefix = "";
                    $lnr -> market_segment = "";
                    $lnr -> rate_code = $rowData[42];
                    $lnr -> rate_program = $rowData[42];
                    $lnr -> rooms = preg_replace('/\s/', '', preg_replace('/\"/', '', $rowData[49]));
                    $lnr -> adr = preg_replace('/\"/', '', preg_replace('/\s/', '', preg_replace('/\$/', '', $rowData[51])));
                    $lnr -> revenue = preg_replace('/\"/', '', preg_replace('/\s/', '', preg_replace('/\$/', '', $rowData[43])));
                    $this -> getLnrTable() -> saveReport($lnr);
                }
            }

            fclose($handle);
        }
         return $this->redirect()->toRoute('report');
      }
      Public function getPropertyTable()
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