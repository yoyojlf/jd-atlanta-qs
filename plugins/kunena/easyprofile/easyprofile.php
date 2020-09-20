<?php
/**
 * Kunena Plugin
 *
 * @package     Kunena.Plugins
 * @subpackage  Easyprofile
 *
 * @copyright   (C) 2008 - 2018 Kunena Team. All rights reserved.
 * @license     https://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @link        https://www.kunena.org
 **/
defined('_JEXEC') or die ();

class plgKunenaEasyprofile extends JPlugin
{
	/**
	 * plgKunenaEasyprofile constructor.
	 *
	 * @param $subject
	 * @param $config
	 */
	public function __construct(&$subject, $config)
	{
		// Do not load if Kunena version is not supported or Kunena is offline
		if (!(class_exists('KunenaForum') && KunenaForum::isCompatible('3.0') && KunenaForum::installed()))
		{
			return;
		}

		// Do not load if Easyprofile is not installed
		$path = JPATH_SITE . '/components/com_jsn/helpers/helper.php';

		if (!is_file($path))
		{
			return;
		}

		include_once($path);

		parent::__construct($subject, $config);

		$this->loadLanguage('plg_kunena_easyprofile.sys', JPATH_ADMINISTRATOR) || $this->loadLanguage('plg_kunena_easyprofile.sys', KPATH_ADMIN);
	}

	/**
	 * Get Kunena avatar integration object.
	 *
	 * @return \KunenaAvatarEasyprofile|null
	 */
	public function onKunenaGetAvatar()
	{
		if (!$this->params->get('avatar', 1))
		{
			return null;
		}

		require_once __DIR__ . "/avatar.php";

		return new KunenaAvatarEasyprofile($this->params);
	}

	/**
	 * Get Kunena profile integration object.
	 *
	 * @return \KunenaProfileEasyprofile|null
	 */
	public function onKunenaGetProfile()
	{
		if (!$this->params->get('profile', 1))
		{
			return null;
		}

		require_once __DIR__ . "/profile.php";

		return new KunenaProfileEasyprofile($this->params);
	}
}
