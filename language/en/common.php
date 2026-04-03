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
    'LOG_CONFIG_MEMBERONBOARDING_UPDATED' => '<strong>Member Onboarding settings updated</strong>',

    'MEMBERONBOARDING_NAV_TITLE' => 'My journey',
    'MEMBERONBOARDING_WIDGET_KICKER' => 'First steps',
    'MEMBERONBOARDING_WIDGET_TITLE' => 'Your starter journey',
    'MEMBERONBOARDING_WIDGET_EXPLAIN' => 'Complete the first steps to activate your account and get to know the community better.',
    'MEMBERONBOARDING_PAGE_TITLE' => 'Member Onboarding',
    'MEMBERONBOARDING_PAGE_EXPLAIN' => 'Track your progress and complete the first steps to get started on the forum.',
    'MEMBERONBOARDING_PROGRESS' => 'Current progress',
    'MEMBERONBOARDING_LEVEL_CURRENT' => 'Current level',
    'MEMBERONBOARDING_LEVEL_NEXT' => 'Next level',
    'MEMBERONBOARDING_LEVEL_NEW' => 'New member',
    'MEMBERONBOARDING_LEVEL_INTEGRATED' => 'Integrated',
    'MEMBERONBOARDING_LEVEL_ACTIVE' => 'Active',
    'MEMBERONBOARDING_LEVELS_TITLE' => 'Activation track',
    'MEMBERONBOARDING_LEVELS_EXPLAIN' => 'Besides the tasks, your journey is also grouped into levels to make progress easier to understand.',
    'MEMBERONBOARDING_LEVEL_RANGE_STARTER' => '0%% to %d%%',
    'MEMBERONBOARDING_LEVEL_RANGE_BETWEEN' => '%1$d%% to %2$d%%',
    'MEMBERONBOARDING_LEVEL_RANGE_FROM' => 'From %d%%',
    'MEMBERONBOARDING_LEVEL_UP_NEXT' => '%1$d%% left to reach %2$s.',
    'MEMBERONBOARDING_LEVEL_MAXED' => 'You are already in the highest journey level.',
    'MEMBERONBOARDING_PROGRESS_TEXT' => '%1$d of %2$d steps completed',
    'MEMBERONBOARDING_DONE' => 'Done',
    'MEMBERONBOARDING_PENDING' => 'Pending',
    'MEMBERONBOARDING_OPEN_TASK' => 'Open step',
    'MEMBERONBOARDING_OPEN_PAGE' => 'View full journey',
    'MEMBERONBOARDING_BACK_TO_BOARD' => 'Back to board',
    'MEMBERONBOARDING_OPEN_PROFILE' => 'Edit profile',
    'MEMBERONBOARDING_START_NEXT_STEP' => 'Start next step',
    'MEMBERONBOARDING_COMPLETED_MESSAGE' => 'Congratulations. You completed the starter journey.',
    'MEMBERONBOARDING_COMPLETED_LABEL' => 'Journey completed',
    'MEMBERONBOARDING_IN_PROGRESS_LABEL' => 'In progress',
    'MEMBERONBOARDING_NEXT_STEP' => 'Next step',
    'MEMBERONBOARDING_RECOMMENDED_ACTION' => 'Recommended action',
    'MEMBERONBOARDING_RECOMMENDED_ACTION_OPEN' => 'Open recommended action',
    'MEMBERONBOARDING_RECOMMENDED_ACTION_PROFILE_TITLE' => 'Fill in one profile detail',
    'MEMBERONBOARDING_RECOMMENDED_ACTION_PROFILE_DESC' => 'Add at least one profile detail selected in the ACP so your account looks more complete.',
    'MEMBERONBOARDING_RECOMMENDED_ACTION_PERSONALIZE_TITLE' => 'Make your account easier to recognize',
    'MEMBERONBOARDING_RECOMMENDED_ACTION_PERSONALIZE_DESC' => 'Add an avatar or signature to make recognition easier for the community.',
    'MEMBERONBOARDING_RECOMMENDED_ACTION_FIRST_POST_TITLE' => 'Make your first interaction',
    'MEMBERONBOARDING_RECOMMENDED_ACTION_FIRST_POST_DESC' => 'Choose a recommended area and publish your first message to start participating.',
    'MEMBERONBOARDING_RECOMMENDED_ACTION_FIRST_TOPIC_TITLE' => 'Open your first discussion',
    'MEMBERONBOARDING_RECOMMENDED_ACTION_FIRST_TOPIC_DESC' => 'Create your own topic to turn your presence into active participation.',
    'MEMBERONBOARDING_RECOMMENDED_ACTION_COMPLETED_TITLE' => 'Keep participating in the community',
    'MEMBERONBOARDING_RECOMMENDED_ACTION_COMPLETED_DESC' => 'Your starter journey is complete. The best next step is to explore the board and keep participating.',
    'MEMBERONBOARDING_RECOMMENDED_REASON_TO_LEVEL' => 'Completing this action helps you move toward the %s level.',
    'MEMBERONBOARDING_RECOMMENDED_REASON_FINAL' => 'This step helps close your starter journey consistently.',
    'MEMBERONBOARDING_RECOMMENDED_REASON_GENERAL' => 'This is the best next action to move forward without making the journey heavier.',
    'MEMBERONBOARDING_RECOMMENDED_REASON_COMPLETED' => 'Your starter journey is already complete. Now it is worth exploring the recommended areas and continuing to participate.',
    'MEMBERONBOARDING_RECOMMENDED_FORUMS' => 'Recommended areas',
    'MEMBERONBOARDING_RECOMMENDED_FORUMS_EXPLAIN' => 'These areas were highlighted by the administration to help with the first visit.',
    'MEMBERONBOARDING_TASKS_TITLE' => 'Activation checklist',
    'MEMBERONBOARDING_TASKS_EXPLAIN' => 'Follow this sequence to complete your profile activation and start participating in the community.',
    'MEMBERONBOARDING_COMPLETED_TASKS_TITLE' => 'Completed steps',
    'MEMBERONBOARDING_ALL_PENDING_DONE_TITLE' => 'Everything is on track',
    'MEMBERONBOARDING_ALL_PENDING_DONE_EXPLAIN' => 'At the moment, there are no more pending steps in your main checklist.',
    'MEMBERONBOARDING_TIPS_TITLE' => 'Quick tips',
    'MEMBERONBOARDING_TIPS_EXPLAIN' => 'Small adjustments that help a new member integrate faster.',
    'MEMBERONBOARDING_TIP_ONE' => 'Complete your profile so the community can recognize you more easily.',
    'MEMBERONBOARDING_TIP_TWO' => 'Choose a recommended area and make your first publication.',
    'MEMBERONBOARDING_TIP_THREE' => 'Follow the progress bar to know what is left to complete.',
    'MEMBERONBOARDING_WELCOME_CARD_KICKER' => 'Welcome',
    'MEMBERONBOARDING_WELCOME_CARD_EXPLAIN' => 'Your starter journey gathers the most important actions to turn a new registration into real participation on the forum.',
    'MEMBERONBOARDING_WELCOME_CARD_GOAL' => 'Starter goal: %d steps',
    'MEMBERONBOARDING_DEFAULT_WELCOME_SUBJECT' => 'Welcome to the forum',
    'MEMBERONBOARDING_DEFAULT_WELCOME_MESSAGE' => "Hello {USERNAME},

