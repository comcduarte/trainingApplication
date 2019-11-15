<?php
namespace Training\Model;

use Midnet\Model\DatabaseObject;

class TrainingModel extends DatabaseObject
{
    public $CODE;
    public $NAME;
    public $CATEGORY;
    public $DATE_SCHEDULE;
    
    public function __construct($adapter = NULL)
    {
        parent::__construct($adapter);
        $this->setTableName('classes');
    }
}