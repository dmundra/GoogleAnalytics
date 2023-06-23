<?php

/* Copyright (c) 1998-2010 ILIAS open source, Extended GPL, see docs/LICENSE */

include_once("./Services/UIComponent/classes/class.ilUIHookPluginGUI.php");

/**
 * User interface hook test class.
 *
 * Enter description here ...
 *
 * @author  Stefan Born <stefan.born@phzh.ch>
 * @version
 *
 * @ingroup ServicesUIComponent
 */
class ilGoogleAnalyticsUIHookGUI extends ilUIHookPluginGUI
{

    /**
     * Modify HTML output of GUI elements. Modifications modes are:
     * - ilUIHookPluginGUI::KEEP (No modification)
     * - ilUIHookPluginGUI::REPLACE (Replace default HTML with your HTML)
     * - ilUIHookPluginGUI::APPEND (Append your HTML to the default HTML)
     * - ilUIHookPluginGUI::PREPEND (Prepend your HTML to the default HTML)
     *
     * @param string $a_comp component
     * @param string $a_part string that identifies the part of the UI that is handled
     * @param string $a_par  array of parameters (depend on $a_comp and $a_part)
     *
     * @return array array with entries "mode" => modification mode, "html" => your html
     */
    public function getHTML($a_comp, $a_part, $a_par = array())
    {
        global $DIC;

        // loading a template and this is NOT an async call?
        if ($a_part == "template_load" && !$DIC->ctrl()->isAsynch()) {
            // is main template?
            if (strpos(strtolower($a_par['html']), "</body>") !== false) {

                /**
                 * @var $plugin_object ilGoogleAnalyticsPlugin
                 */
                $plugin_object = $this->plugin_object;
                $account_id = $plugin_object->getAccountId();

                // only proceed if account id is set!
                if ($account_id != null) {
                    $html = $a_par['html'];
                    $index = strripos($html, "</body>", -7);
                    if ($index !== false) {
                        $tmpl = $plugin_object->getTemplate("tpl.ga_script.html", true, true);
                        $tmpl->setVariable("ACCOUNT_ID", $account_id);

                        // insert code
                        $html = substr($html, 0, $index) . $tmpl->get() . substr($html, $index);

                        return array("mode" => ilUIHookPluginGUI::REPLACE, "html" => $html);
                    }
                }
            }
        }

        return array("mode" => ilUIHookPluginGUI::KEEP, "html" => "");
    }
}
