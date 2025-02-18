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
 * @author      Sascha Szott <szott@zib.de>
 * @copyright   Copyright (c) 2008-2010, OPUS 4 development team
 * @license     http://www.gnu.org/licenses/gpl.html General Public License
 */

use Opus\CollectionRole;
use Opus\Model\NotFoundException;

/**
 * TODO überarbeiten (entfernen?)
 */
class Admin_Model_CollectionRole
{

    private $_collectionRole = null;

    public function __construct($id = null)
    {
        if ($id === '') {
            throw new Admin_Model_Exception('missing parameter roleid');
        }
        if (is_null($id)) {
            $this->initNewCollectionRole();
            return;
        }
        try {
            $this->_collectionRole = new CollectionRole((int) $id);
        } catch (NotFoundException $e) {
            throw new Admin_Model_Exception('roleid parameter value unknown');
        }
    }

    /**
     * Initialisiert Defaultwerte für neue CollectionRole.
     */
    private function initNewCollectionRole()
    {
        $this->_collectionRole = new CollectionRole();
        foreach (['Visible', 'VisibleBrowsingStart', 'VisibleFrontdoor', 'VisibleOai'] as $field) {
            $this->_collectionRole->getField($field)->setValue(1);
        }
    }

    /**
     * Liefert CollectionRole.
     * @return null|CollectionRole
     */
    public function getObject()
    {
        return $this->_collectionRole;
    }

    /**
     * Löscht CollectionRole.
     */
    public function delete()
    {
        $this->_collectionRole->delete();
    }

    /**
     * Setzt Sichtbarkeit von CollectionRole.
     * @param $visibility
     */
    public function setVisibility($visibility)
    {
        $this->_collectionRole->setVisible($visibility);
        $this->_collectionRole->store();
    }

    /**
     * Verschiebt CollectionRole zu neuer Position.
     * @param $position
     * @throws Admin_Model_Exception
     *
     * TODO make robuster
     */
    public function move($position)
    {
        if (is_null($position)) {
            return;
        }
        $position = (int) $position;
        if ($position < 1) {
            throw new Admin_Model_Exception('cannot move collection role');
        }
        $this->_collectionRole->setPosition($position);
        $this->_collectionRole->store();
    }
}
