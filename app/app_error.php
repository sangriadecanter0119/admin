<?php
/**
 * 
 * メソッド単位で独自のエラー処理を定義する。
 * 呼び出し側では
 *      $this->cakeError(メソッド名, array("message" => エラーメッセージ));
 * のように呼び出す。
 * またクラス名は固定とする。
 * @author takano yohei
 *
 */
class AppError extends ErrorHandler {
    function unexpectedError($params) {
        $this->controller->set('message', $params["message"]);
        $this->controller->layout = "error";
        /* app/views/errors/配下にある表示用viewファイル名 */
        $this->_outputMessage('error');
    }
}
?>