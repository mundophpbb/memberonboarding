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
    'ACP_MEMBERONBOARDING_TITLE' => 'Member Onboarding',
    'ACP_MEMBERONBOARDING_SETTINGS' => 'Settings',
    'ACP_MEMBERONBOARDING_EXPLAIN' => 'Initial settings for the guided onboarding journey for new forum members.',
    'ACP_MEMBERONBOARDING_SAVED' => 'Member Onboarding settings saved successfully.',

    'ACP_MEMBERONBOARDING_ENABLE' => 'Enable onboarding',
    'ACP_MEMBERONBOARDING_ENABLE_EXPLAIN' => 'Enables the onboarding flow for new registrations.',

    'ACP_MEMBERONBOARDING_WELCOME_PM' => 'Enable automatic welcome message',
    'ACP_MEMBERONBOARDING_WELCOME_PM_EXPLAIN' => 'Sends an automatic private message to the new member using the first founder found on the board as the sender.',

    'ACP_MEMBERONBOARDING_WELCOME_SUBJECT' => 'Welcome message subject',
    'ACP_MEMBERONBOARDING_WELCOME_SUBJECT_EXPLAIN' => 'You can customize the subject. If you keep the default value, the extension uses the translated starter text.',

    'ACP_MEMBERONBOARDING_WELCOME_MESSAGE' => 'Welcome message text',
    'ACP_MEMBERONBOARDING_WELCOME_MESSAGE_EXPLAIN' => 'The welcome message is sent as a private message. You can use BBCode plus the placeholders {USERNAME}, {BOARD_URL} and {ONBOARDING_URL}.',
    'ACP_MEMBERONBOARDING_WELCOME_PREVIEW' => 'Preview',
    'ACP_MEMBERONBOARDING_WELCOME_PREVIEW_EXPLAIN' => 'This preview is shown in the ACP and simulates how the private message will look with the most common BBCode tags.',

    'ACP_MEMBERONBOARDING_STAFF_ALERT' => 'Enable staff alerts',
    'ACP_MEMBERONBOARDING_STAFF_ALERT_EXPLAIN' => 'Shows a panel of pending new members in the ACP and prioritizes open follow-up entries.',

    'ACP_MEMBERONBOARDING_CHECKLIST_ENABLE' => 'Enable starter checklist',
    'ACP_MEMBERONBOARDING_CHECKLIST_ENABLE_EXPLAIN' => 'Turns on the starter guided checklist.',

    'ACP_MEMBERONBOARDING_INDEX_WIDGET' => 'Show panel on index page',
    'ACP_MEMBERONBOARDING_INDEX_WIDGET_EXPLAIN' => 'Shows a progress panel on the board index for members with a pending journey.',

    'ACP_MEMBERONBOARDING_NAV_LINK' => 'Show link in top navigation',
    'ACP_MEMBERONBOARDING_NAV_LINK_EXPLAIN' => 'Shows a link to the journey page at the top of the board for logged-in users.',

    'ACP_MEMBERONBOARDING_RECOMMEND_FORUMS' => 'Recommended areas',
    'ACP_MEMBERONBOARDING_PROFILE_RULES' => 'Basic profile rule',
    'ACP_MEMBERONBOARDING_PROFILE_RULES_EXPLAIN' => 'Choose which fields count for the “Complete basic profile” task. Filling at least one selected field is enough. If everything is left unchecked, this requirement is disabled.',
    'ACP_MEMBERONBOARDING_PROFILE_RULES_BUILTIN' => 'Default phpBB fields',
    'ACP_MEMBERONBOARDING_PROFILE_RULES_CUSTOM' => 'Custom profile fields (optional)',
    'ACP_MEMBERONBOARDING_PROFILE_RULES_NO_CUSTOM' => 'No active custom profile fields were found on this board.',
    'ACP_MEMBERONBOARDING_PROFILE_FIELD_LOCATION' => 'Location',
    'ACP_MEMBERONBOARDING_PROFILE_FIELD_OCCUPATION' => 'Occupation',
    'ACP_MEMBERONBOARDING_PROFILE_FIELD_INTERESTS' => 'Interests',
    'ACP_MEMBERONBOARDING_PROFILE_FIELD_WEBSITE' => 'Website',
    'ACP_MEMBERONBOARDING_PROFILE_FIELD_REAL_NAME' => 'Real name',
    'ACP_MEMBERONBOARDING_PROFILE_FIELD_FACEBOOK' => 'Facebook',
    'ACP_MEMBERONBOARDING_PROFILE_FIELD_TWITTER' => 'Twitter/X',
    'ACP_MEMBERONBOARDING_PROFILE_FIELD_SKYPE' => 'Skype',
    'ACP_MEMBERONBOARDING_PROFILE_FIELD_YOUTUBE' => 'YouTube',
    'ACP_MEMBERONBOARDING_PROFILE_FIELD_CUSTOM_SUFFIX' => ' (custom profile field)',
    'ACP_MEMBERONBOARDING_RECENT_LIMIT' => 'Recent follow-up limit',
    'ACP_MEMBERONBOARDING_RECENT_LIMIT_EXPLAIN' => 'Defines how many recent records are shown in the ACP. The value is limited between 5 and 50 to avoid extra load on larger boards.',
    'ACP_MEMBERONBOARDING_RECENT_LIMIT_NOTE' => 'If there are many registrations, the ACP will only display the most recent records within this limit.',
    'ACP_MEMBERONBOARDING_LEVEL_INTEGRATED_MIN' => 'Minimum percentage for “Integrated”',
    'ACP_MEMBERONBOARDING_LEVEL_INTEGRATED_MIN_EXPLAIN' => 'When a member reaches this percentage, they leave the “New member” range and move into “Integrated”.',
    'ACP_MEMBERONBOARDING_LEVEL_ACTIVE_MIN' => 'Minimum percentage for “Active”',
    'ACP_MEMBERONBOARDING_LEVEL_ACTIVE_MIN_EXPLAIN' => 'When a member reaches this percentage, they move into the “Active” range. It must be greater than the “Integrated” value.',

    'ACP_MEMBERONBOARDING_RECOMMEND_FORUMS_EXPLAIN' => 'One recommendation per line. Use plain text or the format Title | URL to create clickable links to forums, topics or guides.',

    'ACP_MEMBERONBOARDING_FIRST_BADGE' => 'Enable starter badge',
    'ACP_MEMBERONBOARDING_FIRST_BADGE_EXPLAIN' => 'Automatically records an internal achievement when the member completes 100% of the journey.',
    'ACP_MEMBERONBOARDING_FIRST_BADGE_TITLE' => 'Starter badge title',
    'ACP_MEMBERONBOARDING_FIRST_BADGE_TITLE_EXPLAIN' => 'Name displayed for the achievement unlocked after completing the journey.',

    'ACP_MEMBERONBOARDING_STAFF_PANEL' => 'Staff alerts',
    'ACP_MEMBERONBOARDING_STAFF_PANEL_EXPLAIN' => 'Recent members with pending journeys to make early follow-up easier for the team.',

    'ACP_MEMBERONBOARDING_STATS' => 'Current data',
    'ACP_MEMBERONBOARDING_STATS_EXPLAIN' => 'Quick counters based on the onboarding progress stored by the extension.',
    'ACP_MEMBERONBOARDING_MEMBERS_TOTAL' => 'Tracked members',
    'ACP_MEMBERONBOARDING_MEMBERS_DONE' => 'Completed journeys',
    'ACP_MEMBERONBOARDING_MEMBERS_PENDING' => 'Pending journeys',
    'ACP_MEMBERONBOARDING_COMPLETION_RATE' => 'Completion rate',
    'ACP_MEMBERONBOARDING_MEMBERS_REWARDED' => 'Badges granted',
    'ACP_MEMBERONBOARDING_LEVEL_DISTRIBUTION' => 'Level distribution',
    'ACP_MEMBERONBOARDING_LEVEL_DISTRIBUTION_EXPLAIN' => 'Shows how many tracked members are in each activation range based on their current percentage.',
    'ACP_MEMBERONBOARDING_LEVEL' => 'Level',

    'ACP_MEMBERONBOARDING_RECENT_MEMBERS' => 'Recent follow-up',
    'ACP_MEMBERONBOARDING_RECENT_MEMBERS_EXPLAIN' => 'Quick summary of members tracked by the extension.',
    'ACP_MEMBERONBOARDING_CURRENT_STEP' => 'Current step',
    'ACP_MEMBERONBOARDING_PROGRESS' => 'Progress',
    'ACP_MEMBERONBOARDING_STATUS' => 'Status',
    'ACP_MEMBERONBOARDING_WELCOME_PM_STATUS' => 'Welcome message',
    'ACP_MEMBERONBOARDING_WELCOME_PM_TIME' => 'Send date',
    'ACP_MEMBERONBOARDING_BADGE_STATUS' => 'Badge',
    'ACP_MEMBERONBOARDING_BADGE_TIME' => 'Badge date',
    'ACP_MEMBERONBOARDING_STARTED' => 'Started',
    'ACP_MEMBERONBOARDING_UPDATED' => 'Updated',
    'ACP_MEMBERONBOARDING_NO_RECENT_MEMBERS' => 'No tracked members were found yet.',
    'ACP_MEMBERONBOARDING_ATTENTION' => 'Attention',
    'ACP_MEMBERONBOARDING_LAST_MOVEMENT' => 'Last movement',
    'ACP_MEMBERONBOARDING_SIGNAL_PENDING_WELCOME' => 'Pending welcome',
    'ACP_MEMBERONBOARDING_SIGNAL_STALLED' => 'Stalled',
    'ACP_MEMBERONBOARDING_SIGNAL_NEAR_FINISH' => 'Near finish',
    'ACP_MEMBERONBOARDING_SIGNAL_NEEDS_ATTENTION' => 'Needs attention',
    'ACP_MEMBERONBOARDING_SIGNAL_RECENT' => 'Recent',
    'ACP_MEMBERONBOARDING_SIGNAL_COMPLETED' => 'Completed',
    'ACP_MEMBERONBOARDING_TODAY' => 'Today',
    'ACP_MEMBERONBOARDING_DAYS_AGO_1' => '1 day ago',
    'ACP_MEMBERONBOARDING_DAYS_AGO' => '%d days ago',
]);
