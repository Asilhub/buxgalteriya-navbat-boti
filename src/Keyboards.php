<?php

namespace App;

class Keyboards {
    public static function getLanguage() {
        return [
            'keyboard' => [
                [
                    ['text' => "🇺🇿 O'zbekcha"],
                    ['text' => "🇺🇿 Ўзбекча"]
                ],
                [
                    ['text' => "🇷🇺 Русский"],
                    ['text' => "🇬🇧 English"]
                ]
            ],
            'resize_keyboard' => true,
            'one_time_keyboard' => true
        ];
    }

    public static function getContact($lang = 'uz_lat') {
        return [
            'keyboard' => [
                [
                    ['text' => Lang::t('btn_send_phone', $lang), 'request_contact' => true]
                ],
                [
                    ['text' => Lang::t('btn_change_lang', $lang)]
                ]
            ],
            'resize_keyboard' => true,
            'one_time_keyboard' => true
        ];
    }

    public static function getMain($lang = 'uz_lat', $isAdmin = false) {
        $rows = [
            [
                ['text' => Lang::t('btn_contract', $lang)],
                ['text' => Lang::t('btn_invoice', $lang)]
            ],
            [
                ['text' => Lang::t('btn_attorney', $lang)],
                ['text' => Lang::t('btn_my_queue', $lang)]
            ],
            [
                ['text' => Lang::t('btn_info', $lang)],
                ['text' => Lang::t('btn_settings', $lang)]
            ]
        ];

        if ($isAdmin) {
            $rows[] = [
                ['text' => '👑 Admin Panel']
            ];
        }

        return [
            'keyboard' => $rows,
            'resize_keyboard' => true
        ];
    }

    public static function getAdminPanel() {
        return [
            'keyboard' => [
                [
                    ['text' => '📢 Xabarnoma yuborish (Rassilka)']
                ],
                [
                    ['text' => '📊 Kunlik Statistika & KPI'],
                    ['text' => '📥 PDF hisobot yuklash']
                ],
                [
                    ['text' => '👥 Foydalanuvchilar soni']
                ],
                [
                    ['text' => '🏠 Asosiy menyu']
                ]
            ],
            'resize_keyboard' => true
        ];
    }

    public static function getCancelBroadcast() {
        return [
            'keyboard' => [
                [
                    ['text' => '❌ Xabarnomani bekor qilish']
                ]
            ],
            'resize_keyboard' => true
        ];
    }

    public static function getSettings($lang = 'uz_lat') {
        return [
            'keyboard' => [
                [
                    ['text' => Lang::t('btn_change_lang', $lang)],
                    ['text' => Lang::t('btn_change_phone', $lang)]
                ],
                [
                    ['text' => Lang::t('btn_main_menu', $lang)]
                ]
            ],
            'resize_keyboard' => true
        ];
    }

    public static function getShartnoma($lang = 'uz_lat') {
        return [
            'keyboard' => [
                [
                    ['text' => Lang::t('btn_contract_didox', $lang)],
                    ['text' => Lang::t('btn_contract_smart', $lang)]
                ],
                [
                    ['text' => Lang::t('btn_back', $lang)],
                    ['text' => Lang::t('btn_main_menu', $lang)]
                ]
            ],
            'resize_keyboard' => true
        ];
    }

    public static function getFaktura($lang = 'uz_lat') {
        return [
            'keyboard' => [
                [
                    ['text' => Lang::t('btn_invoice_didox', $lang)],
                    ['text' => Lang::t('btn_invoice_smart', $lang)]
                ],
                [
                    ['text' => Lang::t('btn_back', $lang)],
                    ['text' => Lang::t('btn_main_menu', $lang)]
                ]
            ],
            'resize_keyboard' => true
        ];
    }

    public static function getIshonchnoma($lang = 'uz_lat') {
        return [
            'keyboard' => [
                [
                    ['text' => Lang::t('btn_attorney_didox', $lang)],
                    ['text' => Lang::t('btn_attorney_smart', $lang)]
                ],
                [
                    ['text' => Lang::t('btn_back', $lang)],
                    ['text' => Lang::t('btn_main_menu', $lang)]
                ]
            ],
            'resize_keyboard' => true
        ];
    }

    public static function getSkipComment($lang = 'uz_lat') {
        return [
            'keyboard' => [
                [
                    ['text' => Lang::t('btn_skip', $lang)]
                ],
                [
                    ['text' => Lang::t('btn_back', $lang)],
                    ['text' => Lang::t('btn_main_menu', $lang)]
                ]
            ],
            'resize_keyboard' => true
        ];
    }

    public static function getGroupQueue($queueId, $status) {
        if ($status === 'pending') {
            return [
                'inline_keyboard' => [
                    [
                        ['text' => '⚡ Қабул қилиш', 'callback_data' => "take_{$queueId}"],
                        ['text' => '🚫 Бекор қилиш', 'callback_data' => "askcancel_{$queueId}"]
                    ]
                ]
            ];
        } elseif ($status === 'in_progress') {
            return [
                'inline_keyboard' => [
                    [
                        ['text' => '✅ Бажарилди', 'callback_data' => "done_{$queueId}"],
                        ['text' => '🚫 Бекор қилиш', 'callback_data' => "askcancel_{$queueId}"]
                    ]
                ]
            ];
        }
        return ['inline_keyboard' => []];
    }

    public static function getCancelReasons($queueId) {
        return [
            'inline_keyboard' => [
                [
                    ['text' => '❌ СТИР (ИНН) хато', 'callback_data' => "cancelr_{$queueId}_stir"]
                ],
                [
                    ['text' => '❌ Қарздорлик мавжуд', 'callback_data' => "cancelr_{$queueId}_debt"]
                ],
                [
                    ['text' => '❌ Ҳужжатлар етарли эмас', 'callback_data' => "cancelr_{$queueId}_docs"]
                ],
                [
                    ['text' => '❌ Бошқа сабаб', 'callback_data' => "cancelr_{$queueId}_other"]
                ],
                [
                    ['text' => '⬅️ Орқага', 'callback_data' => "backqueue_{$queueId}"]
                ]
            ]
        ];
    }

    public static function getBroadcastConfirmKeyboard($draftId) {
        return [
            'inline_keyboard' => [
                [
                    ['text' => '🚀 Ҳа, барча мижозларга юборилсин', 'callback_data' => "sendbc_{$draftId}"]
                ],
                [
                    ['text' => '❌ Бекор қилиш', 'callback_data' => "cancelbc_{$draftId}"]
                ]
            ]
        ];
    }

    public static function getClientRating($queueId) {
        return [
            'inline_keyboard' => [
                [
                    ['text' => '⭐ 1', 'callback_data' => "rate_{$queueId}_1"],
                    ['text' => '⭐ 2', 'callback_data' => "rate_{$queueId}_2"],
                    ['text' => '⭐ 3', 'callback_data' => "rate_{$queueId}_3"],
                    ['text' => '⭐ 4', 'callback_data' => "rate_{$queueId}_4"],
                    ['text' => '⭐ 5', 'callback_data' => "rate_{$queueId}_5"]
                ]
            ]
        ];
    }
}
