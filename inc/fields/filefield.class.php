<?php
/**
 * LICENSE
 *
 * Copyright © 2011-2018 Teclib'
 *
 * This file is part of Formcreator Plugin for GLPI.
 *
 * Formcreator is a plugin that allow creation of custom, easy to access forms
 * for users when they want to create one or more GLPI tickets.
 *
 * Formcreator Plugin for GLPI is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation; either version 2 of the License, or
 * (at your option) any later version.
 *
 * Formcreator Plugin for GLPI is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License along
 * with this program; if not, write to the Free Software Foundation, Inc.,
 * 51 Franklin Street, Fifth Floor, Boston, MA 02110-1301 USA.
 * If not, see http://www.gnu.org/licenses/.
 * ------------------------------------------------------------------------------
 * @author    Thierry Bugier
 * @author    Jérémy Moreau
 * @copyright Copyright © 2018 Teclib
 * @license   GPLv2 https://www.gnu.org/licenses/gpl2.txt
 * @link      https://github.com/pluginsGLPI/formcreator/
 * @link      http://plugins.glpi-project.org/#/plugin/formcreator
 * ------------------------------------------------------------------------------
 */
class PluginFormcreatorFileField extends PluginFormcreatorField
{
   public function displayField($canEdit = true) {
      if ($canEdit) {
         $required = $this->isRequired() ? ' required' : '';

         echo '<input type="hidden" class="form-control"
                  name="formcreator_field_' . $this->fields['id'] . '" value="" />' . PHP_EOL;

         echo Html::file([
            'name'    => 'formcreator_field_' . $this->fields['id'],
            'display' => false,
         ]);

      } else {
         $doc = new Document();
         $answer = $this->getAnswer();
         if ($doc->getFromDB($answer)) {
            echo $doc->getDownloadLink();
         }
      }
   }

   public function isValid($value) {
      // If the field is required it can't be empty

      if (!$this->isRequired()) {
         return true;
      }

      if (is_array($_POST['_formcreator_field_' . $this->fields['id']])
         && count($_POST['_formcreator_field_' . $this->fields['id']]) === 1) {
         $file = current($_POST['_formcreator_field_' . $this->fields['id']]);
         if (is_file(GLPI_TMP_DIR . '/' . $file)) {
            return true;
         }
      }

      Session::addMessageAfterRedirect(__('A required file is missing:', 'formcreator') . ' ' . $this->fields['name'], false, ERROR);
      return false;
   }

   public static function getName() {
      return __('File');
   }

   public static function getPrefs() {
      return [
         'required'       => 1,
         'default_values' => 0,
         'values'         => 0,
         'range'          => 0,
         'show_empty'     => 0,
         'regex'          => 0,
         'show_type'      => 1,
         'dropdown_value' => 0,
         'glpi_objects'   => 0,
         'ldap_values'    => 0,
      ];
   }

   public static function getJSFields() {
      $prefs = self::getPrefs();
      return "tab_fields_fields['file'] = 'showFields(" . implode(', ', $prefs) . ");';";
   }
}
