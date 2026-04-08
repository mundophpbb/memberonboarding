<?php
/**
 * @copyright (c) Mundo phpBB
 * @license GNU General Public License, version 2 (GPL-2.0)
 */

namespace mundophpbb\memberonboarding\core;

class manager
{
    const PROGRESS_TOUCH_INTERVAL = 900;

    /** @var \phpbb\config\config */
    protected $config;

    /** @var \phpbb\db\driver\driver_interface */
    protected $db;

    /** @var \phpbb\language\language */
    protected $language;

    /** @var \phpbb\controller\helper */
    protected $helper;

    /** @var string */
    protected $phpbb_root_path;

    /** @var string */
    protected $php_ext;

    /** @var string */
    protected $table_prefix;

    public function __construct(
        \phpbb\config\config $config,
        \phpbb\db\driver\driver_interface $db,
        \phpbb\language\language $language,
        \phpbb\controller\helper $helper,
        $phpbb_root_path,
        $php_ext,
        $table_prefix
    ) {
        $this->config = $config;
        $this->db = $db;
        $this->language = $language;
        $this->helper = $helper;
        $this->phpbb_root_path = $phpbb_root_path;
        $this->php_ext = $php_ext;
        $this->table_prefix = $table_prefix;
    }

    public function is_enabled()
    {
        return !empty($this->config['memberonboarding_enable']);
    }

    public function is_checklist_enabled()
    {
        return $this->is_enabled() && !empty($this->config['memberonboarding_checklist_enable']);
    }

    public function handle_new_user($user_id, array $user_row = [])
    {
        $this->ensure_progress_row($user_id);
        if ($this->has_welcome_flow())
        {
            $this->send_welcome_pm_if_needed($user_id, $user_row);
        }
    }

    public function sync_user_progress(array $user_row)
    {
        $user_id = (int) $user_row['user_id'];

        if ($user_id <= 0 || !$this->is_enabled())
        {
            return [];
        }

        $progress_table = $this->table_prefix . 'memberonboarding_progress';
        $this->ensure_progress_row($user_id);
        $existing_progress = $this->get_progress_row($user_id);
        $user_row = array_merge(
            $user_row,
            $this->get_full_user_row($user_id),
            $this->get_profile_fields_row($user_id)
        );
        $tasks = $this->get_tasks($user_row);
        $total_tasks = count($tasks);
        $completed_tasks = 0;
        $current_step = 'completed';

        foreach ($tasks as $task)
        {
            if (!empty($task['is_done']))
            {
                $completed_tasks++;
                continue;
            }

            $current_step = $task['task_key'];
            break;
        }

        $activation_percent = ($total_tasks > 0) ? (int) floor(($completed_tasks / $total_tasks) * 100) : 0;
        $is_completed = ($total_tasks > 0 && $completed_tasks === $total_tasks);
        $now = time();
        $completed_time = $is_completed
            ? (!empty($existing_progress['completed_time']) ? (int) $existing_progress['completed_time'] : $now)
            : 0;
        $level_data = $this->get_activation_level_by_percent($activation_percent);
        $next_level = $this->get_next_activation_level($activation_percent);
        $reward_state = $this->has_reward_flow() ? $this->get_reward_state($existing_progress) : [
            'enabled' => false,
            'granted' => false,
            'time'    => 0,
            'title'   => '',
        ];

        $sql_ary = [
            'checklist_completed' => $completed_tasks,
            'tasks_completed'     => $completed_tasks,
            'activation_percent'  => $activation_percent,
            'current_step'        => $current_step,
            'updated_time'        => $now,
            'completed_time'      => $completed_time,
        ];

        if ($this->should_update_progress_row($existing_progress, $sql_ary, $now))
        {
            $sql = 'UPDATE ' . $progress_table . '
                SET ' . $this->db->sql_build_array('UPDATE', $sql_ary) . '
                WHERE user_id = ' . $user_id;
            $this->db->sql_query($sql);
        }

        if ($this->has_reward_flow())
        {
            $reward_state = $this->grant_completion_reward_if_needed($user_id, $existing_progress, $completed_tasks, $total_tasks);
        }

        $recommended_action = $this->build_recommended_action($tasks, $level_data, $next_level);

        return [
            'user_id'            => $user_id,
            'completed_tasks'    => $completed_tasks,
            'total_tasks'        => $total_tasks,
            'activation_percent' => $activation_percent,
            'current_step'       => $current_step,
            'completed_time'     => $completed_time,
            'is_completed'       => $is_completed,
            'activation_level_key'   => $level_data['key'],
            'activation_level_label' => $level_data['label'],
            'activation_level_range' => $level_data['range'],
            'next_level_key'         => !empty($next_level['key']) ? $next_level['key'] : '',
            'next_level_label'       => !empty($next_level['label']) ? $next_level['label'] : '',
            'next_level_range'       => !empty($next_level['range']) ? $next_level['range'] : '',
            'next_level_needed'      => !empty($next_level['needed']) ? (int) $next_level['needed'] : 0,
            'recommended_forums' => $this->get_recommended_forums(),
            'recommended_action' => $recommended_action,
            'welcome_pm_sent'    => $this->has_welcome_flow() ? !empty($existing_progress['welcome_pm_sent']) : false,
            'welcome_pm_time'    => $this->has_welcome_flow() && !empty($existing_progress['welcome_pm_time']) ? (int) $existing_progress['welcome_pm_time'] : 0,
            'reward_enabled'     => !empty($reward_state['enabled']),
            'reward_granted'     => !empty($reward_state['granted']),
            'reward_time'        => !empty($reward_state['time']) ? (int) $reward_state['time'] : 0,
            'reward_title'       => !empty($reward_state['title']) ? (string) $reward_state['title'] : '',
            'tasks'              => $tasks,
        ];
    }

