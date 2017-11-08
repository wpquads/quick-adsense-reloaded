<?php
namespace wpquads;

/**
 * Class Settings
 * @package quads
 */
class adSettings
{

    /**
     * @var array
     */
    private $form = array();

    /**
     * @var Tabs
     */
    private $tabs;

    /**
     * Settings constructor.
     * @param Tabs $tabs
     */
    public function __construct($tabs)
    {
        $this->tabs = $tabs;

        foreach ($this->tabs->get() as $id => $name)
        {
            if (!method_exists($this, $id))
            {
                continue;
            }

            $this->{$id}();
        }
    }

    private function general()
    {
        $this->form["general"] = new Form();

        $settings = json_decode(json_encode(get_option("quads_visettings", array())));

        // DB Copy Query Limit
        $element = new Numerical(
            "wpstg_settings[queryLimit]",
            array(
                "class" => "medium-text",
                "step"  => 1,
                "max"   => 999999,
                "min"   => 0
            )
        );

        $this->form["general"]->add(
            $element->setLabel("DB Copy Query Limit")
            ->setDefault(isset($settings->queryLimit) ? $settings->queryLimit : 1000)
        );
        
        $options = array('1' => '1', '10' => '10', '50' => '50', '250' => '250' ,'500' => '500', '1000' => '1000');
        // DB Copy Query Limit
        $element = new Select(
            "wpstg_settings[fileLimit]",
            $options,
            array(
                "class" => "medium-text",
                "step"  => 1,
                "max"   => 999999,
                "min"   => 0
            )
        );      

        $this->form["general"]->add(
            $element->setLabel("File Copy Limit")->setDefault(isset($settings->fileLimit) ? $settings->fileLimit : 1)
        );


        // File Copy Batch Size
        $element = new Numerical(
            "wpstg_settings[batchSize]",
            array(
                "class" => "medium-text",
                "step"  => 1,
                "max"   => 999999,
                "min"   => 0
            )
        );

        $this->form["general"]->add(
            $element->setLabel("File Copy Batch Size")
            ->setDefault(isset($settings->batchSize) ? $settings->batchSize : 2)
        );

        // CPU load priority
        $element = new Select(
            "wpstg_settings[cpuLoad]",
            array(
                "high"      => "High (fast)",
                "medium"    => "Medium (average)",
                "low"       => "Low (slow)"
            )
        );

        $this->form["general"]->add(
            $element->setLabel("CPU load priority")
            ->setDefault(isset($settings->cpuLoad) ? $settings->cpuLoad : "fast")
        );
        

        // Optimizer
        $element = new Check(
            "wpstg_settings[optimizer]",
            array('1' => "")
        );

        $this->form["general"]->add(
            $element->setLabel("Optimizer")
            ->setDefault((isset($settings->optimizer)) ? $settings->optimizer : null)
        );

        // Plugins
        $plugins = array();

        foreach (get_plugins() as $key => $data)
        {
            if ("wp-staging/wp-staging.php" === $key)
            {
                continue;
            }

            $plugins[$key] = $data["Name"];
        }

        $element = new Select(
            "wpstg_settings[blackListedPlugins][]",
            $plugins,
            array(
                "multiple"  => "multiple",
                "style"     => "min-height:400px;"
            )
        );

        $this->form["general"]->add(
            $element->setDefault((isset($settings->blackListedPlugins)) ? $settings->blackListedPlugins : null)
        );

        // Disable admin authorization
//        $element = new Check(
//            "wpstg_settings[disableAdminLogin]",
//            array('1' => '')
//        );
//
//        $this->form["general"]->add(
//            $element->setLabel("Disable admin authorization")
//                ->setDefault((isset($settings->disableAdminLogin)) ? $settings->disableAdminLogin : null)
//        );

        // WordPress in subdirectory
        $element = new Check(
            "wpstg_settings[wpSubDirectory]",
            array('1' => '')
        );

        $this->form["general"]->add(
            $element->setLabel("Wordpress in subdirectory")
                ->setDefault((isset($settings->wpSubDirectory)) ? $settings->wpSubDirectory : null)
        );

        // Debug Mode
        $element = new Check(
            "wpstg_settings[debugMode]",
            array('1' => '')
        );

        $this->form["general"]->add(
            $element->setLabel("Debug Mode")
                ->setDefault((isset($settings->debugMode)) ? $settings->debugMode : null)
        );

        // Remove Data on Uninstall?
        $element = new Check(
            "wpstg_settings[unInstallOnDelete]",
            array('1' => '')
        );

        $this->form["general"]->add(
            $element->setLabel("Remove Data on Uninstall?")
                ->setDefault((isset($settings->unInstallOnDelete)) ? $settings->unInstallOnDelete : null)
        );

        // Check Directory Sizes
        $element = new Check(
            "wpstg_settings[checkDirectorySize]",
            array('1' => '')
        );

        $this->form["general"]->add(
            $element->setLabel("Check Directory Size")
                ->setDefault((isset($settings->checkDirectorySize)) ? $settings->checkDirectorySize : null)
        );
        
        // Get user roles
        $element = new SelectMultiple('wpstg_settings[userRoles][]', $this->getUserRoles());
        
        $this->form["general"]->add(
            $element->setLabel("Access Permissions")
                ->setDefault((isset($settings->userRoles)) ? $settings->userRoles : 'administrator')
        );
    }
    
    /**
     * Get available user Roles
     * @return array
     */
    private function getUserRoles(){
       $userRoles = array();
       foreach (get_editable_roles() as $key => $value){
          $userRoles[$key] = $key;
       }
       return array_merge(array('all' => 'Allow access from all visitors'),$userRoles);
    }

    /**
     * @param string $name
     * @return array|Form
     */
    public function get($name = null)
    {
        return (null === $name) ? $this->form : $this->form[$name];
    }
}