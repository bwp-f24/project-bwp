<?php
// includes/i18n.php
if (session_status() === PHP_SESSION_NONE) session_start();

/* 1) اختيار اللغة (افتراضي ar) */
$LANG = $_SESSION['lang'] ?? ($_COOKIE['lang'] ?? 'ar');
if (isset($_GET['lang']) && in_array($_GET['lang'], ['ar','en'], true)) {
  $LANG = $_GET['lang'];
  $_SESSION['lang'] = $LANG;
  setcookie('lang', $LANG, time()+60*60*24*365, '/');
}

/* 2) اتجاه وملف Bootstrap */
$DIR = ($LANG === 'ar') ? 'rtl' : 'ltr';
$BOOTSTRAP_CSS = ($LANG === 'ar')
  ? 'https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.rtl.min.css'
  : 'https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css';

/* 3) نص اسم العلامة + مفاتيح الترجمة */
$STRINGS = [
  'ar' => [
    'brand' => 'فعاليات مدينتي',
    'nav_home' => 'الرئيسية', 'nav_events' => 'الفعاليات', 'nav_about' => 'عن الدليل', 'nav_contact' => 'اتصل بنا',
    'search_label' => 'ابحث بالعنوان/الوصف', 'search_ph' => 'مثال: كورال، ماراثون', 'category' => 'التصنيف', 'search_btn'=>'بحث',
    'stats_events' => 'فعالية مُسجّلة', 'stats_upcoming'=>'قادمة', 'stats_categories'=>'تصنيف', 'stats_places'=>'مكان',
    'popular_cats' => 'التصنيفات الشائعة', 'this_week' => 'قريبًا هذا الأسبوع', 'latest_events' => 'أحدث الفعاليات',
    'view_more' => 'عرض المزيد', 'explore_all' => 'تصفّح كل الفعاليات', 'send_event' => 'لديك فعالية؟ أرسلها لنا',
    'details'=>'التفاصيل', 'add_to_calendar'=>'أضِف للتقويم',
    'theme' => 'الوضع', 'lang' => 'اللغة', 'dark'=>'داكن', 'light'=>'فاتح', 'auto'=>'تلقائي',
    'ar'=>'العربية', 'en'=>'English'
  ],
  'en' => [
    'brand' => 'City Events',
    'nav_home' => 'Home', 'nav_events' => 'Events', 'nav_about' => 'About', 'nav_contact' => 'Contact',
    'search_label' => 'Search title/description', 'search_ph' => 'e.g. choir, marathon', 'category' => 'Category', 'search_btn' =>'Search',
    'stats_events' => 'Events', 'stats_upcoming'=>'Upcoming', 'stats_categories'=>'Categories', 'stats_places'=>'Places',
    'popular_cats' => 'Popular Categories', 'this_week' => 'This Week', 'latest_events' => 'Latest Events',
    'view_more' => 'View more', 'explore_all' => 'Browse all events', 'send_event' => 'Got an event? Send it',
    'details'=>'Details', 'add_to_calendar'=>'Add to calendar',
    'theme' => 'Theme', 'lang' => 'Language', 'dark'=>'Dark', 'light'=>'Light', 'auto'=>'Auto',
    'ar'=>'العربية', 'en'=>'English'
  ],
];

/* 4) دالة ترجمة */
function t(string $key): string {
  global $LANG, $STRINGS;
  return $STRINGS[$LANG][$key] ?? $key;
}

/* 5) توليد رابط تبديل اللغة مع الحفاظ على باراميترات الرابط */
function lang_switch_url(string $to = 'en'): string {
  $qs = $_GET;
  $qs['lang'] = $to;
  $path = strtok($_SERVER['REQUEST_URI'],'?'); // المسار بدون استعلام
  return htmlspecialchars($path.'?'.http_build_query($qs), ENT_QUOTES, 'UTF-8');
}
