<?php
require_once(dirname(__FILE__) . '/../testconfig.php');
require_once(SIMPLETEST_PATH . 'autorun.php');
require_once(LF_BASE_PATH . "/lfbase/Loader.php");

class TestOfEntryDTO extends UnitTestCase {

	function testEncode_EntryAndSense_JsonCorrect() {
		$entry = \dto\EntryDTO::create("guid0");
		$entry->setEntry(\libraries\lfdictionary\dto\MultiText::create('fr', 'form1'));
		
		$sense = new \dto\Sense();
		$sense->setDefinition(\libraries\lfdictionary\dto\MultiText::create('en', 'definition1'));
		$sense->setSemanticDomainName('semantic-domain-ddp4');
		$sense->setSemanticDomainValue('2.1 Body');
		$sense->addExample(\dto\Example::create(
			\libraries\lfdictionary\dto\MultiText::create('en', 'example1'),
			\libraries\lfdictionary\dto\MultiText::create('fr', 'translation1')
		));
		
		$entry->addSense($sense);
		
		$result = json_encode($entry->encode());
		
		$this->assertEqual('{"guid":"guid0","mercurialSHA":null,"entry":{"fr":"form1"},"senses":[{"definition":{"en":"definition1"},"POS":"","examples":[{"example":{"en":"example1"},"translation":{"fr":"translation1"}}],"SemDomValue":"2.1 Body","SemDomName":"semantic-domain-ddp4"}]}', $result);
	}
	
	function testCreateFromArray_Sample_Correct() {
		$entry = \dto\EntryDTO::create("guid0");
		$entry->setEntry(\libraries\lfdictionary\dto\MultiText::create('fr', 'form1'));
		
		$sense = new \dto\Sense();
		$sense->setDefinition(\libraries\lfdictionary\dto\MultiText::create('en', 'definition1'));
		$sense->setPartOfSpeech('Noun');
		$sense->setSemanticDomainName('semantic-domain-ddp4');
		$sense->setSemanticDomainValue('2.1 Body');
		$sense->addExample(\dto\Example::create(
			\libraries\lfdictionary\dto\MultiText::create('en', 'example1'),
			\libraries\lfdictionary\dto\MultiText::create('fr', 'translation1')
		));
		
		$entry->addSense($sense);
				
		$value = $entry->encode();
		
		$v = \dto\EntryDTO::createFromArray($value);
		
		$this->assertEqual('guid0', $v->_guid);
		$this->assertEqual(array('fr' => 'form1'), $v->_entry->getAll());
		$this->assertEqual(array('en' => 'definition1'), $v->_senses[0]->_definition->getAll());
		$this->assertEqual('Noun', $v->_senses[0]->_partOfSpeech);
		$this->assertEqual(1, count($v->_senses[0]->_examples));
		$this->assertEqual(array('en' => 'example1'), $v->_senses[0]->_examples[0]->_example->getAll());
		$this->assertEqual(array('fr' => 'translation1'), $v->_senses[0]->_examples[0]->_translation->getAll());
		$this->assertEqual('semantic-domain-ddp4', $v->_senses[0]->_semanticDomainName);
		$this->assertEqual('2.1 Body', $v->_senses[0]->_semanticDomainValue);
				
		
	}

}

?>