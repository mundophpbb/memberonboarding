<?php
/**
 * @copyright (c) Mundo phpBB
 * @license GNU General Public License, version 2 (GPL-2.0)
 */

namespace mundophpbb\memberonboarding\migrations;

class v1007_production_hardening extends \phpbb\db\migration\migration
{
    public static function depends_on()
    {
        return ['\mundophpbb\memberonboarding\migrations\v1006_activation_levels'];
    }

    public function update_schema()
    {
        return [
            'add_index' => [
                $this->table_prefix . 'memberonboarding_progress' => [
                    'mbob_upd'   => ['updated_time'],
                    'mbob_start' => ['started_time'],
                    'mbob_comp'  => ['completed_time'],
                ],
            ],
        ];
    }

    public function update_data()
    {
        return [
            ['config.remove', ['memberonboarding_welcome_message']],
        ];
    }

    public function revert_schema()
    {
        return [
            'drop_keys' => [
                $this->table_prefix . 'memberonboarding_progress' => [
                    'mbob_upd',
                    'mbob_start',
                    'mbob_comp',
                ],
            ],
        ];
    }
}
