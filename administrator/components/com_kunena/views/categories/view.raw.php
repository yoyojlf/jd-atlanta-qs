<?php
/**
 * Kunena Component
 *
 * @package     Kunena.Administrator
 * @subpackage  Views
 *
 * @copyright   (C) 2008 - 2018 Kunena Team. All rights reserved.
 * @license     https://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @link        https://www.kunena.org
 **/
defined('_JEXEC') or die();

/**
 * Category View
 *
 * @since  K5.0
 */
class KunenaAdminViewCategories extends KunenaView
{
	/**
	 *
	 */
	public function displayChkAliases()
	{
		$alias = $this->app->input->get('alias', null, 'string');

		$db = JFactory::getDbo();
		$query = 'SELECT id FROM #__kunena_categories WHERE alias = ' . $db->quote($alias);
		$db->setQuery($query);
		$result = $db->loadObject();

		if ($result)
		{
			$response['msg']  = 0;
		}
		else
		{
			$response['msg']  = 1;
		}

		echo json_encode($response);
	}

	/**
	 *
	 */
	public function displayDeleteAlias()
	{
		$alias = $this->app->input->get('alias', null, 'string');

		$db = JFactory::getDbo();
		$query = 'DELETE FROM #__kunena_aliases WHERE alias = ' . $db->quote($alias);
		$db->setQuery($query);

		$response['msg'] = 1;

		try
		{
			$db->execute();
		}
		catch(JDatabaseExceptionExecuting $e)
		{
			$response['msg'] = 0;
		}

		echo json_encode($response);
	}
}
