<?php

/* vim: set expandtab tabstop=4 shiftwidth=4 softtabstop=4 cc=76; */

/**
 * @package     omeka
 * @subpackage  neatline
 * @copyright   2012 Rector and Board of Visitors, University of Virginia
 * @license     http://www.apache.org/licenses/LICENSE-2.0.html
 */


abstract class Neatline_Case_Abstract extends Omeka_Test_AppTestCase
{


    /**
     * Get the Jasmine fixtures directory.
     *
     * @return string The directory.
     */
    abstract protected function getFixturesPath();


    /**
     * Clear the database.
     */
    public function tearDown()
    {
        $this->db->query("DELETE FROM
            `{$this->__exhibits->getTableName()}` WHERE 1=1"
        );
        $this->db->query("DELETE FROM
            `{$this->__records->getTableName()}` WHERE 1=1"
        );
        parent::tearDown();
    }


    /**
     * Create a User.
     *
     * @param string $name An identifier.
     * @param string $role The user's role.
     * @return User $item The item.
     */
    protected function __user($name='user', $role='super')
    {

        $user = new User;
        $user->setArray(array(
            'name'      => $name,
            'email'     => $name.'@test.org',
            'username'  => $name,
            'role'      => $role,
            'active'    => 1
        ));

        $user->setPassword('password');
        $user->save();

        return $user;

    }


    /**
     * Create an Item.
     *
     * @param string $title The exhibit title.
     * @return Item $item The item.
     */
    protected function __item($title='Test Title')
    {
        return $item = insert_item(array(), array(
            'Dublin Core' => array (
                'Title' => array(
                    array('text' => $title, 'html' => false)
                )
            )
        ));
    }


    /**
     * Create a Neatline exhibit.
     *
     * @param string $slug The exhibit slug.
     * @param boolean $public True if the exhibit is public.
     * @return NeatlineExhibit $neatline The exhibit.
     */
    protected function __exhibit($slug='test-slug', $public=true)
    {

        $exhibit = new NeatlineExhibit;
        $exhibit->setArray(array(
            'slug' => $slug, 'public' => $public
        ));

        $exhibit->save();
        return $exhibit;

    }


    /**
     * Create a data record.
     *
     * @param NeatlineExhibit $exhibit The parent exhibit.
     * @param Item $item The parent item.
     * @return NeatlineRecord $record The record.
     */
    protected function __record($exhibit=null, $item=null)
    {

        // Get parent exhibit.
        if (is_null($exhibit)) $exhibit = $this->__exhibit();

        // Create record.
        $record = new NeatlineRecord($exhibit, $item);
        $record->__save();
        return $record;

    }


    /**
     * Write a fixture file.
     *
     * @param string $body The fixture body.
     * @param string $file The name of the fixture file.
     */
    protected function writeFixture($body, $file)
    {

        // Open the fixture file.
        $fixture = fopen($this->getFixturesPath() . $file, 'w');

        // Write fixture.
        fwrite($fixture, $body);
        fclose($fixture);

    }


    /**
     * Write the response body from a route to a fixture file.
     *
     * @param string $route The resource location.
     * @param string $file The name of the fixture file.
     */
    protected function writeFixtureFromRoute($route, $file)
    {

        // Load the page.
        $this->resetResponse();
        $this->dispatch($route);

        // Write the fixture.
        $response = $this->getResponseBody();
        $this->writeFixture($response, $file);

    }


    /**
     * Mock out PUT data before a request.
     *
     * @param array $data Key-value pairs.
     */
    protected function setPut($data=array())
    {

        // Open the file.
        $mockPath = NL_TEST_DIR . '/mocks/put.txt';
        $fileIn = fopen($mockPath, 'w');

        // Write data.
        fwrite($fileIn, json_encode($data));
        fclose($fileIn);

        // Set global fileIn.
        Zend_Registry::set('fileIn', $mockPath);
        $this->request->setMethod('PUT');

    }


    /**
     * Mock out POST data before a request.
     *
     * @param array $data Key-value pairs.
     */
    protected function setPost($data=array())
    {
        $this->request->setMethod('POST')->setRawBody(
            Zend_Json::encode($data)
        );
    }


