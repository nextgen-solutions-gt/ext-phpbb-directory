<?php

namespace E1379\SpeakingUrl;

class SpeakingUrl
{
    private $charMap = array("À" => 'A', "Á" => 'A', "Â" => 'A', "Ã" => 'A', "Ä" => 'Ae', "Å" => 'A', "Æ" => 'AE', "Ç" => 'C', "È" => 'E', "É" => 'E', "Ê" => 'E', "Ë" => 'E', "Ì" => 'I', "Í" => 'I', "Î" => 'I', "Ï" => 'I', "Ð" => 'D', "Ñ" => 'N', "Ò" => 'O', "Ó" => 'O', "Ô" => 'O', "Õ" => 'O', "Ö" => 'Oe', "Ő" => 'O', "Ø" => 'O', "Ù" => 'U', "Ú" => 'U', "Û" => 'U', "Ü" => 'Ue', "Ű" => 'U', "Ý" => 'Y', "Þ" => 'TH', "ß" => 'ss', "à" => 'a', "á" => 'a', "â" => 'a', "ã" => 'a', "ä" => 'ae', "å" => 'a', "æ" => 'ae', "ç" => 'c', "è" => 'e', "é" => 'e', "ê" => 'e', "ë" => 'e', "ì" => 'i', "í" => 'i', "î" => 'i', "ï" => 'i', "ð" => 'd', "ñ" => 'n', "ò" => 'o', "ó" => 'o', "ô" => 'o', "õ" => 'o', "ö" => 'oe', "ő" => 'o', "ø" => 'o', "ù" => 'u', "ú" => 'u', "û" => 'u', "ü" => 'ue', "ű" => 'u', "ý" => 'y', "þ" => 'th', "ÿ" => 'y', "ẞ" => 'SS', "ا" => 'a', "أ" => 'a', "إ" => 'i', "آ" => 'aa', "ؤ" => 'u', "ئ" => 'e', "ء" => 'a', "ب" => 'b', "ت" => 't', "ث" => 'th', "ج" => 'j', "ح" => 'h', "خ" => 'kh', "د" => 'd', "ذ" => 'th', "ر" => 'r', "ز" => 'z', "س" => 's', "ش" => 'sh', "ص" => 's', "ض" => 'dh', "ط" => 't', "ظ" => 'z', "ع" => 'a', "غ" => 'gh', "ف" => 'f', "ق" => 'q', "ك" => 'k', "ل" => 'l', "م" => 'm', "ن" => 'n', "ه" => 'h', "و" => 'w', "ي" => 'y', "ى" => 'a', "ة" => 'h', "ﻻ" => 'la', "ﻷ" => 'laa', "ﻹ" => 'lai', "ﻵ" => 'laa', "گ" => 'g', "چ" => 'ch', "پ" => 'p', "ژ" => 'zh', "ک" => 'k', "ی" => 'y', "َ" => 'a', "ً" => 'an', "ِ" => 'e', "ٍ" => 'en', "ُ" => 'u', "ٌ" => 'on', "ْ" => '', "٠" => '0', "١" => '1', "٢" => '2', "٣" => '3', "٤" => '4', "٥" => '5', "٦" => '6', "٧" => '7', "٨" => '8', "٩" => '9', "۰" => '0', "۱" => '1', "۲" => '2', "۳" => '3', "۴" => '4', "۵" => '5', "۶" => '6', "۷" => '7', "۸" => '8', "۹" => '9', "က" => 'k', "ခ" => 'kh', "ဂ" => 'g', "ဃ" => 'ga', "င" => 'ng', "စ" => 's', "ဆ" => 'sa', "ဇ" => 'z', "စျ" => 'za', "ည" => 'ny', "ဋ" => 't', "ဌ" => 'ta', "ဍ" => 'd', "ဎ" => 'da', "ဏ" => 'na', "တ" => 't', "ထ" => 'ta', "ဒ" => 'd', "ဓ" => 'da', "န" => 'n', "ပ" => 'p', "ဖ" => 'pa', "ဗ" => 'b', "ဘ" => 'ba', "မ" => 'm', "ယ" => 'y', "ရ" => 'ya', "လ" => 'l', "ဝ" => 'w', "သ" => 'th', "ဟ" => 'h', "ဠ" => 'la', "အ" => 'a', "ြ" => 'y', "ျ" => 'ya', "ွ" => 'w', "ြွ" => 'yw', "ျွ" => 'ywa', "ှ" => 'h', "ဧ" => 'e', "၏" => '-e', "ဣ" => 'i', "ဤ" => '-i', "ဉ" => 'u', "ဦ" => '-u', "ဩ" => 'aw', "သြော" => 'aw', "ဪ" => 'aw', "၀" => '0', "၁" => '1', "၂" => '2', "၃" => '3', "၄" => '4', "၅" => '5', "၆" => '6', "၇" => '7', "၈" => '8', "၉" => '9', "္" => '', "့" => '', "း" => '', "č" => 'c', "ď" => 'd', "ě" => 'e', "ň" => 'n', "ř" => 'r', "š" => 's', "ť" => 't', "ů" => 'u', "ž" => 'z', "Č" => 'C', "Ď" => 'D', "Ě" => 'E', "Ň" => 'N', "Ř" => 'R', "Š" => 'S', "Ť" => 'T', "Ů" => 'U', "Ž" => 'Z', "ހ" => 'h', "ށ" => 'sh', "ނ" => 'n', "ރ" => 'r', "ބ" => 'b', "ޅ" => 'lh', "ކ" => 'k', "އ" => 'a', "ވ" => 'v', "މ" => 'm', "ފ" => 'f', "ދ" => 'dh', "ތ" => 'th', "ލ" => 'l', "ގ" => 'g', "ޏ" => 'gn', "ސ" => 's', "ޑ" => 'd', "ޒ" => 'z', "ޓ" => 't', "ޔ" => 'y', "ޕ" => 'p', "ޖ" => 'j', "ޗ" => 'ch', "ޘ" => 'tt', "ޙ" => 'hh', "ޚ" => 'kh', "ޛ" => 'th', "ޜ" => 'z', "ޝ" => 'sh', "ޞ" => 's', "ޟ" => 'd', "ޠ" => 't', "ޡ" => 'z', "ޢ" => 'a', "ޣ" => 'gh', "ޤ" => 'q', "ޥ" => 'w', "ަ" => 'a', "ާ" => 'aa', "ި" => 'i', "ީ" => 'ee', "ު" => 'u', "ޫ" => 'oo', "ެ" => 'e', "ޭ" => 'ey', "ޮ" => 'o', "ޯ" => 'oa', "ް" => '', "ა" => 'a', "ბ" => 'b', "გ" => 'g', "დ" => 'd', "ე" => 'e', "ვ" => 'v', "ზ" => 'z', "თ" => 't', "ი" => 'i', "კ" => 'k', "ლ" => 'l', "მ" => 'm', "ნ" => 'n', "ო" => 'o', "პ" => 'p', "ჟ" => 'zh', "რ" => 'r', "ს" => 's', "ტ" => 't', "უ" => 'u', "ფ" => 'p', "ქ" => 'k', "ღ" => 'gh', "ყ" => 'q', "შ" => 'sh', "ჩ" => 'ch', "ც" => 'ts', "ძ" => 'dz', "წ" => 'ts', "ჭ" => 'ch', "ხ" => 'kh', "ჯ" => 'j', "ჰ" => 'h', "α" => 'a', "β" => 'v', "γ" => 'g', "δ" => 'd', "ε" => 'e', "ζ" => 'z', "η" => 'i', "θ" => 'th', "ι" => 'i', "κ" => 'k', "λ" => 'l', "μ" => 'm', "ν" => 'n', "ξ" => 'ks', "ο" => 'o', "π" => 'p', "ρ" => 'r', "σ" => 's', "τ" => 't', "υ" => 'y', "φ" => 'f', "χ" => 'x', "ψ" => 'ps', "ω" => 'o', "ά" => 'a', "έ" => 'e', "ί" => 'i', "ό" => 'o', "ύ" => 'y', "ή" => 'i', "ώ" => 'o', "ς" => 's', "ϊ" => 'i', "ΰ" => 'y', "ϋ" => 'y', "ΐ" => 'i', "Α" => 'A', "Β" => 'B', "Γ" => 'G', "Δ" => 'D', "Ε" => 'E', "Ζ" => 'Z', "Η" => 'I', "Θ" => 'TH', "Ι" => 'I', "Κ" => 'K', "Λ" => 'L', "Μ" => 'M', "Ν" => 'N', "Ξ" => 'KS', "Ο" => 'O', "Π" => 'P', "Ρ" => 'R', "Σ" => 'S', "Τ" => 'T', "Υ" => 'Y', "Φ" => 'F', "Χ" => 'X', "Ψ" => 'PS', "Ω" => 'O', "Ά" => 'A', "Έ" => 'E', "Ί" => 'I', "Ό" => 'O', "Ύ" => 'Y', "Ή" => 'I', "Ώ" => 'O', "Ϊ" => 'I', "Ϋ" => 'Y', "ā" => 'a', "ē" => 'e', "ģ" => 'g', "ī" => 'i', "ķ" => 'k', "ļ" => 'l', "ņ" => 'n', "ū" => 'u', "Ā" => 'A', "Ē" => 'E', "Ģ" => 'G', "Ī" => 'I', "Ķ" => 'k', "Ļ" => 'L', "Ņ" => 'N', "Ū" => 'U', "Ќ" => 'Kj', "ќ" => 'kj', "Љ" => 'Lj', "љ" => 'lj', "Њ" => 'Nj', "њ" => 'nj', "Тс" => 'Ts', "тс" => 'ts', "ą" => 'a', "ć" => 'c', "ę" => 'e', "ł" => 'l', "ń" => 'n', "ś" => 's', "ź" => 'z', "ż" => 'z', "Ą" => 'A', "Ć" => 'C', "Ę" => 'E', "Ł" => 'L', "Ń" => 'N', "Ś" => 'S', "Ź" => 'Z', "Ż" => 'Z', "Є" => 'Ye', "І" => 'I', "Ї" => 'Yi', "Ґ" => 'G', "є" => 'ye', "і" => 'i', "ї" => 'yi', "ґ" => 'g', "ă" => 'a', "Ă" => 'A', "ș" => 's', "Ș" => 'S', "ț" => 't', "Ț" => 'T', "ţ" => 't', "Ţ" => 'T', "а" => 'a', "б" => 'b', "в" => 'v', "г" => 'g', "д" => 'd', "е" => 'e', "ё" => 'yo', "ж" => 'zh', "з" => 'z', "и" => 'i', "й" => 'i', "к" => 'k', "л" => 'l', "м" => 'm', "н" => 'n', "о" => 'o', "п" => 'p', "р" => 'r', "с" => 's', "т" => 't', "у" => 'u', "ф" => 'f', "х" => 'kh', "ц" => 'c', "ч" => 'ch', "ш" => 'sh', "щ" => 'sh', "ъ" => '', "ы" => 'y', "ь" => '', "э" => 'e', "ю" => 'yu', "я" => 'ya', "А" => 'A', "Б" => 'B', "В" => 'V', "Г" => 'G', "Д" => 'D', "Е" => 'E', "Ё" => 'Yo', "Ж" => 'Zh', "З" => 'Z', "И" => 'I', "Й" => 'I', "К" => 'K', "Л" => 'L', "М" => 'M', "Н" => 'N', "О" => 'O', "П" => 'P', "Р" => 'R', "С" => 'S', "Т" => 'T', "У" => 'U', "Ф" => 'F', "Х" => 'Kh', "Ц" => 'C', "Ч" => 'Ch', "Ш" => 'Sh', "Щ" => 'Sh', "Ъ" => '', "Ы" => 'Y', "Ь" => '', "Э" => 'E', "Ю" => 'Yu', "Я" => 'Ya', "ђ" => 'dj', "ј" => 'j', "ћ" => 'c', "џ" => 'dz', "Ђ" => 'Dj', "Ј" => 'j', "Ћ" => 'C', "Џ" => 'Dz', "ľ" => 'l', "ĺ" => 'l', "ŕ" => 'r', "Ľ" => 'L', "Ĺ" => 'L', "Ŕ" => 'R', "ş" => 's', "Ş" => 'S', "ı" => 'i', "İ" => 'I', "ğ" => 'g', "Ğ" => 'G', "ả" => 'a', "Ả" => 'A', "ẳ" => 'a', "Ẳ" => 'A', "ẩ" => 'a', "Ẩ" => 'A', "đ" => 'd', "Đ" => 'D', "ẹ" => 'e', "Ẹ" => 'E', "ẽ" => 'e', "Ẽ" => 'E', "ẻ" => 'e', "Ẻ" => 'E', "ế" => 'e', "Ế" => 'E', "ề" => 'e', "Ề" => 'E', "ệ" => 'e', "Ệ" => 'E', "ễ" => 'e', "Ễ" => 'E', "ể" => 'e', "Ể" => 'E', "ỏ" => 'o', "ọ" => 'o', "Ọ" => 'o', "ố" => 'o', "Ố" => 'O', "ồ" => 'o', "Ồ" => 'O', "ổ" => 'o', "Ổ" => 'O', "ộ" => 'o', "Ộ" => 'O', "ỗ" => 'o', "Ỗ" => 'O', "ơ" => 'o', "Ơ" => 'O', "ớ" => 'o', "Ớ" => 'O', "ờ" => 'o', "Ờ" => 'O', "ợ" => 'o', "Ợ" => 'O', "ỡ" => 'o', "Ỡ" => 'O', "Ở" => 'o', "ở" => 'o', "ị" => 'i', "Ị" => 'I', "ĩ" => 'i', "Ĩ" => 'I', "ỉ" => 'i', "Ỉ" => 'i', "ủ" => 'u', "Ủ" => 'U', "ụ" => 'u', "Ụ" => 'U', "ũ" => 'u', "Ũ" => 'U', "ư" => 'u', "Ư" => 'U', "ứ" => 'u', "Ứ" => 'U', "ừ" => 'u', "Ừ" => 'U', "ự" => 'u', "Ự" => 'U', "ữ" => 'u', "Ữ" => 'U', "ử" => 'u', "Ử" => 'ư', "ỷ" => 'y', "Ỷ" => 'y', "ỳ" => 'y', "Ỳ" => 'Y', "ỵ" => 'y', "Ỵ" => 'Y', "ỹ" => 'y', "Ỹ" => 'Y', "ạ" => 'a', "Ạ" => 'A', "ấ" => 'a', "Ấ" => 'A', "ầ" => 'a', "Ầ" => 'A', "ậ" => 'a', "Ậ" => 'A', "ẫ" => 'a', "Ẫ" => 'A', "ắ" => 'a', "Ắ" => 'A', "ằ" => 'a', "Ằ" => 'A', "ặ" => 'a', "Ặ" => 'A', "ẵ" => 'a', "Ẵ" => 'A', "“" => '"', "”" => '"', "‘" => "'", "’" => "'", "∂" => 'd', "ƒ" => 'f', "™" => '(TM)', "©" => '(C)', "œ" => 'oe', "Œ" => 'OE', "®" => '(R)', "†" => '+', "℠" => '(SM)', "…" => '...', "˚" => 'o', "º" => 'o', "ª" => 'a', "•" => '*', "၊" => ',', "။" => '.', "$" => 'USD', "€" => 'EUR', "₢" => 'BRN', "₣" => 'FRF', "£" => 'GBP', "₤" => 'ITL', "₦" => 'NGN', "₧" => 'ESP', "₩" => 'KRW', "₪" => 'ILS', "₫" => 'VND', "₭" => 'LAK', "₮" => 'MNT', "₯" => 'GRD', "₱" => 'ARS', "₲" => 'PYG', "₳" => 'ARA', "₴" => 'UAH', "₵" => 'GHS', "¢" => 'cent', "¥" => 'CNY', "元" => 'CNY', "円" => 'YEN', "﷼" => 'IRR', "₠" => 'EWE', "฿" => 'THB', "₨" => 'INR', "₹" => 'INR', "₰" => 'PF', "₺" => 'TRY', "؋" => 'AFN', "₼" => 'AZN', "лв" => 'BGN', "៛" => 'KHR', "₡" => 'CRC', "₸" => 'KZT', "ден" => 'MKD', "zł" => 'PLN', "₽" => 'RUB', "₾" => 'GEL');

