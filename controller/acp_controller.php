<?php
/**
 * @copyright (c) Mundo phpBB
 * @license GNU General Public License, version 2 (GPL-2.0)
 */

namespace mundophpbb\memberonboarding\controller;

class acp_controller
{
    /** @var \phpbb\config\config */
    protected $config;

    /** @var \phpbb\db\driver\driver_interface */
    protected $db;

    /** @var \phpbb\request\request */
    protected $request;

    /** @var \phpbb\template\template */
    protected $template;

    /** @var \phpbb\user */
    protected $user;

    /** @var \phpbb\language\language */
    protected $language;

    /** @var \phpbb\log\log */
    protected $log;

    /** @var \mundophpbb\memberonboarding\core\manager */
    protected $manager;

    protected $phpbb_root_path;
    protected $php_ext;
    protected $table_prefix;
    protected $u_action = '';

    public function __construct(
        \phpbb\config\config $config,
        \phpbb\db\driver\driver_interface $db,
        \phpbb\request\request $request,
        \phpbb\template\template $template,
        \phpbb\user $user,
        \phpbb\language\language $language,
        \phpbb\log\log $log,
        \mundophpbb\memberonboarding\core\manager $manager,
        $phpbb_root_path,
        $php_ext,
        $table_prefix
    ) {
        $this->config = $config;
        $this->db = $db;
        $this->request = $request;
        $this->template = $template;
        $this->user = $user;
        $this->language = $language;
        $this->log = $log;
        $this->manager = $manager;
        $this->phpbb_root_path = $phpbb_root_path;
        $this->php_ext = $php_ext;
        $this->table_prefix = $table_prefix;
    }

    public function set_page_url($u_action)
    {
        $this->u_action = $u_action;
    }

