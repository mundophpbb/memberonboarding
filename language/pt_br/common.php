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
    'LOG_CONFIG_MEMBERONBOARDING_UPDATED' => '<strong>Configurações do Member Onboarding atualizadas</strong>',

    'MEMBERONBOARDING_NAV_TITLE' => 'Minha jornada',
    'MEMBERONBOARDING_WIDGET_KICKER' => 'Primeiros passos',
    'MEMBERONBOARDING_WIDGET_TITLE' => 'Sua jornada inicial',
    'MEMBERONBOARDING_WIDGET_EXPLAIN' => 'Conclua os primeiros passos para ativar sua conta e conhecer melhor a comunidade.',
    'MEMBERONBOARDING_PAGE_TITLE' => 'Member Onboarding',
    'MEMBERONBOARDING_PAGE_EXPLAIN' => 'Acompanhe seu progresso e conclua os passos iniciais para começar bem no fórum.',
    'MEMBERONBOARDING_PROGRESS' => 'Progresso atual',
    'MEMBERONBOARDING_LEVEL_CURRENT' => 'Nível atual',
    'MEMBERONBOARDING_LEVEL_NEXT' => 'Próximo nível',
    'MEMBERONBOARDING_LEVEL_NEW' => 'Novo membro',
    'MEMBERONBOARDING_LEVEL_INTEGRATED' => 'Integrado',
    'MEMBERONBOARDING_LEVEL_ACTIVE' => 'Ativo',
    'MEMBERONBOARDING_LEVELS_TITLE' => 'Trilha de ativação',
    'MEMBERONBOARDING_LEVELS_EXPLAIN' => 'Além das tarefas, sua evolução também aparece em faixas para facilitar o acompanhamento.',
    'MEMBERONBOARDING_LEVEL_RANGE_STARTER' => '0%% a %d%%',
    'MEMBERONBOARDING_LEVEL_RANGE_BETWEEN' => '%1$d%% a %2$d%%',
    'MEMBERONBOARDING_LEVEL_RANGE_FROM' => 'A partir de %d%%',
    'MEMBERONBOARDING_LEVEL_UP_NEXT' => 'Faltam %1$d%% para chegar a %2$s.',
    'MEMBERONBOARDING_LEVEL_MAXED' => 'Você já está na faixa mais alta da jornada.',
    'MEMBERONBOARDING_PROGRESS_TEXT' => '%1$d de %2$d etapas concluídas',
    'MEMBERONBOARDING_DONE' => 'Concluído',
    'MEMBERONBOARDING_PENDING' => 'Pendente',
    'MEMBERONBOARDING_OPEN_TASK' => 'Abrir etapa',
    'MEMBERONBOARDING_OPEN_PAGE' => 'Ver jornada completa',
    'MEMBERONBOARDING_BACK_TO_BOARD' => 'Voltar ao fórum',
    'MEMBERONBOARDING_OPEN_PROFILE' => 'Editar perfil',
    'MEMBERONBOARDING_START_NEXT_STEP' => 'Começar próxima etapa',
    'MEMBERONBOARDING_COMPLETED_MESSAGE' => 'Parabéns. Você concluiu a jornada inicial.',
    'MEMBERONBOARDING_COMPLETED_LABEL' => 'Jornada concluída',
    'MEMBERONBOARDING_IN_PROGRESS_LABEL' => 'Em andamento',
    'MEMBERONBOARDING_NEXT_STEP' => 'Próxima etapa',
    'MEMBERONBOARDING_RECOMMENDED_ACTION' => 'Ação recomendada',
    'MEMBERONBOARDING_RECOMMENDED_ACTION_OPEN' => 'Ir para ação recomendada',
    'MEMBERONBOARDING_RECOMMENDED_ACTION_PROFILE_TITLE' => 'Preencha um ponto do seu perfil',
    'MEMBERONBOARDING_RECOMMENDED_ACTION_PROFILE_DESC' => 'Adicione ao menos uma informação de perfil definida no ACP para deixar sua conta mais completa.',
    'MEMBERONBOARDING_RECOMMENDED_ACTION_PERSONALIZE_TITLE' => 'Deixe sua conta mais reconhecível',
    'MEMBERONBOARDING_RECOMMENDED_ACTION_PERSONALIZE_DESC' => 'Inclua avatar ou assinatura para facilitar sua identificação pela comunidade.',
    'MEMBERONBOARDING_RECOMMENDED_ACTION_FIRST_POST_TITLE' => 'Faça sua primeira interação',
    'MEMBERONBOARDING_RECOMMENDED_ACTION_FIRST_POST_DESC' => 'Escolha uma área recomendada e publique sua primeira mensagem para iniciar sua participação.',
    'MEMBERONBOARDING_RECOMMENDED_ACTION_FIRST_TOPIC_TITLE' => 'Abra sua primeira conversa',
    'MEMBERONBOARDING_RECOMMENDED_ACTION_FIRST_TOPIC_DESC' => 'Crie um tópico próprio para transformar sua presença em participação ativa.',
    'MEMBERONBOARDING_RECOMMENDED_ACTION_COMPLETED_TITLE' => 'Continue participando da comunidade',
    'MEMBERONBOARDING_RECOMMENDED_ACTION_COMPLETED_DESC' => 'Sua jornada inicial foi concluída. Agora o melhor próximo passo é explorar o fórum e manter a participação.',
    'MEMBERONBOARDING_RECOMMENDED_REASON_TO_LEVEL' => 'Concluir esta ação ajuda você a avançar rumo ao nível %s.',
    'MEMBERONBOARDING_RECOMMENDED_REASON_FINAL' => 'Esta etapa ajuda a fechar sua jornada inicial com consistência.',
    'MEMBERONBOARDING_RECOMMENDED_REASON_GENERAL' => 'Esta é a melhor próxima ação para avançar sem inflar sua jornada.',
    'MEMBERONBOARDING_RECOMMENDED_REASON_COMPLETED' => 'Sua jornada inicial já foi concluída. Agora vale explorar as áreas recomendadas e seguir participando.',
    'MEMBERONBOARDING_RECOMMENDED_FORUMS' => 'Áreas recomendadas',
    'MEMBERONBOARDING_RECOMMENDED_FORUMS_EXPLAIN' => 'Estas áreas foram destacadas pela administração para ajudar no seu primeiro acesso.',
    'MEMBERONBOARDING_TASKS_TITLE' => 'Checklist de ativação',
    'MEMBERONBOARDING_TASKS_EXPLAIN' => 'Siga esta sequência para completar a ativação do seu perfil e começar a participar da comunidade.',
    'MEMBERONBOARDING_COMPLETED_TASKS_TITLE' => 'Etapas concluídas',
    'MEMBERONBOARDING_ALL_PENDING_DONE_TITLE' => 'Tudo certo por aqui',
    'MEMBERONBOARDING_ALL_PENDING_DONE_EXPLAIN' => 'No momento, não há mais etapas pendentes no seu checklist principal.',
    'MEMBERONBOARDING_TIPS_TITLE' => 'Dicas rápidas',
    'MEMBERONBOARDING_TIPS_EXPLAIN' => 'Pequenos ajustes que ajudam o novo membro a se integrar com mais rapidez.',
    'MEMBERONBOARDING_TIP_ONE' => 'Complete seu perfil para ficar mais fácil de ser reconhecido pela comunidade.',
    'MEMBERONBOARDING_TIP_TWO' => 'Escolha uma área recomendada e faça sua primeira publicação.',
    'MEMBERONBOARDING_TIP_THREE' => 'Acompanhe a barra de progresso para saber o que falta concluir.',
    'MEMBERONBOARDING_WELCOME_CARD_KICKER' => 'Boas-vindas',
    'MEMBERONBOARDING_WELCOME_CARD_EXPLAIN' => 'Sua jornada inicial reúne as ações mais importantes para transformar um novo cadastro em participação real no fórum.',
    'MEMBERONBOARDING_WELCOME_CARD_GOAL' => 'Meta inicial: %d etapas',
    'MEMBERONBOARDING_DEFAULT_WELCOME_SUBJECT' => 'Bem-vindo ao fórum',
    'MEMBERONBOARDING_DEFAULT_WELCOME_MESSAGE' => "Olá {USERNAME},

