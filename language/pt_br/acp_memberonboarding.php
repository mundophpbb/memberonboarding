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
    'ACP_MEMBERONBOARDING_SETTINGS' => 'Configurações',
    'ACP_MEMBERONBOARDING_EXPLAIN' => 'Configurações iniciais da jornada guiada de onboarding para novos membros do fórum.',
    'ACP_MEMBERONBOARDING_SAVED' => 'Configurações do Member Onboarding salvas com sucesso.',

    'ACP_MEMBERONBOARDING_ENABLE' => 'Ativar onboarding',
    'ACP_MEMBERONBOARDING_ENABLE_EXPLAIN' => 'Ativa o fluxo de onboarding para novos cadastros.',

    'ACP_MEMBERONBOARDING_WELCOME_PM' => 'Ativar mensagem automática de boas-vindas',
    'ACP_MEMBERONBOARDING_WELCOME_PM_EXPLAIN' => 'Envia uma mensagem privada automática ao novo membro usando o primeiro fundador encontrado como remetente.',

    'ACP_MEMBERONBOARDING_WELCOME_SUBJECT' => 'Assunto da mensagem de boas-vindas',
    'ACP_MEMBERONBOARDING_WELCOME_SUBJECT_EXPLAIN' => 'Você pode personalizar o assunto. Se deixar o valor padrão, a extensão usa o texto inicial traduzido.',

    'ACP_MEMBERONBOARDING_WELCOME_MESSAGE' => 'Texto da mensagem de boas-vindas',
    'ACP_MEMBERONBOARDING_WELCOME_MESSAGE_EXPLAIN' => 'Você pode usar os marcadores {USERNAME}, {BOARD_URL} e {ONBOARDING_URL}.',

    'ACP_MEMBERONBOARDING_STAFF_ALERT' => 'Ativar avisos para a staff',
    'ACP_MEMBERONBOARDING_STAFF_ALERT_EXPLAIN' => 'Mostra um painel de novos membros pendentes no ACP e prioriza os acompanhamentos em aberto.',

    'ACP_MEMBERONBOARDING_CHECKLIST_ENABLE' => 'Ativar checklist inicial',
    'ACP_MEMBERONBOARDING_CHECKLIST_ENABLE_EXPLAIN' => 'Liga o checklist guiado inicial.',

    'ACP_MEMBERONBOARDING_INDEX_WIDGET' => 'Exibir painel na página inicial',
    'ACP_MEMBERONBOARDING_INDEX_WIDGET_EXPLAIN' => 'Mostra um painel de progresso no índice do fórum para membros com jornada pendente.',

    'ACP_MEMBERONBOARDING_NAV_LINK' => 'Exibir link no menu superior',
    'ACP_MEMBERONBOARDING_NAV_LINK_EXPLAIN' => 'Mostra um link para a página da jornada no topo do fórum para usuários logados.',

    'ACP_MEMBERONBOARDING_RECOMMEND_FORUMS' => 'Áreas recomendadas',
    'ACP_MEMBERONBOARDING_PROFILE_RULES' => 'Critério do perfil básico',
    'ACP_MEMBERONBOARDING_PROFILE_RULES_EXPLAIN' => 'Escolha quais campos contam para a tarefa “Completar perfil básico”. Basta preencher pelo menos um dos campos selecionados. Se deixar tudo desmarcado, esse critério fica desativado.',
    'ACP_MEMBERONBOARDING_PROFILE_RULES_BUILTIN' => 'Campos padrão do phpBB',
    'ACP_MEMBERONBOARDING_PROFILE_RULES_CUSTOM' => 'Campos personalizados de perfil (opcional)',
    'ACP_MEMBERONBOARDING_PROFILE_RULES_NO_CUSTOM' => 'Nenhum campo personalizado ativo foi encontrado no fórum.',
    'ACP_MEMBERONBOARDING_PROFILE_FIELD_LOCATION' => 'Localização',
    'ACP_MEMBERONBOARDING_PROFILE_FIELD_OCCUPATION' => 'Ocupação',
    'ACP_MEMBERONBOARDING_PROFILE_FIELD_INTERESTS' => 'Interesses',
    'ACP_MEMBERONBOARDING_PROFILE_FIELD_WEBSITE' => 'Website',
    'ACP_MEMBERONBOARDING_PROFILE_FIELD_REAL_NAME' => 'Nome real',
    'ACP_MEMBERONBOARDING_PROFILE_FIELD_FACEBOOK' => 'Facebook',
    'ACP_MEMBERONBOARDING_PROFILE_FIELD_TWITTER' => 'Twitter/X',
    'ACP_MEMBERONBOARDING_PROFILE_FIELD_SKYPE' => 'Skype',
    'ACP_MEMBERONBOARDING_PROFILE_FIELD_YOUTUBE' => 'YouTube',
    'ACP_MEMBERONBOARDING_PROFILE_FIELD_CUSTOM_SUFFIX' => ' (campo personalizado)',
    'ACP_MEMBERONBOARDING_RECENT_LIMIT' => 'Limite do acompanhamento recente',
    'ACP_MEMBERONBOARDING_RECENT_LIMIT_EXPLAIN' => 'Define quantos registros recentes serão exibidos no ACP. O valor é limitado entre 5 e 50 para não pesar em fóruns maiores.',
    'ACP_MEMBERONBOARDING_RECENT_LIMIT_NOTE' => 'Se houver muitos cadastros, o ACP exibirá apenas os registros mais recentes dentro deste limite.',
    'ACP_MEMBERONBOARDING_LEVEL_INTEGRATED_MIN' => 'Percentual mínimo para “Integrado”',
    'ACP_MEMBERONBOARDING_LEVEL_INTEGRATED_MIN_EXPLAIN' => 'Quando o membro atingir este percentual, ele sai da faixa “Novo membro” e entra em “Integrado”.',
    'ACP_MEMBERONBOARDING_LEVEL_ACTIVE_MIN' => 'Percentual mínimo para “Ativo”',
    'ACP_MEMBERONBOARDING_LEVEL_ACTIVE_MIN_EXPLAIN' => 'Quando o membro atingir este percentual, ele passa para a faixa “Ativo”. Deve ser maior que o valor de “Integrado”.',

    'ACP_MEMBERONBOARDING_RECOMMEND_FORUMS_EXPLAIN' => 'Lista separada por vírgula com nomes de áreas ou fóruns recomendados para novos membros.',

    'ACP_MEMBERONBOARDING_FIRST_BADGE' => 'Ativar medalha inicial',
    'ACP_MEMBERONBOARDING_FIRST_BADGE_EXPLAIN' => 'Registra automaticamente uma conquista interna quando o membro conclui 100% da jornada.',
    'ACP_MEMBERONBOARDING_FIRST_BADGE_TITLE' => 'Título da medalha inicial',
    'ACP_MEMBERONBOARDING_FIRST_BADGE_TITLE_EXPLAIN' => 'Nome exibido para a conquista liberada ao concluir a jornada.',

    'ACP_MEMBERONBOARDING_STAFF_PANEL' => 'Avisos para staff',
    'ACP_MEMBERONBOARDING_STAFF_PANEL_EXPLAIN' => 'Membros recentes com jornada pendente, para facilitar o acompanhamento inicial da equipe.',

    'ACP_MEMBERONBOARDING_STATS' => 'Dados atuais',
    'ACP_MEMBERONBOARDING_STATS_EXPLAIN' => 'Contadores rápidos com base no progresso de onboarding armazenado.',
    'ACP_MEMBERONBOARDING_MEMBERS_TOTAL' => 'Membros acompanhados',
    'ACP_MEMBERONBOARDING_MEMBERS_DONE' => 'Jornadas concluídas',
    'ACP_MEMBERONBOARDING_MEMBERS_PENDING' => 'Jornadas pendentes',
    'ACP_MEMBERONBOARDING_COMPLETION_RATE' => 'Taxa de conclusão',
    'ACP_MEMBERONBOARDING_MEMBERS_REWARDED' => 'Medalhas concedidas',
    'ACP_MEMBERONBOARDING_LEVEL_DISTRIBUTION' => 'Distribuição por nível',
    'ACP_MEMBERONBOARDING_LEVEL_DISTRIBUTION_EXPLAIN' => 'Mostra quantos membros estão em cada faixa da trilha de ativação com base no percentual atual.',
    'ACP_MEMBERONBOARDING_LEVEL' => 'Nível',

    'ACP_MEMBERONBOARDING_RECENT_MEMBERS' => 'Acompanhamento recente',
    'ACP_MEMBERONBOARDING_RECENT_MEMBERS_EXPLAIN' => 'Resumo rápido dos membros acompanhados pela extensão.',
    'ACP_MEMBERONBOARDING_CURRENT_STEP' => 'Etapa atual',
    'ACP_MEMBERONBOARDING_PROGRESS' => 'Progresso',
    'ACP_MEMBERONBOARDING_STATUS' => 'Status',
    'ACP_MEMBERONBOARDING_WELCOME_PM_STATUS' => 'Boas-vindas',
    'ACP_MEMBERONBOARDING_WELCOME_PM_TIME' => 'Data do envio',
    'ACP_MEMBERONBOARDING_BADGE_STATUS' => 'Medalha',
    'ACP_MEMBERONBOARDING_BADGE_TIME' => 'Data da medalha',
    'ACP_MEMBERONBOARDING_STARTED' => 'Início',
    'ACP_MEMBERONBOARDING_UPDATED' => 'Atualização',
    'ACP_MEMBERONBOARDING_NO_RECENT_MEMBERS' => 'Nenhum membro acompanhado foi encontrado até o momento.',
    'ACP_MEMBERONBOARDING_ATTENTION' => 'Atenção',
    'ACP_MEMBERONBOARDING_LAST_MOVEMENT' => 'Última movimentação',
    'ACP_MEMBERONBOARDING_SIGNAL_PENDING_WELCOME' => 'Boas-vindas pendentes',
    'ACP_MEMBERONBOARDING_SIGNAL_STALLED' => 'Parado',
    'ACP_MEMBERONBOARDING_SIGNAL_NEAR_FINISH' => 'Quase concluído',
    'ACP_MEMBERONBOARDING_SIGNAL_NEEDS_ATTENTION' => 'Acompanhar',
    'ACP_MEMBERONBOARDING_SIGNAL_RECENT' => 'Recente',
    'ACP_MEMBERONBOARDING_SIGNAL_COMPLETED' => 'Concluído',
    'ACP_MEMBERONBOARDING_TODAY' => 'Hoje',
    'ACP_MEMBERONBOARDING_DAYS_AGO_1' => 'Há 1 dia',
    'ACP_MEMBERONBOARDING_DAYS_AGO' => 'Há %d dias',
]);
