<?php
/**
 * @copyright (c) Mundo phpBB
 * @license GNU General Public License, version 2 (GPL-2.0)
 */

namespace mundophpbb\memberonboarding\migrations;

class v1002_welcome_flow extends \phpbb\db\migration\migration
{
    public function effectively_installed()
    {
        return isset($this->config['memberonboarding_welcome_subject'])
            && isset($this->config['memberonboarding_welcome_message'])
            && $this->db_tools->sql_column_exists($this->table_prefix . 'memberonboarding_progress', 'welcome_pm_sent')
            && $this->db_tools->sql_column_exists($this->table_prefix . 'memberonboarding_progress', 'welcome_pm_time');
    }

    public static function depends_on()
    {
        return ['\\mundophpbb\\memberonboarding\\migrations\\v1001_checklist'];
    }

    public function update_schema()
    {
        return [
            'add_columns' => [
                $this->table_prefix . 'memberonboarding_progress' => [
                    'welcome_pm_sent' => ['BOOL', 0],
                    'welcome_pm_time' => ['TIMESTAMP', 0],
                ],
            ],
        ];
    }

    public function update_data()
    {
        return [
            ['config.add', ['memberonboarding_welcome_subject', '']],
            ['config.add', ['memberonboarding_welcome_message', '']],
        ];
    }

    public function revert_schema()
    {
        return [
            'drop_columns' => [
                $this->table_prefix . 'memberonboarding_progress' => [
                    'welcome_pm_sent',
                    'welcome_pm_time',
                ],
            ],
        ];
    }
}
