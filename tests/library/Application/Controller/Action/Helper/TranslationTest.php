<?php
/*
 * This file is part of OPUS. The software OPUS has been originally developed
 * at the University of Stuttgart with funding from the German Research Net,
 * the Federal Department of Higher Education and Research and the Ministry
 * of Science, Research and the Arts of the State of Baden-Wuerttemberg.
 *
 * OPUS 4 is a complete rewrite of the original OPUS software and was developed
 * by the Stuttgart University Library, the Library Service Center
 * Baden-Wuerttemberg, the Cooperative Library Network Berlin-Brandenburg,
 * the Saarland University and State Library, the Saxon State Library -
 * Dresden State and University Library, the Bielefeld University Library and
 * the University Library of Hamburg University of Technology with funding from
 * the German Research Foundation and the European Regional Development Fund.
 *
 * LICENCE
 * OPUS is free software; you can redistribute it and/or modify it under the
 * terms of the GNU General Public License as published by the Free Software
 * Foundation; either version 2 of the Licence, or any later version.
 * OPUS is distributed in the hope that it will be useful, but WITHOUT ANY
 * WARRANTY; without even the implied warranty of MERCHANTABILITY or FITNESS
 * FOR A PARTICULAR PURPOSE. See the GNU General Public License for more
 * details. You should have received a copy of the GNU General Public License
 * along with OPUS; if not, write to the Free Software Foundation, Inc., 51
 * Franklin Street, Fifth Floor, Boston, MA 02110-1301, USA.
 *
 * @category    Application Unit Tests
 * @author      Jens Schwidder <schwidder@zib.de>
 * @copyright   Copyright (c) 2008-2019, OPUS 4 development team
 * @license     http://www.gnu.org/licenses/gpl.html General Public License
 */

use Opus\Identifier;
use Opus\Language;
use Opus\Note;
use Opus\Person;
use Opus\Model\Dependent\Link\DocumentPerson;
use Opus\Model\ModelException;

/**
 * Test for class Application_Controller_Action_Helper_Translation and translations in general.
 */
class Application_Controller_Action_Helper_TranslationTest extends ControllerTestCase
{

    protected $additionalResources = 'translation';

    /**
     * Translation resource for tests.
     * @var Zend_Translate
     */
    private $translate;

    /**
     * Translation controller helper for tests.
     * @var Application_Controller_Action_Helper_Translation
     */
    private $helper;

    public function setUp()
    {
        parent::setUp();

        $this->translate = Application_Translate::getInstance();
        $this->helper = \Zend_Controller_Action_HelperBroker::getStaticHelper('Translation');
    }

    /**
     * Tests that the generated key is formatted correctly.
     */
    public function testGetKeyForValue()
    {
        $this->assertEquals(
            'Opus_Document_ServerState_Value_Unpublished',
            $this->helper->getKeyForValue('Opus\Document', 'ServerState', 'unpublished')
        );
    }

    public function testGetKeyForValueOfDocumentType()
    {
        $this->assertEquals(
            'testdoctype',
            $this->helper->getKeyForValue('Opus\Document', 'Type', 'testdoctype')
        );
    }

    public function testGetKeyForValueOfDocumentLanguage()
    {
        $this->assertEquals(
            'testdoclang',
            $this->helper->getKeyForValue('Opus\Document', 'Language', 'testdoclang')
        );
    }

    public function testGetKeyForField()
    {
        $this->assertEquals(
            'Language',
            $this->helper->getKeyForField('Opus\Document', 'Language')
        );
    }

    public function testGetKeyForTypeField()
    {
        $this->assertEquals(
            'Opus_Document_Type',
            $this->helper->getKeyForField('Opus\Document', 'Type')
        );
    }

    public function testTranslationOfServerStateValues()
    {
        $doc = $this->createTestDocument();
        $values = $doc->getField('ServerState')->getDefault();

        foreach ($values as $value) {
            $key = $this->helper->getKeyForValue('Opus\Document', 'ServerState', $value);
            $this->assertNotEquals(
                $key,
                $this->translate->translate($key),
                "Translation key '$key' is missing."
            );
        }
    }