    public function get_tasks(array $user_row)
    {
        if (!$this->is_checklist_enabled())
        {
            return [];
        }

        $tasks_table = $this->table_prefix . 'memberonboarding_tasks';
        $task_rows = [];
        $user_id = (int) $user_row['user_id'];
        $topic_count = null;

        $sql = 'SELECT task_key, task_title, task_desc, task_order
            FROM ' . $tasks_table . '
            WHERE task_enabled = 1
            ORDER BY task_order ASC, task_id ASC';
        $result = $this->db->sql_query($sql);

        while ($row = $this->db->sql_fetchrow($result))
        {
            if ($row['task_key'] === 'first_topic' && $topic_count === null)
            {
                $topic_count = $this->get_topic_count($user_id);
            }

            $is_done = $this->is_task_complete($row['task_key'], $user_row, $topic_count);
            $task_rows[] = [
                'task_key'    => $row['task_key'],
                'title'       => $this->language->lang($row['task_title']),
                'desc'        => $this->language->lang($row['task_desc']),
                'is_done'     => $is_done,
                'status_text' => $this->language->lang($is_done ? 'MEMBERONBOARDING_DONE' : 'MEMBERONBOARDING_PENDING'),
                'task_url'    => $this->build_task_url($row['task_key']),
            ];
        }
        $this->db->sql_freeresult($result);

        return $task_rows;
    }

    public function ensure_progress_row($user_id)
    {
        $progress_table = $this->table_prefix . 'memberonboarding_progress';
        $logs_table = $this->table_prefix . 'memberonboarding_logs';

        $sql = 'SELECT progress_id
            FROM ' . $progress_table . '
            WHERE user_id = ' . (int) $user_id;
        $result = $this->db->sql_query($sql);
        $progress_id = (int) $this->db->sql_fetchfield('progress_id');
        $this->db->sql_freeresult($result);

        if ($progress_id)
        {
            return;
        }

        $now = time();

        $sql_ary = [
            'user_id'             => (int) $user_id,
            'checklist_completed' => 0,
            'tasks_completed'     => 0,
            'activation_percent'  => 0,
            'current_step'        => 'registered',
            'started_time'        => $now,
            'updated_time'        => $now,
            'completed_time'      => 0,
        ];

        if ($this->has_welcome_flow())
        {
            $sql_ary['welcome_pm_sent'] = 0;
            $sql_ary['welcome_pm_time'] = 0;
        }

        if ($this->has_reward_flow())
        {
            $sql_ary['reward_granted'] = 0;
            $sql_ary['reward_time'] = 0;
            $sql_ary['reward_title'] = '';
        }

        $sql = 'INSERT INTO ' . $progress_table . ' ' . $this->db->sql_build_array('INSERT', $sql_ary);
        $this->db->sql_query($sql);

        $log_ary = [
            'user_id'    => (int) $user_id,
            'action_key' => 'registered',
            'old_value'  => '',
            'new_value'  => 'progress_created',
            'log_time'   => $now,
        ];

        $sql = 'INSERT INTO ' . $logs_table . ' ' . $this->db->sql_build_array('INSERT', $log_ary);
        $this->db->sql_query($sql);
    }

