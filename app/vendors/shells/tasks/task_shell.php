<?php
/**
 *  1.CRON スケジュール設定の実行コマンド
 *
 *    cd /home/アカウント名/cakephp/app; /usr/local/bin/php /home/アカウント名/cakephp/cake/console/cake.php task  1>/dev/null
 *
 *    ※cd CakePHPのappまでのパス; phpのパス CakePHPのコンソールまでのパス shellファイル名 1>/dev/null
 *
 *  2.スケジュールの設定
 *    月：「*」、日：「*」、時：「12」、分：「0」 (１日１回、昼12時に起動)
 */
class TaskShell extends Shell
{
	var $uses = array('CustomerMst','EnvMst');

	function main(){
	 $start = date('Y-m-d H:i:s');
     $start_sec = microtime();

     $ret = $this->CustomerMst->updateCustomerStatusIfWeddingFinished('自動');

     $end_sec = microtime();
     $finish=date('Y-m-d H:i:s');

     $message = '【開始】：'.$start.'  【終了】：'.$finish.' 【処理時間(分)】'.($start_sec-$end_sec);
     $message.= "\n【結果】".$ret['message']."\n\n";
     file_put_contents('task_log.txt', $message, FILE_APPEND | LOCK_EX);
	}
}
?>