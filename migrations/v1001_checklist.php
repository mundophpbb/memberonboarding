<?php
/**
 * @copyright (c) Mundo phpBB
 * @license GNU General Public License, version 2 (GPL-2.0)
 */

namespace mundophpbb\memberonboarding\migrations;

class v1001_checklist extends \phpbb\db\migration\migration
{
    public function effectively_installed()
    {
        return isset($this->config['memberonboarding_index_widget'])
            && isset($this->config['memberonboarding_nav_link']);
    }

    public static function depends_on()
    {
        return ['\mundophpbb\memberonboarding\migrations\v1000_install'];
    }

    public function update_data()
    {
        return [
            ['config.add', ['memberonboarding_index_widget', 1]],
            ['config.add', ['memberonboarding_nav_link', 1]],
            ['custom', [[$this, 'insert_default_tasks']]],
        ];
    }

    public function insert_default_tasks()
    {
        $tasks_table = $this->table_prefix . 'memberonboarding_tasks';
        $now = time();

        $tasks = [
            [
                'task_key'   => 'complete_profile',
                'task_title' => 'MEMBERONBOARDING_TASK_COMPLETE_PROFILE',
                'task_desc'  => 'MEMBERONBOARDING_TASK_COMPLETE_PROFILE_EXPLAIN',
                'task_order' => 1,
            ],
            [
                'task_key'   => 'personalize_account',
                'task_title' => 'MEMBERONBOARDING_TASK_PERSONALIZE_ACCOUNT',
                'task_desc'  => 'MEMBERONBOARDING_TASK_PERSONALIZE_ACCOUNT_EXPLAIN',
                'task_order' => 2,
            ],
            [
                'task_key'   => 'first_post',
                'task_title' => 'MEMBERONBOARDING_TASK_FIRST_POST',
                'task_desc'  => 'MEMBERONBOARDING_TASK_FIRST_POST_EXPLAIN',
                'task_order' => 3,
            ],
            [
                'task_key'   => 'first_topic',
                'task_title' => 'MEMBERONBOARDING_TASK_FIRST_TOPIC',
                'task_desc'  => 'MEMBERONBOARDING_TASK_FIRST_TOPIC_EXPLAIN',
                'task_order' => 4,
            ],
        ];

        foreach ($tasks as $task)
        {
            $sql = 'SELECT task_id
                FROM ' . $tasks_table . "
                WHERE task_key = '" . $this->db->sql_escape($task['task_key']) . "'";
            $result = $this->db->sql_query($sql);
            $task_id = (int) $this->db->sql_fetchfield('task_id');
            $this->db->sql_freeresult($result);

            if ($task_id)
            {
                continue;
            }

            $sql_ary = [
                'task_key'     => $task['task_key'],
                'task_title'   => $task['task_title'],
                'task_desc'    => $task['task_desc'],
                'task_order'   => (int) $task['task_order'],
                'task_enabled' => 1,
                'task_reward'  => '',
                'created_time' => $now,
                'updated_time' => $now,
            ];

            $sql = 'INSERT INTO ' . $tasks_table . ' ' . $this->db->sql_build_array('INSERT', $sql_ary);
            $this->db->sql_query($sql);
        }
    }
}
