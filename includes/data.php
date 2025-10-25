<?php
// includes/data.php
// تُرجع مصفوفة الفعاليات كمصفوفات تجميعية Associative Array
// المصدر: قاعدة البيانات (إن توفّرت) وإلا JSON fallback

function db_connect_or_null() {
  // يحاول الاتصال بقاعدة البيانات إذا كان ملف db.php موجود ومُعد
  $pdo = null;
  if (file_exists(__DIR__ . '/../db.php')) {
    require_once __DIR__ . '/../db.php'; // يجب أن يُعرّف $pdo
    if (isset($pdo) && $pdo instanceof PDO) {
      try {
        $pdo->query("SELECT 1"); // اختبار سريع
        return $pdo;
      } catch (Exception $e) {
        // تجاهل، سنسقط إلى JSON
      }
    }
  }
  return null;
}

function load_events_all() {
  // 1) جرّب DB
  if ($pdo = db_connect_or_null()) {
    $stmt = $pdo->query("SELECT id, title, description, category, location, event_date, image FROM events ORDER BY event_date DESC");
    $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);
    return $rows ?: [];
  }

  // 2) وإلا: JSON fallback
  $jsonPath = __DIR__ . '/../assets/data/events_seed.json';
  if (!file_exists($jsonPath)) return [];
  $json = file_get_contents($jsonPath);
  $arr = json_decode($json, true);
  return is_array($arr) ? $arr : [];
}

function filter_events($events, $filters = []) {
  $category  = isset($filters['category'])  ? trim($filters['category']) : '';
  $q         = isset($filters['q'])         ? trim($filters['q'])        : '';
  $date_from = isset($filters['date_from']) ? trim($filters['date_from']): '';
  $date_to   = isset($filters['date_to'])   ? trim($filters['date_to'])  : '';

  return array_values(array_filter($events, function($e) use ($category,$q,$date_from,$date_to){
    // تطابق التصنيف
    if ($category !== '' && isset($e['category']) && $e['category'] !== $category) return false;

    // نص البحث (العنوان أو الوصف)
    if ($q !== '') {
      $hay = (isset($e['title']) ? $e['title'] : '') . ' ' . (isset($e['description']) ? $e['description'] : '');
      if (mb_stripos($hay, $q) === false) return false;
    }

    // الفترة الزمنية
    $evDate = isset($e['event_date']) ? strtotime($e['event_date']) : 0;
    if ($date_from !== '') {
      $from = strtotime($date_from);
      if ($from && $evDate < $from) return false;
    }
    if ($date_to !== '') {
      $to = strtotime($date_to);
      if ($to && $evDate > $to) return false;
    }
    return true;
  }));
}

function find_event_by_id($id) {
  $events = load_events_all();

  // إن كان مصدر DB، قد تكون القيم strings أو ints—نحوّل للعدد للمقارنة
  foreach ($events as $e) {
    if (isset($e['id']) && (string)$e['id'] === (string)$id) {
      return $e;
    }
  }
  return null;
}
function load_events_page($filters, $page=1, $perPage=9, &$total=0) {
  $page   = max(1, (int)$page);
  $perPage= max(1, (int)$perPage);

  // لو فيه DB متاح: نفّذ استعلامات مع WHERE و LIMIT
  if ($pdo = db_connect_or_null()) {
    $where = [];
    $params = [];

    if (!empty($filters['category'])) { $where[] = "category = :cat"; $params[':cat']=$filters['category']; }
    if (!empty($filters['q']))        { $where[] = "(title LIKE :q OR description LIKE :q)"; $params[':q']="%".$filters['q']."%"; }
    if (!empty($filters['date_from'])){ $where[] = "event_date >= :df"; $params[':df']=$filters['date_from']; }
    if (!empty($filters['date_to']))  { $where[] = "event_date <= :dt"; $params[':dt']=$filters['date_to']; }

    $wsql = $where ? ('WHERE '.implode(' AND ',$where)) : '';

    // المجموع
    $stmt = $pdo->prepare("SELECT COUNT(*) FROM events $wsql");
    $stmt->execute($params);
    $total = (int)$stmt->fetchColumn();

    // الصفحة
    $offset = ($page-1)*$perPage;
    $sql = "SELECT id,title,description,category,location,event_date,image
            FROM events $wsql ORDER BY event_date DESC LIMIT :lim OFFSET :off";
    $stmt = $pdo->prepare($sql);
    foreach ($params as $k=>$v) $stmt->bindValue($k,$v);
    $stmt->bindValue(':lim', (int)$perPage, PDO::PARAM_INT);
    $stmt->bindValue(':off', (int)$offset, PDO::PARAM_INT);
    $stmt->execute();
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
  }

  // JSON fallback
  $all = filter_events(load_events_all(), $filters);
  $total = count($all);
  $offset = ($page-1)*$perPage;
  return array_slice($all, $offset, $perPage);
}
