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
 * @category    Cronjob
 * @package     Tests
 * @author      Gunar Maiwald (maiwald@zib.de)
 * @copyright   Copyright (c) 2008-2019, OPUS 4 development team
 * @license     http://www.gnu.org/licenses/gpl.html General Public License
 */

require_once('CronTestCase.php');

use Opus\Document;
use Opus\Job;
use Opus\Job\Worker\MetadataImport;

class MetadataImportTest extends CronTestCase
{

    protected $additionalResources = 'database';

    private $documentImported;

    private $xmlDir;


    public function setUp()
    {
        parent::setUp();
        $this->documentImported = false;
        $this->xmlDir = dirname(dirname(dirname(__FILE__))) . '/import/';
    }

    public function tearDown()
    {
        if ($this->documentImported) {
            $ids = Document::getAllIds();
            $last_id = array_pop($ids);
            $doc = Document::get($last_id);
            $doc->delete();
        }
        parent::tearDown();
    }

    public function testJobFailedWithoutXml()
    {
        $xml = null;
        $this->createJob(MetadataImport::LABEL, ['xml' => $xml]);
        $this->executeScript('cron-import-metadata.php');

        $allJobs = Job::getByLabels([MetadataImport::LABEL], null, Job::STATE_UNDEFINED);
        $this->assertTrue(empty($allJobs), 'Expected no more jobs in queue');
        $failedJobs = Job::getByLabels([MetadataImport::LABEL], null, Job::STATE_FAILED);
        $this->assertEquals(1, count($failedJobs), 'Expected one failed job in queue');

        // TODO Opus\\\\Job... is necessary to match Opus\\Job... in the actual error message - Fix!
        $this->assertJobException(array_pop($failedJobs), 'Opus\\\\Job\\\\Worker\\\\InvalidJobException');
    }

    public function testJobFailedWithSkippedDocumentsException()
    {
        $filename = 'test_import_invalid_collectionid.xml';
        $xml = new DOMDocument();
        $this->assertTrue($xml->load($this->xmlDir . $filename), 'Could not load xml as DomDocument');

        $this->createJob(MetadataImport::LABEL, ['xml' => $xml->saveXML()]);
        $this->executeScript('cron-import-metadata.php');

        $allJobs = Job::getByLabels([MetadataImport::LABEL], null, Job::STATE_UNDEFINED);
        $this->assertTrue(empty($allJobs), 'Expected no more jobs in queue');
        $failedJobs = Job::getByLabels([MetadataImport::LABEL], null, Job::STATE_FAILED);
        $this->assertEquals(1, count($failedJobs), 'Expected one failed job in queue');
        $this->assertJobException(array_pop($failedJobs), 'Opus\\\\Util\\\\MetadataImportSkippedDocumentsException');
    }

    public function testJobFailedWithInvalidXmlException()
    {
        $filename = 'test_import_schemainvalid.xml';
        $xml = new DOMDocument();
        $this->assertTrue($xml->load($this->xmlDir . $filename), 'Could not load xml as DomDocument');

        $this->createJob(MetadataImport::LABEL, ['xml' => $xml->saveXML()]);
        $this->executeScript('cron-import-metadata.php');

        $allJobs = Job::getByLabels([MetadataImport::LABEL], null, Job::STATE_UNDEFINED);
        $this->assertTrue(empty($allJobs), 'Expected no more jobs in queue');
        $failedJobs = Job::getByLabels([MetadataImport::LABEL], null, Job::STATE_FAILED);
        $this->assertEquals(1, count($failedJobs), 'Expected one failed job in queue');
        $this->assertJobException(array_pop($failedJobs), 'Opus\\\\Util\\\\MetadataImportInvalidXmlException');
    }


    public function testJobSuccess()
    {
        $filename = 'test_import_minimal.xml';
        $xml = new DOMDocument();
        $this->assertTrue($xml->load($this->xmlDir . $filename), 'Could not load xml as DomDocument');

        $this->createJob(MetadataImport::LABEL, ['xml' => $xml->saveXML()]);
        $this->executeScript('cron-import-metadata.php');

        $allJobs = Job::getByLabels([MetadataImport::LABEL], null, Job::STATE_UNDEFINED);
        $this->assertTrue(empty($allJobs), 'Expected no more jobs in queue');
        $failedJobs = Job::getByLabels([MetadataImport::LABEL], null, Job::STATE_FAILED);
        $this->assertTrue(empty($failedJobs), 'Expected no failed jobs in queue');

        $this->documentImported = true;
    }

    private function assertJobException($job, $exception)
    {
        $this->assertStringStartsWith('{"exception":"' . $exception . '"', $job->getErrors());
    }
}