Welcome to the forum. Your starter journey is already available at {ONBOARDING_URL}.

Use this space to complete your profile, review the recommended areas and take your first steps in the community.

You can also access the board here: {BOARD_URL}",
    'MEMBERONBOARDING_DEFAULT_BADGE_TITLE' => 'First steps completed',
    'MEMBERONBOARDING_REWARD_UNLOCKED' => 'Achievement unlocked',
    'MEMBERONBOARDING_REWARD_EXPLAIN' => 'This starter badge was recorded automatically because you completed the full onboarding journey.',

    'MEMBERONBOARDING_STEP_REGISTERED' => 'Registration completed',
    'MEMBERONBOARDING_STEP_COMPLETED' => 'Journey completed',

    'MEMBERONBOARDING_TASK_COMPLETE_PROFILE' => 'Complete basic profile',
    'MEMBERONBOARDING_TASK_COMPLETE_PROFILE_EXPLAIN' => 'Add at least one piece of information among the fields selected by the administration for the basic profile.',
    'MEMBERONBOARDING_TASK_PERSONALIZE_ACCOUNT' => 'Personalize account',
    'MEMBERONBOARDING_TASK_PERSONALIZE_ACCOUNT_EXPLAIN' => 'Add an avatar or signature to make your account more recognizable.',
    'MEMBERONBOARDING_TASK_FIRST_POST' => 'Make the first post',
    'MEMBERONBOARDING_TASK_FIRST_POST_EXPLAIN' => 'Publish your first message to start participating in the community.',
    'MEMBERONBOARDING_TASK_FIRST_TOPIC' => 'Create the first topic',
    'MEMBERONBOARDING_TASK_FIRST_TOPIC_EXPLAIN' => 'Open your first topic to start a conversation on the forum.',
]);