    public function get_recent_members($limit = 10, $only_pending = false)
    {
        $progress_table = $this->table_prefix . 'memberonboarding_progress';
        $users_table = defined('USERS_TABLE') ? USERS_TABLE : $this->table_prefix . 'users';
        $rows = [];

        $where = $only_pending ? 'WHERE p.completed_time = 0' : '';
        $welcome_fields = $this->has_welcome_flow()
            ? 'p.welcome_pm_sent, p.welcome_pm_time,'
            : '0 AS welcome_pm_sent, 0 AS welcome_pm_time,';
        $reward_fields = $this->has_reward_flow()
            ? 'p.reward_granted, p.reward_time, p.reward_title,'
            : "0 AS reward_granted, 0 AS reward_time, '' AS reward_title,";

        $sql = 'SELECT p.user_id, p.activation_percent, p.current_step, p.started_time, p.updated_time, p.completed_time,
                       ' . $welcome_fields . ' ' . $reward_fields . ' u.username, u.user_colour
            FROM ' . $progress_table . ' p
            LEFT JOIN ' . $users_table . ' u
                ON u.user_id = p.user_id
            ' . $where . '
            ORDER BY p.updated_time DESC, p.progress_id DESC';
        $result = $this->db->sql_query_limit($sql, (int) $limit);

        while ($row = $this->db->sql_fetchrow($result))
        {
            $rows[] = [
                'user_id'            => (int) $row['user_id'],
                'username'           => (string) $row['username'],
                'username_full'      => get_username_string('full', (int) $row['user_id'], (string) $row['username'], (string) $row['user_colour']),
                'activation_percent' => (int) $row['activation_percent'],
                'activation_level'   => $this->get_activation_level_by_percent((int) $row['activation_percent'])['label'],
                'current_step'       => $this->get_step_label((string) $row['current_step']),
                'started_time'       => (int) $row['started_time'],
                'updated_time'       => (int) $row['updated_time'],
                'completed_time'     => (int) $row['completed_time'],
                'welcome_pm_sent'    => (int) $row['welcome_pm_sent'],
                'welcome_pm_time'    => (int) $row['welcome_pm_time'],
                'reward_granted'     => (int) $row['reward_granted'],
                'reward_time'        => (int) $row['reward_time'],
                'reward_title'       => (string) $row['reward_title'],
                'is_completed'       => ((int) $row['completed_time'] > 0),
            ];
        }
        $this->db->sql_freeresult($result);

        return $rows;
    }

    public function get_completion_counts()
    {
        $progress_table = $this->table_prefix . 'memberonboarding_progress';
        $counts = [
            'members_total' => 0,
            'members_completed' => 0,
            'members_pending' => 0,
            'members_rewarded' => 0,
        ];

        $reward_count_sql = $this->has_reward_flow()
            ? 'SUM(CASE WHEN reward_granted > 0 THEN 1 ELSE 0 END) AS members_rewarded'
            : '0 AS members_rewarded';

        $sql = 'SELECT COUNT(*) AS members_total,
                       SUM(CASE WHEN completed_time > 0 THEN 1 ELSE 0 END) AS members_completed,
                       SUM(CASE WHEN completed_time = 0 THEN 1 ELSE 0 END) AS members_pending,
                       ' . $reward_count_sql . '
            FROM ' . $progress_table;
        $result = $this->db->sql_query($sql);
        $row = $this->db->sql_fetchrow($result);
        $this->db->sql_freeresult($result);

        if ($row)
        {
            $counts['members_total'] = (int) $row['members_total'];
            $counts['members_completed'] = (int) $row['members_completed'];
            $counts['members_pending'] = (int) $row['members_pending'];
            $counts['members_rewarded'] = (int) $row['members_rewarded'];
        }

        return $counts;
    }

    public function get_staff_alert_members($limit = 5, $days = 7)
    {
        $progress_table = $this->table_prefix . 'memberonboarding_progress';
        $users_table = defined('USERS_TABLE') ? USERS_TABLE : $this->table_prefix . 'users';
        $rows = [];
        $cutoff = time() - max(1, (int) $days) * 86400;

        $welcome_fields = $this->has_welcome_flow()
            ? 'p.welcome_pm_sent, p.welcome_pm_time,'
            : '0 AS welcome_pm_sent, 0 AS welcome_pm_time,';

        $sql = 'SELECT p.user_id, p.started_time, p.updated_time, p.activation_percent, p.current_step, ' . $welcome_fields . '
                       u.username, u.user_colour
            FROM ' . $progress_table . ' p
            LEFT JOIN ' . $users_table . ' u
                ON u.user_id = p.user_id
            WHERE p.completed_time = 0
                AND p.started_time >= ' . (int) $cutoff . '
            ORDER BY p.started_time DESC, p.progress_id DESC';
        $result = $this->db->sql_query_limit($sql, (int) $limit);

        while ($row = $this->db->sql_fetchrow($result))
        {
            $rows[] = [
                'user_id'            => (int) $row['user_id'],
                'username_full'      => get_username_string('full', (int) $row['user_id'], (string) $row['username'], (string) $row['user_colour']),
                'started_time'       => (int) $row['started_time'],
                'updated_time'       => (int) $row['updated_time'],
                'activation_percent' => (int) $row['activation_percent'],
                'activation_level'   => $this->get_activation_level_by_percent((int) $row['activation_percent'])['label'],
                'current_step'       => $this->get_step_label((string) $row['current_step']),
                'welcome_pm_sent'    => (int) $row['welcome_pm_sent'],
                'welcome_pm_time'    => (int) $row['welcome_pm_time'],
                'is_completed'       => false,
            ];
        }
        $this->db->sql_freeresult($result);

        return $rows;
    }



