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
 */

use Opus\Note;

/**
 * Unterformular fuer Bemerkungen/Notizen.
 *
 * @category    Application
 * @package     Admin_Form
 * @author      Jens Schwidder <schwidder@zib.de>
 * @copyright   Copyright (c) 2013, OPUS 4 development team
 * @license     http://www.gnu.org/licenses/gpl.html General Public License
 */
class Admin_Form_Document_Note extends Admin_Form_AbstractModelSubForm
{

    const ELEMENT_ID = 'Id';
    const ELEMENT_VISIBILITY = 'Visibility';
    const ELEMENT_MESSAGE = 'Message';

    public function init()
    {
        parent::init();

        $this->addElement('hidden', self::ELEMENT_ID);
        $this->addElement('checkbox', self::ELEMENT_VISIBILITY, ['label' => 'Opus_Note_Visibility_Value_Public']);
        $this->addElement('textarea', self::ELEMENT_MESSAGE, [
            'required' => true,
            'rows' => 4,
            'decorators' => [
                'ViewHelper',
                'Errors',
                'Description',
                'ElementHtmlTag',
                [['dataWrapper' => 'HtmlTagWithId'], ['tag' => 'div', 'class' => 'data-wrapper']]
            ]
        ]);

        $this->getElement(self::ELEMENT_VISIBILITY)
                ->setViewCheckedValue('Opus_Note_Visibility_Value_Public')
                ->setViewUncheckedValue('Opus_Note_Visibility_Value_Private');

        $this->setRemoveEmptyCheckbox(false);
    }

    public function populateFromModel($note)
    {
        $this->getElement(self::ELEMENT_ID)->setValue($note->getId());
        $this->getElement(self::ELEMENT_MESSAGE)->setValue($note->getMessage());
        $this->getElement(self::ELEMENT_VISIBILITY)->setChecked($note->getVisibility() === 'public');
    }

    public function updateModel($note)
    {
        $note->setMessage($this->getElement(self::ELEMENT_MESSAGE)->getValue());
        $note->setVisibility(($this->getElement(self::ELEMENT_VISIBILITY)->getValue()) ? 'public' : 'private');
    }

    public function getModel()
    {
        $noteId = $this->getElement(self::ELEMENT_ID)->getValue();

        if (empty($noteId)) {
            $noteId = null;
        }

        $note = new Note($noteId);

        $this->updateModel($note);

        return $note;
    }

    public function loadDefaultDecorators()
    {
        parent::loadDefaultDecorators();

        $this->removeDecorator('Fieldset');
    }

    public function prepareRenderingAsView()
    {
        parent::prepareRenderingAsView();
        $this->getElement(self::ELEMENT_VISIBILITY)->removeDecorator('Label');
    }
}
