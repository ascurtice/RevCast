<?php
namespace Report\Controller;

use Zend\Mvc\Controller\AbstractActionController;
use Zend\View\Model\ViewModel;
use Admin\Model\Property;
use Admin\Model\Brand;
use Zend\Session\Container;
use Upload\Model\Lnr;

    class ReportController extends AbstractActionController
    {
      protected $brandTable;
      protected $propertyTable;
      protected $lnrTable;
      protected $reportkeyTable;

        public function indexAction()
        { 
          $brands = $this -> getBrandTable() -> fetchAll();
          foreach($brands as $brand){
            $brandArray[$brand -> id] = $brand -> brand;
          }
          return new ViewModel(array(
            'brands' => $brandArray,
            'properties' => $this -> getPropertyTable() -> getActiveProperties(),
          ));
        }

        public function generateAction(){
          //Get id of property from parameters
          $id = (int) $this->params()->fromRoute('id', 0);
          if (!$id) {
              return $this->redirect()->toRoute('report', array(
                  'action' => 'index'
              ));
          }

          //Get Property Information
          $property = $this -> getPropertyTable() -> getProperty($id);
          $brand = $this -> getBrandTable() -> getBrand($property -> brand_id);

          $error = null;
          
          /***********************MTD***********************/
          //Get Report keys for current year and previous Year
          $cymtdKey = $this -> getReportKey($id, date('m'), 'cy');
          $pymtdKey = $this -> getReportKey($id, date('m'), 'py');

          $MTDFinal = array();

          //Get report data and keys
          $CYArray = array();
          $CYKey = array();
          if($cymtdKey){
            $CYArray = $this -> getReportData($cymtdKey);
            $CYKey = $this -> getRateCodeKeys($CYArray);
          }

          $PYArray = array();
          $PYKey = array();
          if($pymtdKey){
            $PYArray = $this -> getReportData($pymtdKey);
            $PYKey = $this -> getRateCodeKeys($PYArray);
          }

          //Add both sets of keys to an array and then parse out duplicates
          $MTDrateCodes = array();
          foreach($CYKey as $c){
            array_push($MTDrateCodes, $c);
          }
          foreach($PYKey as $p){
            array_push($MTDrateCodes, $p);
          }
          
          $MTDrateCodes = array_unique($MTDrateCodes);          
          
          //Create final data set
          $MTDFinal = $this -> getFinalData($CYArray, $PYArray, $MTDrateCodes);
          $MTDRateCodeTotals  = $this -> getRateCodeCalculations($MTDFinal);
          $MTDTotals = $this -> getFinalCalculations($MTDRateCodeTotals);

          /***********************YTD***********************/
          //Get Report Keys for all months within range for current year and previous year
          $cykeys = array();
          $pykeys = array();
          for($i = 1; $i <= date('m'); $i++){
            $ckey = $this -> getReportKey($id, $i, 'cy');
            array_push($cykeys, $ckey);
            $pkey = $this -> getReportKey($id, $i, 'py');
            array_push($pykeys, $pkey);
          }

          //Get report data and keys
          $CYData = array();
          $PYData = array();
          
          foreach($cykeys as $c){
            if($c != 0){
              $data = $this -> getReportData($c);
              foreach($data as $d){
                array_push($CYData, $d);
              }
            }
          }
          $CYTDKeys = $this -> getRateCodeKeys($CYData);

          foreach($pykeys as $p){
            if($p != 0){
              $pdata = $this -> getReportData($p);
              foreach($pdata as $pd){
                array_push($PYData, $pd);
              }
            }
          }
          $PYTDKeys = $this -> getRateCodeKeys($PYData);
          
          //Add both sets of keys to an array and then parse out duplicates
          $YTDrateCodes = array();
          foreach($CYTDKeys as $c){
            array_push($YTDrateCodes, $c);
          }
          foreach($PYTDKeys as $p){
            array_push($YTDrateCodes, $p);
          }
          $YTDrateCodes = array_unique($YTDrateCodes); 

          //Create final data set
          $YTDFinal = $this -> getFinalData($CYData, $PYData, $YTDrateCodes);
          $YTDRateCodeTotals  = $this -> getRateCodeCalculations($YTDFinal);
          $YTDTotals = $this -> getFinalCalculations($YTDRateCodeTotals);

          
          return new ViewModel(array(
            'property' => $property,
            'brand' => $brand,
            'MTDData' => $MTDFinal,
            'MTDTotals' => $MTDRateCodeTotals,
            'finalMTD' => $MTDTotals,
            'YTDData' => $YTDFinal,
            'YTDTotals' => $YTDRateCodeTotals,
            'finalYTD' => $YTDTotals,
          ));
        }

        public function getReportKey($property, $month, $year){
          $month = sprintf("%02s", $month);
          $reportKey = 0;
          $keyDate = null;
          
          if ($year == 'cy'){
            $keyDate = date('Y') . '-' . $month . '-01';
          } else{
            $keyDate = date('Y',strtotime('-1 year')) . '-' . $month . '-01';
          }

          $returnKey = $this -> getReportkeyTable() -> getReportkeys($property, $keyDate);

          foreach($returnKey as $rk){
            $reportKey = $rk -> id;
          }

          return $reportKey;
        }
        public function getReportData($reportKeys){
          $reportData = array();
          $rateCodes = array();
          if($reportKeys != null && $reportKeys != 0){
            $returnData = $this -> getLnrTable() -> getData($reportKeys);
            foreach($returnData as $rd){
              $data = array(
                'date' => $rd -> date,
                'rc' => $rd -> rate_code,
                'rp' => $rd -> rate_program,
                'rooms' => $rd -> rooms,
                'adr' => $rd -> adr,
                'revenue' => $rd -> revenue
              );
              array_push($reportData, $data);

              $rate = $rd -> rate_code . '-' . $rd -> rate_program;
              array_push($rateCodes, $rate);
            }
          }
          return $reportData;
        } 

        public function getFinalData($cyA, $pyA, $rcKey){
          $final = array();

          foreach($rcKey as $rc){
            $final[$rc] = array(
              'cy' => array(),
              'py' => array()
            );
          }

          foreach($cyA as $cy){
            $key = $cy['rc'] . '-' . $cy['rp'];
            if(array_key_exists($key, $final)){
              array_push($final[$key]['cy'], $cy);
            }
          }
          foreach($pyA as $py){
            $key = $py['rc'] . '-' . $py['rp'];
            if(array_key_exists($key, $final)){
              array_push($final[$key]['py'], $py);
            }
          }

          return $final;
        }

        public function getRateCodeKeys($data){
          $rateCodeKeys = array();

          foreach($data as $d){
            array_push($rateCodeKeys, $d['rc'] . '-' . $d['rp']);
          }

          return $rateCodeKeys;

        }

        public function getRateCodeCalculations($data){
          $totals = array();
          
          foreach($data as $key=>$value){
            //Current Year
            $roomsTotal = 0;
            $adrTotal = 0;
            $revenueTotal = 0;
            for($i = 0; $i < count($value['cy']); $i++){
              $roomsTotal += $value['cy'][$i]['rooms'];
              $adrTotal += $value['cy'][$i]['adr'];
              $revenueTotal += $value['cy'][$i]['revenue'];
            }

            //Previous Year
            $PYroomsTotal = 0;
            $PYadrTotal = 0;
            $PYrevenueTotal = 0;
            for($i = 0; $i < count($value['py']); $i++){
              $PYroomsTotal += $value['py'][$i]['rooms'];
              $PYadrTotal += $value['py'][$i]['adr'];
              $PYrevenueTotal += $value['py'][$i]['revenue'];
            }

            $totals[$key] = array(
              'rooms' => $roomsTotal,
              'adr' => $adrTotal,
              'rev' => $revenueTotal,
              'pyrooms' => $PYroomsTotal,
              'pyadr' => $PYadrTotal,
              'pyrev' => $PYrevenueTotal,
              'vrooms' => $roomsTotal - $PYroomsTotal,
              'vadr' => $adrTotal - $PYadrTotal,
              'vrev' => $revenueTotal - $PYrevenueTotal,
            );
          }

          return $totals;
        }

        public function getFinalCalculations($data){
          $rooms = 0;
          $adr = 0;
          $rev = 0;
          $pyrooms = 0;
          $pyadr = 0;
          $pyrev = 0;
          $vrooms = 0;
          $vadr = 0;
          $vrev = 0;

          foreach($data as $totals){
            $rooms += $totals['rooms'];
            $adr += $totals['adr'];
            $rev += $totals['rev'];
            $pyrooms += $totals['pyrooms'];
            $pyadr += $totals['pyadr'];
            $pyrev += $totals['pyrev'];
            $vrooms += $totals['vrooms'];
            $vadr += $totals['vadr'];
            $vrev += $totals['vrev'];
          } 

          $finalCalculations = array(
            'rooms' => $rooms,
            'adr' => $adr,
            'rev' => $rev,
            'pyrooms' => $pyrooms,
            'pyadr' => $pyadr,
            'pyrev' => $pyrev,
            'vrooms' => $vrooms,
            'vadr' => $vadr,
            'vrev' => $vrev,
          );

          return $finalCalculations;
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
        public function getLnrTable()
        {
            if (!$this->lnrTable) {
                $sm = $this->getServiceLocator();
                $this->lnrTable = $sm->get('Upload\Model\LnrTable');
            }
            return $this->lnrTable;
        }
        public function getReportkeyTable()
        {
            if (!$this->reportkeyTable) {
                $sm = $this->getServiceLocator();
                $this->reportkeyTable = $sm->get('Upload\Model\ReportkeyTable');
            }
            return $this->reportkeyTable;
        }
    }