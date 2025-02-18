<?php
/**
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
 * @category    Application Unit Test
 * @author      Jens Schwidder <schwidder@zib.de>
 * @copyright   Copyright (c) 2013-2019, OPUS 4 development team
 * @license     http://www.gnu.org/licenses/gpl.html General Public License
 */

use Opus\Document;
use Opus\Person;

class Admin_Form_InfoBoxTest extends ControllerTestCase
{

    protected $additionalResources = ['database'];

    public function testConstructForm()
    {
        $form = new Admin_Form_InfoBox();

        $this->assertEquals(1, count($form->getDecorators()));
        $this->assertNotNull($form->getDecorator('ViewScript'));
        $this->assertNull($form->getDocument());
    }

    public function testPopulateFromModel()
    {
        $form = new Admin_Form_InfoBox();

        $document = Document::get(146);

        $form->populateFromModel($document);

        $this->assertNotNull($form->getDocument());
        $this->assertEquals($document, $form->getDocument());
    }

    public function testPopulateFromModelWithBadObject()
    {
        $form = new Admin_Form_InfoBox();

        $logger = new MockLogger();
        $form->setLogger($logger);

        $form->populateFromModel(null);

        $messages = $logger->getMessages();

        $this->assertEquals(1, count($messages));
        $this->assertContains('Called with instance of', $messages[0]);

        $logger->clear();

        $form->populateFromModel($this);

        $messages = $logger->getMessages();

        $this->assertEquals(1, count($messages));
        $this->assertContains('Called with instance of \'' . __CLASS__ . '\'', $messages[0]);
    }

    public function testConstructFromPost()
    {
        $form = new Admin_Form_InfoBox();

        $document = Document::get(146);

        $form->constructFromPost([], $document);

        $this->assertNotNull($form->getDocument());
        $this->assertEquals($document, $form->getDocument());
    }

    public function testConstructFromPostWithBadObject()
    {
        $form = new Admin_Form_InfoBox();

        $logger = new MockLogger();
        $form->setLogger($logger);

        $form->constructFromPost([], null);
        $messages = $logger->getMessages();

        $this->assertEquals(1, count($messages));
        $this->assertContains('Called with instance of', $messages[0]);

        $logger->clear();

        $form->constructFromPost([], new Person());

        $messages = $logger->getMessages();

        $this->assertEquals(1, count($messages));
        $this->assertContains('Called with instance of \'Opus\Person\'', $messages[0]);
    }

    public function testIsEmpty()
    {
        $form = new Admin_Form_InfoBox();

        $this->assertFalse($form->isEmpty());
    }
}