    private $lookAheadCharArray = array('်', 'ް');

    private $diatricMap = array("ာ" => 'a', "ါ" => 'a', "ေ" => 'e', "ဲ" => 'e', "ိ" => 'i', "ီ" => 'i', "ို" => 'o', "ု" => 'u', "ူ" => 'u', "ေါင်" => 'aung', "ော" => 'aw', "ော်" => 'aw', "ေါ" => 'aw', "ေါ်" => 'aw', "်" => '်', "က်" => 'et', "ိုက်" => 'aik', "ောက်" => 'auk', "င်" => 'in', "ိုင်" => 'aing', "ောင်" => 'aung', "စ်" => 'it', "ည်" => 'i', "တ်" => 'at', "ိတ်" => 'eik', "ုတ်" => 'ok', "ွတ်" => 'ut', "ေတ်" => 'it', "ဒ်" => 'd', "ိုဒ်" => 'ok', "ုဒ်" => 'ait', "န်" => 'an', "ာန်" => 'an', "ိန်" => 'ein', "ုန်" => 'on', "ွန်" => 'un', "ပ်" => 'at', "ိပ်" => 'eik', "ုပ်" => 'ok', "ွပ်" => 'ut', "န်ုပ်" => 'nub', "မ်" => 'an', "ိမ်" => 'ein', "ုမ်" => 'on', "ွမ်" => 'un', "ယ်" => 'e', "ိုလ်" => 'ol', "ဉ်" => 'in', "ံ" => 'an', "ိံ" => 'ein', "ုံ" => 'on', "ައް" => 'ah', "ަށް" => 'ah');

