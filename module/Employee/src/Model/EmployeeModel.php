<?php
namespace Employee\Model;

use Midnet\Model\DatabaseObject;

class EmployeeModel extends DatabaseObject
{
    public $FNAME;
    public $LNAME;
    public $EMAIL;
    public $EMP_NUM;
    public $DEPT;
    
    public function __construct($adapter = NULL)
    {
        parent::__construct($adapter);
        $this->setTableName('employees');
    }
}