<?php
/**
 * @copyright (c) Mundo phpBB
 * @license GNU General Public License, version 2 (GPL-2.0)
 */

namespace mundophpbb\memberonboarding\migrations;

class v1000_install extends \phpbb\db\migration\migration
{
    public function effectively_installed()
    {
        return isset($this->config['memberonboarding_enable']);
    }

    public static function depends_on()
    {
        return ['\phpbb\db\migration\data\v330\v330'];
    }

    public function update_schema()
    {
        return [
            'add_tables' => [
                $this->table_prefix . 'memberonboarding_progress' => [
                    'COLUMNS' => [
                        'progress_id'         => ['UINT', null, 'auto_increment'],
                        'user_id'             => ['UINT', 0],
                        'checklist_completed' => ['UINT', 0],
                        'tasks_completed'     => ['UINT', 0],
                        'activation_percent'  => ['UINT', 0],
                        'current_step'        => ['VCHAR:100', 'registered'],
                        'started_time'        => ['TIMESTAMP', 0],
                        'updated_time'        => ['TIMESTAMP', 0],
                        'completed_time'      => ['TIMESTAMP', 0],
                    ],
                    'PRIMARY_KEY' => 'progress_id',
                    'KEYS' => [
                        'user_id' => ['UNIQUE', 'user_id'],
                        'current_step' => ['INDEX', 'current_step'],
                    ],
                ],
                $this->table_prefix . 'memberonboarding_tasks' => [
                    'COLUMNS' => [
                        'task_id'         => ['UINT', null, 'auto_increment'],
                        'task_key'        => ['VCHAR:100', ''],
                        'task_title'      => ['VCHAR:255', ''],
                        'task_desc'       => ['TEXT_UNI', ''],
                        'task_order'      => ['UINT', 0],
                        'task_enabled'    => ['BOOL', 1],
                        'task_reward'     => ['VCHAR:255', ''],
                        'created_time'    => ['TIMESTAMP', 0],
                        'updated_time'    => ['TIMESTAMP', 0],
                    ],
                    'PRIMARY_KEY' => 'task_id',
                    'KEYS' => [
                        'task_key' => ['UNIQUE', 'task_key'],
                        'task_order' => ['INDEX', 'task_order'],
                    ],
                ],
                $this->table_prefix . 'memberonboarding_logs' => [
                    'COLUMNS' => [
                        'log_id'       => ['UINT', null, 'auto_increment'],
                        'user_id'      => ['UINT', 0],
                        'action_key'   => ['VCHAR:100', ''],
                        'old_value'    => ['VCHAR:255', ''],
                        'new_value'    => ['VCHAR:255', ''],
                        'log_time'     => ['TIMESTAMP', 0],
                    ],
                    'PRIMARY_KEY' => 'log_id',
                    'KEYS' => [
                        'user_id' => ['INDEX', 'user_id'],
                        'action_key' => ['INDEX', 'action_key'],
                    ],
                ],
            ],
        ];
    }

    public function update_data()
    {
        return [
            ['config.add', ['memberonboarding_enable', 1]],
            ['config.add', ['memberonboarding_welcome_pm', 1]],
            ['config.add', ['memberonboarding_staff_alert', 1]],
            ['config.add', ['memberonboarding_checklist_enable', 1]],
            ['config.add', ['memberonboarding_recommend_forums', '']],
            ['config.add', ['memberonboarding_first_badge_enable', 1]],

            ['module.add', [
                'acp',
                'ACP_CAT_DOT_MODS',
                'ACP_MEMBERONBOARDING_TITLE',
            ]],
            ['module.add', [
                'acp',
                'ACP_MEMBERONBOARDING_TITLE',
                [
                    'module_basename' => '\\mundophpbb\\memberonboarding\\acp\\main_module',
                    'modes'           => ['settings'],
                ],
            ]],
        ];
    }

    public function revert_schema()
    {
        return [
            'drop_tables' => [
                $this->table_prefix . 'memberonboarding_progress',
                $this->table_prefix . 'memberonboarding_tasks',
                $this->table_prefix . 'memberonboarding_logs',
            ],
        ];
    }
}
