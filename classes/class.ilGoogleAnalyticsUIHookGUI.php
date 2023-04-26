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
				// get the account information
				$account_id = $plugin_object->getAccountId();
				$anonymize_ip = $plugin_object->getAnonymizeIp();
				$track_downloads = $plugin_object->getTrackDownloads();

				// only proceed if account id is set!
				if ($account_id != NULL) {

					$html = $a_par['html'];
					$index = strripos($html, "</body>", -7);
					if ($index !== false) {
						$tmpl = $plugin_object->getTemplate("tpl.ga_script.html", true, true);
						$tmpl->setVariable("ACCOUNT_ID", $account_id);

						// anonymize?
						if ($anonymize_ip) {
							$tmpl->touchBlock("anonymize_ip");
						}

						// track downloads?
						if ($track_downloads) {
							$tmpl->setCurrentBlock("track_downloads");
							$tmpl->setVariable("ACCOUNT_ID_DOWNLOAD", $account_id);
							$tmpl->parseCurrentBlock();
						}

						// insert code
						$html = substr($html, 0, $index) . $tmpl->get() . substr($html, $index);

						// get the tag manager information
						$use_tag_manager = $plugin_object->getUseTagManager();
						$container_id = $plugin_object->getContainerId();

						// only add gtm-snippets if the tag manager should be used and the container id is set!
						if ($use_tag_manager and $container_id != NULL) {

							$index_gtm_script = strripos($html, "<head>", -6);
							if ($index_gtm_script !== false) {

								$tmpl_gtm_script = $plugin_object->getTemplate(
									"tpl.gtm_script.html",
									true,
									true
								);
								$tmpl_gtm_script->setVariable("CONTAINER_ID", $container_id);

								// insert google tag manager script code
								$html = substr($html, 0, $index_gtm_script + 6)
									. $tmpl_gtm_script->get()
									. substr($html, $index_gtm_script + 6);
							}

							$opening_body_tag = NULL;
							preg_match("<body.*>", $html, $opening_body_tag);
							if ($opening_body_tag != NULL) {
								$length_body_tag = strlen($opening_body_tag[0]);
								$index_gtm_noscript = strripos(
									$html,
									$opening_body_tag[0],
									-$length_body_tag
								);

								if ($index_gtm_noscript !== false) {

									$tmpl_gtm_noscript = $plugin_object->getTemplate(
										"tpl.gtm_noscript.html",
										true,
										true
									);
									$tmpl_gtm_noscript->setVariable("CONTAINER_ID", $container_id);

									// insert google tag manager no-script code
									$html = substr($html, 0, $index_gtm_noscript + $length_body_tag)
										. $tmpl_gtm_noscript->get()
										. substr($html, $index_gtm_noscript + $length_body_tag);
								}
							}
						}

						return array("mode" => ilUIHookPluginGUI::REPLACE, "html" => $html);
					}
				}
			}
		}

		return array("mode" => ilUIHookPluginGUI::KEEP, "html" => "");
	}
}
