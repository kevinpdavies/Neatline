<?php
/* vim: set expandtab tabstop=4 shiftwidth=4 softtabstop=4; */

/**
 * Testing helper class.
 *
 * PHP version 5
 *
 * Licensed under the Apache License, Version 2.0 (the "License"); you may not
 * use this file except in compliance with the License. You may obtain a copy of
 * the License at http://www.apache.org/licenses/LICENSE-2.0 Unless required by
 * applicable law or agreed to in writing, software distributed under the
 * License is distributed on an "AS IS" BASIS, WITHOUT WARRANTIES OR CONDITIONS
 * OF ANY KIND, either express or implied. See the License for the specific
 * language governing permissions and limitations under the License.
 *
 * @package     omeka
 * @subpackage  neatline
 * @author      Scholars' Lab <>
 * @author      Bethany Nowviskie <bethany@virginia.edu>
 * @author      Adam Soroka <ajs6f@virginia.edu>
 * @author      David McClure <david.mcclure@virginia.edu>
 * @copyright   2011 The Board and Visitors of the University of Virginia
 * @license     http://www.apache.org/licenses/LICENSE-2.0.html Apache 2 License
 */
?>

<?php

require_once '../NeatlinePlugin.php';
require_once '../../NeatlineMaps/NeatlineMapsPlugin.php';

class Neatline_Test_AppTestCase extends Omeka_Test_AppTestCase
{

    private $_dbHelper;

    /**
     * Spin up the plugins and prepare the database.
     *
     * @return void.
     */
    public function setUpPlugin()
    {

        parent::setUp();

        $this->user = $this->db->getTable('User')->find(1);
        $this->_authenticateUser($this->user);

        // Set up Neatline Maps.
        $neatline_maps_plugin_broker = get_plugin_broker();
        $this->_addNeatlineMapsPluginHooksAndFilters($neatline_maps_plugin_broker, 'NeatlineMaps');
        $neatline_maps_plugin_helper = new Omeka_Test_Helper_Plugin;
        $neatline_maps_plugin_helper->setUp('NeatlineMaps');

        // Set up Neatline.
        $neatline_plugin_broker = get_plugin_broker();
        $this->_addNeatlinePluginHooksAndFilters($neatline_plugin_broker, 'Neatline');
        $neatline_plugin_helper = new Omeka_Test_Helper_Plugin;
        $neatline_plugin_helper->setUp('Neatline');

        $this->_dbHelper = Omeka_Test_Helper_Db::factory($this->core);

    }

    /**
     * Install Neatline Maps.
     *
     * @return void.
     */
    public function _addNeatlineMapsPluginHooksAndFilters($plugin_broker, $plugin_name)
    {

        $plugin_broker->setCurrentPluginDirName($plugin_name);
        new NeatlineMapsPlugin;

    }

    /**
     * Install Neatline.
     *
     * @return void.
     */
    public function _addNeatlinePluginHooksAndFilters($plugin_broker, $plugin_name)
    {

        $plugin_broker->setCurrentPluginDirName($plugin_name);
        new NeatlinePlugin;

    }

}
