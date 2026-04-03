<?php
/**
 * @copyright (c) Mundo phpBB
 * @license GNU General Public License, version 2 (GPL-2.0)
 */

namespace mundophpbb\memberonboarding\migrations;

class v1005_welcome_message_config_text extends \phpbb\db\migration\migration
{
    public static function depends_on()
    {
        return ['\mundophpbb\memberonboarding\migrations\v1004_first_badge_reward'];
    }

    public function effectively_installed()
    {
        if (!$this->db_tools->sql_table_exists($this->table_prefix . 'config_text'))
        {
            return false;
        }

        $sql = 'SELECT config_name
            FROM ' . $this->table_prefix . "config_text
            WHERE config_name = 'memberonboarding_welcome_message'";
        $result = $this->db->sql_query_limit($sql, 1);
        $value = $this->db->sql_fetchfield('config_name');
        $this->db->sql_freeresult($result);

        return $value !== false;
    }

    public function update_data()
    {
        return [
            ['config_text.add', ['memberonboarding_welcome_message', '']],
        ];
    }

    public function revert_data()
    {
        return [
            ['config_text.remove', ['memberonboarding_welcome_message']],
        ];
    }
}
