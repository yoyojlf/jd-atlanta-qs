<?php
/**
 * Kunena Component
 * @package     Kunena.Site
 * @subpackage  Controller.Message
 *
 * @copyright   (C) 2008 - 2018 Kunena Team. All rights reserved.
 * @license     https://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @link        https://www.kunena.org
 **/
defined('_JEXEC') or die;

/**
 * Class ComponentKunenaControllerMessageItemActionsDisplay
 *
 * @since  K4.0
 */
class ComponentKunenaControllerMessageItemActionsDisplay extends KunenaControllerDisplay
{
	protected $name = 'Message/Item/Actions';

	/**
	 * @var KunenaForumTopic
	 */
	public $topic;

	public $message;

	public $messageButtons;

	/**
	 * Prepare message actions display.
	 *
	 * @return void
	 */
	protected function before()
	{
		parent::before();

		$mesid = $this->input->getInt('mesid');
		$me = KunenaUserHelper::getMyself();

		$this->message = KunenaForumMessage::getInstance($mesid);
		$this->topic = $this->message->getTopic();

		$id = $this->message->thread;
		$catid = $this->message->catid;
		$token = JSession::getFormToken();
		$config = KunenaConfig::getInstance();

		$task = "index.php?option=com_kunena&view=topic&task=%s&catid={$catid}&id={$id}&mesid={$mesid}&{$token}=1";
		$layout = "index.php?option=com_kunena&view=topic&layout=%s&catid={$catid}&id={$id}&mesid={$mesid}";

		$this->messageButtons = new JObject;
		$this->message_closed = null;

		$kemplate = KunenaFactory::getTemplate();
		$fullactions = $kemplate->params->get('fullactions');
		$topicicontype = $kemplate->params->get('topicicontype');

		$button = $fullactions ? true : false;

		if ($config->read_only)
		{
			return false;
		}

		if ($this->message->isAuthorised('reply'))
		{
			$this->quickreply = true;
		}
		else
		{
			$this->quickreply = false;
		}

		// Reply / Quote
		if ($this->message->isAuthorised('reply'))
		{
			if ($topicicontype == 'B2' && !$fullactions)
			{
				$this->messageButtons->set('reply',
					$this->getButton(sprintf($layout, 'reply'), 'reply', 'message', 'communication', 'reply', $button, 'icon icon-undo'));
			}
			elseif ($topicicontype == 'B3' && !$fullactions)
			{
				$this->messageButtons->set('reply',
					$this->getButton(sprintf($layout, 'reply'), 'reply', 'message', 'communication', 'reply', $button, 'glyphicon glyphicon-share-alt'));
			}
			elseif ($topicicontype == 'fa' && !$fullactions)
			{
				$this->messageButtons->set('reply',
					$this->getButton(sprintf($layout, 'reply'), 'reply', 'message', 'communication', 'reply', $button, 'fa fa-reply'));
			}
			elseif ($topicicontype == 'image' && !$fullactions)
			{
				$this->messageButtons->set('reply',
					$this->getButton(sprintf($layout, 'reply'), 'reply', 'message', 'communication', 'reply', $button, 'kicon-reply'));
			}
			else
			{
				$this->messageButtons->set('reply',
					$this->getButton(sprintf($layout, 'reply'), 'reply', 'message', 'communication', 'reply', $button));
			}

			if ($me->exists())
			{
				if ($topicicontype == 'B2')
				{
					$this->messageButtons->set('quickreply',
						$this->getButton(sprintf($layout, 'reply'), 'quickreply', 'message', 'communication', "kreply{$mesid}", 'reply', 'icon icon-undo')
					);
				}
				elseif ($topicicontype == 'B3')
				{
					$this->messageButtons->set('quickreply',
						$this->getButton(sprintf($layout, 'reply'), 'quickreply', 'message', 'communication', "kreply{$mesid}", 'reply', 'glyphicon glyphicon-share-alt')
					);
				}
				elseif ($topicicontype == 'fa')
				{
					$this->messageButtons->set('quickreply',
						$this->getButton(sprintf($layout, 'reply'), 'quickreply', 'message', 'communication', "kreply{$mesid}", 'reply', 'fa fa-reply')
					);
				}
				elseif ($topicicontype == 'image')
				{
					$this->messageButtons->set('quickreply',
						$this->getButton(sprintf($layout, 'reply'), 'quickreply', 'message', 'communication', "kreply{$mesid}", 'reply', 'kicon-reply')
					);
				}
				else
				{
					$this->messageButtons->set('quickreply',
						$this->getButton(sprintf($layout, 'reply'), 'quickreply', 'message', 'communication', "kreply{$mesid}", 'reply')
					);
				}
			}

			if ($topicicontype == 'B2' && !$fullactions)
			{
				$this->messageButtons->set('quote',
					$this->getButton(sprintf($layout, 'reply&quote=1'), 'quote', 'message', 'communication', 'quote', $button, 'icon icon-comment'));
			}
			elseif ($topicicontype == 'B3' && !$fullactions)
			{
				$this->messageButtons->set('quote',
					$this->getButton(sprintf($layout, 'reply&quote=1'), 'quote', 'message', 'communication', 'quote', $button, 'glyphicon glyphicon-comment'));
			}
			elseif ($topicicontype == 'fa' && !$fullactions)
			{
				$this->messageButtons->set('quote',
					$this->getButton(sprintf($layout, 'reply&quote=1'), 'quote', 'message', 'communication', 'quote', $button, 'fa fa-quote-left'));
			}
			elseif ($topicicontype == 'image' && !$fullactions)
			{
				$this->messageButtons->set('quote',
					$this->getButton(sprintf($layout, 'reply&quote=1'), 'quote', 'message', 'communication', 'quote', $button, 'kicon-quote'));
			}
			else
			{
				$this->messageButtons->set('quote',
					$this->getButton(sprintf($layout, 'reply&quote=1'), 'quote', 'message', 'communication', 'quote', $button));
			}
		}
		elseif (!$me->isModerator($this->topic->getCategory()))
		{
			// User is not allowed to write a post.
			$this->message_closed = $this->topic->locked ? JText::_('COM_KUNENA_POST_LOCK_SET') :
				($me->exists() ? JText::_('COM_KUNENA_REPLY_USER_REPLY_DISABLED'): ' ');
		}

		$login = KunenaLogin::getInstance();

		if (!$this->message->isAuthorised('reply') && !$this->message_closed && $login->enabled() && !$this->message->hold
			&& !$config->read_only || !$this->message->isAuthorised('reply') && !$this->topic->locked && $login->enabled()
			&& !$me->userid && !$this->message->hold && !$config->read_only)
		{
			$loginurl = JRoute::_('index.php?option=com_users&view=login&return=' . base64_encode((string)JUri::getInstance()));
			$logintext = sprintf('<a class="btn-link" href="%s" rel="nofollow">%s</a>', $loginurl, JText::_('JLOGIN'));

			if ($login->getRegistrationUrl())
			{
				$register =  ' ' . JText::_('COM_KUNENA_LOGIN_OR') . ' <a class="btn-link" href="' . $login->getRegistrationUrl()
					. '">' . JText::_('COM_KUNENA_PROFILEBOX_CREATE_ACCOUNT') . '</a>';
			}
			else
			{
				$register = '';
			}

			echo '<p>' . JText::sprintf('COM_KUNENA_LOGIN_PLEASE', $logintext, $register) . '</p>';
		}

		// Thank you.
		if (isset($this->message->thankyou))
		{
			if ($this->message->isAuthorised('thankyou') && !array_key_exists($me->userid, $this->message->thankyou))
			{
				$ktemplate = KunenaFactory::getTemplate();
				$topicicontype = $ktemplate->params->get('topicicontype');

				if ($topicicontype == 'B2')
				{
					$this->messageButtons->set('thankyou',
						$this->getButton(sprintf($task, 'thankyou'), 'thankyou', 'message', 'user', 'thankyou', false, 'icon-thumbs-up')
					);
				}
				elseif ($topicicontype == 'B3')
				{
					$this->messageButtons->set('thankyou',
						$this->getButton(sprintf($task, 'thankyou'), 'thankyou', 'message', 'user', 'thankyou', false, 'glyphicon glyphicon-thumbs-up')
					);
				}
				elseif ($topicicontype == 'fa')
				{
					$this->messageButtons->set('thankyou',
						$this->getButton(sprintf($task, 'thankyou'), 'thankyou', 'message', 'user', 'thankyou', false, 'fa fa-thumbs-up')
					);
				}
				elseif ($topicicontype == 'image')
				{
					$this->messageButtons->set('thankyou',
						$this->getButton(sprintf($task, 'thankyou'), 'thankyou', 'message', 'user', 'thankyou', false, 'kicon-thumbs-up')
					);
				}
				else
				{
					$this->messageButtons->set('thankyou',
						$this->getButton(sprintf($task, 'thankyou'), 'thankyou', 'message', 'user', 'thankyou', false)
					);
				}
			}
		}

		// Unthank you

		if (KunenaFactory::getConfig()->showthankyou)
		{
			if ($this->message->isAuthorised('unthankyou') && array_key_exists($me->userid, $this->message->thankyou))
			{
				$ktemplate = KunenaFactory::getTemplate();
				$topicicontype = $ktemplate->params->get('topicicontype');

				if ($topicicontype == 'B2')
				{
					$this->messageButtons->set('unthankyou',
						$this->getButton(sprintf($task, 'unthankyou&userid=' . $me->userid), 'unthankyou', 'message', 'user', 'unthankyou', false, 'icon-thumbs-down')
					);
				}
				elseif ($topicicontype == 'B3')
				{
					$this->messageButtons->set('unthankyou',
						$this->getButton(sprintf($task, 'unthankyou&userid=' . $me->userid), 'unthankyou', 'message', 'user', 'unthankyou', false, 'glyphicon glyphicon-thumbs-down')
					);
				}
				elseif ($topicicontype == 'fa')
				{
					$this->messageButtons->set('unthankyou',
						$this->getButton(sprintf($task, 'unthankyou&userid=' . $me->userid), 'unthankyou', 'message', 'user', 'unthankyou', false, 'fa fa-thumbs-down')
					);
				}
				elseif ($topicicontype == 'image')
				{
					$this->messageButtons->set('unthankyou',
						$this->getButton(sprintf($task, 'unthankyou&userid=' . $me->userid), 'unthankyou', 'message', 'user', 'unthankyou', false, 'kicon-thumbs-down')
					);
				}
				else
				{
					$this->messageButtons->set('unthankyou',
						$this->getButton(sprintf($task, 'unthankyou&userid=' . $me->userid), 'unthankyou', 'message', 'user', 'unthankyou', false)
					);
				}
			}
		}

		// Report this.
		if (KunenaFactory::getConfig()->reportmsg && $me->exists())
		{
			if ($me->isModerator($this->topic->getCategory()) || KunenaFactory::getConfig()->user_report ||
				!KunenaFactory::getConfig()->user_report && $me->userid != $this->message->userid)
			{
				$ktemplate = KunenaFactory::getTemplate();
				$topicicontype = $ktemplate->params->get('topicicontype');

				if ($topicicontype == 'B2')
				{
					$icon = 'icon icon-flag';
				}
				elseif ($topicicontype == 'B3')
				{
					$icon = 'glyphicon glyphicon-exclamation-sign';
				}
				elseif ($topicicontype == 'fa')
				{
					$icon = 'fa fa-exclamation';
				}
				elseif ($topicicontype == 'image')
				{
					$icon = 'kicon-report';
				}
				else
				{
					$icon = '';
				}

				$this->messageButtons->set('report',
					$this->getButton(sprintf($layout, '#report'. $mesid .''), 'report', 'message', 'user', 'btn_report', $button, $icon));
			}
		}

		// Moderation and own post actions.
		if ($this->message->isAuthorised('edit'))
		{
			if ($topicicontype == 'B2' && !$fullactions)
			{
				$this->messageButtons->set('edit',
					$this->getButton(sprintf($layout, 'edit'), 'edit', 'message', 'moderation', 'edit', $button, 'icon icon-edit'));
			}
			elseif ($topicicontype == 'B3' && !$fullactions)
			{
				$this->messageButtons->set('edit',
					$this->getButton(sprintf($layout, 'edit'), 'edit', 'message', 'moderation', 'edit', $button, 'glyphicon glyphicon-edit'));
			}
			elseif ($topicicontype == 'fa' && !$fullactions)
			{
				$this->messageButtons->set('edit',
					$this->getButton(sprintf($layout, 'edit'), 'edit', 'message', 'moderation', 'edit', $button, 'fa fa-edit'));
			}
			elseif ($topicicontype == 'image' && !$fullactions)
			{
				$this->messageButtons->set('edit',
					$this->getButton(sprintf($layout, 'edit'), 'edit', 'message', 'moderation', 'edit', $button, 'kicon-edit'));
			}
			else
			{
				$this->messageButtons->set('edit',
					$this->getButton(sprintf($layout, 'edit'), 'edit', 'message', 'moderation', 'edit', $button)
				);
			}
		}

		if ($this->message->isAuthorised('move'))
		{
			if ($topicicontype == 'B2' && !$fullactions)
			{
				$this->messageButtons->set('moderate',
					$this->getButton(sprintf($layout, 'moderate'), 'moderate', 'message', 'moderation', 'edit', $button, 'icon icon-wrench'));
			}
			elseif ($topicicontype == 'B3' && !$fullactions)
			{
				$this->messageButtons->set('moderate',
					$this->getButton(sprintf($layout, 'moderate'), 'moderate', 'message', 'moderation', 'edit', $button, 'glyphicon glyphicon-random'));
			}
			elseif ($topicicontype == 'fa' && !$fullactions)
			{
				$this->messageButtons->set('moderate',
					$this->getButton(sprintf($layout, 'moderate'), 'moderate', 'message', 'moderation', 'edit', $button, 'fa fa-random'));
			}
			elseif ($topicicontype == 'image' && !$fullactions)
			{
				$this->messageButtons->set('moderate',
					$this->getButton(sprintf($layout, 'moderate'), 'moderate', 'message', 'moderation', 'edit', $button, 'kicon-move'));
			}
			else
			{
				$this->messageButtons->set('moderate',
					$this->getButton(sprintf($layout, 'moderate'), 'moderate', 'message', 'moderation', 'edit', $button)
				);
			}
		}

		if ($this->message->hold == 1)
		{
			if ($this->message->isAuthorised('approve'))
			{
				if ($topicicontype == 'B2' && !$fullactions)
				{
					$this->messageButtons->set('publish',
						$this->getButton(sprintf($task, 'approve'), 'approve', 'message', 'moderation', 'approve', $button, 'icon icon-checkmark-circle'));
				}
				elseif ($topicicontype == 'B3' && !$fullactions)
				{
					$this->messageButtons->set('publish',
						$this->getButton(sprintf($task, 'approve'), 'approve', 'message', 'moderation', 'approve', $button, 'glyphicon glyphicon-ok-circle'));
				}
				elseif ($topicicontype == 'fa' && !$fullactions)
				{
					$this->messageButtons->set('publish',
						$this->getButton(sprintf($task, 'approve'), 'approve', 'message', 'moderation', 'approve', $button, 'fa fa-check-circle-o'));
				}
				elseif ($topicicontype == 'image' && !$fullactions)
				{
					$this->messageButtons->set('publish',
						$this->getButton(sprintf($task, 'approve'), 'approve', 'message', 'moderation', 'approve', $button, 'kicon-approve'));
				}
				else
				{
					$this->messageButtons->set('publish',
						$this->getButton(sprintf($task, 'approve'), 'approve', 'message', 'moderation', 'approve', $button)
					);
				}
			}

			if ($topicicontype == 'B2' && !$fullactions)
			{
				$this->messageButtons->set('delete',
					$this->getButton(sprintf($task, 'delete'), 'delete', 'message', 'moderation', 'delete', $button, 'icon icon-trash'));
			}
			elseif ($topicicontype == 'B3' && !$fullactions)
			{
				$this->messageButtons->set('delete',
					$this->getButton(sprintf($task, 'delete'), 'delete', 'message', 'moderation', 'delete', $button, 'glyphicon glyphicon-trash'));
			}
			elseif ($topicicontype == 'fa' && !$fullactions)
			{
				$this->messageButtons->set('delete',
					$this->getButton(sprintf($task, 'delete'), 'delete', 'message', 'moderation', 'delete', $button, 'fa fa-trash'));
			}
			elseif ($topicicontype == 'image' && !$fullactions)
			{
				$this->messageButtons->set('delete',
					$this->getButton(sprintf($task, 'delete'), 'delete', 'message', 'moderation', 'delete', $button, 'kicon-delete'));
			}
			else
			{
				$this->messageButtons->set('delete',
					$this->getButton(sprintf($task, 'delete'), 'delete', 'message', 'moderation', 'delete', $button)
				);
			}
		}
		elseif ($this->message->hold == 2 || $this->message->hold == 3)
		{
			if ($this->message->isAuthorised('undelete'))
			{
				if ($topicicontype == 'B2' && !$fullactions)
				{
					$this->messageButtons->set('undelete',
						$this->getButton(sprintf($task, 'undelete'), 'undelete', 'message', 'moderation', 'undelete', $button, 'icon icon-checkmark-circle')
					);
				}
				elseif ($topicicontype == 'B3' && !$fullactions)
				{
					$this->messageButtons->set('undelete',
						$this->getButton(sprintf($task, 'undelete'), 'undelete', 'message', 'moderation', 'undelete', $button, 'glyphicon glyphicon-ok-circle')
					);
				}
				elseif ($topicicontype == 'fa' && !$fullactions)
				{
					$this->messageButtons->set('undelete',
						$this->getButton(sprintf($task, 'undelete'), 'undelete', 'message', 'moderation', 'undelete', $button, 'fa fa-check-circle-o')
					);
				}
				elseif ($topicicontype == 'image' && !$fullactions)
				{
					$this->messageButtons->set('undelete',
						$this->getButton(sprintf($task, 'undelete'), 'undelete', 'message', 'moderation', 'undelete', $button, 'kicon-undelete')
					);
				}
				else
				{
					$this->messageButtons->set('undelete',
						$this->getButton(sprintf($task, 'undelete'), 'undelete', 'message', 'moderation', 'undelete', $button)
					);
				}
			}

			if ($this->message->getTopic()->isAuthorised('permdelete'))
			{
				if ($topicicontype == 'B2' && !$fullactions)
				{
					$this->messageButtons->set('permdelete',
						$this->getButton(sprintf($task, 'permdelete'), 'permdelete', 'message', 'moderation', 'permdelete', $button, 'icon icon-notification-circle')
					);
				}
				elseif ($topicicontype == 'B3' && !$fullactions)
				{
					$this->messageButtons->set('permdelete',
						$this->getButton(sprintf($task, 'permdelete'), 'permdelete', 'message', 'moderation', 'permdelete', $button, 'glyphicon glyphicon-exclamation-sign')
					);
				}
				elseif ($topicicontype == 'fa' && !$fullactions)
				{
					$this->messageButtons->set('permdelete',
						$this->getButton(sprintf($task, 'permdelete'), 'permdelete', 'message', 'moderation', 'permdelete', $button, 'fa fa-exclamation')
					);
				}
				elseif ($topicicontype == 'image' && !$fullactions)
				{
					$this->messageButtons->set('permdelete',
						$this->getButton(sprintf($task, 'permdelete'), 'permdelete', 'message', 'moderation', 'permdelete', $button, 'kicon-permdelete')
					);
				}
				else
				{
					$this->messageButtons->set('permdelete',
						$this->getButton(sprintf($task, 'permdelete'), 'permdelete', 'message', 'moderation', 'permdelete', $button)
					);
				}
			}
		}
		elseif ($this->message->isAuthorised('delete'))
		{
			if ($topicicontype == 'B2' && !$fullactions)
			{
				$this->messageButtons->set('delete',
					$this->getButton(sprintf($task, 'delete'), 'delete', 'message', 'moderation', 'delete', $button, 'icon icon-trash'));
			}
			elseif ($topicicontype == 'B3' && !$fullactions)
			{
				$this->messageButtons->set('delete',
					$this->getButton(sprintf($task, 'delete'), 'delete', 'message', 'moderation', 'delete', $button, 'glyphicon glyphicon-trash'));
			}
			elseif ($topicicontype == 'fa' && !$fullactions)
			{
				$this->messageButtons->set('delete',
					$this->getButton(sprintf($task, 'delete'), 'delete', 'message', 'moderation', 'delete', $button, 'fa fa-trash'));
			}
			elseif ($topicicontype == 'image' && !$fullactions)
			{
				$this->messageButtons->set('delete',
					$this->getButton(sprintf($task, 'delete'), 'delete', 'message', 'moderation', 'delete', $button, 'kicon-delete'));
			}
			else
			{
				$this->messageButtons->set('delete',
					$this->getButton(sprintf($task, 'delete'), 'delete', 'message', 'moderation', 'delete', $button)
				);
			}
		}

		JPluginHelper::importPlugin('kunena');
		$dispatcher = JEventDispatcher::getInstance();
		$dispatcher->trigger('onKunenaGetButtons', array('message.action', $this->messageButtons, $this));
	}

	/**
	 * Get button.
	 *
	 * @param   string $url    Target link (do not route it).
	 * @param   string $name   Name of the button.
	 * @param   string $scope  Scope of the button.
	 * @param   string $type   Type of the button.
	 * @param   int    $id     Id of the button.
	 * @param   bool   $normal Define if the button will have the class btn or btn-small
	 *
	 * @param string   $icon
	 *
	 * @return string
	 */
	public function getButton($url, $name, $scope, $type, $id = null, $normal = true, $icon = '')
	{
		return KunenaLayout::factory('Widget/Button')
			->setProperties(array('url' => KunenaRoute::_($url), 'name' => $name, 'scope' => $scope,
			                      'type' => $type, 'id' => 'btn_' . $id, 'normal' => $normal, 'icon' => $icon));
	}
}
