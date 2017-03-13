<?php
/* 商品年度（年度管理を廃止したので固定とする） */
define('GOODS_YEAR','2012');

/* 導線1 */
define('LD1_NONE' ,'0');
define('LD1_MAIL','1');
define('LD1_PHONE' ,'2');

/* 導線2 */
define('LD2_NONE' ,'0');
define('LD2_GENERAL','1');
define('LD2_INTRODUCING' ,'2');

/* 挙式予約状況 */
define('WS_NO_ORDER','0');
define('WS_TEMPORARY_ORDERED','1');
define('WS_ORDERING','2');
define('WS_ORDERED' ,'3');

/* 支払区分 */
define('PC_INDIRECT_ABOARD_PAY','1');
define('PC_DIRECT_ABOARD_PAY','2');
define('PC_CREDIT_ABOARD_PAY','3');
define('PC_DOMESTIC_DIRECT_PAY','4');
define('PC_DOMESTIC_CREDIT_PAY','5');

/* リネン区分 */
define('TABLE_CLOTH','73');
define('TABLE_LINEN','80');
define('CHAIR_COVER','81');
define('SASH','82');

/* フラワー区分  */
define('FC_MAIN','1');
define('FC_CEREMONY','2');
define('FC_RECEPTION','3');

/* レセプションメニュー区分 */
define('RC_FOOD','1');
define('RC_DRINK','2');

/* コーディネーター区分 */
define('CC_NONE','0');
define('CC_MAIN','1');

/* ヘアメイク区分 */
define('HC_NONE','0');
define('HC_MAIN','1');

/* トランスポーテーションレセプション区分 */
define('TC_NONE','0');
define('TC_MAIN','1');

/* 存在区分 */
define('EXISTS','0');
define('DELETE','1');

/* 非表示区分 */
define('DISPLAY','0');
define('NON_DISPLAY','1');

/* ユーザー区分 */
define('UC_ADMIN','1');
define('UC_MEMBER','2');


/* 新郎・新婦フラグ */
define('GROOM','0');
define('BRIDE','1');

/* メール宛先区分 */
define('RECEIVER','1');
define('CC','2');
define('BCC','3');

/* ベンダー区分 */
define('VC_UNKNOWN','1');  //未決定のベンダー

/* 見積採用・未採用フラグ */
define('ESTIMATE_UNADOPTED','0');
define('ESTIMATE_ADOPTED','1');

/* 通貨区分 */
define('FOREIGN','0');   //外貨
define('DOMESTIC','1');  //円貨

/* セット商品区分 */
define('UNSET_GOODS','0');
define('SET_GOODS','1');

/* 入金区分 */
define('NC_UCHIKIN','1');  //内金・残金
define('NC_ZANKIN','2');  //残金
define('NC_DRESS','3');  //ドレス
define('NC_TRAVEL','4');  //旅行
define('NC_BUPPAN','5');  //物販
define('NC_GIFT','6');  //GIFT
define('NC_VENDOR','7');  //業者
define('NC_EXTRA','10');   //その他


/* 顧客ステータス */
define('CS_CONTACT','1');  //問い合わせ
/* define('CS_VISIT','2');    //新規接客 */
define('CS_ESTIMATED','3'); //見積提示済み
define('CS_AFTER_ESTIMATED','3_4_5_6_7_8'); //見積提示済以降
define('CS_CONTRACTING','4'); //仮約定
define('CS_CONTRACTED','5'); //成約
define('CS_AFTER_CONTRACTED','5_6_7_8'); //成約以降
define('CS_INVOICED','6');    //請求書発行済み
define('CS_UNPAIED','7');  //挙式完了・未入金
define('CS_PAIED','8');    //挙式完了・入金済み
define('CS_POSTPONE','9'); // 延期
define('CS_CANCEL','10');   //キャンセル
define('CS_BUPPAN','20');   //物販
define('CS_DRESS','21');   //ドレス
define('CS_TRIP','22');   //旅行
define('CS_GIFT','23');   //GIFT
define('CS_VENDOR','24');   //業者
define('CS_EXTRA','30');   //その他

/* 商品カテゴリ区分 */
define('GC_WEDDING','1');
define('GC_HAIR_MAKE_CPL','2');
define('GC_HAIR_MAKE_GST','3');
define('GC_TRANS_CPL','4');
define('GC_TRANS_GST','5');
define('GC_COORDINATOR','6');
define('GC_FLOWER','7');
define('GC_ALBUM','8');
define('GC_PHOTO','9');
define('GC_VIDEO','10');
define('GC_ENTERTAINMENT','11');
define('GC_MINISTER','12');
define('GC_RECEPTION','13');
define('GC_RECEPTION_TRANS','14');
define('GC_PARTY_OPTION','15');
define('GC_LINEN','16');
define('GC_AV','17');
define('GC_CAKE','18');
define('GC_PAPER','19');
define('GC_MC','20');
define('GC_HOUSE_WEDDING','21');
define('GC_CEREMONY_OPTION','22');
define('GC_FLOWER_MAIN','26');
define('GC_FLOWER_CEREMONY','27');
define('GC_FLOWER_RECEPTION','28');

define('GC_TRANSPORTATION','50');
define('GC_HAIR_MAKE','51');
define('GC_PERSONAL_INFO','52');
define('GC_BASIC_INFO','53');
define('GC_TRAVEL','54');

define('GC_CEREMONY_RING'  ,'55');
define('GC_CEREMONY_FLOWER','56');
define('GC_CEREMONY_BRIDE' ,'57');
define('GC_CEREMONY_GROOM' ,'58');

?>