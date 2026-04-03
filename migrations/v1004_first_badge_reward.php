<?php
/**
 * @copyright (c) Mundo phpBB
 * @license GNU General Public License, version 2 (GPL-2.0)
 */

namespace mundophpbb\memberonboarding\migrations;

class v1004_first_badge_reward extends \phpbb\db\migration\migration
{
    public static function depends_on()
    {
        return ['\mundophpbb\memberonboarding\migrations\v1003_profile_rules_recent_limit'];
    }

    public function effectively_installed()
    {
        return isset($this->config['memberonboarding_first_badge_title']);
    }

    public function update_data()
    {
        return [
            ['config.add', ['memberonboarding_first_badge_title', '']],
        ];
    }

    public function update_schema()
    {
        return [
            'add_columns' => [
                $this->table_prefix . 'memberonboarding_progress' => [
                    'reward_granted' => ['BOOL', 0],
                    'reward_time'    => ['TIMESTAMP', 0],
                    'reward_title'   => ['VCHAR:255', ''],
                ],
            ],
        ];
    }

    public function revert_schema()
    {
        return [
            'drop_columns' => [
                $this->table_prefix . 'memberonboarding_progress' => [
                    'reward_granted',
                    'reward_time',
                    'reward_title',
                ],
            ],
        ];
    }
}
