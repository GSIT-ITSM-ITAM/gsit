<?php

namespace App;

use Laminas\I18n\Translator\Translator;

final class Translation
{
  public $languages = [
    //Code       Name in native lang          LANG FILE      jquery tinymce english name            standard plural nb
    'ar_SA'  => ['العَرَبِيَّةُ',                     'ar_SA.mo',    'ar',    'ar', 'arabic',               103],
    'bg_BG'  => ['Български',                 'bg_BG.mo',    'bg',    'bg', 'bulgarian',            2],
    'id_ID'  => ['Bahasa Indonesia',          'id_ID.mo',    'id',    'id', 'indonesian',           2],
    'ms_MY'  => ['Bahasa Melayu',             'ms_MY.mo',    'ms',    'ms', 'malay',                2],
    'ca_ES'  => ['Català',                    'ca_ES.mo',    'ca',    'ca', 'catalan',              2], // ca_CA
    'cs_CZ'  => ['Čeština',                   'cs_CZ.mo',    'cs',    'cs', 'czech',                10],
    'de_DE'  => ['Deutsch',                   'de_DE.mo',    'de',    'de', 'german',               2],
    'da_DK'  => ['Dansk',                     'da_DK.mo',    'da',    'da', 'danish',               2]     , // dk_DK
    'et_EE'  => ['Eesti',                     'et_EE.mo',    'et',    'et', 'estonian',             2], // ee_ET
    'en_GB'  => ['English',                   'en_GB.mo',    'en-GB', 'en', 'english',              2],
    'en_US'  => ['English (US)',              'en_US.mo',    'en-GB', 'en', 'english',              2],
    'es_AR'  => ['Español (Argentina)',       'es_AR.mo',    'es',    'es', 'spanish',              2],
    'es_CO'  => ['Español (Colombia)',        'es_CO.mo',    'es',    'es', 'spanish',              2],
    'es_ES'  => ['Español (España)',          'es_ES.mo',    'es',    'es', 'spanish',              2],
    'es_419' => ['Español (América Latina)',  'es_419.mo',   'es',    'es', 'spanish',              2],
    'es_MX'  => ['Español (Mexico)',          'es_MX.mo',    'es',    'es', 'spanish',              2],
    'es_VE'  => ['Español (Venezuela)',       'es_VE.mo',    'es',    'es', 'spanish',              2],
    'eu_ES'  => ['Euskara',                   'eu_ES.mo',    'eu',    'eu', 'basque',               2],
    'fr_FR'  => ['Français',                  'fr_FR.mo',    'fr',    'fr', 'french',               2],
    'fr_CA'  => ['Français (Canada)',         'fr_CA.mo',    'fr',    'fr', 'french',               2],
    'fr_BE'  => ['Français (Belgique)',       'fr_BE.mo',    'fr',    'fr', 'french',               2],
    'gl_ES'  => ['Galego',                    'gl_ES.mo',    'gl',    'gl', 'galician',             2],
    'el_GR'  => ['Ελληνικά',                  'el_GR.mo',    'el',    'el', 'greek',                2], // el_EL
    'he_IL'  => ['עברית',                      'he_IL.mo',    'he',    'he', 'hebrew',               2], // he_HE
    'hi_IN'  => ['हिन्दी',                    'hi_IN.mo',    'hi',    'hi_IN', 'hindi' ,            2],
    'hr_HR'  => ['Hrvatski',                  'hr_HR.mo',    'hr',    'hr', 'croatian',             2],
    'hu_HU'  => ['Magyar',                    'hu_HU.mo',    'hu',    'hu', 'hungarian',            2],
    'it_IT'  => ['Italiano',                  'it_IT.mo',    'it',    'it', 'italian',              2],
    'kn'     => ['ಕನ್ನಡ',                     'kn.mo',       'en-GB', 'en', 'kannada',              2],
    'lv_LV'  => ['Latviešu',                  'lv_LV.mo',    'lv',    'lv', 'latvian',              2],
    'lt_LT'  => ['Lietuvių',                  'lt_LT.mo',    'lt',    'lt', 'lithuanian',           2],
    'mn_MN'  => ['Монгол хэл',                'mn_MN.mo',    'mn',    'mn', 'mongolian',            2],
    'nl_NL'  => ['Nederlands',                'nl_NL.mo',    'nl',    'nl', 'dutch',                2],
    'nl_BE'  => ['Flemish',                   'nl_BE.mo',    'nl',    'nl', 'flemish',              2],
    'nb_NO'  => ['Norsk (Bokmål)',            'nb_NO.mo',    'no',    'nb', 'norwegian',            2], // no_NB
    'nn_NO'  => ['Norsk (Nynorsk)',           'nn_NO.mo',    'no',    'nn', 'norwegian',            2], // no_NN
    'fa_IR'  => ['فارسی',                     'fa_IR.mo',    'fa',    'fa', 'persian',              2],
    'pl_PL'  => ['Polski',                    'pl_PL.mo',    'pl',    'pl', 'polish',               2],
    'pt_PT'  => ['Português',                 'pt_PT.mo',    'pt',    'pt', 'portuguese',           2],
    'pt_BR'  => ['Português do Brasil',       'pt_BR.mo',    'pt-BR', 'pt', 'brazilian portuguese', 2],
    'ro_RO'  => ['Română',                    'ro_RO.mo',    'ro',    'en', 'romanian',             2],
    'ru_RU'  => ['Русский',                   'ru_RU.mo',    'ru',    'ru', 'russian',              2],
    'sk_SK'  => ['Slovenčina',                'sk_SK.mo',    'sk',    'sk', 'slovak',               10],
    'sl_SI'  => ['Slovenščina',               'sl_SI.mo',    'sl',    'sl', 'slovenian slovene',    2],
    'sr_RS'  => ['Srpski',                    'sr_RS.mo',    'sr',    'sr', 'serbian',              2],
    'fi_FI'  => ['Suomi',                     'fi_FI.mo',    'fi',    'fi', 'finish',               2],
    'sv_SE'  => ['Svenska',                   'sv_SE.mo',    'sv',    'sv', 'swedish',              2],
    'vi_VN'  => ['Tiếng Việt',                'vi_VN.mo',    'vi',    'vi', 'vietnamese',           2],
    'th_TH'  => ['ภาษาไทย',                   'th_TH.mo',    'th',    'th', 'thai',                 2],
    'tr_TR'  => ['Türkçe',                    'tr_TR.mo',    'tr',    'tr', 'turkish',              2],
    'uk_UA'  => ['Українська',                'uk_UA.mo',    'uk',    'en', 'ukrainian',            2], // ua_UA
    'ja_JP'  => ['日本語',                     'ja_JP.mo',    'ja',    'ja', 'japanese',             2],
    'zh_CN'  => ['简体中文',                   'zh_CN.mo',    'zh-CN', 'zh', 'chinese',              2],
    'zh_TW'  => ['繁體中文',                   'zh_TW.mo',    'zh-TW', 'zh', 'chinese',              2],
    'ko_KR'  => ['한국/韓國',                  'ko_KR.mo',    'ko',    'ko', 'korean',               1],
    'zh_HK'  => ['香港',                      'zh_HK.mo',    'zh-HK', 'zh', 'chinese',              2],
    'be_BY'  => ['Belarussian',               'be_BY.mo',    'be',    'be', 'belarussian',          3],
    'is_IS'  => ['íslenska',                  'is_IS.mo',    'is',    'en', 'icelandic',            2],
    'eo'     => ['Esperanto',                 'eo.mo',       'eo',    'en', 'esperanto',            2],
    'es_CL'  => ['Español chileno',           'es_CL',       'es',    'es', 'spanish chilean',      2]
  ];

