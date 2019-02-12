<?php
/**
 * Piwik - free/libre analytics platform
 *
 * @link http://piwik.org
 * @license http://www.gnu.org/licenses/gpl-3.0.html GPL v3 or later
 */

namespace Piwik\Plugins\SiteAccessProvisioner;

use Piwik\Piwik;
use Piwik\Nonce;
use Piwik\Notification;
use Piwik\Common;
use Piwik\Option;
use Piwik\Settings\Storage;
use Piwik\Settings\Setting;
use Piwik\Plugin;
use Piwik\Site;
use Piwik\Url;
use Piwik\View;

class Controller extends \Piwik\Plugin\Controller
{
	const PLUGIN_NAME = "SiteAccessProvisioner";
	
    public function index()
    {

    }
	
	public function accessRequest()
	{
		Piwik::checkUserIsNotAnonymous();
		
		$idUser = trim(Common::getRequestVar('idUser', '', 'string',$_GET));
		$site = Common::getRequestVar('site', 0, 'string',$_GET);
		$timestamp = Common::getRequestVar('timestamp', 0, 'int',$_GET);
		$token = Common::getRequestVar('token', 0, 'string',$_GET);
		
		list($msg, $idSite) = SiteAccessProvisioner::processRequest($idUser,$site,$timestamp,$token);
		
		if(!empty($msg))
		{
			$notification = new Notification($msg["message"]);
			$notification->context = ($msg["status"] === "success")? Notification::CONTEXT_SUCCESS : Notification::CONTEXT_ERROR;
			$notification->type = Notification::TYPE_TOAST;
			Notification\Manager::notify(self::PLUGIN_NAME."Notice", $notification);
		}
		
		$this->redirectToIndex('CoreHome', 'index', $idSite);
	}
}