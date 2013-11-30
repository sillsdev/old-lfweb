<?php

use models\lex\LexEntryModel;
use models\lex\LexEntryListModel;
use models\lex\Example;
use models\lex\MultiText;
use models\lex\Sense;

require_once(dirname(__FILE__) . '/../../TestConfig.php');
require_once(SIMPLETEST_PATH . 'autorun.php');
require_once(TEST_PATH . 'common/MongoTestEnvironment.php');

require_once(SourcePath . "models/ProjectModel.php");
require_once(SourcePath . "models/lex/LexEntryModel.php");

class TestLexEntryModel extends UnitTestCase {
	
	function testLexEntryModel_CRUD_Works() {
		$e = new MongoTestEnvironment();
		$e->clean();
		$project = $e->createProject(LF_TESTPROJECT);
	
		// List
		$list = new LexEntryListModel($project);
		$list->read();
		$this->assertEqual(0, $list->count);
	
		// Create
		$entry = new LexEntryModel($project);
		$lexeme1 = MultiText::create('fr', 'Some form');
		$entry->lexeme = $lexeme1;
		$sense = new Sense();
		$sense->definition = MultiText::create('en', 'Some definition');
		$sense->semanticDomainName = 'semantic-domain-ddp4';
		$sense->semanticDomainValue = '2.1 Body';
		$sense->examples->append(Example::create(
			MultiText::create('en', 'Some example'),
			MultiText::create('fr', 'Some translation')
		));
		$entry->senses->append($sense);
		$id = $entry->write();
		$this->assertNotNull($id);
		$this->assertIsA($id, 'string');
		$this->assertEqual($id, $entry->id->asString());
		
		echo "<pre>";
// 		var_dump($entry);
		echo "</pre>";
				
		// Read back
		$otherEntry = new LexEntryModel($project, $id);
		$this->assertEqual($id, $otherEntry->id->asString());
		$this->assertEqual($lexeme1, $otherEntry->lexeme);
		$this->assertEqual($sense, $otherEntry->senses[0]);
	
		// Update
		$lexeme2 = MultiText::create('fr', 'Other form');
		$otherEntry->lexeme = $lexeme2;
		$otherEntry->write();
	
		// Read back
		$otherEntry = new LexEntryModel($project, $id);
		$this->assertEqual($lexeme2, $otherEntry->lexeme);
	
		// List
		$list->read();
		$this->assertEqual(1, $list->count);
	
		// Delete
		LexEntryModel::remove($id);
	
		// List
		$list->read();
		$this->assertEqual(0, $list->count);
	}
	
/*
	function testEncode_EntryAndSense_JsonCorrect() {
		$entry = LexEntryModel::create("guid0");
		$entry->setEntry(MultiText::create('fr', 'form1'));
		
		$sense = new Sense();
		$sense->setDefinition(MultiText::create('en', 'definition1'));
		$sense->setSemanticDomainName('semantic-domain-ddp4');
		$sense->setSemanticDomainValue('2.1 Body');
		$sense->addExample(Example::create(
			MultiText::create('en', 'example1'),
			MultiText::create('fr', 'translation1')
		));
		
		$entry->addSense($sense);
		
		$result = json_encode($entry->encode());
		
		$this->assertEqual('{"guid":"guid0","mercurialSHA":null,"entry":{"fr":"form1"},"senses":[{"definition":{"en":"definition1"},"POS":"","examples":[{"example":{"en":"example1"},"translation":{"fr":"translation1"}}],"SemDomValue":"2.1 Body","SemDomName":"semantic-domain-ddp4"}]}', $result);
	}
	
	function testCreateFromArray_Sample_Correct() {
		$entry = LexEntryModel::create("guid0");
		$entry->setEntry(MultiText::create('fr', 'form1'));
		
		$sense = new Sense();
		$sense->setDefinition(MultiText::create('en', 'definition1'));
		$sense->setPartOfSpeech('Noun');
		$sense->setSemanticDomainName('semantic-domain-ddp4');
		$sense->setSemanticDomainValue('2.1 Body');
		$sense->addExample(Example::create(
			MultiText::create('en', 'example1'),
			MultiText::create('fr', 'translation1')
		));
		
		$entry->addSense($sense);
				
		$value = $entry->encode();
		
		$v = LexEntryModel::createFromArray($value);
		
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
*/
}

?>
