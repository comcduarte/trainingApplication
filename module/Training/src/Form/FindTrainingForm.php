<?php
namespace Training\Form;

use Midnet\Form\Element\DatabaseSelectObject;
use Zend\Db\Adapter\AdapterAwareTrait;
use Zend\Form\Form;
use Zend\Form\Element\Csrf;
use Zend\Form\Element\Submit;

class FindTrainingForm extends Form
{
    use AdapterAwareTrait;
    
    public function initialize()
    {
        $this->add([
            'name' => 'NAME',
            'type' => DatabaseSelectObject::class,
            'attributes' => [
                'class' => 'form-control',
                'id' => 'NAME',
                'required' => 'true',
            ],
            'options' => [
                'label' => 'Search for Class',
                'database_adapter' => $this->adapter,
                'database_table' => 'list_classes',
                'database_id_column' => 'NAME',
                'database_value_column' => 'NAME',
            ],
        ],['priority' => 100]);
        
        $this->add(new Csrf('SECURITY'));
        
        $this->add([
            'name' => 'SUBMIT',
            'type' => Submit::class,
            'attributes' => [
                'value' => 'Search',
                'class' => 'btn btn-primary mt-2',
                'id' => 'SUBMIT',
            ],
        ],['priority' => 0]);
    }
}