    public function get_recent_limit()
    {
        $limit = isset($this->config['memberonboarding_recent_limit']) ? (int) $this->config['memberonboarding_recent_limit'] : 8;
        return min(50, max(5, $limit));
    }

    public function user_has_completion_reward($user_id)
    {
        $user_id = (int) $user_id;

        if ($user_id <= 0 || !$this->is_enabled() || !$this->has_reward_flow() || empty($this->config['memberonboarding_first_badge_enable']))
        {
            return false;
        }

        $progress_row = $this->get_progress_row($user_id);

        return !empty($progress_row['reward_granted']);
    }

    public function get_builtin_profile_fields()
    {
        return [
            'user_from'      => $this->language->lang('ACP_MEMBERONBOARDING_PROFILE_FIELD_LOCATION'),
            'user_occ'       => $this->language->lang('ACP_MEMBERONBOARDING_PROFILE_FIELD_OCCUPATION'),
            'user_interests' => $this->language->lang('ACP_MEMBERONBOARDING_PROFILE_FIELD_INTERESTS'),
            'user_website'   => $this->language->lang('ACP_MEMBERONBOARDING_PROFILE_FIELD_WEBSITE'),
        ];
    }

    public function get_custom_profile_fields()
    {
        $profile_fields_table = defined('PROFILE_FIELDS_TABLE') ? PROFILE_FIELDS_TABLE : $this->table_prefix . 'profile_fields';
        $fields = [];

        $sql = 'SELECT field_ident
            FROM ' . $profile_fields_table . '
            WHERE field_active = 1
            ORDER BY field_order ASC, field_id ASC';
        $result = $this->db->sql_query($sql);

        while ($row = $this->db->sql_fetchrow($result))
        {
            if (empty($row['field_ident']))
            {
                continue;
            }

            $fields['pf_' . (string) $row['field_ident']] = (string) $row['field_ident'];
        }
        $this->db->sql_freeresult($result);

        return $fields;
    }

    public function get_selected_builtin_profile_fields()
    {
        $selected = $this->parse_csv_config(isset($this->config['memberonboarding_profile_required_builtin']) ? (string) $this->config['memberonboarding_profile_required_builtin'] : '');
        $allowed = array_keys($this->get_builtin_profile_fields());

        return array_values(array_intersect($allowed, $selected));
    }

    public function get_selected_custom_profile_fields()
    {
        $selected = $this->parse_csv_config(isset($this->config['memberonboarding_profile_required_custom']) ? (string) $this->config['memberonboarding_profile_required_custom'] : '');
        $allowed = array_keys($this->get_custom_profile_fields());
        return array_values(array_intersect($allowed, $selected));
    }

    protected function get_selected_profile_fields()
    {
        return array_values(array_unique(array_merge(
            $this->get_selected_builtin_profile_fields(),
            $this->get_selected_custom_profile_fields()
        )));
    }


    public function get_level_thresholds()
    {
        $integrated_min = isset($this->config['memberonboarding_level_integrated_min']) ? (int) $this->config['memberonboarding_level_integrated_min'] : 25;
        $active_min = isset($this->config['memberonboarding_level_active_min']) ? (int) $this->config['memberonboarding_level_active_min'] : 75;

        $integrated_min = min(99, max(1, $integrated_min));
        $active_min = min(100, max($integrated_min + 1, $active_min));

        return [
            'integrated_min' => $integrated_min,
            'active_min'     => $active_min,
        ];
    }

    public function get_activation_levels()
    {
        $thresholds = $this->get_level_thresholds();

        return [
            [
                'key'   => 'new',
                'label' => $this->language->lang('MEMBERONBOARDING_LEVEL_NEW'),
                'min'   => 0,
                'max'   => max(0, $thresholds['integrated_min'] - 1),
            ],
            [
                'key'   => 'integrated',
                'label' => $this->language->lang('MEMBERONBOARDING_LEVEL_INTEGRATED'),
                'min'   => $thresholds['integrated_min'],
                'max'   => max($thresholds['integrated_min'], $thresholds['active_min'] - 1),
            ],
            [
                'key'   => 'active',
                'label' => $this->language->lang('MEMBERONBOARDING_LEVEL_ACTIVE'),
                'min'   => $thresholds['active_min'],
                'max'   => 100,
            ],
        ];
    }