    public function testTranslationOfPersonRoleValues()
    {
        $model = new DocumentPerson();
        $values = $model->getField('Role')->getDefault();

        foreach ($values as $value) {
            $key = $this->helper->getKeyForValue('Opus\Person', 'Role', $value);
            $this->assertNotEquals(
                $key,
                $this->translate->translate($key),
                "Translation key '$key' is missing."
            );
        }
    }

    public function translationOfTypeValuesDataProvider()
    {
        return [
            ['Opus\Title'],
            ['Opus\TitleAbstract'],
            ['Opus\Identifier'],
            ['Opus\Reference'],
            ['Opus\Subject']
        ];
    }

    /**
     * @throws ModelException
     * @dataProvider translationOfTypeValuesDataProvider
     */
    public function testTranslationOfTypeValues($className)
    {
        $model = new $className();
        $values = $model->getField('Type')->getDefault();

        foreach ($values as $value) {
            $key = $this->helper->getKeyForValue($className, 'Type', $value);
            $this->assertNotEquals(
                $key,
                $this->translate->translate($key),
                "Translation key '$key' is missing."
            );
        }
    }

    public function testTranslationOfNoteVisibilityValues()
    {
        $model = new Note();
        $values = $model->getField('Visibility')->getDefault();

        foreach ($values as $value) {
            $key = $this->helper->getKeyForValue('Opus\Note', 'Visibility', $value);
            $this->assertNotEquals(
                $key,
                $this->translate->translate($key),
                "Translation key '$key' is missing."
            );
        }
    }

    public function testTranslationOfOpusDocumentFields()
    {
        $model = $this->createTestDocument();

        $fieldNames = $model->describe();

        foreach ($fieldNames as $name) {
            $key = $this->helper->getKeyForField('Opus\Document', $name);
            $this->assertTrue(
                $this->translate->isTranslated($key),
                "Translation key '$key' is missing."
            );
        }
    }

    public function testTranslationOfOpusIdentifierFields()
    {
        $model = new Identifier();

        $fieldNames = $model->describe();

        foreach ($fieldNames as $name) {
            if ($name == 'Status' || $name == 'RegistrationTs') {
                // do not provide translations for DOI specific fields
                continue;
            }
            $key = $this->helper->getKeyForField('Opus\Identifier', $name);
            $this->assertTrue(
                $this->translate->isTranslated($key),
                "Translation key '$key' is missing."
            );
        }
    }

    public function testTranslationOfDocumentPersonFields()
    {
        $model = new DocumentPerson();
        $target = new Person();
        $model->setModel($target);

        $fieldNames = $model->describe();

        foreach ($fieldNames as $name) {
            $key = $this->helper->getKeyForField('Opus\Person', $name);
            $this->assertTrue(
                $this->translate->isTranslated($key),
                "Translation key '$key' is missing."
            );
        }
    }

    public function translationOfFieldsDataProvider()
    {
        return [
            ['Opus\Reference'],
            ['Opus\Title'],
            ['Opus\TitleAbstract'],
            ['Opus\Subject'],
            ['Opus\Patent'],
            ['Opus\Note'],
            ['Opus\Enrichment']
        ];
    }

    /**
     * @throws ModelException
     * @dataProvider translationOfFieldsDataProvider
     */
    public function testTranslationOfOpusEnrichmentFields($className)
    {
        $model = new $className();

        $fieldNames = $model->describe();

        foreach ($fieldNames as $name) {
            $key = $this->helper->getKeyForField($className, $name);
            $this->assertTrue(
                $this->translate->isTranslated($key),
                "Translation key '$key' is missing."
            );
        }
    }

    public function testTranslationOfLanguages()
    {
        $languages = Language::getAll();

        foreach ($languages as $language) {
            $key = $language->getPart2T();
            $this->assertNotEquals($key, $this->translate->translateLanguage($key));
        }
    }
}
