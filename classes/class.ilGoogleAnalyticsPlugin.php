<?php

include_once("./Services/UIComponent/classes/class.ilUserInterfaceHookPlugin.php");

/**
 * Example user interface plugin
 *
 * @author Stefan Born <stefan.born@phzh.ch>
 *
 */
class ilGoogleAnalyticsPlugin extends ilUserInterfaceHookPlugin
{

    /**
     * @var ilSetting
     */
    private $settings = null;
    private $account_id = null;


    /**
     * Object initialization. Can be overwritten by plugin class
     * (and should be made protected final)
     */
    protected function init() : void
    {
        $this->settings = new ilSetting("ui_uihk_googa");
        $this->account_id = $this->settings->get("account_id", null);
    }


    /**
     * Gets the name of the plugin.
     *
     * @return string The name of the plugin.
     */
    public function getPluginName() : string
    {
        return "GoogleAnalytics";
    }


    /**
     * After activation processing
     */
    protected function afterActivation() : void
    {
        // save the settings
        $this->setAccountId($this->getAccountId());
    }


    /**
     * Sets the Google Analytics account id.
     *
     * @param int $a_value The new value
     */
    public function setAccountId($a_value)
    {
        $this->account_id = strlen($a_value) > 0 ? $a_value : null;
        $this->settings->set('account_id', $this->account_id);
    }


    /**
     * Gets the Google Analytics account id.
     *
     * @return int The current value
     */
    public function getAccountId()
    {
        return $this->account_id;
    }
}