    public function get_activation_level_by_percent($percent)
    {
        $percent = min(100, max(0, (int) $percent));
        $levels = $this->get_activation_levels();
        $selected = end($levels);

        foreach ($levels as $level)
        {
            if ($percent >= (int) $level['min'] && $percent <= (int) $level['max'])
            {
                $selected = $level;
                break;
            }
        }

        $selected['range'] = $this->get_activation_level_range_text($selected);

        return $selected;
    }

    public function get_next_activation_level($percent)
    {
        $percent = min(100, max(0, (int) $percent));

        foreach ($this->get_activation_levels() as $level)
        {
            if ($percent < (int) $level['min'])
            {
                $level['needed'] = (int) $level['min'] - $percent;
                $level['range'] = $this->get_activation_level_range_text($level);
                return $level;
            }
        }

        return [];
    }

    public function get_level_distribution_counts()
    {
        $progress_table = $this->table_prefix . 'memberonboarding_progress';
        $counts = [
            'new'        => 0,
            'integrated' => 0,
            'active'     => 0,
        ];

        $sql = 'SELECT activation_percent
            FROM ' . $progress_table;
        $result = $this->db->sql_query($sql);

        while ($row = $this->db->sql_fetchrow($result))
        {
            $level = $this->get_activation_level_by_percent((int) $row['activation_percent']);
            $counts[$level['key']]++;
        }
        $this->db->sql_freeresult($result);

        return $counts;
    }

    protected function should_update_progress_row(array $existing_progress, array $new_progress, $now)
    {
        $compare_keys = [
            'checklist_completed',
            'tasks_completed',
            'activation_percent',
            'current_step',
            'completed_time',
        ];

        foreach ($compare_keys as $key)
        {
            $old_value = isset($existing_progress[$key]) ? (string) $existing_progress[$key] : '';
            $new_value = isset($new_progress[$key]) ? (string) $new_progress[$key] : '';

            if ($old_value !== $new_value)
            {
                return true;
            }
        }

        $last_touch = !empty($existing_progress['updated_time']) ? (int) $existing_progress['updated_time'] : 0;

        return ($last_touch <= 0 || (($now - $last_touch) >= self::PROGRESS_TOUCH_INTERVAL));
    }


    protected function parse_csv_config($value)
    {
        $items = array_map('trim', explode(',', (string) $value));
        $items = array_filter($items, function ($item) {
            return $item !== '';
        });
        return array_values(array_unique($items));
    }


    protected function get_activation_level_range_text(array $level)
    {
        if ((int) $level['min'] <= 0)
        {
            return $this->language->lang('MEMBERONBOARDING_LEVEL_RANGE_STARTER', (int) $level['max']);
        }

        if ((int) $level['max'] >= 100)
        {
            return $this->language->lang('MEMBERONBOARDING_LEVEL_RANGE_FROM', (int) $level['min']);
        }

        return $this->language->lang('MEMBERONBOARDING_LEVEL_RANGE_BETWEEN', (int) $level['min'], (int) $level['max']);
    }


    protected function build_recommended_action(array $tasks, array $level_data, array $next_level)
    {
        foreach ($tasks as $task)
        {
            if (empty($task['is_done']))
            {
                return [
                    'title'  => $this->get_recommended_action_title($task['task_key']),
                    'desc'   => $this->get_recommended_action_desc($task['task_key']),
                    'reason' => $this->get_recommended_action_reason($level_data, $next_level),
                    'url'    => $task['task_url'],
                    'label'  => $this->language->lang('MEMBERONBOARDING_RECOMMENDED_ACTION_OPEN'),
                ];
            }
        }

        return [
            'title'  => $this->language->lang('MEMBERONBOARDING_RECOMMENDED_ACTION_COMPLETED_TITLE'),
            'desc'   => $this->language->lang('MEMBERONBOARDING_RECOMMENDED_ACTION_COMPLETED_DESC'),
            'reason' => $this->language->lang('MEMBERONBOARDING_RECOMMENDED_REASON_COMPLETED'),
            'url'    => append_sid("{$this->phpbb_root_path}index.{$this->php_ext}"),
            'label'  => $this->language->lang('MEMBERONBOARDING_BACK_TO_BOARD'),
        ];
    }

    protected function get_recommended_action_title($task_key)
    {
        $map = [
            'complete_profile'    => 'MEMBERONBOARDING_RECOMMENDED_ACTION_PROFILE_TITLE',
            'personalize_account' => 'MEMBERONBOARDING_RECOMMENDED_ACTION_PERSONALIZE_TITLE',
            'first_post'          => 'MEMBERONBOARDING_RECOMMENDED_ACTION_FIRST_POST_TITLE',
            'first_topic'         => 'MEMBERONBOARDING_RECOMMENDED_ACTION_FIRST_TOPIC_TITLE',
        ];

        if (isset($map[$task_key]))
        {
            return $this->language->lang($map[$task_key]);
        }

        return $this->get_step_label($task_key);
    }

