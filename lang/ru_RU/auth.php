<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Authentication Language Lines
    |--------------------------------------------------------------------------
    |
    | The following language lines are used during authentication for various
    | messages that we need to display to the user. You are free to modify
    | these language lines according to your application's requirements.
    |
    */

    'register' => 'Регистрация',
    'register_welcome' => 'Добро пожаловать на LinkAce! Вы получили приглашение присоединиться к этому инструменту социальных закладок. Пожалуйста, выберите имя пользователя и пароль. После успешной регистрации вы будете перенаправлены на панель управления.',

    'failed' => 'Учетные данные не совпадают с нашими записями.',
    'throttle' => 'Слишком много попыток входа. Пожалуйста, повторите попытку через :seconds секунд.',
    'unauthorized' => 'Вход в систему не разрешен. Пожалуйста, свяжитесь с вашим администратором.',

    'confirm_title' => 'Требуется подтверждение',
    'confirm' => 'Пожалуйста, подтвердите это действие, используя свой текущий пароль.',
    'confirm_action' => 'Подтвердить действие',

    'two_factor' => 'Двухфакторная аутентификация',
    'two_factor_check' => 'Пожалуйста, введите одноразовый пароль, предоставленный вашим приложением для двухфакторной аутентификации.',
    'two_factor_with_recovery' => 'Аутентификация с помощью кода восстановления',

    'api_tokens' => 'Токены API',
    'api_tokens.no_tokens_found' => 'API-токены не найдены',
    'api_tokens.generate' => 'Сгенерировать новый API-токен',
    'api_tokens.generate_short' => 'Сгенерировать токен',
    'api_tokens.generate_help' => 'API-токены используются для аутентификации при использовании API LinkAce.',
    'api_tokens.generated_successfully' => 'API-токен был сгенерирован успешно: <code>:token</code>',
    'api_tokens.generated_help' => 'Пожалуйста, храните этот токен в безопасном месте. Восстановить токен в случае его потери <strong>не</strong> возможно.»',
    'api_tokens.name' => 'Имя токена',
    'api_tokens.name_help' => 'Выберите имя для вашего токена. Имя может содержать только буквенно-цифровые символы, тире и символы подчеркивания. Полезно, если вы хотите создать отдельные токены для разных случаев использования или приложений.',

    'api_token_system' => 'Системный API-токен',
    'api_tokens_system' => 'Токены API системы',
    'api_tokens.generate_help_system' => 'API-токены используются для доступа к LinkAce API из других приложений или скриптов. По умолчанию доступны только публичные или внутренние данные, но при необходимости токенам может быть предоставлен дополнительный доступ к приватным данным.',
    'api_tokens.private_access' => 'Токен может получить доступ к приватным данным',
    'api_tokens.private_access_help' => 'Токен может получать доступ и изменять приватные ссылки, списки, теги и заметки любого пользователя на основе указанных способностей',
    'api_tokens.abilities' => 'Способности токена',
    'api_tokens.abilities_select' => 'Выберите способности токена...',
    'api_tokens.abilities_help' => 'Выберите все способности, которыми может обладать токен. Способности не могут быть изменены позже.',
    'api_tokens.ability_private_access' => 'Токен может получить доступ к приватным данным',

    'api_tokens.revoke' => 'Отозвать токен',
    'api_tokens.revoke_confirm' => 'Вы действительно хотите отозвать этот токен? Этот шаг нельзя отменить, и токен не может быть восстановлен.',
    'api_tokens.revoke_successful' => 'Токен был успешно отозван.',

    'sso' => 'Единый вход',
    'sso_account_provider' => 'Вход через',
    'sso_account_id' => 'SSO ID',
    'sso_provider_disabled' => 'Выбранный вход недоступен. Пожалуйста, выберите другой.',
    'sso_wrong_provider' => 'Невозможно войти в систему с помощью :currentProvider. Пожалуйста, используйте для входа :userProvider или обратитесь за помощью к администратору.',

    'sso_provider' => [
        'auth0' => 'Auth0',
        'authentik' => 'Authentik',
        'azure' => 'Azure',
        'cognito' => 'Cognito',
        'fusionauth' => 'FusionAuth',
        'google' => 'Google',
        'github' => 'GitHub',
        'gitlab' => 'GitLab',
        'keycloak' => 'Keycloak',
        'oidc' => 'OIDC',
        'okta' => 'Okta',
        'zitadel' => 'Zitadel',
    ],
];
