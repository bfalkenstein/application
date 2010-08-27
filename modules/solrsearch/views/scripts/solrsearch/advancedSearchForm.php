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
 * @category    TODO
 * @author      Julian Heise <heise@zib.de>
 * @copyright   Copyright (c) 2008-2010, OPUS 4 development team
 * @license     http://www.gnu.org/licenses/gpl.html General Public License
 * @version     $Id$
 */
?>

<form action="<?= $this->url(array('module'=>'solrsearch','controller'=>'solrsearch','action'=>'searchdispatch')); ?>" method="post">

    <?php if($this->searchType != 'authorsearch') : ?>
    <fieldset>
        <legend>Allgemeine Suchoptionen</legend>
        <label for="rows">Treffer pro Seite</label>
        <select name="rows" id="rows">
            <option value="10" <?= $this->rows === '10' || !isset($this->rows)? 'selected="true"' : '' ?>>10</option>
            <option value="20" <?= $this->rows === '20' ? 'selected="true"' : '' ?>>20</option>
            <option value="50" <?= $this->rows === '50' ? 'selected="true"' : '' ?>>50</option>
            <option value="100" <?= $this->rows === '100' ? 'selected="true"' : '' ?>>100</option>
        </select>
    </fieldset>
    <?php endif ?>

    <fieldset>
        <legend>Suchfelder</legend>
        <table>
            <?php
            if ($this->searchType === 'authorsearch') {
                $fieldnames = array('author', 'title', 'referee', 'abstract', 'fulltext');
            }
            else {
                $fieldnames = array('author', 'title', 'referee', 'abstract', 'fulltext', 'year');
            }
            
            foreach ($fieldnames as $fieldname) :
                    $fieldnameQuery = $fieldname . 'Query';
                    $fieldnameQueryModifier = $fieldnameQuery . 'Modifier';
            ?>
            <tr>
                <td>
                    <label for="<?= $fieldname ?>"><?= $this->translate("solrsearch_advancedsearch_field_$fieldname"); ?></label>
                </td>
                <td>
                    <select name="<?= $fieldname ?>modifier">
                        <option value="<?= Opus_SolrSearch_Query::SEARCH_MODIFIER_CONTAINS_ALL ?>" <?= $this->$fieldnameQueryModifier === Opus_SolrSearch_Query::SEARCH_MODIFIER_CONTAINS_ALL || !isset($this->$fieldnameQueryModifier) ? 'selected="true"' : '' ?>>alle Wörter</option>
                        <option value="<?= Opus_SolrSearch_Query::SEARCH_MODIFIER_CONTAINS_ANY ?>" <?= $this->$fieldnameQueryModifier === Opus_SolrSearch_Query::SEARCH_MODIFIER_CONTAINS_ANY ? 'selected="true"' : '' ?>>mindestens ein Wort</option>
                        <?php if ($fieldname !== 'fulltext') : ?>
                            <option value="<?= Opus_SolrSearch_Query::SEARCH_MODIFIER_CONTAINS_NONE ?>" <?= $this->$fieldnameQueryModifier === Opus_SolrSearch_Query::SEARCH_MODIFIER_CONTAINS_NONE ? 'selected="true"' : '' ?>>keines der Wörter</option>
                        <?php endif ?>
                    </select>
                </td>
                <td>
                    <input type="text" id="<?= $fieldname ?>" name="<?= $fieldname ?>" value="<?= htmlspecialchars($this->$fieldnameQuery) ?>" title="<?= $this->translate("solrsearch_advancedsearch_tooltip_$fieldname"); ?>" />
                </td>
            </tr>
            <?php endforeach ?>
        </table>
    </fieldset>
    <input type="submit" value="Suchen" />
    <input type="hidden" name="searchtype" value="advanced" />
    <input type="hidden" name="start" value="0" />
    <input type="hidden" name="sortfield" value="score" />
    <input type="hidden" name="sordorder" value="desc" />
</form>

<form action="<?= $this->url(array('module'=>'solrsearch','controller'=>'solrsearch','action'=>'advanced'), null, true) ?>">
    <input type="submit" value="Formular zurücksetzen" />
</form>