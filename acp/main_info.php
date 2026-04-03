<?php
/**
 * @copyright (c) Mundo phpBB
 * @license GNU General Public License, version 2 (GPL-2.0)
 */

namespace mundophpbb\memberonboarding\acp;

class main_info
{
    public function module()
    {
        return [
            'filename'  => '\\mundophpbb\\memberonboarding\\acp\\main_module',
            'title'     => 'ACP_MEMBERONBOARDING_TITLE',
            'modes'     => [
                'settings' => [
                    'title' => 'ACP_MEMBERONBOARDING_SETTINGS',
                    'auth'  => 'ext_mundophpbb/memberonboarding && acl_a_board',
                    'cat'   => ['ACP_MEMBERONBOARDING_TITLE'],
                ],
            ],
        ];
    }
}