    protected function get_recommended_action_desc($task_key)
    {
        $map = [
            'complete_profile'    => 'MEMBERONBOARDING_RECOMMENDED_ACTION_PROFILE_DESC',
            'personalize_account' => 'MEMBERONBOARDING_RECOMMENDED_ACTION_PERSONALIZE_DESC',
            'first_post'          => 'MEMBERONBOARDING_RECOMMENDED_ACTION_FIRST_POST_DESC',
            'first_topic'         => 'MEMBERONBOARDING_RECOMMENDED_ACTION_FIRST_TOPIC_DESC',
        ];

        if (isset($map[$task_key]))
        {
            return $this->language->lang($map[$task_key]);
        }

        return '';
    }

    protected function get_recommended_action_reason(array $level_data, array $next_level)
    {
        if (!empty($next_level['label']))
        {
            return $this->language->lang('MEMBERONBOARDING_RECOMMENDED_REASON_TO_LEVEL', $next_level['label']);
        }

        if (!empty($level_data['key']) && $level_data['key'] === 'active')
        {
            return $this->language->lang('MEMBERONBOARDING_RECOMMENDED_REASON_FINAL');
        }

        return $this->language->lang('MEMBERONBOARDING_RECOMMENDED_REASON_GENERAL');
    }

    protected function has_welcome_flow()
    {
        return isset($this->config['memberonboarding_welcome_subject']);
    }

    protected function has_reward_flow()
    {
        return isset($this->config['memberonboarding_first_badge_title']);
    }

    protected function get_reward_title_value()
    {
        $value = isset($this->config['memberonboarding_first_badge_title']) ? trim((string) $this->config['memberonboarding_first_badge_title']) : '';
        return $value !== '' ? $value : $this->language->lang('MEMBERONBOARDING_DEFAULT_BADGE_TITLE');
    }

    protected function get_reward_state(array $progress_row)
    {
        return [
            'enabled' => !empty($this->config['memberonboarding_first_badge_enable']),
            'granted' => !empty($progress_row['reward_granted']),
            'time'    => !empty($progress_row['reward_time']) ? (int) $progress_row['reward_time'] : 0,
            'title'   => !empty($progress_row['reward_title']) ? (string) $progress_row['reward_title'] : $this->get_reward_title_value(),
        ];
    }

