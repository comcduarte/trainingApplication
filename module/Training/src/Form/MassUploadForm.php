<?php
namespace Training\Form;

use Midnet\Form\AbstractBaseForm;
use Midnet\Form\Element\DatabaseSelectObject;
use Zend\Db\Adapter\AdapterAwareTrait;
use Zend\Form\Element\Select;
use Zend\Form\Element\Text;

class MassUploadForm extends AbstractBaseForm
{
    use AdapterAwareTrait;
    
    public function initialize()
    {
        parent::initialize();
        
        $this->add([
            'name' => 'SESSION',
            'type' => DatabaseSelectObject::class,
            'attributes' => [
                'class' => 'form-control',
                'id' => 'SESSION',
                'required' => 'true',
            ],
            'options' => [
                'label' => 'Auto-assign following class',
                'database_adapter' => $this->adapter,
                'database_table' => 'classes',
                'database_id_column' => 'UUID',
                'database_value_column' => 'CODE',
            ],
        ],['priority' => 100]);
        
        $this->add([
            'name' => 'FIELD',
            'type' => Select::class,
            'attributes' => [
                'class' => 'form-control',
                'id' => 'FIELD',
                'required' => 'true',
            ],
            'options' => [
                'label' => 'File Name -> Field',
                'value_options' => [
                    'EMP_NUM' => 'employee.emp_num',
                    'CODE' => 'classes.code',
                ],
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
                'label' => 'New Name of File Imported',
            ],
        ],['priority' => 100]);
        
        $this->remove('STATUS');
    }
}