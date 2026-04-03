<?php
/**
 * @copyright (c) Mundo phpBB
 * @license GNU General Public License, version 2 (GPL-2.0)
 */

namespace mundophpbb\memberonboarding\migrations;

class v1003_profile_rules_recent_limit extends \phpbb\db\migration\migration
{
    public function effectively_installed()
    {
        return isset($this->config['memberonboarding_profile_required_builtin'])
            && isset($this->config['memberonboarding_profile_required_custom'])
            && isset($this->config['memberonboarding_recent_limit']);
    }

    public static function depends_on()
    {
        return ['\mundophpbb\memberonboarding\migrations\v1002_welcome_flow'];
    }

    public function update_data()
    {
        return [
            ['config.add', ['memberonboarding_profile_required_builtin', 'user_from,user_occ,user_interests,user_website']],
            ['config.add', ['memberonboarding_profile_required_custom', '']],
            ['config.add', ['memberonboarding_recent_limit', 8]],
        ];
    }
}