    protected function grant_completion_reward_if_needed($user_id, array $progress_row, $completed_tasks, $total_tasks)
    {
        $state = $this->get_reward_state($progress_row);

        if (empty($state['enabled']) || $total_tasks <= 0 || (int) $completed_tasks !== (int) $total_tasks)
        {
            return $state;
        }

        if (!empty($state['granted']))
        {
            return $state;
        }

        $progress_table = $this->table_prefix . 'memberonboarding_progress';
        $now = time();
        $title = $this->get_reward_title_value();

        $sql = 'UPDATE ' . $progress_table . '
            SET ' . $this->db->sql_build_array('UPDATE', [
                'reward_granted' => 1,
                'reward_time'    => $now,
                'reward_title'   => $title,
                'updated_time'   => $now,
            ]) . '
            WHERE user_id = ' . (int) $user_id;
        $this->db->sql_query($sql);

        $this->add_custom_log($user_id, 'first_badge', '', $title);

        $state['granted'] = true;
        $state['time'] = $now;
        $state['title'] = $title;

        return $state;
    }

    protected function send_welcome_pm_if_needed($user_id, array $user_row = [])
    {
        if (!$this->has_welcome_flow() || empty($this->config['memberonboarding_welcome_pm']))
        {
            return false;
        }

        if (!isset($this->config['allow_privmsg']) || empty($this->config['allow_privmsg']))
        {
            return false;
        }

        $progress_row = $this->get_progress_row($user_id);
        if (!empty($progress_row['welcome_pm_sent']))
        {
            return false;
        }

        $sender = $this->get_founder_sender();
        if (empty($sender['user_id']) || empty($sender['username']))
        {
            return false;
        }

        $subject = trim((string) $this->config['memberonboarding_welcome_subject']);
        $message = trim($this->get_welcome_message_config());

        if ($subject === '')
        {
            $subject = $this->language->lang('MEMBERONBOARDING_DEFAULT_WELCOME_SUBJECT');
        }

        if ($message === '')
        {
            $message = $this->language->lang('MEMBERONBOARDING_DEFAULT_WELCOME_MESSAGE');
        }

        $message = $this->replace_welcome_placeholders($message, $user_id, $user_row);

        if ($subject === '' || $message === '')
        {
            return false;
        }

        if (!function_exists('submit_pm'))
        {
            include_once($this->phpbb_root_path . 'includes/functions_privmsgs.' . $this->php_ext);
        }

        if (!function_exists('generate_text_for_storage'))
        {
            include_once($this->phpbb_root_path . 'includes/functions_content.' . $this->php_ext);
        }

        $uid = $bitfield = '';
        $options = 0;
        generate_text_for_storage($message, $uid, $bitfield, $options, true, true, true, true, true, true, 'post');

        $pm_data = [
            'from_user_id'    => (int) $sender['user_id'],
            'from_user_ip'    => '127.0.0.1',
            'from_username'   => (string) $sender['username'],
            'icon_id'         => 0,
            'enable_bbcode'   => true,
            'enable_smilies'  => true,
            'enable_urls'     => true,
            'enable_sig'      => false,
            'bbcode_bitfield' => $bitfield,
            'bbcode_uid'      => $uid,
            'message'         => $message,
            'address_list'    => [
                'u' => [
                    (int) $user_id => 'to',
                ],
            ],
        ];

        submit_pm('post', $subject, $pm_data, false);
        $this->mark_welcome_pm_sent($user_id);
        $this->add_custom_log($user_id, 'welcome_pm', '', 'sent');

        return true;
    }

    protected function get_welcome_message_config()
    {
        $sql = "SELECT config_value
            FROM " . $this->table_prefix . "config_text
            WHERE config_name = 'memberonboarding_welcome_message'";
        $result = $this->db->sql_query_limit($sql, 1);
        $value = $this->db->sql_fetchfield('config_value');
        $this->db->sql_freeresult($result);

        if ($value !== false && $value !== null && $value !== '')
        {
            return (string) $value;
        }

        return isset($this->config['memberonboarding_welcome_message']) ? (string) $this->config['memberonboarding_welcome_message'] : '';
    }

    protected function get_progress_row($user_id)
    {
        $progress_table = $this->table_prefix . 'memberonboarding_progress';
        $defaults = [
            'progress_id' => 0,
            'user_id' => (int) $user_id,
            'welcome_pm_sent' => 0,
            'welcome_pm_time' => 0,
            'reward_granted' => 0,
            'reward_time' => 0,
            'reward_title' => '',
        ];

        $sql = 'SELECT *
            FROM ' . $progress_table . '
            WHERE user_id = ' . (int) $user_id;
        $result = $this->db->sql_query_limit($sql, 1);
        $row = $this->db->sql_fetchrow($result);
        $this->db->sql_freeresult($result);

        if (!$row)
        {
            return $defaults;
        }

        return array_merge($defaults, $row);
    }

    protected function mark_welcome_pm_sent($user_id)
    {
        $progress_table = $this->table_prefix . 'memberonboarding_progress';
        $now = time();

        $sql = 'UPDATE ' . $progress_table . '
            SET ' . $this->db->sql_build_array('UPDATE', [
                'welcome_pm_sent' => 1,
                'welcome_pm_time' => $now,
                'updated_time'    => $now,
            ]) . '
            WHERE user_id = ' . (int) $user_id;
        $this->db->sql_query($sql);
    }

    protected function add_custom_log($user_id, $action_key, $old_value, $new_value)
    {
        $logs_table = $this->table_prefix . 'memberonboarding_logs';
        $sql = 'INSERT INTO ' . $logs_table . ' ' . $this->db->sql_build_array('INSERT', [
            'user_id'    => (int) $user_id,
            'action_key' => (string) $action_key,
            'old_value'  => (string) $old_value,
            'new_value'  => (string) $new_value,
            'log_time'   => time(),
        ]);
        $this->db->sql_query($sql);
    }

    protected function get_founder_sender()
    {
        $users_table = defined('USERS_TABLE') ? USERS_TABLE : $this->table_prefix . 'users';
        $sql = 'SELECT user_id, username
            FROM ' . $users_table . '
            WHERE user_type = ' . (defined('USER_FOUNDER') ? USER_FOUNDER : 3) . '
                AND user_inactive_reason = 0
            ORDER BY user_id ASC';
        $result = $this->db->sql_query_limit($sql, 1);
        $row = $this->db->sql_fetchrow($result);
        $this->db->sql_freeresult($result);

        return $row ?: [];
    }

    protected function replace_welcome_placeholders($message, $user_id, array $user_row = [])
    {
        $username = !empty($user_row['username']) ? (string) $user_row['username'] : $this->get_username($user_id);
        $board_url = function_exists('generate_board_url') ? generate_board_url() : '';
        $onboarding_url = '';

        try
        {
            $onboarding_url = $this->helper->route('mundophpbb_memberonboarding_main');
        }
        catch (\Exception $e)
        {
            $onboarding_url = '';
        }

        return str_replace(
            ['{USERNAME}', '{BOARD_URL}', '{ONBOARDING_URL}'],
            [$username, $board_url, $onboarding_url],
            $message
        );
    }

    protected function get_username($user_id)
    {
        $users_table = defined('USERS_TABLE') ? USERS_TABLE : $this->table_prefix . 'users';
        $sql = 'SELECT username
            FROM ' . $users_table . '
            WHERE user_id = ' . (int) $user_id;
        $result = $this->db->sql_query_limit($sql, 1);
        $username = (string) $this->db->sql_fetchfield('username');
        $this->db->sql_freeresult($result);

        return $username;
    }

    protected function is_task_complete($task_key, array $user_row, $topic_count = null)
    {
        switch ($task_key)
        {
            case 'complete_profile':
                return $this->has_profile_details($user_row);

            case 'personalize_account':
                return !empty($user_row['user_avatar']) || !empty($user_row['user_sig']);

            case 'first_post':
                return !empty($user_row['user_posts']);

            case 'first_topic':
                return ((int) $topic_count > 0);
        }

        return false;
    }

    protected function has_profile_details(array $user_row)
    {
        $fields = $this->get_selected_profile_fields();

        if (empty($fields))
        {
            return true;
        }

        foreach ($fields as $field)
        {
            if (!array_key_exists($field, $user_row) || is_array($user_row[$field]))
            {
                continue;
            }

            if (trim((string) $user_row[$field]) !== '')
            {
                return true;
            }
        }

        return false;
    }

    protected function get_full_user_row($user_id)
    {
        $users_table = defined('USERS_TABLE') ? USERS_TABLE : $this->table_prefix . 'users';

        $sql = 'SELECT *
            FROM ' . $users_table . '
            WHERE user_id = ' . (int) $user_id;
        $result = $this->db->sql_query_limit($sql, 1);
        $row = $this->db->sql_fetchrow($result);
        $this->db->sql_freeresult($result);

        return $row ?: [];
    }

    protected function get_profile_fields_row($user_id)
    {
        $profile_fields_table = defined('PROFILE_FIELDS_DATA_TABLE') ? PROFILE_FIELDS_DATA_TABLE : $this->table_prefix . 'profile_fields_data';

        $sql = 'SELECT *
            FROM ' . $profile_fields_table . '
            WHERE user_id = ' . (int) $user_id;
        $result = $this->db->sql_query_limit($sql, 1);
        $row = $this->db->sql_fetchrow($result);
        $this->db->sql_freeresult($result);

        return $row ?: [];
    }

    protected function get_topic_count($user_id)
    {
        $topics_table = defined('TOPICS_TABLE') ? TOPICS_TABLE : $this->table_prefix . 'topics';

        $sql = 'SELECT COUNT(topic_id) AS total_topics
            FROM ' . $topics_table . '
            WHERE topic_poster = ' . (int) $user_id;
        $result = $this->db->sql_query($sql);
        $count = (int) $this->db->sql_fetchfield('total_topics');
        $this->db->sql_freeresult($result);

        return $count;
    }

    protected function build_task_url($task_key)
    {
        switch ($task_key)
        {
            case 'complete_profile':
                return append_sid("{$this->phpbb_root_path}ucp.{$this->php_ext}", 'i=ucp_profile&mode=profile_info');

            case 'personalize_account':
                return append_sid("{$this->phpbb_root_path}ucp.{$this->php_ext}", 'i=ucp_profile&mode=avatar');

            case 'first_post':
            case 'first_topic':
                return append_sid("{$this->phpbb_root_path}index.{$this->php_ext}");
        }

        return append_sid("{$this->phpbb_root_path}index.{$this->php_ext}");
    }

    protected function get_step_label($current_step)
    {
        $map = [
            'registered' => 'MEMBERONBOARDING_STEP_REGISTERED',
            'complete_profile' => 'MEMBERONBOARDING_TASK_COMPLETE_PROFILE',
            'personalize_account' => 'MEMBERONBOARDING_TASK_PERSONALIZE_ACCOUNT',
            'first_post' => 'MEMBERONBOARDING_TASK_FIRST_POST',
            'first_topic' => 'MEMBERONBOARDING_TASK_FIRST_TOPIC',
            'completed' => 'MEMBERONBOARDING_STEP_COMPLETED',
        ];

        if (isset($map[$current_step]))
        {
            return $this->language->lang($map[$current_step]);
        }

        return (string) $current_step;
    }

    public function get_recommended_forums()
    {
        $items = array_filter(array_map('trim', explode(',', (string) $this->config['memberonboarding_recommend_forums'])));
        return array_values($items);
    }
}