    public function handle()
    {
        $this->language->add_lang('acp_memberonboarding', 'mundophpbb/memberonboarding');
        $this->language->add_lang('common', 'mundophpbb/memberonboarding');

        add_form_key('mundophpbb_memberonboarding');

        if ($this->request->is_set_post('submit'))
        {
            if (!check_form_key('mundophpbb_memberonboarding'))
            {
                trigger_error('FORM_INVALID');
            }

            $enable = $this->request->variable('memberonboarding_enable', 0);
            $welcome_pm = $this->request->variable('memberonboarding_welcome_pm', 0);
            $staff_alert = $this->request->variable('memberonboarding_staff_alert', 0);
            $checklist_enable = $this->request->variable('memberonboarding_checklist_enable', 0);
            $recommend_forums = $this->request->variable('memberonboarding_recommend_forums', '', true);
            $first_badge_enable = $this->request->variable('memberonboarding_first_badge_enable', 0);
            $index_widget = $this->request->variable('memberonboarding_index_widget', 0);
            $nav_link = $this->request->variable('memberonboarding_nav_link', 0);
            $welcome_subject = $this->request->variable('memberonboarding_welcome_subject', '', true);
            $welcome_message = $this->request->variable('memberonboarding_welcome_message', '', true);
            $profile_builtin = $this->request->variable('memberonboarding_profile_required_builtin', ['']);
            $profile_custom = $this->request->variable('memberonboarding_profile_required_custom', ['']);
            $recent_limit = $this->request->variable('memberonboarding_recent_limit', 8);
            $first_badge_title = $this->request->variable('memberonboarding_first_badge_title', '', true);
            $level_integrated_min = $this->request->variable('memberonboarding_level_integrated_min', 25);
            $level_active_min = $this->request->variable('memberonboarding_level_active_min', 75);

            $available_builtin = array_keys($this->manager->get_builtin_profile_fields());
            $available_custom = array_keys($this->manager->get_custom_profile_fields());
            $profile_builtin = array_values(array_intersect($available_builtin, array_filter((array) $profile_builtin)));
            $profile_custom = array_values(array_intersect($available_custom, array_filter((array) $profile_custom)));
            $recent_limit = min(50, max(5, (int) $recent_limit));
            $level_integrated_min = min(99, max(1, (int) $level_integrated_min));
            $level_active_min = min(100, max($level_integrated_min + 1, (int) $level_active_min));

            $this->set_config_value('memberonboarding_enable', $enable);
            $this->set_config_value('memberonboarding_welcome_pm', $welcome_pm);
            $this->set_config_value('memberonboarding_staff_alert', $staff_alert);
            $this->set_config_value('memberonboarding_checklist_enable', $checklist_enable);
            $this->set_text_config('memberonboarding_recommend_forums', $recommend_forums);
            $this->set_config_value('memberonboarding_first_badge_enable', $first_badge_enable);
            $this->set_config_value('memberonboarding_index_widget', $index_widget);
            $this->set_config_value('memberonboarding_nav_link', $nav_link);
            $this->set_config_value('memberonboarding_welcome_subject', $welcome_subject);
            $this->set_text_config('memberonboarding_welcome_message', $welcome_message);
            $this->set_config_value('memberonboarding_profile_required_builtin', implode(',', $profile_builtin));
            $this->set_config_value('memberonboarding_profile_required_custom', implode(',', $profile_custom));
            $this->set_config_value('memberonboarding_recent_limit', $recent_limit);
            $this->set_config_value('memberonboarding_first_badge_title', $first_badge_title);
            $this->set_config_value('memberonboarding_level_integrated_min', $level_integrated_min);
            $this->set_config_value('memberonboarding_level_active_min', $level_active_min);

            $this->log->add(
                'admin',
                $this->user->data['user_id'],
                $this->user->ip,
                'LOG_CONFIG_MEMBERONBOARDING_UPDATED'
            );

            trigger_error($this->language->lang('ACP_MEMBERONBOARDING_SAVED') . adm_back_link($this->u_action));
        }

        $counts = $this->manager->get_completion_counts();
        $completion_rate = 0;
        $recent_limit = $this->manager->get_recent_limit();
        $selected_builtin = $this->manager->get_selected_builtin_profile_fields();
        $selected_custom = $this->manager->get_selected_custom_profile_fields();

        if ($counts['members_total'] > 0)
        {
            $completion_rate = (int) floor(($counts['members_completed'] / $counts['members_total']) * 100);
        }

        $recent_members = $this->manager->get_recent_members($recent_limit, false);
        $staff_alert_members = !empty($this->config['memberonboarding_staff_alert']) ? $this->manager->get_staff_alert_members(min(10, $recent_limit), 7) : [];
        $level_thresholds = $this->manager->get_level_thresholds();
        $level_distribution = $this->manager->get_level_distribution_counts();

        $this->template->assign_vars([
            'U_ACTION' => $this->u_action,
            'MEMBERONBOARDING_ENABLE'           => (int) $this->config['memberonboarding_enable'],
            'MEMBERONBOARDING_WELCOME_PM'       => (int) $this->config['memberonboarding_welcome_pm'],
            'MEMBERONBOARDING_STAFF_ALERT'      => (int) $this->config['memberonboarding_staff_alert'],
            'MEMBERONBOARDING_CHECKLIST_ENABLE' => (int) $this->config['memberonboarding_checklist_enable'],
            'MEMBERONBOARDING_RECOMMEND_FORUMS' => $this->get_recommend_forums_value(),
            'MEMBERONBOARDING_FIRST_BADGE'      => (int) $this->config['memberonboarding_first_badge_enable'],
            'MEMBERONBOARDING_INDEX_WIDGET'     => (int) $this->config['memberonboarding_index_widget'],
            'MEMBERONBOARDING_NAV_LINK'         => (int) $this->config['memberonboarding_nav_link'],
            'MEMBERONBOARDING_WELCOME_SUBJECT'  => $this->get_welcome_subject_value(),
            'MEMBERONBOARDING_WELCOME_MESSAGE'  => $this->get_welcome_message_value(),
            'MEMBERONBOARDING_RECENT_LIMIT'     => $recent_limit,
            'MEMBERONBOARDING_FIRST_BADGE_TITLE' => $this->get_first_badge_title_value(),
            'MEMBERONBOARDING_LEVEL_INTEGRATED_MIN' => (int) $level_thresholds['integrated_min'],
            'MEMBERONBOARDING_LEVEL_ACTIVE_MIN'     => (int) $level_thresholds['active_min'],
            'MEMBERONBOARDING_LEVEL_NEW_COUNT'      => (int) $level_distribution['new'],
            'MEMBERONBOARDING_LEVEL_INTEGRATED_COUNT' => (int) $level_distribution['integrated'],
            'MEMBERONBOARDING_LEVEL_ACTIVE_COUNT'   => (int) $level_distribution['active'],
            'MEMBERONBOARDING_MEMBERS_TOTAL'    => $counts['members_total'],
            'MEMBERONBOARDING_MEMBERS_DONE'     => $counts['members_completed'],
            'MEMBERONBOARDING_MEMBERS_PENDING'  => $counts['members_pending'],
            'MEMBERONBOARDING_MEMBERS_REWARDED' => $counts['members_rewarded'],
            'MEMBERONBOARDING_COMPLETION_RATE'  => $completion_rate,
            'S_MEMBERONBOARDING_HAS_RECENT'     => !empty($recent_members),
            'S_MEMBERONBOARDING_HAS_ALERTS'     => !empty($staff_alert_members),
        ]);

        foreach ($this->manager->get_builtin_profile_fields() as $field_key => $field_label)
        {
            $this->template->assign_block_vars('profile_builtin_field', [
                'FIELD_KEY'   => $field_key,
                'FIELD_LABEL' => $field_label,
                'S_CHECKED'   => in_array($field_key, $selected_builtin),
            ]);
        }

        foreach ($this->manager->get_custom_profile_fields() as $field_key => $field_label)
        {
            $this->template->assign_block_vars('profile_custom_field', [
                'FIELD_KEY'   => $field_key,
                'FIELD_LABEL' => $field_label,
                'S_CHECKED'   => in_array($field_key, $selected_custom),
            ]);
        }

        foreach ($staff_alert_members as $member)
        {
            $attention = $this->build_attention_meta($member);

            $this->template->assign_block_vars('staff_alert_member', [
                'USERNAME_FULL'     => $member['username_full'],
                'STARTED_TIME'      => $this->user->format_date($member['started_time']),
                'CURRENT_STEP'      => $member['current_step'],
                'LEVEL'             => $member['activation_level'],
                'LEVEL_CLASS'       => $this->get_level_class($member['activation_level']),
                'PERCENT'           => $member['activation_percent'],
                'PROGRESS_WIDTH'    => min(100, max(0, (int) $member['activation_percent'])),
                'PM_STATUS'         => $this->language->lang($member['welcome_pm_sent'] ? 'MEMBERONBOARDING_DONE' : 'MEMBERONBOARDING_PENDING'),
                'PM_STATUS_CLASS'   => $member['welcome_pm_sent'] ? 'memberonboarding-chip-good' : 'memberonboarding-chip-alert',
                'ATTENTION_TEXT'    => $attention['label'],
                'ATTENTION_CLASS'   => $attention['class'],
                'LAST_MOVEMENT'     => $this->format_relative_days((int) $member['updated_time']),
                'ROW_CLASS'         => $attention['row_class'],
            ]);
        }

        foreach ($recent_members as $member)
        {
            $attention = $this->build_attention_meta($member);

            $this->template->assign_block_vars('recent_member', [
                'USERNAME_FULL'      => $member['username_full'],
                'CURRENT_STEP'       => $member['current_step'],
                'LEVEL'              => $member['activation_level'],
                'LEVEL_CLASS'        => $this->get_level_class($member['activation_level']),
                'PERCENT'            => $member['activation_percent'],
                'PROGRESS_WIDTH'     => min(100, max(0, (int) $member['activation_percent'])),
                'STARTED_TIME'       => $this->user->format_date($member['started_time']),
                'UPDATED_TIME'       => $this->user->format_date($member['updated_time']),
                'LAST_MOVEMENT'      => $this->format_relative_days((int) $member['updated_time']),
                'ATTENTION_TEXT'     => $attention['label'],
                'ATTENTION_CLASS'    => $attention['class'],
                'STATUS_TEXT'        => $this->language->lang($member['is_completed'] ? 'MEMBERONBOARDING_DONE' : 'MEMBERONBOARDING_PENDING'),
                'STATUS_CLASS'       => $member['is_completed'] ? 'memberonboarding-chip-good' : 'memberonboarding-chip-watch',
                'PM_STATUS_TEXT'     => $this->language->lang($member['welcome_pm_sent'] ? 'MEMBERONBOARDING_DONE' : 'MEMBERONBOARDING_PENDING'),
                'PM_STATUS_CLASS'    => $member['welcome_pm_sent'] ? 'memberonboarding-chip-good' : 'memberonboarding-chip-alert',
                'PM_TIME'            => $member['welcome_pm_time'] ? $this->user->format_date($member['welcome_pm_time']) : '-',
                'BADGE_STATUS_TEXT'  => $this->language->lang(!empty($member['reward_granted']) ? 'MEMBERONBOARDING_DONE' : 'MEMBERONBOARDING_PENDING'),
                'BADGE_STATUS_CLASS' => !empty($member['reward_granted']) ? 'memberonboarding-chip-good' : 'memberonboarding-chip-neutral',
                'BADGE_TITLE'        => !empty($member['reward_title']) ? $member['reward_title'] : $this->get_first_badge_title_value(),
                'BADGE_TIME'         => !empty($member['reward_time']) ? $this->user->format_date($member['reward_time']) : '-',
                'ROW_CLASS'          => $attention['row_class'],
            ]);
        }
    }