    /**
     * Inject and return a mock `Omeka_Job_Dispatcher_Default`.
     *
     * @return Omeka_Job_Dispatcher_Default PHPUnit mock.
     */
    protected function mockJobDispatcher()
    {

        // Create a testing-double job dispatcher.
        $jobs = $this->getMockBuilder('Omeka_Job_Dispatcher_Default')
            ->disableOriginalConstructor()
            ->getMock();

        // Inject the mock.
        Zend_Registry::set('job_dispatcher', $jobs);

        return $jobs;

    }


    /**
     * Register mock exhibit widgets filter callback.
     */
    protected function mockExhibitWidgets()
    {
        add_filter('neatline_exhibit_widgets', 'nl_mockWidgets');
    }


    /**
     * Register mock record widgets filter callback.
     */
    protected function mockRecordWidgets()
    {
        add_filter('neatline_record_widgets', 'nl_mockWidgets');
    }


    /**
     * Register mock presenters filter callback.
     */
    protected function mockPresenters()
    {
        add_filter('neatline_presenters', 'nl_mockPresenters');
    }


    /**
     * Register the mock layers JSON.
     */
    protected function mockLayers()
    {
        Zend_Registry::set('layers', NL_TEST_DIR . '/mocks/layers');
    }


    /**
     * Register the mock theme scripts.
     */
    protected function mockTheme()
    {
        get_view()->addScriptPath(NL_TEST_DIR . '/mocks/theme/neatline');
    }


    /**
     * Query all records in an exhibit.
     *
     * @param NeatlineExhibit $exhibit The parent exhibit.
     * @param boolean $findOne If true, return only first record.
     * @return array The child records.
     */
    protected function getRecordsByExhibit($exhibit, $findOne=false)
    {
        return $this->__records->findBySql(
            'exhibit_id=?', array($exhibit->id), $findOne
        );
    }


    /**
     * Get the body of the last response.
     *
     * @return array The response body.
     */
    protected function getResponseBody()
    {
        return $this->getResponse()->getBody('default');
    }


    /**
     * Get the body of the last response as an array.
     *
     * @return array The response data.
     */
    protected function getResponseArray()
    {
        return json_decode($this->getResponseBody());
    }


    /**
     * Reload a record.
     *
     * @param Omeka_Record_AbstractRecord $record A record to reload.
     * @return Omeka_Record_AbstractRecord The reloaded record.
     */
    protected function reload($record)
    {
        return $record->getTable()->find($record->id);
    }


    /**
     * Get the last record in a table.
     *
     * @param Omeka_Db_Table $table A table.
     * @return Neatline_AbstractRecord The last record.
     */
    protected function getLastRow($table)
    {
        return end($table->findAll());
    }


    /**
     * Log out the currently-authenticated user.
     */
    protected function logout()
    {

        // Clear out user on the bootstrap.
        $bootstrap = $this->application->getBootstrap();
        $bootstrap->auth->getStorage()->write(null);
        $bootstrap->getContainer()->currentuser = null;
        $bootstrap->currentUser = null;

        // Clear out the current user.
        Zend_Controller_Action_HelperBroker::getStaticHelper('Acl')
            ->setCurrentUser(null);

    }


    /**
     * Log in as a "Researcher" user.
     *
     * @param $name An identifier for the user.
     * @return Omeka_User The new user.
     */
    protected function loginAsResearcher($name='user')
    {
        $user = $this->__user($name, 'researcher');
        $this->_authenticateUser($user);
        return $user;
    }


    /**
     * Log in as a "Contributor" user.
     *
     * @param $name An identifier for the user.
     * @return Omeka_User The new user.
     */
    protected function loginAsContributor($name='user')
    {
        $user = $this->__user($name, 'contributor');
        $this->_authenticateUser($user);
        return $user;
    }


    /**
     * Register an expected 404 controller exception.
     */
    protected function expect404()
    {
        $this->setExpectedException('Omeka_Controller_Exception_404');
    }


}