  public function loadLanguage($lang = null)
  {
    if (is_null($lang))
    {
      $lang = $this->getPreferredLanguage();
    }
    $mofile = __DIR__ . '/../locales/' . $this->languages[$lang][1];

    $translator = new Translator();
    $translator->setLocale($lang);
    $translator->addTranslationFile('gettext', $mofile, 'default', $lang);
    return $translator;
  }

  /**
   * Return preffered language (from HTTP headers, fallback to default GSIT lang).
   *
   * @return string
   */
  private function getPreferredLanguage(): string
  {
    // Extract accepted languages from headers
    // Accept-Language: fr-FR, fr;q=0.9, en;q=0.8, de;q=0.7, *;q=0.5
    $accepted_languages = [];
    $values = explode(',', $_SERVER['HTTP_ACCEPT_LANGUAGE'] ?? '');
    foreach ($values as $value)
    {
        $parts = explode(';q=', trim($value));
        $language = str_replace('-', '_', $parts[0]);
        $qfactor  = $parts[1] ?? 1; //q-factor defaults to 1
        $accepted_languages[$language] = $qfactor;
    }
    arsort($accepted_languages); // sort by qfactor

    foreach (array_keys($accepted_languages) as $language)
    {
      if (array_key_exists($language, $this->languages))
      {
        return $language;
      }
    }
    return 'en_GB';
  }
}
