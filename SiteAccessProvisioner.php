<?php
/**
 * Piwik - free/libre analytics platform
 *
 * @link http://piwik.org
 * @license http://www.gnu.org/licenses/gpl-3.0.html GPL v3 or later
 */

namespace Piwik\Plugins\SiteAccessProvisioner;

use Piwik\Piwik;
use Piwik\Access;
use Piwik\Common;
use Piwik\Option;
use Piwik\Settings\Storage;
use Piwik\Settings\Setting;
use Piwik\Plugins\SitesManager\API as APISitesManager;
use Piwik\Plugins\UsersManager\API as APIUsersManager;

class SiteAccessProvisioner extends \Piwik\Plugin
{
	public static function processRequest($idUser, $site, $timestamp, $token)
	{
		$settings = new SystemSettings();
		
		$sharedSecret = $settings->sharedSecret->getValue();
		
		if(empty($sharedSecret))
		{
			throw new \Exception("Plugin function is inactive. Shared secret not set.");
		}
		
		$tokenttl = $settings->tokenttl->getValue();
		
		$enforceMatchingUserId = $settings->enforceMatchingUserId->getValue();
		
		$msg = array();
		
		$idSite = false;
		
		$computedToken = hash('sha256',implode('',[$sharedSecret, $idUser, $site, $timestamp]));
		$currentTimestamp = time();
		
		if($token === $computedToken && (!$enforceMatchingUserId || ($enforceMatchingUserId && strtolower($idUser) == strtolower(Piwik::getCurrentUserLogin()))))
		{
			if(($currentTimestamp - $timestamp) <= $tokenttl)
			{
				$idSite = (is_int($site))? $site : self::getIdSiteFromUrl($site);
				
				if($idSite !== false)
				{
					if(!Piwik::isUserHasViewAccess($idSite) && !Piwik::isUserHasAdminAccess($idSite) && !Piwik::hasUserSuperUserAccess())
					{
						self::grantSiteAccess($idSite,"view");
						if(Piwik::isUserHasViewAccess($idSite))
						{
							$msg["status"] = "success";
							$msg["message"] = sprintf("You were successfully granted view access");
						}
					}
				}else{
					$msg["status"] = "error";
					$msg["message"] = "Could not get idSite";
				}
				
			}else{
				$msg["status"] = "error";
				$msg["message"] = "Access token has expired";
			}
		}else{
			$msg["status"] = "error";
			$msg["message"] = "Invalid token";
		}
		
		return [$msg, $idSite];
	}
	
	private static function getIdSiteFromUrl($url)
	{
		$result = Access::doAsSuperUser(function() use ($url){
			$sitesManager = APISitesManager::getInstance();
			return $sitesManager->getSitesIdFromSiteUrl($url);
		});
		
		return (!empty($result))? $result[0]["idsite"] : false;
	}
	
	private static function grantSiteAccess($idSite, $accessLevel)
	{
		$login = Piwik::getCurrentUserLogin();
		
		return Access::doAsSuperUser(function() use ($login, $accessLevel, $idSite){
			$usersManager = APIUsersManager::getInstance();
			return $usersManager->setUserAccess($login, $accessLevel, $idSite);
		});
	}
}
