<?php
namespace Upload\Controller;

use Zend\Mvc\Controller\AbstractActionController;
use Upload\Form\UploadForm;
use Upload\Model\Upload;
use Zend\Validator\File\Size;
use Zend\Session\Container;

    class UploadController extends AbstractActionController
    {
        public function indexAction()
        {
            $form = new UploadForm();
            $request = $this->getRequest();
            if ($request->isPost()) {

                $upload = new Upload();
                $form->setInputFilter($upload->getInputFilter());

                $nonFile = $request->getPost()->toArray();
                $File = $this->params()->fromFiles('fileupload');
                $data = array_merge(
                    $nonFile,
                    array('fileupload'=> $File['name'])
                );

                $form->setData($data);

                if ($form->isValid()) {

                    $size = new Size(array('min'=>200)); //minimum bytes filesize

                    $adapter = new \Zend\File\Transfer\Adapter\Http();
                    $adapter->setValidators(array($size), $File['name']);
                    if (!$adapter->isValid()){
                        $dataError = $adapter->getMessages();
                        $error = array();
                        foreach($dataError as $key=>$row)
                        {
                            $error[] = $row;
                        }
                        $form->setMessages(array('fileupload'=>$error ));
                    } else {
                        $adapter->setDestination('C:\Program Files (x86)\Zend\Apache2\htdocs\RevCast\data');
                        if ($adapter->receive($File['name'])) {
                            $upload->exchangeArray($form->getData());

                            $session = new Container('upload');
                            $session->filename = $upload->fileupload;

                            $mt = '/.*MT[_].*\.xls.*/i';
                            $ihg = '/.*IH[_].*\.txt/i';
                            $hi = '/.*HI[_].*\.xls/i';
                            $hg = '/.*HG[_].*\.csv/i';
                            
                            if(preg_match($mt, $upload->fileupload)){
                                return $this->redirect()->toRoute('mt', array(
                                    'action' => 'index'
                                ));
                            }

                            if(preg_match($ihg, $upload->fileupload)){
                                return $this->redirect()->toRoute('ihg', array(
                                    'action' => 'index'
                                ));
                            }

                            if(preg_match($hi, $upload->fileupload)){
                                return $this->redirect()->toRoute('hi', array(
                                    'action' => 'index'
                                ));
                            }

                            if(preg_match($hg, $upload->fileupload)){
                                return $this->redirect()->toRoute('hg', array(
                                    'action' => 'index'
                                ));
                            }
                            
                            echo $upload->fileupload;
                        }
                    }
                }
            }

            return array('form' => $form);
        }
    }