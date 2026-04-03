<?php
/**
 * @copyright (c) Mundo phpBB
 * @license GNU General Public License, version 2 (GPL-2.0)
 */

namespace mundophpbb\memberonboarding\acp;

class main_module
{
    public $u_action;
    public $tpl_name;
    public $page_title;

    public function main($id, $mode)
    {
        global $phpbb_container;

        $this->tpl_name = 'acp_memberonboarding';
        $this->page_title = 'ACP_MEMBERONBOARDING_TITLE';

        /** @var \mundophpbb\memberonboarding\controller\acp_controller $controller */
        $controller = $phpbb_container->get('mundophpbb.memberonboarding.acp_controller');
        $controller->set_page_url($this->u_action);
        $controller->handle();
    }
}
