<!DOCTYPE html>
<html lang="ar" dir="rtl">
<?php
date_default_timezone_set('Asia/Riyadh');
//引入配置文件
@include_once dirname(__DIR__).'/base.php';

if(isset($_GET['ID']))
{
   $ID = (int) trim($_GET['ID']);
}else{
   exit(json_encode(['status'=>400,'info'=>'error','data'=>'need post ID']));
}
$platform = '';
if(isset($_GET['platform']))
{
   $platform = trim($_GET['platform']);
}
$userId = 0;
if(isset($_GET['userId']))
{
   $userId = (int) trim($_GET['userId']);
}

$table_post = $table_prefix.'posts';
$table_author = $table_prefix.'users';
$table_tag = $table_prefix.'terms';
$table_tag_relationships = $table_prefix.'term_relationships';
$table_postmeta = $table_prefix.'postmeta';
$table_term_taxonomy = $table_prefix.'term_taxonomy';
$table_yuzoviews = $table_prefix.'yuzoviews';

$field = '`ID`,`post_title`,`post_content`,`post_date_gmt`,`post_mime_type`,`post_author`';


$post = $pdo->query("SELECT {$field} FROM $table_post WHERE ID={$ID} AND (post_status='publish') LIMIT 1")->fetch(PDO::FETCH_ASSOC);

$data = [];
$data['post'] = $post;
$data['author'] = getAuthor($post['post_author']);
$data['tags'] = getTags($post['ID']);
$data['btc_price'] = getBtcPrice($post['ID']);
// $data['helped'] = gethelped($post['ID']);
// $data['unhelped'] = getUnHelped($post['ID']);
$data['RecommendArticles'] = getRecommendArticles($post['ID']);
$data['post_view'] = getPostView($post['ID']);

function getAuthor($authorId)
{
   global $table_author,$pdo;
   $author = $pdo->query("SELECT `ID`,`user_nicename` FROM $table_author WHERE ID={$authorId} AND user_status='0' LIMIT 1")->fetch(PDO::FETCH_ASSOC);
   return $author;
}

function getTags($ID)
{
    global $table_tag,$pdo,$table_tag_relationships,$table_term_taxonomy;
    $tags = $pdo->query("SELECT * FROM $table_tag_relationships WHERE object_id={$ID}")->fetchAll(PDO::FETCH_ASSOC);
    $tagIds = '';
    foreach ($tags as $key => $value) {
        $tagIds.= ','.$value['term_taxonomy_id'];
    }
    $tagIds = trim($tagIds,',');

    $tags = $pdo->query("SELECT * FROM $table_term_taxonomy WHERE term_taxonomy_id IN ({$tagIds}) AND taxonomy='post_tag' ")->fetchAll(PDO::FETCH_ASSOC);
    $tagIds = '';
    foreach ($tags as $key => $value) {
        $tagIds.= ','.$value['term_taxonomy_id'];
    }
    $tagIds = trim($tagIds,',');

    if($row = $pdo->query("SELECT * FROM $table_tag WHERE term_id IN ({$tagIds})"))
    {
        $tagObj = $row->fetchAll(PDO::FETCH_ASSOC);
    }else{
        $tagObj = (object) [];
    }
    
    return $tagObj;
}

function getBtcPrice($ID)
{
    global $pdo,$table_postmeta;
    $price = $pdo->query("SELECT * FROM $table_postmeta WHERE post_id={$ID} AND meta_key='ApiMeta_btc_price' ")->fetch(PDO::FETCH_ASSOC);
    if($price){
        $price = $price['meta_value'];
    }else{
        $price = '0.00';
    }
    return $price;
}

function gethelped($ID)
{
    global $pdo,$table_postmeta;
    $helped = $pdo->query("SELECT * FROM $table_postmeta WHERE post_id={$ID} AND meta_key='ApiMeta_helped' ")->fetch(PDO::FETCH_ASSOC);
    if($helped){
        $helped = (int) $helped['meta_value'];
    }else{
        $helped = 0;
    }
    return $helped;
}

function getUnHelped($ID)
{
    global $pdo,$table_postmeta;
    $helped = $pdo->query("SELECT * FROM $table_postmeta WHERE post_id={$ID} AND meta_key='ApiMeta_unHelped' ")->fetch(PDO::FETCH_ASSOC);
    if($helped){
        $helped = (int) $helped['meta_value'];
    }else{
        $helped = 0;
    }
    return $helped;
}