    private $langCharMap = array("en" => array(), "az" => array("ç" => 'c', "ə" => 'e', "ğ" => 'g', "ı" => 'i', "ö" => 'o', "ş" => 's', "ü" => 'u', "Ç" => 'C', "Ə" => 'E', "Ğ" => 'G', "İ" => 'I', "Ö" => 'O', "Ş" => 'S', "Ü" => 'U'), "cs" => array("č" => 'c', "ď" => 'd', "ě" => 'e', "ň" => 'n', "ř" => 'r', "š" => 's', "ť" => 't', "ů" => 'u', "ž" => 'z', "Č" => 'C', "Ď" => 'D', "Ě" => 'E', "Ň" => 'N', "Ř" => 'R', "Š" => 'S', "Ť" => 'T', "Ů" => 'U', "Ž" => 'Z'), "fi" => array("ä" => 'a', "Ä" => 'A', "ö" => 'o', "Ö" => 'O'), "hu" => array("ä" => 'a', "Ä" => 'A', "ö" => 'o', "Ö" => 'O', "ü" => 'u', "Ü" => 'U', "ű" => 'u', "Ű" => 'U'), "lt" => array("ą" => 'a', "č" => 'c', "ę" => 'e', "ė" => 'e', "į" => 'i', "š" => 's', "ų" => 'u', "ū" => 'u', "ž" => 'z', "Ą" => 'A', "Č" => 'C', "Ę" => 'E', "Ė" => 'E', "Į" => 'I', "Š" => 'S', "Ų" => 'U', "Ū" => 'U'), "lv" => array("ā" => 'a', "č" => 'c', "ē" => 'e', "ģ" => 'g', "ī" => 'i', "ķ" => 'k', "ļ" => 'l', "ņ" => 'n', "š" => 's', "ū" => 'u', "ž" => 'z', "Ā" => 'A', "Č" => 'C', "Ē" => 'E', "Ģ" => 'G', "Ī" => 'i', "Ķ" => 'k', "Ļ" => 'L', "Ņ" => 'N', "Š" => 'S', "Ū" => 'u', "Ž" => 'Z'), "pl" => array("ą" => 'a', "ć" => 'c', "ę" => 'e', "ł" => 'l', "ń" => 'n', "ó" => 'o', "ś" => 's', "ź" => 'z', "ż" => 'z', "Ą" => 'A', "Ć" => 'C', "Ę" => 'e', "Ł" => 'L', "Ń" => 'N', "Ó" => 'O', "Ś" => 'S', "Ź" => 'Z', "Ż" => 'Z'), "sk" => array("ä" => 'a', "Ä" => 'A'), "sr" => array("љ" => 'lj', "њ" => 'nj', "Љ" => 'Lj', "Њ" => 'Nj', "đ" => 'dj', "Đ" => 'Dj'), "tr" => array("Ü" => 'U', "Ö" => 'O', "ü" => 'u', "ö" => 'o'));

