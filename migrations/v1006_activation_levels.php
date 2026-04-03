<?php
/**
 * @copyright (c) Mundo phpBB
 * @license GNU General Public License, version 2 (GPL-2.0)
 */

namespace mundophpbb\memberonboarding\migrations;

class v1006_activation_levels extends \phpbb\db\migration\migration
{
    public static function depends_on()
    {
        return ['\mundophpbb\memberonboarding\migrations\v1005_welcome_message_config_text'];
    }

    public function effectively_installed()
    {
        return isset($this->config['memberonboarding_level_active_min']);
    }

    public function update_data()
    {
        return [
            ['config.add', ['memberonboarding_level_integrated_min', 25]],
            ['config.add', ['memberonboarding_level_active_min', 75]],
        ];
    }
}