function getRecommendArticles($ID)
{
    global $pdo,$table_tag_relationships,$table_post,$field;
    $tags = $pdo->query("SELECT * FROM $table_tag_relationships WHERE object_id={$ID}")->fetchAll(PDO::FETCH_ASSOC);
    $tagIds = '';
    foreach ($tags as $key => $value) {
        $tagIds.= ','.$value['term_taxonomy_id'];
    }
    $tagIds = trim($tagIds,',');
    if($row = $pdo->query("SELECT * FROM $table_tag_relationships WHERE term_taxonomy_id IN ({$tagIds}) AND object_id <> {$ID} GROUP BY object_id ORDER BY rand() LIMIT 5"))
    {
        $ids = $row->fetchAll(PDO::FETCH_ASSOC);
    }else{
        $ids = [];
    }
    $Ids = '';
    foreach ($ids as $key => $value) {
        $Ids.= ','.$value['object_id'];
    }
    $Ids = trim($Ids,',');
    if($ps = $pdo->query("SELECT $field FROM $table_post WHERE ID IN ({$Ids}) LIMIT 5"))
    {
        $posts = $ps->fetchAll(PDO::FETCH_ASSOC);
        foreach ($posts as $key => $value) {
            $posts[$key]['first_img'] = catch_that_image($value['ID']);
        }
    }else{
        $posts = (object) [];
    }
    foreach ($posts as $key => $value) {
        unset($posts[$key]['post_content']);
    }
    return $posts;
}

function catch_that_image($post_id) {
   global $pdo,$table_postmeta,$table_post;
   $row = $pdo->query("SELECT * FROM $table_postmeta WHERE `post_id` = $post_id AND `meta_key` = '_thumbnail_id' Limit 1;")->fetch(PDO::FETCH_ASSOC);
   if($row)
   {
      $row2 = $pdo->query("SELECT * FROM $table_post WHERE `ID` = {$row['meta_value']} Limit 1;")->fetch(PDO::FETCH_ASSOC);
      if($row2)
      {
         return $row2['guid'];
      }else{
         return "";
      }
   }else{
      return "";
   }
}

function getPostView($ID)
{
    global $pdo,$table_yuzoviews;
    $view = $pdo->query("SELECT * FROM $table_yuzoviews WHERE post_id={$ID} ")->fetch(PDO::FETCH_ASSOC);
    if($view){
        $view = (int) $view['views'];
    }else{
        $view = 0;
    }
    return $view;
}

?>
<head>
    <meta charset="UTF-8">
    <meta name="viewport"
          content="width=device-width,initial-scale=1.0, minimum-scale=1.0, maximum-scale=1.0, user-scalable=no, viewport-fit=cover">
    <meta name="keywords" content="BTC,Bitcoin"/>
    <meta property="og:title" content="يتم توزيع ١٠٠ بيتكوين مجانا">
    <meta property="og:type" content="website"/>
    <meta property="og:description" content="العرض متاح لعدد محدود من المستخدمين، سارع في استلام نصيبك قبل النفاذ">
    <meta property="og:image" content="https://cdn.arcoinonline.com/images/2018/06/09065532/share-s-upex.png">
    <meta name="twitter:card" content="summary_large_image">
    <meta name="twitter:title" content="لماذا تصبح أحلامك مزعجة قبيل الدورة الشهرية">
    <meta name="twitter:description" content="حادث تحطم حافلة، تذكر تفاصيل ا">
    <meta name="twitter:image"
          content="https://cdn-media-01.hayatapp.com/period/production/uploads/app_article_pgc/logo/57dff2d8201225d5eee1832c064b767d.jpg">

    <title><?php echo isset($data['post']['post_title']) ? $data['post']['post_title'] : ''; ?></title>
    <link rel="stylesheet" href="dist/css/application.min.css">
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.3.1/jquery.min.js"></script>
    <script src="dist/js/application.js"></script>

