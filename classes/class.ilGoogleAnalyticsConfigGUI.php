<?php

//include_once("./Services/Component/classes/class.ilPluginConfigGUI.php");

/**
 * Google Analytics configuration user interface class
 *
 * @author Stefan Born <stefan.born@phzh.ch>
 *
 * @ilCtrl_IsCalledBy ilGoogleAnalyticsConfigGUI: ilObjComponentSettingsGUI
 *
 */
class ilGoogleAnalyticsConfigGUI extends ilPluginConfigGUI
{

    /**
     * Handles all commmands, default is 'configure'
     */
    function performCommand(string $cmd) : void
    {
        switch ($cmd) {
            case 'configure':
            case 'save':
                $this->$cmd();
                break;
        }
    }

    /**
     * Configure screen
     */
    function configure()
    {
        global $DIC;

        /**
         * @var $plugin ilGoogleAnalyticsPlugin
         */
        $plugin = $this->getPluginObject();
        $form = $this->initConfigurationForm($plugin);

        // get binary
        $account_id = $plugin->getAccountId();
        if ($account_id == null) {
            $DIC->ui()->mainTemplate()->setOnScreenMessage(ilGlobalTemplateInterface::MESSAGE_TYPE_FAILURE, $plugin->txt("warning_no_account_id"));
        }

        // set all plugin settings values
        $val = array();
        $val["account_id"] = $account_id;
        $form->setValuesByArray($val);

        $DIC->ui()->mainTemplate()->setContent($form->getHTML());
    }


    /**
     * Save form input
     *
     */
    public function save()
    {
        global $DIC;

        /**
         * @var $plugin ilGoogleAnalyticsPlugin
         */
        $plugin = $this->getPluginObject();
        $form = $this->initConfigurationForm($plugin);

        if ($form->checkInput()) {
            $plugin->setAccountId($_POST["account_id"]);

            $DIC->ui()->mainTemplate()->setOnScreenMessage(ilGlobalTemplateInterface::MESSAGE_TYPE_SUCCESS, $DIC->language()->txt("saved_successfully"));
            $DIC->ctrl()->redirect($this, "configure");
        } else {
            $form->setValuesByPost();
            $DIC->ui()->mainTemplate()->setContent($form->getHtml());
        }
    }

    /**
     * Init configuration form.
     *
     * @return object form object
     */
    public function initConfigurationForm()
    {
        global $DIC;

        include_once("Services/Form/classes/class.ilPropertyFormGUI.php");
        $form = new ilPropertyFormGUI();
        $form->setTableWidth("100%");
        $form->setTitle($this->plugin_object->txt("plugin_configuration"));
        $form->setFormAction($DIC->ctrl()->getFormAction($this));

        // account id
        $input = new ilTextInputGUI($this->plugin_object->txt("account_id"), "account_id");
        $input->setRequired(true);
        $input->setValue($this->plugin_object->getAccountId());
        $input->setInfo($this->plugin_object->txt("account_id_info"));
        $form->addItem($input);

        $form->addCommandButton("save", $DIC->language()->txt("save"));

        return $form;
    }
}