    protected function build_attention_meta(array $member)
    {
        $idle_days = $this->get_days_since((int) $member['updated_time']);
        $is_completed = !empty($member['is_completed']);
        $activation_percent = isset($member['activation_percent']) ? (int) $member['activation_percent'] : 0;
        $level_thresholds = $this->manager->get_level_thresholds();
        $active_min = isset($level_thresholds['active_min']) ? (int) $level_thresholds['active_min'] : 75;

        if ($is_completed)
        {
            return [
                'label' => $this->language->lang('ACP_MEMBERONBOARDING_SIGNAL_COMPLETED'),
                'class' => 'memberonboarding-chip-good',
                'row_class' => 'memberonboarding-row-done',
            ];
        }

        $welcome_enabled = !empty($this->config['memberonboarding_welcome_pm']);

        if ($welcome_enabled && empty($member['welcome_pm_sent']))
        {
            return [
                'label' => $this->language->lang('ACP_MEMBERONBOARDING_SIGNAL_PENDING_WELCOME'),
                'class' => 'memberonboarding-chip-alert',
                'row_class' => 'memberonboarding-row-alert',
            ];
        }

        if ($idle_days >= 7)
        {
            return [
                'label' => $this->language->lang('ACP_MEMBERONBOARDING_SIGNAL_STALLED'),
                'class' => 'memberonboarding-chip-alert',
                'row_class' => 'memberonboarding-row-alert',
            ];
        }

        if ($activation_percent >= $active_min)
        {
            return [
                'label' => $this->language->lang('ACP_MEMBERONBOARDING_SIGNAL_NEAR_FINISH'),
                'class' => 'memberonboarding-chip-good',
                'row_class' => 'memberonboarding-row-pending',
            ];
        }

        if ($idle_days >= 3)
        {
            return [
                'label' => $this->language->lang('ACP_MEMBERONBOARDING_SIGNAL_NEEDS_ATTENTION'),
                'class' => 'memberonboarding-chip-watch',
                'row_class' => 'memberonboarding-row-pending',
            ];
        }

        return [
            'label' => $this->language->lang('ACP_MEMBERONBOARDING_SIGNAL_RECENT'),
            'class' => 'memberonboarding-chip-neutral',
            'row_class' => 'memberonboarding-row-pending',
        ];
    }