    private $symbolMap = array("ar" => array("∆" => 'delta', "∞" => 'la-nihaya', "♥" => 'hob', "&" => 'wa', "|" => 'aw', "<" => 'aqal-men', ">" => 'akbar-men', "∑" => 'majmou', "¤" => 'omla'), "az" => array(), "ca" => array("∆" => 'delta', "∞" => 'infinit', "♥" => 'amor', "&" => 'i', "|" => 'o', "<" => 'menys que', ">" => 'mes que', "∑" => 'suma dels', "¤" => 'moneda'), "cs" => array("∆" => 'delta', "∞" => 'nekonecno', "♥" => 'laska', "&" => 'a', "|" => 'nebo', "<" => 'mensi nez', ">" => 'vetsi nez', "∑" => 'soucet', "¤" => 'mena'), "de" => array("∆" => 'delta', "∞" => 'unendlich', "♥" => 'Liebe', "&" => 'und', "|" => 'oder', "<" => 'kleiner als', ">" => 'groesser als', "∑" => 'Summe von', "¤" => 'Waehrung'), "dv" => array("∆" => 'delta', "∞" => 'kolunulaa', "♥" => 'loabi', "&" => 'aai', "|" => 'noonee', "<" => 'ah vure kuda', ">" => 'ah vure bodu', "∑" => 'jumula', "¤" => 'faisaa'), "en" => array("∆" => 'delta', "∞" => 'infinity', "♥" => 'love', "&" => 'and', "|" => 'or', "<" => 'less than', ">" => 'greater than', "∑" => 'sum', "¤" => 'currency'), "es" => array("∆" => 'delta', "∞" => 'infinito', "♥" => 'amor', "&" => 'y', "|" => 'u', "<" => 'menos que', ">" => 'mas que', "∑" => 'suma de los', "¤" => 'moneda'), "fa" => array("∆" => 'delta', "∞" => 'bi-nahayat', "♥" => 'eshgh', "&" => 'va', "|" => 'ya', "<" => 'kamtar-az', ">" => 'bishtar-az', "∑" => 'majmooe', "¤" => 'vahed'), "fi" => array("∆" => 'delta', "∞" => 'aarettomyys', "♥" => 'rakkaus', "&" => 'ja', "|" => 'tai', "<" => 'pienempi kuin', ">" => 'suurempi kuin', "∑" => 'summa', "¤" => 'valuutta'), "fr" => array("∆" => 'delta', "∞" => 'infiniment', "♥" => 'Amour', "&" => 'et', "|" => 'ou', "<" => 'moins que', ">" => 'superieure a', "∑" => 'somme des', "¤" => 'monnaie'), "ge" => array("∆" => 'delta', "∞" => 'usasruloba', "♥" => 'siqvaruli', "&" => 'da', "|" => 'an', "<" => 'naklebi', ">" => 'meti', "∑" => 'jami', "¤" => 'valuta'), "gr" => array(), "hu" => array("∆" => 'delta', "∞" => 'vegtelen', "♥" => 'szerelem', "&" => 'es', "|" => 'vagy', "<" => 'kisebb mint', ">" => 'nagyobb mint', "∑" => 'szumma', "¤" => 'penznem'), "it" => array("∆" => 'delta', "∞" => 'infinito', "♥" => 'amore', "&" => 'e', "|" => 'o', "<" => 'minore di', ">" => 'maggiore di', "∑" => 'somma', "¤" => 'moneta'), "lt" => array("∆" => 'delta', "∞" => 'begalybe', "♥" => 'meile', "&" => 'ir', "|" => 'ar', "<" => 'maziau nei', ">" => 'daugiau nei', "∑" => 'suma', "¤" => 'valiuta'), "lv" => array("∆" => 'delta', "∞" => 'bezgaliba', "♥" => 'milestiba', "&" => 'un', "|" => 'vai', "<" => 'mazak neka', ">" => 'lielaks neka', "∑" => 'summa', "¤" => 'valuta'), "my" => array("∆" => 'kwahkhyaet', "∞" => 'asaonasme', "♥" => 'akhyait', "&" => 'nhin', "|" => 'tho', "<" => 'ngethaw', ">" => 'kyithaw', "∑" => 'paungld', "¤" => 'ngwekye'), "mk" => array(), "nl" => array("∆" => 'delta', "∞" => 'oneindig', "♥" => 'liefde', "&" => 'en', "|" => 'of', "<" => 'kleiner dan', ">" => 'groter dan', "∑" => 'som', "¤" => 'valuta'), "pl" => array("∆" => 'delta', "∞" => 'nieskonczonosc', "♥" => 'milosc', "&" => 'i', "|" => 'lub', "<" => 'mniejsze niz', ">" => 'wieksze niz', "∑" => 'suma', "¤" => 'waluta'), "pt" => array("∆" => 'delta', "∞" => 'infinito', "♥" => 'amor', "&" => 'e', "|" => 'ou', "<" => 'menor que', ">" => 'maior que', "∑" => 'soma', "¤" => 'moeda'), "ro" => array("∆" => 'delta', "∞" => 'infinit', "♥" => 'dragoste', "&" => 'si', "|" => 'sau', "<" => 'mai mic ca', ">" => 'mai mare ca', "∑" => 'suma', "¤" => 'valuta'), "ru" => array("∆" => 'delta', "∞" => 'beskonechno', "♥" => 'lubov', "&" => 'i', "|" => 'ili', "<" => 'menshe', ">" => 'bolshe', "∑" => 'summa', "¤" => 'valjuta'), "sk" => array("∆" => 'delta', "∞" => 'nekonecno', "♥" => 'laska', "&" => 'a', "|" => 'alebo', "<" => 'menej ako', ">" => 'viac ako', "∑" => 'sucet', "¤" => 'mena'), "sr" => array(), "tr" => array("∆" => 'delta', "∞" => 'sonsuzluk', "♥" => 'ask', "&" => 've', "|" => 'veya', "<" => 'kucuktur', ">" => 'buyuktur', "∑" => 'toplam', "¤" => 'para birimi'), "uk" => array("∆" => 'delta', "∞" => 'bezkinechnist', "♥" => 'lubov', "&" => 'i', "|" => 'abo', "<" => 'menshe', ">" => 'bilshe', "∑" => 'suma', "¤" => 'valjuta'), "vn" => array("∆" => 'delta', "∞" => 'vo cuc', "♥" => 'yeu', "&" => 'va', "|" => 'hoac', "<" => 'nho hon', ">" => 'lon hon', "∑" => 'tong', "¤" => 'tien te'));

