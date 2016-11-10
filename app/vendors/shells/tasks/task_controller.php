<?php
//30分
set_time_limit(1800);
/**
 * APIパターン
 * [注意]
 *   Authコンポーネントによる認証を行うシステムで、cron指定するアクションを要ログインのアクションにしていたのでcronが上手く動いてくれなかった．
 *   beforeFilterの$this->Auth->allow()でcronで動かすアクションをログイン無しでアクセスできるようにしたら動きました．
 *
 * [パターン1]
 *    実行したいURL : http://host.com/users/check/

      1.CRONに設定したいファイル作成(cron.php)
   	    <?php
	      $_GET['url'] = "users/check/";
	      require_once( dirname( dirname(__FILE__) ) . "/index.php" );
 *      ?>
 *
 *    2.CRON スケジュール設定の実行コマンド(cron.phpをwebrootに置いた場合)
 *
 *      cd /home/???/www/???/app/webroot/; /usr/local/bin/php cron.php 1>/dev/null
 *	    cd /home/???/www/;                 /usr/local/bin/php cron.php 1>/dev/null
 *
 *  [パターン2] 参考：http://ameblo.jp/n0bisuke/entry-11185172452.html
 *    1.実行したいコントローラーを作成
 *        app/vendors/shells/cron/controller_action.php
 *
 *    2.CRON スケジュール設定の実行コマンド
 *       cd /home/(アカウント名)/www/hoge/(アプリ名)/app/vendors/shells/cron/; /usr/local/bin/php controller_action.php 1>/dev/null
 *
 *   ※ファイルに実行権限を与える必要があるかもしれない
 *
 *
 */
class TaskController extends AppController
{
 public $uses = array('CustomerMst');
 public $components = array('Auth');

 function beforeFilter(){
 	$this->Auth->allow('main');
 }
 /**
  * 挙式済みの顧客ステータスの更新
  */
 function main(){

 	 $start = date('Y-m-d H:i:s');
     $start_sec = microtime();

     $ret = $this->CustomerMst->updateCustomerStatusIfWeddingFinished('自動');

     if($ret['result']){
     	$this->EnvMst->updateAll(array("status_upd_dt"=>"'".date('Y-m-d H:i:s')."'"));
     }

     $end_sec = microtime();
     $finish=date('Y-m-d H:i:s');

     $message = '【開始】：'.$start.'  【終了】：'.$finish.' 【処理時間(分)】'.($start_sec-$end_sec);
     $message.= "\n【結果】".$ret['message']."\n\n";
     file_put_contents('task_log.txt', $message, FILE_APPEND | LOCK_EX);
 }


}
?>
