<?php
namespace Employee\Form;

use Midnet\Form\AbstractBaseForm;
use Zend\Db\Adapter\AdapterAwareTrait;
use Zend\Form\Element\Text;
use Midnet\Form\Element\DatabaseSelectObject;

class DepartmentForm extends AbstractBaseForm
{
    use AdapterAwareTrait;
    
    public function initialize()
    {
        parent::initialize();
        
        $this->add([
            'name' => 'CODE',
            'type' => Text::class,
            'attributes' => [
                'id' => 'CODE',
                'class' => 'form-control',
                'required' => 'true',
            ],
            'options' => [
                'label' => 'Department Code',
            ],
        ],['priority' => 100]);
        
        $this->add([
            'name' => 'NAME',
            'type' => Text::class,
            'attributes' => [
                'id' => 'NAME',
                'class' => 'form-control',
                'required' => 'true',
            ],
            'options' => [
                'label' => 'Department Name',
            ],
        ],['priority' => 100]);
        
        $this->add([
            'name' => 'PARENT',
            'type' => DatabaseSelectObject::class,
            'attributes' => [
                'id' => 'PARENT',
                'class' => 'form-control',
            ],
            'options' => [
                'label' => 'Parent Department',
                'database_adapter' => $this->adapter,
                'database_table' => 'departments',
                'database_id_column' => 'UUID',
                'database_value_column' => 'NAME',
            ],
        ],['priority' => 100]);
        
    }
}