    protected function get_level_class($level_label)
    {
        switch ((string) $level_label)
        {
            case $this->language->lang('MEMBERONBOARDING_LEVEL_ACTIVE'):
                return 'memberonboarding-chip-good';

            case $this->language->lang('MEMBERONBOARDING_LEVEL_INTEGRATED'):
                return 'memberonboarding-chip-watch';

            default:
                return 'memberonboarding-chip-neutral';
        }
    }

    protected function get_days_since($timestamp)
    {
        $timestamp = (int) $timestamp;

        if ($timestamp <= 0)
        {
            return 999;
        }

        return (int) floor(max(0, time() - $timestamp) / 86400);
    }

    protected function format_relative_days($timestamp)
    {
        $days = $this->get_days_since($timestamp);

        if ($days <= 0)
        {
            return $this->language->lang('ACP_MEMBERONBOARDING_TODAY');
        }

        if ($days === 1)
        {
            return $this->language->lang('ACP_MEMBERONBOARDING_DAYS_AGO_1');
        }

        return $this->language->lang('ACP_MEMBERONBOARDING_DAYS_AGO', $days);
    }


    protected function set_config_value($key, $value)
    {
        $key = (string) $key;
        $value = (string) $value;

        if (isset($this->config[$key]))
        {
            $this->config->set($key, $value);
            return;
        }

        $sql = "SELECT config_name
            FROM " . $this->table_prefix . "config
            WHERE config_name = '" . $this->db->sql_escape($key) . "'";
        $result = $this->db->sql_query_limit($sql, 1);
        $exists = (bool) $this->db->sql_fetchfield('config_name');
        $this->db->sql_freeresult($result);

        if ($exists)
        {
            $sql = "UPDATE " . $this->table_prefix . "config
                SET config_value = '" . $this->db->sql_escape($value) . "'
                WHERE config_name = '" . $this->db->sql_escape($key) . "'";
            $this->db->sql_query($sql);
            return;
        }

        $this->config->set($key, $value);
    }


