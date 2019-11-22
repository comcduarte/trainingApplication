<?php
namespace Employee\Model;

use Midnet\Model\DatabaseObject;

class DepartmentModel extends DatabaseObject
{
    public $CODE;
    public $NAME;
    public $PARENT;
    
    public function __construct($adapter = NULL)
    {
        parent::__construct($adapter);
        $this->setTableName('departments');
    }
}