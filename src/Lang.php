<?php

namespace App;

class Lang {
    public static $serviceMap = [
        // Lotincha
        "🏢 Shartnoma (DIDOX)"          => "contract_didox",
        "🛒 Shartnoma (Smart Market)"    => "contract_smart",
        "🏢 Hisob-faktura (DIDOX)"      => "invoice_didox",
        "🛒 Hisob-faktura (Smart Market)" => "invoice_smart",
        "🏢 Ishonchnoma (DIDOX)"        => "attorney_didox",
        "🛒 Ishonchnoma (Smart Market)"  => "attorney_smart",

        // Кириллча
        "🏢 Шартнома (DIDOX)"          => "contract_didox",
        "🛒 Шартнома (Smart Market)"    => "contract_smart",
        "🏢 Ҳисоб-фактура (DIDOX)"      => "invoice_didox",
        "🛒 Ҳисоб-фактура (Smart Market)" => "invoice_smart",
        "🏢 Ишончнома (DIDOX)"        => "attorney_didox",
        "🛒 Ишончнома (Smart Market)"  => "attorney_smart",

        // Русча
        "🏢 Договор (DIDOX)"           => "contract_didox",
        "🛒 Договор (Smart Market)"     => "contract_smart",
        "🏢 Счет-фактура (DIDOX)"       => "invoice_didox",
        "🛒 Счет-фактура (Smart Market)"  => "invoice_smart",
        "🏢 Доверенность (DIDOX)"       => "attorney_didox",
        "🛒 Доверенность (Smart Market)"  => "attorney_smart",

        // Инглизча
        "🏢 Contract (DIDOX)"           => "contract_didox",
        "🛒 Contract (Smart Market)"     => "contract_smart",
        "🏢 Invoice (DIDOX)"            => "invoice_didox",
        "🛒 Invoice (Smart Market)"      => "invoice_smart",
        "🏢 Power of Attorney (DIDOX)"  => "attorney_didox",
        "🛒 Power of Attorney (Smart Market)" => "attorney_smart",
    ];

