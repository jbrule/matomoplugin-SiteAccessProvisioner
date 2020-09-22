<?php
/**
 * Piwik - free/libre analytics platform
 *
 * @link http://piwik.org
 * @license http://www.gnu.org/licenses/gpl-3.0.html GPL v3 or later
 */

namespace Piwik\Plugins\SiteAccessProvisioner;

use Piwik\Settings\Setting;
use Piwik\Settings\FieldConfig;
use Piwik\Validators\NotEmpty;
use Piwik\Validators\CharacterLength;
use Piwik\Validators\NumberRange;

class SystemSettings extends \Piwik\Settings\Plugin\SystemSettings
{
    public $sharedSecret;
	
	public $tokenttl;

	public $enforceMatchingUserId;

    protected function init()
    {
        $this->sharedSecret = $this->createSharedSecretSetting();
		
        $this->tokenttl = $this->createTokenttlSetting();
		
		$this->enforceMatchingUserId = $this->createEnforceMatchingUserIdSetting();
    }

    private function createSharedSecretSetting()
    {
		return $this->makeSetting('sharedSecret', $default = '', FieldConfig::TYPE_STRING, function (FieldConfig $field) {
            $field->title = 'Shared Secret';
            $field->uiControl = FieldConfig::UI_CONTROL_TEXT;
            $field->description = 'Set the shared secret. This value should not be exposed to users.';
			$field->inlineHelp = '<br /><strong>NOTICE:</strong> The access provider must use this same value for its secret.<br /><br /><a target="_blank" href="https://github.com/jbrule/matomoplugin-SiteAccessProvisioner">How do I create an access provider?</a>';
            $field->validators[] = new NotEmpty();
			$field->validators[] = new CharacterLength(10,500);
        });
    }

    private function createTokenttlSetting()
    {
        return $this->makeSetting('Tokenttl', $default = 120, FieldConfig::TYPE_INT, function (FieldConfig $field) {
            $field->title = 'Token Time to Live';
            $field->uiControl = FieldConfig::UI_CONTROL_TEXT;
            $field->description = 'Time (in seconds) that the token is valid for.';
			$field->inlineHelp = '<br /><strong>NOTICE:</strong> If the access provider and Matomo server clocks are not in approximate sync, tokens may be flagged as expired.<br />';
            $field->validators[] = new NotEmpty();
			$field->validators[] = new NumberRange(1,3600); //1hr
        });
    }
	
	private function createEnforceMatchingUserIdSetting()
    {
        return $this->makeSetting('enforceMatchingUserId', $default = 1, FieldConfig::TYPE_BOOL, function (FieldConfig $field) {
            $field->title = 'UserId must match Matomo username';
            $field->uiControl = FieldConfig::UI_CONTROL_CHECKBOX;
            $field->description = 'Enforce checking if the idUser param provided by the access provider matches the logged in user.';
        });
    }
}
