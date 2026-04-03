<?php
/**
 * @copyright (c) Mundo phpBB
 * @license GNU General Public License, version 2 (GPL-2.0)
 */

if (!defined('IN_PHPBB'))
{
    exit;
}

if (empty($lang) || !is_array($lang))
{
    $lang = [];
}

$lang = array_merge($lang, [
    'ACP_MEMBERONBOARDING_TITLE'    => 'Onboarding de Membros',
    'ACP_MEMBERONBOARDING_SETTINGS' => 'Configurações',
]);
