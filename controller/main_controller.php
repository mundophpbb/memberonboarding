<?php
/**
 * @copyright (c) Mundo phpBB
 * @license GNU General Public License, version 2 (GPL-2.0)
 */

namespace mundophpbb\memberonboarding\controller;

class main_controller
{
    /** @var \phpbb\controller\helper */
    protected $helper;

    /** @var \phpbb\template\template */
    protected $template;

    /** @var \phpbb\user */
    protected $user;

    /** @var \phpbb\language\language */
    protected $language;

    /** @var \mundophpbb\memberonboarding\core\manager */
    protected $manager;

    /** @var string */
    protected $phpbb_root_path;

    /** @var string */
    protected $php_ext;

    public function __construct(
        \phpbb\controller\helper $helper,
        \phpbb\template\template $template,
        \phpbb\user $user,
        \phpbb\language\language $language,
        \mundophpbb\memberonboarding\core\manager $manager,
        $phpbb_root_path,
        $php_ext
    ) {
        $this->helper = $helper;
        $this->template = $template;
        $this->user = $user;
        $this->language = $language;
        $this->manager = $manager;
        $this->phpbb_root_path = $phpbb_root_path;
        $this->php_ext = $php_ext;
    }

    public function handle()
    {
        if ((int) $this->user->data['user_id'] === ANONYMOUS)
        {
            login_box($this->helper->route('mundophpbb_memberonboarding_main'));
        }

        $this->language->add_lang('common', 'mundophpbb/memberonboarding');

        $summary = $this->manager->sync_user_progress($this->user->data);
        $recommended = !empty($summary['recommended_forums']) ? $summary['recommended_forums'] : [];
        $recommended_action = !empty($summary['recommended_action']) && is_array($summary['recommended_action']) ? $summary['recommended_action'] : [];
        $live_progress = $this->build_live_progress($summary);

        $this->template->assign_vars([
            'S_MEMBERONBOARDING_PAGE'            => true,
            'S_MEMBERONBOARDING_IS_COMPLETED'    => $live_progress['is_completed'],
            'S_MEMBERONBOARDING_SHOW_WELCOME'    => true,
            'S_MEMBERONBOARDING_HAS_RECOMMENDED' => !empty($recommended),
            'S_MEMBERONBOARDING_HAS_NEXT_STEP'   => !empty($recommended_action),
            'S_MEMBERONBOARDING_HAS_RECOMMENDED_ACTION' => !empty($recommended_action),
            'MEMBERONBOARDING_PERCENT'           => $live_progress['percent'],
            'MEMBERONBOARDING_COMPLETED'         => $live_progress['completed'],
            'MEMBERONBOARDING_TOTAL'             => $live_progress['total'],
            'MEMBERONBOARDING_PROGRESS_TEXT'     => $this->language->lang('MEMBERONBOARDING_PROGRESS_TEXT', $live_progress['completed'], $live_progress['total']),
            'MEMBERONBOARDING_USERNAME'          => $this->user->data['username'],
            'MEMBERONBOARDING_WELCOME_TEXT'      => $this->language->lang('MEMBERONBOARDING_WELCOME_CARD_EXPLAIN'),
            'MEMBERONBOARDING_WELCOME_KICKER'    => $this->language->lang('MEMBERONBOARDING_WELCOME_CARD_KICKER'),
            'MEMBERONBOARDING_WELCOME_GOAL'      => $this->language->lang('MEMBERONBOARDING_WELCOME_CARD_GOAL', $live_progress['total']),
            'MEMBERONBOARDING_WELCOME_STATUS'    => $live_progress['is_completed'] ? $this->language->lang('MEMBERONBOARDING_COMPLETED_LABEL') : $this->language->lang('MEMBERONBOARDING_IN_PROGRESS_LABEL'),
            'MEMBERONBOARDING_LEVEL_LABEL'       => $summary['activation_level_label'],
            'MEMBERONBOARDING_LEVEL_RANGE'       => $summary['activation_level_range'],
            'S_MEMBERONBOARDING_HAS_NEXT_LEVEL'  => !empty($summary['next_level_label']),
            'MEMBERONBOARDING_NEXT_LEVEL_LABEL'  => !empty($summary['next_level_label']) ? $summary['next_level_label'] : '',
            'MEMBERONBOARDING_NEXT_LEVEL_RANGE'  => !empty($summary['next_level_range']) ? $summary['next_level_range'] : '',
            'MEMBERONBOARDING_NEXT_LEVEL_TEXT'   => !empty($summary['next_level_label']) ? $this->language->lang('MEMBERONBOARDING_LEVEL_UP_NEXT', (int) $summary['next_level_needed'], $summary['next_level_label']) : $this->language->lang('MEMBERONBOARDING_LEVEL_MAXED'),
            'S_MEMBERONBOARDING_HAS_REWARD'      => !empty($summary['reward_enabled']) && !empty($summary['reward_granted']),
            'MEMBERONBOARDING_REWARD_TITLE'      => !empty($summary['reward_title']) ? $summary['reward_title'] : '',
            'MEMBERONBOARDING_REWARD_TIME'       => !empty($summary['reward_time']) ? $this->user->format_date((int) $summary['reward_time']) : '',
            'U_MEMBERONBOARDING_BOARD'           => append_sid("{$this->phpbb_root_path}index.{$this->php_ext}"),
            'U_MEMBERONBOARDING_PROFILE'         => append_sid("{$this->phpbb_root_path}ucp.{$this->php_ext}", 'i=ucp_profile&mode=profile_info'),
        ]);

        if (!empty($recommended_action))
        {
            $this->template->assign_vars([
                'MEMBERONBOARDING_NEXT_STEP_TITLE' => $recommended_action['title'],
                'MEMBERONBOARDING_NEXT_STEP_DESC'  => $recommended_action['desc'],
                'MEMBERONBOARDING_RECOMMENDED_ACTION_REASON' => $recommended_action['reason'],
                'MEMBERONBOARDING_RECOMMENDED_ACTION_LABEL'  => $recommended_action['label'],
                'U_MEMBERONBOARDING_NEXT_STEP'     => $recommended_action['url'],
            ]);
        }

        $pending_tasks = 0;
        $completed_tasks = 0;

        foreach ($summary['tasks'] as $task)
        {
            $block_name = !empty($task['is_done']) ? 'memberonboarding_completed_task' : 'memberonboarding_pending_task';

            if (!empty($task['is_done']))
            {
                $completed_tasks++;
            }
            else
            {
                $pending_tasks++;
            }

            $this->template->assign_block_vars($block_name, [
                'TASK_TITLE'  => $task['title'],
                'TASK_DESC'   => $task['desc'],
                'TASK_STATUS' => $task['status_text'],
                'TASK_URL'    => $task['task_url'],
                'S_TASK_DONE' => !empty($task['is_done']),
            ]);
        }

        $this->template->assign_vars([
            'S_MEMBERONBOARDING_HAS_PENDING_TASKS'   => ($pending_tasks > 0),
            'S_MEMBERONBOARDING_HAS_COMPLETED_TASKS' => ($completed_tasks > 0),
            'MEMBERONBOARDING_PENDING_TASKS'         => $pending_tasks,
            'MEMBERONBOARDING_COMPLETED_TASKS'       => $completed_tasks,
        ]);

        foreach ($recommended as $forum_name)
        {
            $this->template->assign_block_vars('recommended_forum', [
                'FORUM_NAME' => $forum_name,
            ]);
        }

        foreach ($this->manager->get_activation_levels() as $level)
        {
            $level_details = $this->manager->get_activation_level_by_percent((int) $level['min']);
            $this->template->assign_block_vars('activation_level', [
                'LEVEL_LABEL' => $level['label'],
                'LEVEL_RANGE' => $level_details['range'],
                'S_CURRENT'   => ($summary['activation_level_key'] === $level['key']),
            ]);
        }

        return $this->helper->render('memberonboarding_body.html', $this->language->lang('MEMBERONBOARDING_PAGE_TITLE'));
    }

    protected function build_live_progress(array $summary)
    {
        $tasks = !empty($summary['tasks']) && is_array($summary['tasks']) ? $summary['tasks'] : [];
        $total = count($tasks);
        $completed = 0;

        foreach ($tasks as $task)
        {
            if (!empty($task['is_done']))
            {
                $completed++;
            }
        }

        if ($total <= 0)
        {
            return [
                'completed' => (int) (!empty($summary['completed_tasks']) ? $summary['completed_tasks'] : 0),
                'total' => (int) (!empty($summary['total_tasks']) ? $summary['total_tasks'] : 0),
                'percent' => (int) (!empty($summary['activation_percent']) ? $summary['activation_percent'] : 0),
                'is_completed' => !empty($summary['is_completed']),
            ];
        }

        return [
            'completed' => $completed,
            'total' => $total,
            'percent' => (int) floor(($completed / $total) * 100),
            'is_completed' => ($completed === $total),
        ];
    }
}