Seja bem-vindo ao fórum. Sua jornada inicial já está disponível em {ONBOARDING_URL}.

Use este espaço para completar seu perfil, conhecer as áreas recomendadas e dar os primeiros passos na comunidade.

Acesse o fórum também por: {BOARD_URL}",
    'MEMBERONBOARDING_DEFAULT_BADGE_TITLE' => 'Primeiros passos concluídos',
    'MEMBERONBOARDING_REWARD_UNLOCKED' => 'Conquista liberada',
    'MEMBERONBOARDING_REWARD_EXPLAIN' => 'Esta medalha inicial foi registrada automaticamente porque você concluiu toda a jornada de onboarding.',

    'MEMBERONBOARDING_STEP_REGISTERED' => 'Cadastro realizado',
    'MEMBERONBOARDING_STEP_COMPLETED' => 'Jornada concluída',

    'MEMBERONBOARDING_TASK_COMPLETE_PROFILE' => 'Completar perfil básico',
    'MEMBERONBOARDING_TASK_COMPLETE_PROFILE_EXPLAIN' => 'Adicione ao menos uma informação entre os campos definidos pela administração para o perfil básico.',
    'MEMBERONBOARDING_TASK_PERSONALIZE_ACCOUNT' => 'Personalizar conta',
    'MEMBERONBOARDING_TASK_PERSONALIZE_ACCOUNT_EXPLAIN' => 'Adicione um avatar ou assinatura para deixar sua conta mais reconhecível.',
    'MEMBERONBOARDING_TASK_FIRST_POST' => 'Fazer a primeira postagem',
    'MEMBERONBOARDING_TASK_FIRST_POST_EXPLAIN' => 'Publique sua primeira mensagem para começar a participar da comunidade.',
    'MEMBERONBOARDING_TASK_FIRST_TOPIC' => 'Criar o primeiro tópico',
    'MEMBERONBOARDING_TASK_FIRST_TOPIC_EXPLAIN' => 'Abra seu primeiro tópico para iniciar uma conversa no fórum.',
]);
