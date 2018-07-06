<?php

return [

    'feedtypes' => [
        'regular',
        'extra',
        'eqvol',
        'other',
    ],

    'kinds' => [
        '季節観測' => [
            'view' => 'entry.kisetsu',
        ],

        '生物季節観測' => [
            'view' => 'entry.Sbt',
        ],

        '特殊気象報' => [
            'view' => 'entry.tokusyu',
        ],

        '紫外線観測データ' => [
            'view' => 'entry.env_uvindex',
        ],

        '全般台風情報' => [
            'view' => 'entry.ippanho',
        ],

        '全般台風情報（定型）' => [
            'view' => 'entry.ippanho',
        ],

        '全般台風情報（詳細）' => [
            'view' => 'entry.ippanho',
        ],

        '台風解析・予報情報（３日予報）' => [
            'view' => 'entry.kfxc80',
        ],

        '台風解析・予報情報（５日予報）' => [
            'view' => 'entry.kfxc80',
        ],

        '全般海上警報（定時）' => [
            'view' => 'entry.umikeiho1',
        ],

        '全般海上警報（定時）（Ｈ２９）' => [
            'view' => 'entry.umikeiho1',
        ],

        '全般海上警報（臨時）' => [
            'view' => 'entry.umikeiho1',
        ],

        '全般海上警報（臨時）（Ｈ２９）' => [
            'view' => 'entry.umikeiho1',
        ],

        '地方海上警報' => [
            'view' => 'entry.chihoumikeiho',
        ],

        '地方海上警報（Ｈ２８）' => [
            'view' => 'entry.chiho_kaijo_keiho',
        ],

        '地方海上予報' => [
            'view' => 'entry.chihoumiyoho',
        ],

        '地方海上予報（Ｈ２８）' => [
            'view' => 'entry.chiho_kaijo_yoho',
        ],

        '気象警報・注意報' => [
            'view' => 'entry.kei_all_line_div',
        ],

        '気象特別警報・警報・注意報' => [
            'view' => 'entry.kei_all_line_div',
        ],

        '気象警報・注意報（Ｈ２７）' => [
            'view' => 'entry.kei_h27',
        ],

        '指定河川洪水予報' => [
            'view' => 'entry.kozui',
        ],

        '土砂災害警戒情報' => [
            'view' => 'entry.dosya',
        ],

        '記録的短時間大雨情報' => [
            'view' => 'entry.kiroame',
        ],

        '竜巻注意情報' => [
            'view' => 'entry.tatsumaki',
        ],

        '竜巻注意情報（目撃情報付き）' => [
            'view' => 'entry.tatsumaki',
        ],

        '全般気象情報' => [
            'view' => 'entry.ippanho',
        ],

        '地方気象情報' => [
            'view' => 'entry.ippanho',
        ],

        '府県気象情報' => [
            'view' => 'entry.ippanho',
        ],

        '府県天気概況' => [
            'view' => 'entry.ippanho',
        ],

        '府県天気予報' => [
            'view' => 'entry.yoho',
        ],

        '全般週間天気予報' => [
            'view' => 'entry.ippanho',
        ],

        '地方週間天気予報' => [
            'view' => 'entry.ippanho',
        ],

        '府県週間天気予報' => [
            'view' => 'entry.shukan',
        ],

        'スモッグ気象情報' => [
            'view' => 'entry.ippanho',
        ],

        '全般天候情報' => [
            'view' => 'entry.TenkoJohoHtmlPlain',
        ],

        '地方天候情報' => [
            'view' => 'entry.TenkoJohoHtmlPlain',
        ],

        '府県天候情報' => [
            'view' => 'entry.TenkoJohoHtmlPlain',
        ],

        '震度速報' => [
            'view' => 'entry.jishin_tsunami_tokai_decode_all',
        ],

        '震源に関する情報' => [
            'view' => 'entry.jishin_tsunami_tokai_decode_all',
        ],

        '顕著な地震の震源要素更新のお知らせ' => [
            'view' => 'entry.jishin_tsunami_tokai_decode_all',
        ],

        '地震回数に関する情報' => [
            'view' => 'entry.jishin_tsunami_tokai_decode_all',
        ],

        '地震の活動状況等に関する情報' => [
            'view' => 'entry.jishin_tsunami_tokai_decode_all',
        ],

        '震源・震度に関する情報' => [
            'view' => 'entry.jishin_tsunami_tokai_decode_all',
        ],

        '緊急地震速報（予報）' => [
            'view' => 'entry.jishin_tsunami_tokai_decode_all',
        ],

        '緊急地震速報（警報）' => [
            'view' => 'entry.jishin_tsunami_tokai_decode_all',
        ],

        '津波情報a' => [
            'view' => 'entry.jishin_tsunami_tokai_decode_all',
        ],

        '津波警報・注意報・予報a' => [
            'view' => 'entry.jishin_tsunami_tokai_decode_all',
        ],

        '東海地震予知情報' => [
            'view' => 'entry.jishin_tsunami_tokai_decode_all',
        ],

        '東海地震注意情報' => [
            'view' => 'entry.jishin_tsunami_tokai_decode_all',
        ],

        '東海地震観測情報' => [
            'view' => 'entry.jishin_tsunami_tokai_decode_all',
        ],

        '火山に関するお知らせ' => [
            'view' => 'entry.kazan',
        ],

        '噴火に関する火山観測報' => [
            'view' => 'entry.kazan',
        ],

        '火山の状況に関する解説情報' => [
            'view' => 'entry.kazan',
        ],

        '噴火警報・予報' => [
            'view' => 'entry.kazan',
        ],

        '火山現象に関する海上警報・海上予報' => [
            'view' => 'entry.kazan',
        ],

        '府県海氷予報' => [
            'view' => 'entry.fukenkaihyou',
        ],

        '全般潮位情報' => [
            'view' => 'entry.choui1',
        ],

        '地方潮位情報' => [
            'view' => 'entry.choui1',
        ],

        '府県潮位情報' => [
            'view' => 'entry.choui1',
        ],

        '全般１か月予報' => [
            'view' => 'entry.KisetuYohoHtmlPlain',
        ],

        '全般３か月予報' => [
            'view' => 'entry.KisetuYohoHtmlPlain',
        ],

        '全般暖・寒候期予報' => [
            'view' => 'entry.KisetuYohoHtmlPlain',
        ],

        '地方１か月予報' => [
            'view' => 'entry.KisetuYohoHtmlPlain',
        ],

        '地方３か月予報' => [
            'view' => 'entry.KisetuYohoHtmlPlain',
        ],

        '地方暖・寒候期予報' => [
            'view' => 'entry.KisetuYohoHtmlPlain',
        ],

        '異常天候早期警戒情報' => [
            'view' => 'entry.SoukeiHtmlPlain',
        ],

        '緊急地震速報配信テスト' => [
            'view' => 'entry.jishin_tsunami_tokai_decode_all',
        ],

        '全般スモッグ気象情報' => [
            'view' => 'entry.ippanho',
        ],

        '地方高温注意情報' => [
            'view' => 'entry.ippanho',
        ],

        '府県高温注意情報' => [
            'view' => 'entry.ippanho',
        ],

        '地上実況図' => [
            'view' => 'entry.tenkizu',
        ],

        '地上２４時間予想図' => [
            'view' => 'entry.tenkizu',
        ],

        '地上４８時間予想図' => [
            'view' => 'entry.tenkizu',
        ],

        '沖合の津波観測に関する情報' => [
            'view' => 'entry.jishin_tsunami_tokai_decode_all',
        ],

        '気象特別警報報知' => [
            'view' => 'entry.kei_all_line_div',
        ],

        'アジア太平洋地上実況図' => [
            'view' => 'entry.tenkizu',
        ],

        'アジア太平洋海上悪天２４時間予想図' => [
            'view' => 'entry.tenkizu',
        ],

        'アジア太平洋海上悪天４８時間予想図' => [
            'view' => 'entry.tenkizu',
        ],

        '降灰予報（定時）' => [
            'view' => 'entry.kazan',
        ],

        '降灰予報（速報）' => [
            'view' => 'entry.kazan',
        ],

        '降灰予報（詳細）' => [
            'view' => 'entry.kazan',
        ],

        '噴火速報' => [
            'view' => 'entry.kazan',
        ],

        '警報級の可能性（明日まで）' => [
            'view' => 'entry.keihoukyuu_asu',
        ],

        '警報級の可能性（明後日以降）' => [
            'view' => 'entry.keihoukyuu_asatte',
        ],
    ],

];
