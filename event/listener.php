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

    /** @var array<int, bool> */
    protected $topic_reward_cache = [];

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
            'core.viewtopic_modify_post_row' => 'assign_viewtopic_miniprofile_vars',
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

        $live_progress = $this->build_live_progress($summary);
        $show_nav = !empty($this->config['memberonboarding_nav_link']);
        $show_widget = $this->manager->is_checklist_enabled() && !empty($this->config['memberonboarding_index_widget']) && !$live_progress['is_completed'];
        $page_url = $this->helper->route('mundophpbb_memberonboarding_main');
        $recommended_action = !empty($summary['recommended_action']) && is_array($summary['recommended_action']) ? $summary['recommended_action'] : [];

        $this->template->assign_vars([
            'S_MEMBERONBOARDING_NAV_LINK'      => $show_nav,
            'S_MEMBERONBOARDING_SHOW'          => $show_widget,
            'S_MEMBERONBOARDING_IS_COMPLETED'  => $live_progress['is_completed'],
            'S_MEMBERONBOARDING_HAS_NEXT_STEP' => !empty($recommended_action),
            'S_MEMBERONBOARDING_HAS_RECOMMENDED_ACTION' => !empty($recommended_action),
            'U_MEMBERONBOARDING_PAGE'          => $page_url,
            'MEMBERONBOARDING_PERCENT'         => $live_progress['percent'],
            'MEMBERONBOARDING_COMPLETED'       => $live_progress['completed'],
            'MEMBERONBOARDING_TOTAL'           => $live_progress['total'],
            'MEMBERONBOARDING_PROGRESS_TEXT'   => $this->user->lang('MEMBERONBOARDING_PROGRESS_TEXT', $live_progress['completed'], $live_progress['total']),
            'MEMBERONBOARDING_WELCOME_STATUS'  => $live_progress['is_completed'] ? $this->user->lang('MEMBERONBOARDING_COMPLETED_LABEL') : $this->user->lang('MEMBERONBOARDING_IN_PROGRESS_LABEL'),
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

    public function assign_viewtopic_miniprofile_vars($event)
    {
        if (!$this->manager->is_enabled())
        {
            return;
        }

        $poster_id = isset($event['poster_id']) ? (int) $event['poster_id'] : 0;

        if ($poster_id <= 0 || $poster_id === ANONYMOUS)
        {
            return;
        }

        if (!array_key_exists($poster_id, $this->topic_reward_cache))
        {
            $this->topic_reward_cache[$poster_id] = $this->manager->user_has_completion_reward($poster_id);
        }

        $post_row = $event['post_row'];
        $post_row['S_MEMBERONBOARDING_MINIPROFILE_STAR'] = !empty($this->topic_reward_cache[$poster_id]);
        $event['post_row'] = $post_row;
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