    public static $dict = [
        'uz_lat' => [
            'choose_lang'          => "🌐 <b>Iltimos, tilni tanlang / Тилни танланг:</b>",
            'lang_changed'         => "✅ <b>Til muvaffaqiyatli o'zgartirildi!</b>",
            'welcome'              => "👋 <b>Assalomu alaykum, {name}!</b>\n\n🏢 <b>Buxgalteriya Elektron Navbat Tizimiga xush kelibsiz!</b>\nBiz orqali shartnoma, hisob-faktura va ishonchnoma xizmatlariga onlayn navbat olishingiz mumkin.\n\n📱 <b>Sizning telefon raqamingiz:</b> <code>{phone}</code>\n\n👇 <i>Quyidagi menyudan kerakli xizmat turini tanlang:</i>",
            'ask_phone'            => "👋 <b>Assalomu alaykum, {name}!</b>\n\n🏢 <b>Buxgalteriya Elektron Navbat Xizmati</b>\nTezkor va qulay xizmat ko'rsatish uchun shaxsingizni tasdiqlang.\n\n📲 <i>Iltimos, pastdagi tugmani bosib <b>telefon raqamingizni yuboring</b> yoki qo'lda yozib qoldiring (+998XXXXXXXXX):</i>",
            'btn_send_phone'       => "📱 Telefon raqamni yuborish",
            'phone_saved'          => "✅ <b>Telefon raqamingiz saqlandi:</b> <code>{phone}</code>\n\n👇 <i>Kerakli xizmat turini tanlang:</i>",
            'phone_invalid'        => "⚠️ <b>Iltimos, telefon raqamingizni to'g'ri formatda kiriting:</b>\n(Masalan: <code>+998901234567</code>) yoki pastdagi tugmani bosing:",
            'ask_new_phone'        => "📲 <b>Yangi telefon raqamingizni yuboring:</b>\n\n<i>(Pastdagi tugmani bosing yoki +998... ko'rinishida yozing)</i>",
            
            // Menu tugmalari
            'btn_contract'         => "📄 Shartnoma olish",
            'btn_invoice'          => "🧾 Hisob-faktura",
            'btn_attorney'         => "📑 Ishonchnoma",
            'btn_my_queue'         => "📊 Mening navbatim",
            'btn_news'             => "📰 Yangiliklar va Qonunchilik",
            'btn_info'             => "ℹ️ Ma'lumot va yordam",
            'btn_settings'         => "⚙️ Sozlamalar",
            'btn_change_lang'      => "🌐 Tilni o'zgartirish",
            'btn_change_phone'     => "📱 Raqamni o'zgartirish",
            'btn_back'             => "⬅️ Orqaga",
            'btn_main_menu'        => "🏠 Asosiy menyu",
            'btn_skip'             => "⏭ O'tkazib yuborish",

            // Ichki menyu xizmatlari
            'btn_contract_didox'   => "🏢 Shartnoma (DIDOX)",
            'btn_contract_smart'   => "🛒 Shartnoma (Smart Market)",
            'btn_invoice_didox'    => "🏢 Hisob-faktura (DIDOX)",
            'btn_invoice_smart'    => "🛒 Hisob-faktura (Smart Market)",
            'btn_attorney_didox'   => "🏢 Ishonchnoma (DIDOX)",
            'btn_attorney_smart'   => "🛒 Ishonchnoma (Smart Market)",

            // Dialoglar
            'select_system'        => "📄 <b>{service}</b> turini tanlang:\n\n<i>Qaysi tizim orqali rasmiylashtirish kerak?</i> 👇",
            'ask_stir'             => "💼 <b>Tanlangan xizmat:</b> <b>{service}</b>\n\n📝 <b>Qo'shimcha ma'lumot yoki Fayl (PDF / Rasm):</b>\nIltimos, tashkilotingiz <b>STIR (INN)</b> raqamini yozing yoki rekvizitlar faylini/rasmini yuboring.\n\n💡 <i>Agar hozir kiritishni xohlamasangiz, pastdagi «⏭ O'tkazib yuborish» tugmasini bosing.</i>",
            'ticket_title'         => "🎫 <b>SIZNING NAVBAT CHIPTANGIZ</b>\n━━━━━━━━━━━━━━━━━━━━\n🔢 <b>Navbat raqami:</b> #<b>{queue_number}</b>\n🛠 <b>Xizmat turi:</b> {service}\n{stir}{file_attach}📞 <b>Telefon:</b> <code>{phone}</code>\n⏰ <b>Vaqt:</b> <code>{time}</code>\n📊 <b>Holat:</b> ⏳ <b>Kutilmoqda</b>\n━━━━━━━━━━━━━━━━━━━━\n\n",
            'working_hours_notice' => "⏰ <b>Eslatma:</b> Hozir ish vaqtidan tashqari vaqt (Ish vaqti: 09:00 — 18:00).\nArizangiz navbatga qo'yildi va ish vaqti boshlanishi bilan birinchi bo'lib ko'rib chiqiladi. ⏳\n\n",
            'ticket_wait_text'     => "⏳ <i>Mutaxassislarimiz tez orada arizangiz bo'yicha ish boshlashadi.</i>\n\n📌 <i>Navbatingiz holatini <b>«📊 Mening navbatim»</b> tugmasi orqali kuzatib borishingiz mumkin.</i>",
            'info_text'            => "ℹ️ <b>BUXGALTERIYA HAQIDA MA'LUMOT</b>\n━━━━━━━━━━━━━━━━━━━━\n🏢 <b>Xizmatlar:</b>\n• Shartnomalar tayyorlash (DIDOX, Smart Market)\n• Elektron hisob-fakturalar rasmiylashtirish\n• Ishonchnomalar berish va tasdiqlash\n\n🕒 <b>Ish vaqti:</b>\n• Dushanba — Shanba: <code>09:00 — 18:00</code>\n• Yakshanba: <code>Dam olish kuni</code>\n━━━━━━━━━━━━━━━━━━━━\n📞 <b>Yordam va qo'llab-quvvatlash:</b>\nSavollar bo'yicha bizning xodimlarimiz bilan bog'lanishingiz mumkin.",
            'no_queue'             => "ℹ️ <b>Sizda hozircha faol navbat yoki ariza mavjud emas.</b>\n\n👇 <i>Yangi navbat olish uchun xizmat turlaridan birini tanlang:</i>",
            'my_queues_title'      => "📊 <b>SIZNING AKTIV NAVBATLARINGIZ:</b>\n━━━━━━━━━━━━━━━━━━━━\n\n",
            'status_pending'       => "⏳ Kutilmoqda",
            'status_in_progress'   => "👨‍💻 Ko'rib chiqilmoqda (Jarayonda)",
            'status_done'          => "✅ Muvaffaqiyatli bajarildi",
            'status_cancelled'     => "🚫 Bekor qilindi",
            'ahead_count'          => "👥 <b>Oldingizda:</b> <code>{count} ta ariza bor</code>\n",
            'you_are_first'        => "👥 <b>Oldingizda:</b> <b>Siz birinchisiz 🎯</b>\n",
            'operator_taken'       => "👨‍💻 <b>Hurmatli mijoz!</b>\n\nSizning <b>#{queue_number}</b>-raqamli arizangizni mutaxassisimiz <b>{operator}</b> qabul qildi va hozir ko'rib chiqmoqda. ⏳\n\n🛠 <b>Xizmat turi:</b> {service}",
            'operator_done'        => "🎉 <b>Hurmatli mijoz!</b>\n\nSizning <b>#{queue_number}</b>-raqamli arizangiz («{service}») muvaffaqiyatli <b>bajarildi</b>! ✅\n\nBizning xizmatimizdan foydalanganingiz uchun tashakkur!\n🌟 <i>Iltimos, ko'rsatilgan xizmat sifatini baholang:</i>",
            'operator_cancelled'   => "🚫 <b>Hurmatli mijoz!</b>\n\nSizning <b>#{queue_number}</b>-raqamli arizangiz («{service}») quyidagi sababga ko'ra <b>bekor qilindi</b>:\n\n📌 <b>Sabab:</b> <i>{reason}</i>\n\nSavollar bo'yicha buxgalteriya bo'limi bilan bog'lanishingiz mumkin.",
            'doc_delivered'        => "📄 <b>Hurmatli mijoz!</b>\n\nSizning <b>#{queue_number}</b>-raqamli arizangiz bo'yicha tayyorlangan hujjat ilova qilindi. ✅\n\n🌟 <i>Iltimos, ko'rsatilgan xizmat sifatini baholang:</i>",
            'rating_thanks'        => "✅ <b>Bahoingiz qabul qilindi:</b> {stars} (<b>{rating}/5</b>)\n\n<i>Bizni tanlaganingiz uchun katta rahmat! 🙏</i>",
            'settings_menu'        => "⚙️ <b>Sozlamalar bo'limi:</b>\n\nQuyidagi tugmalardan birini tanlang:",
            'select_service_prompt'=> "👇 <b>Kerakli buxgalteriya xizmat turini tanlang:</b>",
            'news_title'           => "📰 <b>SOLIQ VA BUXGALTERIYA YANGILIKLARI</b>\n━━━━━━━━━━━━━━━━━━━━\n\n",
            'no_news'              => "ℹ️ <b>Hozircha yangiliklar joylanmagan.</b>\nTez orada yangi qonunchilik va soliq yangiliklari shu yerda e'lon qilib boriladi."
        ],
        'uz_cyr' => [
            'choose_lang'          => "🌐 <b>Илтимос, тилни танланг / Iltimos, tilni tanlang:</b>",
            'lang_changed'         => "✅ <b>Тил муваффақиятли ўзгартирилди!</b>",
            'welcome'              => "👋 <b>Ассалому алайкум, {name}!</b>\n\n🏢 <b>Бухгалтерия Электрон Навбат Тизимига хуш келибсиз!</b>\nБиз орқали шартнома, ҳисоб-фактура ва ишончнома хизматларига онлайн навбат олишингиз мумкин.\n\n📱 <b>Сизнинг телефон рақамингиз:</b> <code>{phone}</code>\n\n👇 <i>Қуйидаги менюдан керакли хизмат турини танланг:</i>",
            'ask_phone'            => "👋 <b>Ассалому алайкум, {name}!</b>\n\n🏢 <b>Бухгалтерия Электрон Навбат Хизмати</b>\nТезкор ва қулай хизмат кўрсатиш учун шахсингизни тасдиқланг.\n\n📲 <i>Илтимос, пастдаги тугмани босиб <b>телефон рақамингизни юборинг</b> ёки қўлда ёзиб қолдиринг (+998XXXXXXXXX):</i>",
            'btn_send_phone'       => "📱 Телефон рақамни юбориш",
            'phone_saved'          => "✅ <b>Телефон рақамингиз сақланди:</b> <code>{phone}</code>\n\n👇 <i>Керакли хизмат турини танланг:</i>",
            'phone_invalid'        => "⚠️ <b>Илтимос, телефон рақамингизни тўғри форматда киритинг:</b>\n(Масалан: <code>+998901234567</code>) ёки пастдаги тугмани босинг:",
            'ask_new_phone'        => "📲 <b>Янги телефон рақамингизни юборинг:</b>\n\n<i>(Пастдаги тугмани босинг ёки +998... кўринишида ёзинг)</i>",
            
            // Menu tugmalari
            'btn_contract'         => "📄 Шартнома олиш",
            'btn_invoice'          => "🧾 Ҳисоб-фактура",
            'btn_attorney'         => "📑 Ишончнома",
            'btn_my_queue'         => "📊 Менинг навбатим",
            'btn_news'             => "📰 Янгиликлар ва Қонунчилик",
            'btn_info'             => "ℹ️ Маълумот ва ёрдам",
            'btn_settings'         => "⚙️ Созламалар",
            'btn_change_lang'      => "🌐 Тилни ўзгартириш",
            'btn_change_phone'     => "📱 Рақамни ўзгартириш",
            'btn_back'             => "⬅️ Орқага",
            'btn_main_menu'        => "🏠 Асосий меню",
            'btn_skip'             => "⏭ Ўтказиб юбориш",

            // Ichki menyu xizmatlari
            'btn_contract_didox'   => "🏢 Шартнома (DIDOX)",
            'btn_contract_smart'   => "🛒 Шартнома (Smart Market)",
            'btn_invoice_didox'    => "🏢 Ҳисоб-фактура (DIDOX)",
            'btn_invoice_smart'    => "🛒 Ҳисоб-фактура (Smart Market)",
            'btn_attorney_didox'   => "🏢 Ишончнома (DIDOX)",
            'btn_attorney_smart'   => "🛒 Ишончнома (Smart Market)",

            // Dialoglar
            'select_system'        => "📄 <b>{service}</b> турини танланг:\n\n<i>Қайси тизим орқали расмийлаштириш керак?</i> 👇",
            'ask_stir'             => "💼 <b>Танланган хизмат:</b> <b>{service}</b>\n\n📝 <b>Қўшимча маълумот ёки Ҳужжат (PDF / Расм):</b>\nИлтимос, ташкилотингиз <b>СТИР (ИНН)</b> рақамини ёзинг ёки реквизитлар файлини/расмини юборинг.\n\n💡 <i>Агар ҳозир киритишни хоҳламасангиз, пастдаги «⏭ Ўтказиб юбориш» тугмасини босинг.</i>",
            'ticket_title'         => "🎫 <b>СИЗНИНГ НАВБАТ ЧИПТАНГИЗ</b>\n━━━━━━━━━━━━━━━━━━━━\n🔢 <b>Навбат рақами:</b> #<b>{queue_number}</b>\n🛠 <b>Хизмат тури:</b> {service}\n{stir}{file_attach}📞 <b>Телефон:</b> <code>{phone}</code>\n⏰ <b>Вақт:</b> <code>{time}</code>\n📊 <b>Ҳолат:</b> ⏳ <b>Кутилмоқда</b>\n━━━━━━━━━━━━━━━━━━━━\n\n",
            'working_hours_notice' => "⏰ <b>Эслатма:</b> Ҳозир иш вақтидан ташқари вақт (Иш вақти: 09:00 — 18:00).\nАризангиз навбатга қўйилди ва иш вақти бошланиши билан биринчи бўлиб кўриб чиқилади. ⏳\n\n",
            'ticket_wait_text'     => "⏳ <i>Мутахассисларимиз тез орада аризангиз бўйича иш бошлашади.</i>\n\n📌 <i>Навбатингиз ҳолатини <b>«📊 Менинг навбатим»</b> тугмаси орқали кузатиб боришингиз мумкин.</i>",
            'info_text'            => "ℹ️ <b>БУХГАЛТЕРИЯ ҲАҚИДА МАЪЛУМОТ</b>\n━━━━━━━━━━━━━━━━━━━━\n🏢 <b>Хизматлар:</b>\n• Шартномалар тайёрлаш (DIDOX, Smart Market)\n• Электрон ҳисоб-фактуралар расмийлаштириш\n• Ишончномалар бериш ва тасдиқлаш\n\n🕒 <b>Иш вақти:</b>\n• Душанба — Шанба: <code>09:00 — 18:00</code>\n• Якшанба: <code>Дам олиш куни</code>\n━━━━━━━━━━━━━━━━━━━━\n📞 <b>Ёрдам ва қўллаб-қувватлаш:</b>\nСаволлар бўйича бизнинг ходимларимиз билан боғланишингиз мумкин.",
            'no_queue'             => "ℹ️ <b>Сизда ҳозирча фаол навбат ёки ариза мавжуд эмас.</b>\n\n👇 <i>Янги навбат олиш учун хизмат турларидан бирини танланг:</i>",
            'my_queues_title'      => "📊 <b>СИЗНИНГ АКТИВ НАВБАТЛАРИНГИЗ:</b>\n━━━━━━━━━━━━━━━━━━━━\n\n",
            'status_pending'       => "⏳ Кутилмоқда",
            'status_in_progress'   => "👨‍💻 Кўриб чиқилмоқда (Жараёнда)",
            'status_done'          => "✅ Муваффақиятли бажарилди",
            'status_cancelled'     => "🚫 Бекор қилинди",
            'ahead_count'          => "👥 <b>Олдингизда:</b> <code>{count} та ариза бор</code>\n",
            'you_are_first'        => "👥 <b>Олдингизда:</b> <b>Сиз биринчисиз 🎯</b>\n",
            'operator_taken'       => "👨‍💻 <b>Ҳурматли мижоз!</b>\n\nСизнинг <b>#{queue_number}</b>-рақамли аризангизни мутахассисимиз <b>{operator}</b> қабул қилди ва ҳозир кўриб чиқмоқда. ⏳\n\n🛠 <b>Хизмат тури:</b> {service}",
            'operator_done'        => "🎉 <b>Ҳурматли мижоз!</b>\n\nСизнинг <b>#{queue_number}</b>-рақамли аризангиз («{service}») муваффақиятли <b>бажарилди</b>! ✅\n\nБизнинг хизматимиздан фойдаланганингиз учун ташаккур!\n🌟 <i>Илтимос, кўрсатилган хизмат сифатини баҳоланг:</i>",
            'operator_cancelled'   => "🚫 <b>Ҳурматли мижоз!</b>\n\nСизнинг <b>#{queue_number}</b>-рақамли аризангиз («{service}») қуйидаги сабабга кўра <b>бекор қилинди</b>:\n\n📌 <b>Сабаб:</b> <i>{reason}</i>\n\nСаволлар бўйича бухгалтерия бўлими билан боғланишингиз мумкин.",
            'doc_delivered'        => "📄 <b>Ҳурматли мижоз!</b>\n\nСизнинг <b>#{queue_number}</b>-рақамли аризангиз бўйича тайёрланган ҳужжат илова қилинди. ✅\n\n🌟 <i>Илтимос, кўрсатилган хизмат сифатини баҳоланг:</i>",
            'rating_thanks'        => "✅ <b>Баҳоингиз қабул қилинди:</b> {stars} (<b>{rating}/5</b>)\n\n<i>Бизни танлаганингиз учун катта раҳмат! 🙏</i>",
            'settings_menu'        => "⚙️ <b>Созламалар бўлими:</b>\n\nҚуйидаги тугмалардан бирини танланг:",
            'select_service_prompt'=> "👇 <b>Керакли бухгалтерия хизмат турини танланг:</b>",
            'news_title'           => "📰 <b>СОЛИҚ ВА БУХГАЛТЕРИЯ ЯНГИЛИКЛАРИ</b>\n━━━━━━━━━━━━━━━━━━━━\n\n",
            'no_news'              => "ℹ️ <b>Ҳозирча янгиликлар жойланмаган.</b>\nТез орада янги қонунчилик ва солиқ янгиликлари шу ерда эълон қилиб борилади."
        ],
        'ru' => [
            'choose_lang'          => "🌐 <b>Пожалуйста, выберите язык:</b>",
            'lang_changed'         => "✅ <b>Язык успешно изменен!</b>",
            'welcome'              => "👋 <b>Здравствуйте, {name}!</b>\n\n🏢 <b>Добро пожаловать в электронную очередь бухгалтерии!</b>\nЗдесь вы можете занять онлайн-очередь на оформление договоров, счетов-фактур и доверенностей.\n\n📱 <b>Ваш номер телефона:</b> <code>{phone}</code>\n\n👇 <i>Выберите необходимую услугу из меню:</i>",
            'ask_phone'            => "👋 <b>Здравствуйте, {name}!</b>\n\n🏢 <b>Бухгалтерская служба электронной очереди</b>\nДля быстрого обслуживания, пожалуйста, подтвердите ваш номер телефона.\n\n📲 <i>Нажмите кнопку ниже, чтобы <b>отправить номер телефона</b>, или введите его вручную (+998XXXXXXXXX):</i>",
            'btn_send_phone'       => "📱 Отправить номер телефона",
            'phone_saved'          => "✅ <b>Ваш номер телефона сохранен:</b> <code>{phone}</code>\n\n👇 <i>Выберите услугу:</i>",
            'phone_invalid'        => "⚠️ <b>Пожалуйста, введите номер телефона в правильном формате:</b>\n(Например: <code>+998901234567</code>) или нажмите кнопку ниже:",
            'ask_new_phone'        => "📲 <b>Отправьте ваш новый номер телефона:</b>\n\n<i>(Нажмите кнопку ниже или напишите в формате +998...)</i>",
            
            // Menu tugmalari
            'btn_contract'         => "📄 Оформить договор",
            'btn_invoice'          => "🧾 Счет-фактура",
            'btn_attorney'         => "📑 Доверенность",
            'btn_my_queue'         => "📊 Моя очередь",
            'btn_news'             => "📰 Новости и законы",
            'btn_info'             => "ℹ️ Информация и помощь",
            'btn_settings'         => "⚙️ Настройки",
            'btn_change_lang'      => "🌐 Сменить язык",
            'btn_change_phone'     => "📱 Сменить номер",
            'btn_back'             => "⬅️ Назад",
            'btn_main_menu'        => "🏠 Главное меню",
            'btn_skip'             => "⏭ Пропустить",

            // Ichki menyu xizmatlari
            'btn_contract_didox'   => "🏢 Договор (DIDOX)",
            'btn_contract_smart'   => "🛒 Договор (Smart Market)",
            'btn_invoice_didox'    => "🏢 Счет-фактура (DIDOX)",
            'btn_invoice_smart'    => "🛒 Счет-фактура (Smart Market)",
            'btn_attorney_didox'   => "🏢 Доверенность (DIDOX)",
            'btn_attorney_smart'   => "🛒 Доверенность (Smart Market)",

            // Dialoglar
            'select_system'        => "📄 Выберите тип услуги <b>{service}</b>:\n\n<i>Через какую систему оформить?</i> 👇",
            'ask_stir'             => "💼 <b>Выбранная услуга:</b> <b>{service}</b>\n\n📝 <b>Дополнительная информация или Файл (PDF / Фото):</b>\nПожалуйста, введите <b>ИНН (СТИР)</b> организации или прикрепите файл реквизитов/документа.\n\n💡 <i>Если не хотите вводить сейчас, нажмите «⏭ Пропустить».</i>",
            'ticket_title'         => "🎫 <b>ВАШ ТАЛОН ОЧЕРЕДИ</b>\n━━━━━━━━━━━━━━━━━━━━\n🔢 <b>Номер очереди:</b> #<b>{queue_number}</b>\n🛠 <b>Услуга:</b> {service}\n{stir}{file_attach}📞 <b>Телефон:</b> <code>{phone}</code>\n⏰ <b>Время:</b> <code>{time}</code>\n📊 <b>Статус:</b> ⏳ <b>Ожидание</b>\n━━━━━━━━━━━━━━━━━━━━\n\n",
            'working_hours_notice' => "⏰ <b>Примечание:</b> Сейчас нерабочее время (Часы работы: 09:00 — 18:00).\nВаша заявка принята в очередь и будет рассмотрена первой с началом рабочего дня. ⏳\n\n",
            'ticket_wait_text'     => "⏳ <i>Наши специалисты свяжутся с вами в ближайшее время.</i>\n\n📌 <i>Вы можете следить за статусом через <b>«📊 Моя очередь»</b>.</i>",
            'info_text'            => "ℹ️ <b>ИНФОРМАЦИЯ О БУХГАЛТЕРИИ</b>\n━━━━━━━━━━━━━━━━━━━━\n🏢 <b>Услуги:</b>\n• Составление договоров (DIDOX, Smart Market)\n• Оформление электронных счетов-фактур\n• Выдача и подтверждение доверенностей\n\n🕒 <b>Рабочее время:</b>\n• Понедельник — Суббота: <code>09:00 — 18:00</code>\n• Воскресенье: <code>Выходной</code>\n━━━━━━━━━━━━━━━━━━━━\n📞 <b>Поддержка:</b>\nПо всем вопросам вы можете связаться с нашими специалистами.",
            'no_queue'             => "ℹ️ <b>У вас пока нет активных очередей или заявок.</b>\n\n👇 <i>Выберите услугу для получения номера:</i>",
            'my_queues_title'      => "📊 <b>ВАШИ АКТИВНЫЕ ЗАЯВКИ:</b>\n━━━━━━━━━━━━━━━━━━━━\n\n",
            'status_pending'       => "⏳ Ожидание",
            'status_in_progress'   => "👨‍💻 В обработке",
            'status_done'          => "✅ Успешно выполнено",
            'status_cancelled'     => "🚫 Отменено",
            'ahead_count'          => "👥 <b>Перед вами в очереди:</b> <code>{count} чел.</code>\n",
            'you_are_first'        => "👥 <b>Перед вами:</b> <b>Вы первый в очереди 🎯</b>\n",
            'operator_taken'       => "👨‍💻 <b>Уважаемый клиент!</b>\n\nВашу заявку <b>#{queue_number}</b> принял специалист <b>{operator}</b> и сейчас рассматривает ее. ⏳\n\n🛠 <b>Услуга:</b> {service}",
            'operator_done'        => "🎉 <b>Уважаемый клиент!</b>\n\nВаша заявка <b>#{queue_number}</b> («{service}») успешно <b>выполнена</b>! ✅\n\nСпасибо, что пользуетесь нашими услугами!\n🌟 <i>Пожалуйста, оцените качество обслуживания:</i>",
            'operator_cancelled'   => "🚫 <b>Уважаемый клиент!</b>\n\nВаша заявка <b>#{queue_number}</b> («{service}») была <b>отменена</b> по причине:\n\n📌 <b>Причина:</b> <i>{reason}</i>\n\nПо вопросам вы можете обратиться в бухгалтерию.",
            'doc_delivered'        => "📄 <b>Уважаемый клиент!</b>\n\nВам направлен готовый документ по заявке <b>#{queue_number}</b>. ✅\n\n🌟 <i>Пожалуйста, оцените качество обслуживания:</i>",
            'rating_thanks'        => "✅ <b>Ваша оценка принята:</b> {stars} (<b>{rating}/5</b>)\n\n<i>Большое спасибо за выбор нашей компании! 🙏</i>",
            'settings_menu'        => "⚙️ <b>Раздел настроек:</b>\n\nВыберите нужный пункт:",
            'select_service_prompt'=> "👇 <b>Выберите необходимую бухгалтерскую услугу:</b>",
            'news_title'           => "📰 <b>НАЛОГОВЫЕ И БУХГАЛТЕРСКИЕ НОВОСТИ</b>\n━━━━━━━━━━━━━━━━━━━━\n\n",
            'no_news'              => "ℹ️ <b>Новостей пока нет.</b>\nВ скором времени здесь будут публиковаться изменения в законодательстве и налогах."
        ],
        'en' => [
            'choose_lang'          => "🌐 <b>Please select your language:</b>",
            'lang_changed'         => "✅ <b>Language changed successfully!</b>",
            'welcome'              => "👋 <b>Hello, {name}!</b>\n\n🏢 <b>Welcome to the Accounting Queue System!</b>\nYou can take an online queue for contracts, invoices, and power of attorney services.\n\n📱 <b>Your phone number:</b> <code>{phone}</code>\n\n👇 <i>Please choose a service from the menu below:</i>",
            'ask_phone'            => "👋 <b>Hello, {name}!</b>\n\n🏢 <b>Accounting Online Queue Service</b>\nPlease share your phone number to proceed.\n\n📲 <i>Click the button below to <b>send your phone number</b> or type it manually (+998XXXXXXXXX):</i>",
            'btn_send_phone'       => "📱 Send phone number",
            'phone_saved'          => "✅ <b>Your phone number is saved:</b> <code>{phone}</code>\n\n👇 <i>Please select a service:</i>",
            'phone_invalid'        => "⚠️ <b>Please enter your phone number in valid format:</b>\n(e.g., <code>+998901234567</code>) or click the button below:",
            'ask_new_phone'        => "📲 <b>Please send your new phone number:</b>\n\n<i>(Click the button below or type starting with +998...)</i>",
            
            // Menu buttons
            'btn_contract'         => "📄 Contract",
            'btn_invoice'          => "🧾 Invoice",
            'btn_attorney'         => "📑 Power of Attorney",
            'btn_my_queue'         => "📊 My queue",
            'btn_news'             => "📰 Tax & Legal News",
            'btn_info'             => "ℹ️ Info & Help",
            'btn_settings'         => "⚙️ Settings",
            'btn_change_lang'      => "🌐 Change language",
            'btn_change_phone'     => "📱 Change phone number",
            'btn_back'             => "⬅️ Back",
            'btn_main_menu'        => "🏠 Main menu",
            'btn_skip'             => "⏭ Skip",

            // Submenus
            'btn_contract_didox'   => "🏢 Contract (DIDOX)",
            'btn_contract_smart'   => "🛒 Contract (Smart Market)",
            'btn_invoice_didox'    => "🏢 Invoice (DIDOX)",
            'btn_invoice_smart'    => "🛒 Invoice (Smart Market)",
            'btn_attorney_didox'   => "🏢 Power of Attorney (DIDOX)",
            'btn_attorney_smart'   => "🛒 Power of Attorney (Smart Market)",

            // Dialogs
            'select_system'        => "📄 Select type for <b>{service}</b>:\n\n<i>Which platform do you need?</i> 👇",
            'ask_stir'             => "💼 <b>Selected service:</b> <b>{service}</b>\n\n📝 <b>Additional details or File (PDF / Photo):</b>\nPlease enter your organization's <b>TIN / STIR</b> number or attach requisites/document file.\n\n💡 <i>If you want to skip, click «⏭ Skip» below.</i>",
            'ticket_title'         => "🎫 <b>YOUR QUEUE TICKET</b>\n━━━━━━━━━━━━━━━━━━━━\n🔢 <b>Ticket number:</b> #<b>{queue_number}</b>\n🛠 <b>Service:</b> {service}\n{stir}{file_attach}📞 <b>Phone:</b> <code>{phone}</code>\n⏰ <b>Time:</b> <code>{time}</code>\n📊 <b>Status:</b> ⏳ <b>Pending</b>\n━━━━━━━━━━━━━━━━━━━━\n\n",
            'working_hours_notice' => "⏰ <b>Note:</b> Current time is outside business hours (Working hours: 09:00 — 18:00).\nYour ticket has been queued and will be handled first thing next working day. ⏳\n\n",
            'ticket_wait_text'     => "⏳ <i>Our specialists will attend to your request shortly.</i>\n\n📌 <i>Track your position anytime via <b>«📊 My queue»</b>.</i>",
            'info_text'            => "ℹ️ <b>ACCOUNTING INFORMATION</b>\n━━━━━━━━━━━━━━━━━━━━\n🏢 <b>Services:</b>\n• Contract creation (DIDOX, Smart Market)\n• Electronic invoices\n• Power of attorney\n\n🕒 <b>Working hours:</b>\n• Monday — Saturday: <code>09:00 — 18:00</code>\n• Sunday: <code>Closed</code>\n━━━━━━━━━━━━━━━━━━━━\n📞 <b>Support:</b>\nContact our team for any assistance.",
            'no_queue'             => "ℹ️ <b>You currently have no active tickets in the queue.</b>\n\n👇 <i>Choose a service below to get a queue ticket:</i>",
            'my_queues_title'      => "📊 <b>YOUR ACTIVE TICKETS:</b>\n━━━━━━━━━━━━━━━━━━━━\n\n",
            'status_pending'       => "⏳ Pending",
            'status_in_progress'   => "👨‍💻 In progress",
            'status_done'          => "✅ Completed",
            'status_cancelled'     => "🚫 Cancelled",
            'ahead_count'          => "👥 <b>Ahead of you in queue:</b> <code>{count} requests</code>\n",
            'you_are_first'        => "👥 <b>Ahead of you:</b> <b>You are first in queue 🎯</b>\n",
            'operator_taken'       => "👨‍💻 <b>Dear client!</b>\n\nYour ticket <b>#{queue_number}</b> has been assigned to specialist <b>{operator}</b> and is in progress. ⏳\n\n🛠 <b>Service:</b> {service}",
            'operator_done'        => "🎉 <b>Dear client!</b>\n\nYour ticket <b>#{queue_number}</b> («{service}») has been successfully <b>completed</b>! ✅\n\nThank you for choosing our service!\n🌟 <i>Please rate our service quality:</i>",
            'operator_cancelled'   => "🚫 <b>Dear client!</b>\n\nYour ticket <b>#{queue_number}</b> («{service}») was <b>cancelled</b> due to reason:\n\n📌 <b>Reason:</b> <i>{reason}</i>\n\nFor inquiries, please contact our accounting department.",
            'doc_delivered'        => "📄 <b>Dear client!</b>\n\nYour completed document for ticket <b>#{queue_number}</b> is attached below. ✅\n\n🌟 <i>Please rate our service quality:</i>",
            'rating_thanks'        => "✅ <b>Rating received:</b> {stars} (<b>{rating}/5</b>)\n\n<i>Thank you very much for your feedback! 🙏</i>",
            'settings_menu'        => "⚙️ <b>Settings:</b>\n\nPlease select an option below:",
            'select_service_prompt'=> "👇 <b>Please select an accounting service:</b>",
            'news_title'           => "📰 <b>TAX & ACCOUNTING NEWS</b>\n━━━━━━━━━━━━━━━━━━━━\n\n",
            'no_news'              => "ℹ️ <b>No news published yet.</b>\nUpdates regarding tax legislation and regulatory changes will be shared here."
        ]
    ];

    public static function t($key, $lang = 'uz_lat', $params = []) {
        if (!isset(self::$dict[$lang])) {
            $lang = 'uz_lat';
        }
        $text = self::$dict[$lang][$key] ?? (self::$dict['uz_lat'][$key] ?? $key);
        foreach ($params as $k => $v) {
            $text = str_replace('{' . $k . '}', $v, $text);
        }
        return $text;
    }

    public static function getServiceTitle($serviceKey, $lang = 'uz_cyr') {
        $map = [
            'contract_didox'  => 'btn_contract_didox',
            'contract_smart'  => 'btn_contract_smart',
            'invoice_didox'   => 'btn_invoice_didox',
            'invoice_smart'   => 'btn_invoice_smart',
            'attorney_didox'  => 'btn_attorney_didox',
            'attorney_smart'  => 'btn_attorney_smart',
        ];

        if (isset($map[$serviceKey])) {
            return self::t($map[$serviceKey], $lang);
        }
        return $serviceKey;
    }
}