</head>
<body>
<div class="home">
    <div class="details-section-wrap">
        <div id="details-section-wrap">
            <h1 class="details-section-h1 bold"><?php echo isset($data['post']['post_title']) ? $data['post']['post_title'] : ''; ?></h1>
            <div class="head-wrap">
                <img class="user-icon lazy"
                     src="dist/image/lazy-head-img.png"
                     data-original="https://yt3.ggpht.com/-SsPthNTlpnE/AAAAAAAAAAI/AAAAAAAAAAA/K1TGDVrueTo/s76-c-k-no-mo-rj-c0xffffff/photo.jpg"/>
                <div class="user-name">
                    <p><?php echo isset($data['author']['user_nicename']) ? $data['author']['user_nicename'] : ''; ?></p>
                    <p class="time"><?php echo isset($data['post']['post_date_gmt']) ? $data['post']['post_date_gmt'] : ''; ?></p>
                </div>
                <span class="read-user"><?php echo $data['post_view'] ?></span>
                <i class="iconfont read-icon">&#xe75c;</i>
            </div>
            <!--正文部分 start-->
             <style type="text/css">
                .container{
                    margin:.2rem 0;
                    color:#666;
                    font-size:.16rem;
                    line-height:.3rem;
                    text-align:justify;
                }
            </style>
            <div class="container">
                <?php echo isset($data['post']['post_content']) ? $data['post']['post_content'] : ''; ?>
            </div>
            <!--正文部分 end-->
           
            <div class="now-btc-wrap">
                <p class="now-btc"><span class="icon"></span>$<?php echo isset($data['btc_price']) ? $data['btc_price'] : ''; ?></p>
                <p class="now-text">سعر البيتكوين في وقت النشر</p>
            </div>
        </div>
        <div class="app">
            <div class="share-wrap ">
                <div class="icon_facebook share"></div>
                <div class="icon_twitter share"></div>
                <div class="icon_whatsapp share"></div>
                <div class="icon_telegram share"></div>
                <div class="icon_reddit share"></div>
                <div class="icon_share share"></div>
            </div>
        </div>
        <p class="share-text app">شارك هذه المقالة مع أصدقائي</p>
        <div class="good-wrap app">
            <span class="icon"></span><span>مثل</span>
        </div>
    </div>
    <div class="list-span">
        <h2 class="details-h2 bold">التصنيف</h2>
        <div class="span-box clear">
            <?php
                foreach ($data['tags'] as $key => $value) {
            ?>
                <span><?php echo $value['name']; ?></span>
            <?php
                }
            ?>
        </div>

    </div>
    <div class="details-more-wrap">
        <h2 class="details-h2 bold">ﺔﻠﺼﻟﺍ ﺕﺍﺫ ﺔﻴﺻﻮﺘﻟﺍ</h2>
        <ul class="more-wrap">
            <?php
                foreach ($data['RecommendArticles'] as $key => $value) {
            ?>
            <li class="clear">
            <div class="more-wrap-img"
                 style="background-image: url('<?php echo $value['first_img']; ?>')"></div>
            <p class="more-wrap-text"><?php echo $value['post_title']; ?></p>
            <p class="more-wrap-time"><?php echo $value['post_date_gmt']; ?></p>
            </li>
            <?php
                }
            ?>
            
        </ul>
    </div>
    <?php

if($platform != 'android')
{
?>
    <div class="details-bottom">
          <div class="details-bottom-logo-wrap">
              <div class="details-bottom-logo"></div>
              <div class="details-bottom-text">
                  <h1 class="bold">افتح يا سمسم</h1>
                  <h2>افتح باب ثروتك</h2>
              </div>
          </div>
          <div class="details-bottom-button down-load bold">تحميل الآن</div>
      </div>
    <?php
}
?>
</div>
<textarea id="copy" style="position: absolute;top:-111111px"></textarea>
</body>
<script>
    $(function () {
        $(".lazy").lazyload({effect: "fadeIn"});
    })
</script>
<!-- Global site tag (gtag.js) - Google Analytics -->
<!--<script async src="https://www.googletagmanager.com/gtag/js?id=UA-105082350-2"></script>
<script>
    window.dataLayer = window.dataLayer || [];

    function gtag() {
        dataLayer.push(arguments);
    }

    gtag('js', new Date());

    gtag('config', 'UA-105082350-2');
</script>-->
<?php

if($platform != 'android')
{
?>
<script>
 (function (b, r, a, n, c, h, _, s, d, k) {
        if (!b[n] || !b[n]._q) {
            for (; s < _.length;) c(h, _[s++]);
            d = r.createElement(a);
            d.async = 1;
            d.src = "https://cdn.branch.io/branch-latest.min.js";
            k = r.getElementsByTagName(a)[0];
            k.parentNode.insertBefore(d, k);
            b[n] = h
        }
    })(window, document, "script", "branch", function (b, r) {
        b[r] = function () {
            b._q.push([r, arguments])
        }
    }, {
        _q: [],
        _v: 1
    }, "addListener applyCode banner closeBanner creditHistory credits data deepview deepviewCta first getCode init link logout redeem referrals removeListener sendSMS setBranchViewData setIdentity track validateCode".split(" "), 0);
    branch.init('key_live_covERTSNbjlwOW7mrnV9biloxEnRIviA');//替换这里的key
    branch.deepview(
        {
            'channel': 'BGT_H5',
            'feature': 'BGT_H5_download',
            data: {
                'feature': 'BGT'
            }
        }, {
            'open_app': true
        }
    );
</script>
    <?php
}
?>
</html>

