<?php

/* vim: set expandtab tabstop=4 shiftwidth=4 softtabstop=4 cc=76; */

/**
 * @package     omeka
 * @subpackage  neatline
 * @copyright   2012 Rector and Board of Visitors, University of Virginia
 * @license     http://www.apache.org/licenses/LICENSE-2.0.html
 */

class Neatline_Form_Exhibit extends Omeka_Form
{


    /**
     * Construct the exhibit add/edit form.
     */
    public function init()
    {
        parent::init();
        $this->registerElements();
    }


    /**
     * Bind an exhibit record to the form.
     *
     * @param NeatlineExhibit $exhibit The exhibit record.
     */
    public function setExhibit(NeatlineExhibit $exhibit)
    {
        $this->exhibit = $exhibit;
    }


    /**
     * Define the form elements.
     */
    public function registerElements()
    {

        // Title.
        $this->addElement('text', 'title', array(
            'label'         => __('Title'),
            'description'   => __('A title for the exhibit, displayed in the page header in the public view for the exhibit.'),
            'size'          => 40,
            'value'         => $this->exhibit->title,
            'required'      => true,
            'class'         => 'title',
            'validators'    => array(
                array('validator' => 'NotEmpty', 'breakChainOnFailure' => true, 'options' =>
                    array(
                        'messages' => array(
                            Zend_Validate_NotEmpty::IS_EMPTY => __('Enter a title.')
                        )
                    )
                )
            )
        ));

        // Slug.
        $this->addElement('text', 'slug', array(
            'label'         => __('URL Slug'),
            'description'   => __('A unique string used to form the public-facing URL for the exhibit. Can contain letters, numbers, and hyphens.'),
            'size'          => 40,
            'required'      => true,
            'value'         => $this->exhibit->slug,
            'filters'       => array('StringTrim'),
            'validators'    => array(
                array('validator' => 'NotEmpty', 'breakChainOnFailure' => true, 'options' =>
                    array(
                        'messages' => array(
                            Zend_Validate_NotEmpty::IS_EMPTY => __('Enter a slug.')
                        )
                    )
                ),
                array('validator' => 'Regex', 'breakChainOnFailure' => true, 'options' =>
                    array(
                        'pattern' => '/^[0-9a-z\-]+$/',
                        'messages' => array(
                            Zend_Validate_Regex::NOT_MATCH => __('The slug can only contain letters, numbers, and hyphens.')
                        )
                    )
                ),
                array('validator' => 'Db_NoRecordExists', 'options' =>
                    array(
                        'table'     => $this->exhibit->getTable()->getTableName(),
                        'adapter'   => $this->exhibit->getDb()->getAdapter(),
                        'field'     => 'slug',
                        'exclude'   => array('field' => 'id', 'value' => (int)$this->exhibit->id),
                        'messages'  => array(
                            'recordFound' => __('The slug is already in use.')
                        )
                    )
                )
            )
        ));

        // Description.
        $this->addElement('textarea', 'description', array(
            'label'         => __('Description'),
            'description'   => __('A short snippet of text describing the exhibit. Used to summarize the project in exhibit browse listings.'),
            'value'         => $this->exhibit->description,
            'attribs'       => array('rows' => '5')
        ));

        // Narrative.
        $this->addElement('textarea', 'narrative', array(
            'label'         => __('Narrative'),
            'description'   => __('A long-format prose narrative to accompany exhibit.'),
            'value'         => $this->exhibit->narrative,
            'attribs'       => array('rows' => '10')
        ));

        // Widgets.
        $this->addElement('multiselect', 'widgets', array(
            'label'         => __('Widgets'),
            'description'   => __('Select the sub-plugin widgets available in the exhibit.'),
            'attribs'       => array('data-placeholder' => 'Select one or more widgets', 'class' => 'chosen'),
            'multiOptions'  => array_flip(nl_getExhibitWidgets()),
            'value'         => nl_explode($this->exhibit->widgets),
        ));

        // Base Layers.
        $this->addElement('multiselect', 'base_layers', array(
            'label'         => __('Base Layers'),
            'description'   => __('Select the base layers available in the exhibit.'),
            'attribs'       => array('data-placeholder' => 'Select one or more layers', 'class' => 'chosen'),
            'multiOptions'  => nl_getLayersForSelect(),
            'value'         => nl_explode($this->exhibit->base_layers)
        ));

        // Default Layer.
        $this->addElement('select', 'base_layer', array(
            'label'         => __('Default Layer'),
            'description'   => __('Select which layer is visible by default when the exhibit starts.'),
            'attribs'       => array('data-placeholder' => 'Select a layer', 'class' => 'chosen'),
            'multiOptions'  => nl_getLayersForSelect(),
            'value'         => nl_explode($this->exhibit->base_layer),
            'required'      => true,
            'validators'    => array(
                array('validator' => 'NotEmpty', 'breakChainOnFailure' => true, 'options' =>
                    array(
                        'messages' => array(
                            Zend_Validate_NotEmpty::IS_EMPTY => __('Select a layer.')
                        )
                    )
                )
            )
        ));

        // Public.
        $this->addElement('checkbox', 'public', array(
            'label'         => __('Public'),
            'description'   => __('By default, exhibits are visible only to site administrators. Check here to publish the exhibit to the public site.'),
            'value'         => $this->exhibit->public
        ));

        // Submit.
        $this->addElement('submit', 'submit', array(
            'label' => __('Save Exhibit')
        ));

        // Group the metadata fields.
        $this->addDisplayGroup(array(
            'title',
            'slug',
            'description',
            'narrative',
            'widgets',
            'base_layers',
            'base_layer',
            'image',
            'public'
        ), 'exhibit_info');

        // Group the submit button sparately.
        $this->addDisplayGroup(array(
            'submit'
        ), 'submit_button');

    }


}