    protected function get_recommend_forums_value()
    {
        $value = $this->get_text_config('memberonboarding_recommend_forums');

        if ($value === '' && isset($this->config['memberonboarding_recommend_forums']))
        {
            $value = (string) $this->config['memberonboarding_recommend_forums'];
        }

        return (string) $value;
    }

    protected function get_welcome_subject_value()
    {
        $value = (string) $this->config['memberonboarding_welcome_subject'];
        return $value !== '' ? $value : $this->language->lang('MEMBERONBOARDING_DEFAULT_WELCOME_SUBJECT');
    }

    protected function get_welcome_message_value()
    {
        $value = $this->get_text_config('memberonboarding_welcome_message');

        if ($value === '' && isset($this->config['memberonboarding_welcome_message']))
        {
            $value = (string) $this->config['memberonboarding_welcome_message'];
        }

        return $value !== '' ? $value : $this->language->lang('MEMBERONBOARDING_DEFAULT_WELCOME_MESSAGE');
    }

    protected function get_text_config($key)
    {
        $sql = "SELECT config_value
            FROM " . $this->table_prefix . "config_text
            WHERE config_name = '" . $this->db->sql_escape($key) . "'";
        $result = $this->db->sql_query_limit($sql, 1);
        $value = $this->db->sql_fetchfield('config_value');
        $this->db->sql_freeresult($result);

        return ($value === false || $value === null) ? '' : (string) $value;
    }

    protected function set_text_config($key, $value)
    {
        $sql = "UPDATE " . $this->table_prefix . "config_text
            SET config_value = '" . $this->db->sql_escape((string) $value) . "'
            WHERE config_name = '" . $this->db->sql_escape($key) . "'";
        $this->db->sql_query($sql);

        if (!$this->db->sql_affectedrows())
        {
            $sql = 'INSERT INTO ' . $this->table_prefix . 'config_text ' . $this->db->sql_build_array('INSERT', [
                'config_name'  => (string) $key,
                'config_value' => (string) $value,
            ]);
            $this->db->sql_query($sql);
        }
    }

    protected function get_first_badge_title_value()
    {
        $value = isset($this->config['memberonboarding_first_badge_title']) ? (string) $this->config['memberonboarding_first_badge_title'] : '';
        return $value !== '' ? $value : $this->language->lang('MEMBERONBOARDING_DEFAULT_BADGE_TITLE');
    }
}
