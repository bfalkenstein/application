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
 * @package     View_Helper
 * @author      Jens Schwidder <schwidder@zib.de>
 * @copyright   Copyright (c) 2017, OPUS 4 development team
 * @license     http://www.gnu.org/licenses/gpl.html General Public License
 */

use Opus\Document;

/**
 * View helper for rendering the fulltext logo for documents in the search result list.
 */
class Application_View_Helper_FulltextLogo extends Application_View_Helper_Document_HelperAbstract
{

    public function fulltextLogo($doc = null)
    {
        if (is_null($doc)) {
            $doc = $this->getDocument();
        }

        if (! $doc instanceof Document) {
            // TODO log
            return;
        }

        $cssClass = "fulltext-logo";
        $tooltip = null;


        if ($doc->hasFulltext()) {
            $cssClass .= ' fulltext';
            $tooltip = 'fulltext_icon_tooltip';
        }

        if ($doc->isOpenAccess()) {
            $cssClass .= ' openaccess';
            $tooltip = 'fulltext_icon_oa_tooltip';
        }

        $output = "<div class=\"$cssClass\"";

        if (! is_null($tooltip)) {
            $tooltip = $this->view->translate([$tooltip]);
            $output .= " title=\"$tooltip\"";
        }

        $output .= "></div>";

        return $output;
    }
}