    private $uricChars;

    private $uricNoSlashChars;

    private $markChars;

    public function __construct()
    {
        $this->markChars = join('', array('.', '!', '~', '*', "'", '(', ')'));
        $this->uricNoSlashChars = join('', array(';', '?', ':', '@', '&', '=', '+', '$', ','));
        $this->uricChars = join('', array(';', '?', ':', '@', '&', '=', '+', '$', ',', '/'));
    }

    function getSlug($input, $opts = [])
    {
        $separator = '-';
        $result = '';
        $diatricString = '';
        $customReplacements = [];
        $maintainCase = null;
        $titleCase = null;
        $truncate = null;
        $uricFlag = null;
        $uricNoSlashFlag = null;
        $markFlag = null;
        $symbol = null;
        $langChar = null;
        $lucky = null;
        $i = null;
        $ch = null;
        $l = null;
        $lastCharWasSymbol = null;
        $lastCharWasDiatric = null;
        $allowedChars = '';

        if (!is_string($input)) {
            return '';
        }

        if (!is_array($opts)) {
            $separator = (string) $opts;
        }

        $symbol = $this->symbolMap['en'];
        $langChar = $this->langCharMap['en'];

        if (is_array($opts)) {
            $maintainCase = isset($opts['maintainCase']) ? $opts['maintainCase'] : false;
            $customReplacements = (isset($opts['custom']) && is_array($opts['custom'])) ? $opts['custom'] : $customReplacements;
            $truncate = (isset($opts['truncate']) && (int) $opts['truncate'] > 1) ? (int) $opts['truncate'] : false;

            $uricFlag = isset($opts['uric']) ? $opts['uric'] : false;
            $uricNoSlashFlag = isset($opts['uricNoSlash']) ? $opts['uricNoSlash'] : false;
            $markFlag = isset($opts['mark']) ? $opts['mark'] : false;
            $convertSymbols = ((isset($opts['symbols']) && $opts['symbols'] === false) || (isset($opts['lang']) && $opts['lang'] === false)) ? false : true;
            $separator = isset($opts['separator']) ? $opts['separator'] : $separator;

            if ($uricFlag) {
                $allowedChars .= $this->uricChars;
            }

            if ($uricNoSlashFlag) {
                $allowedChars .= $this->uricNoSlashChars;
            }

            if ($markFlag) {
                $allowedChars .= $this->markChars;
            }

            $symbol = (isset($opts['lang']) && isset($this->symbolMap[$opts['lang']]) && $convertSymbols) ?
                $this->symbolMap[$opts['lang']] : ($convertSymbols ? $symbol : []);

			if (isset($opts['lang']) && isset($this->langCharMap[$opts['lang']]))
			{
				$this->langCharMap[$opts['lang']];
			}
			else
			{
			if (isset($opts['lang']) && ($opts['lang'] === true || $opts['lang'] === false))
			{
				[];
			}
			else
			{
				$langChar;
			}
			}

            if (isset($opts['titleCase']) && is_array($opts['titleCase']) && !empty($opts['titleCase'])) {
                foreach ($opts['titleCase'] as $v) {
                    $customReplacements[(string) $v] = (string) $v;
                }

                $titleCase = true;
            } else {
                $titleCase = isset($opts['titleCase']) ? (bool) $opts['titleCase'] : false;
            }

            foreach ($customReplacements as $v => $r) {
                if (mb_strlen($v) > 1) {
                    $input = preg_replace(sprintf('/\\b%s\\b/i', preg_quote($v, '/')), $r, $input);
                } else {
                    $input = preg_replace(sprintf('/%s/i', preg_quote($v, '/')), $r, $input);
                }
            }

            foreach ($customReplacements as $ch) {
                $allowedChars .= $ch;
            }

        }

        $allowedChars .= $separator;

        // escape all necessary chars
        $allowedChars = preg_quote($allowedChars, '/');

        // trim whitespaces
        $input = preg_replace('/(^\s+|\s+$)/', '', $input);

        $lastCharWasSymbol = false;
        $lastCharWasDiatric = false;

        $inputChars = preg_split('//u', $input, null, PREG_SPLIT_NO_EMPTY);
        $l = count($inputChars);

        foreach ($inputChars as $i => $ch) {

            if (in_array($ch, $customReplacements)) {
                // don't convert a already converted char
                $lastCharWasSymbol = false;
            } else if (isset($langChar[$ch])) {
                // process language specific diactrics chars conversion
                $ch = $lastCharWasSymbol && preg_match('/[A-Za-z0-9]/', $langChar[$ch]) ? ' ' . $langChar[$ch] : $langChar[$ch];

                $lastCharWasSymbol = false;
            } else if (array_key_exists($ch, $this->charMap)) {
                // the transliteration changes entirely when some special characters are added
                if ($i + 1 < $l && in_array($inputChars[$i + 1], $this->lookAheadCharArray)) {
                    $diatricString .= $ch;
                    $ch = '';
                } else if ($lastCharWasDiatric === true) {
                    $ch = $this->diatricMap[$diatricString] . $this->charMap[$ch];
                    $diatricString = '';
                } else {
                    // process diactrics chars
                    $ch = $lastCharWasSymbol && preg_match('/[A-Za-z0-9]/', $this->charMap[$ch]) ? ' ' . $this->charMap[$ch] : $this->charMap[$ch];
                }

                $lastCharWasSymbol = false;
                $lastCharWasDiatric = false;
            } else if (array_key_exists($ch, $this->diatricMap)) {
                $diatricString .= $ch;
                $ch = '';
                // end of string, put the whole meaningful word
                if ($i === $l - 1) {
                    $ch = $this->diatricMap[$diatricString];
                }
                $lastCharWasDiatric = true;
            } else if (
                // process symbol chars
                isset($symbol[$ch])
                && !($uricFlag && mb_strpos($this->uricChars, $ch) !== -1)
                && !($uricNoSlashFlag && mb_strpos($this->uricNoSlashChars, $ch) !== -1)
            ) {
                $ch = $lastCharWasSymbol || preg_match('/[A-Za-z0-9]/', mb_substr($result, -1)) ? $separator . $symbol[$ch] : $symbol[$ch];
                $ch .= isset($inputChars[$i + 1]) && preg_match('/[A-Za-z0-9]/', $inputChars[$i + 1]) ? $separator : '';

                $lastCharWasSymbol = true;
            } else {
                if ($lastCharWasDiatric === true) {
                    $ch = $this->diatricMap[$diatricString] . $ch;
                    $diatricString = '';
                    $lastCharWasDiatric = false;
                } else if ($lastCharWasSymbol && (preg_match('/[A-Za-z0-9]/', $ch) || preg_match('/[A-Za-z0-9]/', mb_substr($result, -1)))) {
                    // process latin chars
                    $ch = ' ' . $ch;
                }
                $lastCharWasSymbol = false;
            }

            // add allowed chars
            $result .= preg_replace('/[^\\w\\s' . $allowedChars . '_-]/', $separator, $ch);
        }

        if ($titleCase) {
            $result = preg_replace_callback('/(\w)(\S*)/', function ($m) use (&$customReplacements) {
                $j = mb_strtoupper($m[1]) . $m[2];

                return (array_key_exists(mb_strtolower($j), $customReplacements)) ? mb_strtolower($j) : $j;

            }, $result);
        }

        // eliminate duplicate separators
        // add separator
        // trim separators from start and end

        $result = preg_replace('/\\s+/', $separator, $result);
        $result = preg_replace('/' . preg_quote($separator, '/') . '+/', $separator, $result);
        $result = preg_replace('/(^' . preg_quote($separator, '/') . '+|' . preg_quote($separator, '/') . '+$)/', '', $result);

        if ($truncate && mb_strlen($result) > $truncate) {
            $lucky = mb_substr($result, $truncate, 1) === $separator;
            $result = mb_substr($result, 0, $truncate);

            if (!$lucky) {
                $result = mb_substr($result, 0, mb_strrpos($result, $separator));
            }
        }

        if (!$maintainCase && !$titleCase) {
            $result = mb_strtolower($result);
        }

        return $result;
    }
}