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
 * @category    Application
 * @package     Module_Admin
 * @author      Jens Schwidder <schwidder@zib.de>
 * @copyright   Copyright (c) 2013, OPUS 4 development team
 * @license     http://www.gnu.org/licenses/gpl.html General Public License
 */

use Opus\TitleAbstract;
use Opus\Model\NotFoundException;

/**
 * Unterformular zum Editieren einer Zusammenfassung (abstract).
 *
 * @category    Application
 * @package     Module_Admin
 */
class Admin_Form_Document_Abstract extends Admin_Form_AbstractModelSubForm
{

    const ELEMENT_ID = 'Id';

    const ELEMENT_LANGUAGE = 'Language';

    const ELEMENT_VALUE = 'Value';

    public function init()
    {
        parent::init();

        $this->addElement('Hidden', self::ELEMENT_ID);
        $this->addElement('Language', self::ELEMENT_LANGUAGE);
        $this->addElement('Textarea', self::ELEMENT_VALUE, [
            'required' => true,
            'rows' => 12,
            'decorators' => [
                'ViewHelper',
                'Errors',
                'Description',
                'ElementHtmlTag',
                [['dataWrapper' => 'HtmlTagWithId'], ['tag' => 'div', 'class' => 'data-wrapper']]
            ]
        ]);
    }

    public function populateFromModel($abstract)
    {
        $this->getElement(self::ELEMENT_ID)->setValue($abstract->getId());
        $this->getElement(self::ELEMENT_LANGUAGE)->setValue($abstract->getLanguage());
        $this->getElement(self::ELEMENT_VALUE)->setValue($abstract->getValue());
    }

    public function updateModel($abstract)
    {
        $abstract->setLanguage($this->getElementValue(self::ELEMENT_LANGUAGE));
        $abstract->setValue($this->getElementValue(self::ELEMENT_VALUE));
    }

    public function getModel()
    {
        $abstractId = $this->getElement(self::ELEMENT_ID)->getValue();

        if (empty($abstractId) || ! is_numeric($abstractId)) {
            $abstractId = null;
        }

        try {
            $abstract = new TitleAbstract($abstractId);
        } catch (NotFoundException $omnfe) {
            $this->getLogger()->err(__METHOD__ . " Unknown ID = '$abstractId' (" . $omnfe->getMessage() . ').');
            $abstract = new TitleAbstract();
        }

        $this->updateModel($abstract);

        return $abstract;
    }

    public function loadDefaultDecorators()
    {
        parent::loadDefaultDecorators();

        $this->removeDecorator('Fieldset');
    }
}
