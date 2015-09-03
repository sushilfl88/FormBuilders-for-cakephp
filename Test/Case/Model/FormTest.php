<?php
class FormTest extends CakeTestCase
{
	private $Form;
 	public function setUp(){
        $this->Form = ClassRegistry::init('Formbuilders.Form');
        parent::setUp();
    }	

    
    
    public function test_getFormAssociationData()
    {
    	$type = 8;
    	$hid = 284;
    	
    	$expectedResult = array(
								(int) 0 => array(
									'FormAssociation' => array(
										'id' => '214'
									),
									'Form' => array(
										'label' => 'BPTest'
									)
								),
								(int) 1 => array(
									'FormAssociation' => array(
										'id' => '223'
									),
									'Form' => array(
										'label' => 'BPTestTwo'
									)
								)
							);
    	$getAllFormData = $this->Form->getFormAssociationData($type, $hid);
    	$this->assertEquals($expectedResult, $getAllFormData);
    }
    
    
 	
    

}
