<?php
namespace Employee\Model;

use Midnet\Model\DatabaseObject;
use Zend\InputFilter\InputFilter;

class EmployeeModel extends DatabaseObject
{
    public $FNAME;
    public $LNAME;
    public $EMAIL;
    public $EMP_NUM;
    public $DEPT;
    public $TIME_GROUP;
    public $TIME_SUBGROUP;
    
    public function __construct($adapter = NULL)
    {
        parent::__construct($adapter);
        $this->setTableName('employees');
    }
    
    public function create()
    {
        parent::create();
        
        if (!file_exists('./data/files/' . $this->UUID)) {
            mkdir('./data/files/' . $this->UUID, 0777, true);
        }
        
        return $this;
    }
    
    public function delete()
    {
        parent::delete();
        
        // TODO: Remove file upload directory and its contents.  Possibly Archive them.
        
        return true;
    }
    
    public function getInputFilter()
    {
        $inputFilter = new InputFilter();
        $inputFilter = parent::getInputFilter();
        
        $inputFilter->add([
            'name' => 'FILE',
            'required' => FALSE,
            'filters' => [
                [
                    'name' => 'filerenameupload',
                    'options' => [
                        'target'    => './data/files/' . $this->UUID,
                        'useUploadName' => TRUE,
                        'useUploadExtension' => TRUE,
                        'overwrite' => TRUE,
                        'randomize' => FALSE,
                    ],
                ],
            ],
            'validators' => [
                [
                    'name'    => 'FileMimeType',
                    'options' => [
                        'mimeType'  => ['application/pdf', 'text/plain']
                    ]
                ],
            ],
        ]);
        
        return $inputFilter;
    }
}