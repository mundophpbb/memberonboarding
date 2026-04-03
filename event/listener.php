<?php
/**
 * @copyright (c) Mundo phpBB
 * @license GNU General Public License, version 2 (GPL-2.0)
 */

namespace mundophpbb\memberonboarding\event;

use Symfony\Component\EventDispatcher\EventSubscriberInterface;

class listener implements EventSubscriberInterface
{
    /** @var \phpbb\config\config */
    protected $config;

    /** @var \phpbb\user */
    protected $user;

    /** @var \phpbb\template\template */
    protected $template;

    /** @var \phpbb\controller\helper */
    protected $helper;

    /** @var \mundophpbb\memberonboarding\core\manager */
    protected $manager;

    public function __construct(
        \phpbb\config\config $config,
        \phpbb\user $user,
        \phpbb\template\template $template,
        \phpbb\controller\helper $helper,
        \mundophpbb\memberonboarding\core\manager $manager
    ) {
        $this->config = $config;
        $this->user = $user;
        $this->template = $template;
        $this->helper = $helper;
        $this->manager = $manager;
    }

    public static function getSubscribedEvents()
    {
        return [
            'core.user_setup'     => 'load_language_on_setup',
            'core.user_add_after' => 'create_progress_for_new_user',
            'core.page_header'    => 'assign_template_vars',
        ];
    }

    public function load_language_on_setup($event)
    {
        $lang_set_ext = $event['lang_set_ext'];
        $lang_set_ext[] = [
            'ext_name' => 'mundophpbb/memberonboarding',
            'lang_set' => 'common',
        ];
        $event['lang_set_ext'] = $lang_set_ext;
    }

    public function create_progress_for_new_user($event)
    {
        if (!$this->manager->is_enabled())
        {
            return;
        }

        $user_id = (int) $event['user_id'];
        $user_row = !empty($event['user_row']) && is_array($event['user_row']) ? $event['user_row'] : [];

        if ($user_id > 0)
        {
            $this->manager->handle_new_user($user_id, $user_row);
        }
    }

    public function assign_template_vars($event)
    {
        if ((int) $this->user->data['user_id'] === ANONYMOUS || !$this->manager->is_enabled())
        {
            return;
        }

        $summary = $this->manager->sync_user_progress($this->user->data);

        if (empty($summary))
        {
            return;
        }

        $show_nav = !empty($this->config['memberonboarding_nav_link']);
        $show_widget = !empty($this->config['memberonboarding_index_widget']) && !$summary['is_completed'];
        $page_url = $this->helper->route('mundophpbb_memberonboarding_main');
        $recommended_action = !empty($summary['recommended_action']) && is_array($summary['recommended_action']) ? $summary['recommended_action'] : [];

        $this->template->assign_vars([
            'S_MEMBERONBOARDING_NAV_LINK'      => $show_nav,
            'S_MEMBERONBOARDING_SHOW'          => $show_widget,
            'S_MEMBERONBOARDING_IS_COMPLETED'  => !empty($summary['is_completed']),
            'S_MEMBERONBOARDING_HAS_NEXT_STEP' => !empty($recommended_action),
            'S_MEMBERONBOARDING_HAS_RECOMMENDED_ACTION' => !empty($recommended_action),
            'U_MEMBERONBOARDING_PAGE'          => $page_url,
            'MEMBERONBOARDING_PERCENT'         => (int) $summary['activation_percent'],
            'MEMBERONBOARDING_COMPLETED'       => (int) $summary['completed_tasks'],
            'MEMBERONBOARDING_TOTAL'           => (int) $summary['total_tasks'],
            'MEMBERONBOARDING_PROGRESS_TEXT'   => $this->user->lang('MEMBERONBOARDING_PROGRESS_TEXT', (int) $summary['completed_tasks'], (int) $summary['total_tasks']),
            'MEMBERONBOARDING_WELCOME_STATUS'  => !empty($summary['is_completed']) ? $this->user->lang('MEMBERONBOARDING_COMPLETED_LABEL') : $this->user->lang('MEMBERONBOARDING_IN_PROGRESS_LABEL'),
            'MEMBERONBOARDING_LEVEL_LABEL'     => $summary['activation_level_label'],
            'S_MEMBERONBOARDING_HAS_NEXT_LEVEL' => !empty($summary['next_level_label']),
            'MEMBERONBOARDING_NEXT_LEVEL_TEXT' => !empty($summary['next_level_label']) ? $this->user->lang('MEMBERONBOARDING_LEVEL_UP_NEXT', (int) $summary['next_level_needed'], $summary['next_level_label']) : $this->user->lang('MEMBERONBOARDING_LEVEL_MAXED'),
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

        $tasks_for_widget = [];

        foreach ($summary['tasks'] as $task)
        {
            if (empty($task['is_done']))
            {
                $tasks_for_widget[] = $task;
            }
        }

        if (empty($tasks_for_widget))
        {
            $tasks_for_widget = $summary['tasks'];
        }

        $count = 0;
        foreach ($tasks_for_widget as $task)
        {
            $count++;
            if ($count > 3)
            {
                break;
            }

            $this->template->assign_block_vars('memberonboarding_widget_task', [
                'TASK_TITLE'       => $task['title'],
                'TASK_DESC'        => $task['desc'],
                'TASK_STATUS'      => $task['status_text'],
                'TASK_URL'         => $task['task_url'],
                'S_TASK_DONE'      => !empty($task['is_done']),
            ]);
        }
    }
}
