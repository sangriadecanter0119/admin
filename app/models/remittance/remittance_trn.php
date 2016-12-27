<?php
class RemittanceTrn extends AppModel {
    var $name = 'RemittanceTrn';

  /**
   *
   * 送金管理データの新規作成
   * @param $contract_id
   * @param $user_name
   * @return 正常： 新規データのID　
   *         異常：
   */
  function createNew($contract_id,$user_name){

    $remittance_data = array(
 	                         "contract_id"=>$contract_id,
 	                         "reg_nm"=>$user_name,
 	                         "reg_dt"=>date('Y-m-d H:i:s')
 	                         );
 	$this->create();
    if($this->save($remittance_data)==false){
    	return array('result'=>false,'message'=>"送金管理データの作成に失敗しました。",'reason'=>$this->getDbo()->error."[".date('Y-m-d H:i:s')."]");
    }
    return array('result'=>true,'newID'=>$this->getLastInsertID());
  }

  /**
   *
   * 明細行の計算
   * @param $data
   */
  function _calculateLine($data){

       //価格計算等の準備
       $sales_rate= $data['EstimateDtlTrn']['sales_exchange_rate'];
       $cost_rate = $data['EstimateDtlTrn']['cost_exchange_rate'];
       $num       = $data['EstimateDtlTrn']['num'];
       $aw_rate   = $data['EstimateDtlTrn']['aw_share'];
       $rw_rate   = $data['EstimateDtlTrn']['rw_share'];

       $foreign_unit_price=0;
       $foreign_amount_price=0;
       $foreign_unit_cost=0;
       $foreign_cost=0;
       $foreign_net=0;
       $foreign_profit_rate=0;
       $foreign_aw_share=0;
       $foreign_rw_share=0;
       //国内払いの金額
       $foreign_domestic_pay = 0;
       $foreign_domestic_pay_cost = 0;

       $unit_price=0;
       $amount_price=0;
       $unit_cost=0;
       $cost=0;
       $net=0;
       $profit_rate=0;
       $aw_share=0;
       $rw_share=0;
       //国内払いの金額
 	   $domestic_pay = 0;
 	   $domestic_pay_cost = 0;

 	   //価格がドルベース
       if($data['EstimateDtlTrn']['currency_kbn']==0)
       {
       	   /* ドル価格の計算  */
         	$foreign_unit_price = $data['EstimateDtlTrn']['sales_price'];
            $foreign_amount_price = $foreign_unit_price * $num;
            $foreign_unit_cost = $data['EstimateDtlTrn']['sales_cost'];
            $foreign_cost = $foreign_unit_cost * $num;
            $foreign_net = $foreign_amount_price - $foreign_cost;
            $foreign_aw_share = $foreign_net * $aw_rate;
            $foreign_rw_share = $foreign_net * $rw_rate;
            //利益率
            if($foreign_amount_price != 0){
            	$foreign_profit_rate = round($foreign_net / $foreign_amount_price * 100);
            }

           /* 円価格に変換して計算 */
            $unit_price = round($data['EstimateDtlTrn']['sales_price'] * $sales_rate);
            $amount_price = $unit_price * $num;
            $unit_cost = round($data['EstimateDtlTrn']['sales_cost'] * $cost_rate);
            $cost = $unit_cost * $num;
            $net = $amount_price - $cost;
            $aw_share = $net * $aw_rate;
            $rw_share = $net * $rw_rate;
            //利益率
            if($amount_price != 0){
            	$profit_rate = round($net / $amount_price * 100);
            }
      }
      //価格が円ベース
      else
      {
      	  /* 円価格の計算 */
            $unit_price   = round($data['EstimateDtlTrn']['sales_price']);
            $amount_price = $unit_price * $num;
            $domestic_pay = $amount_price;
            $unit_cost    = round($data['EstimateDtlTrn']['sales_cost']);
            $cost         = $unit_cost * $num;
            $domestic_pay_cost = $cost;
            $net          = $amount_price - $cost;
            $aw_share = $net * $aw_rate;
            $rw_share = $net * $rw_rate;
            //利益率
            if($amount_price != 0){
            	$profit_rate = round($net / $amount_price * 100);
            }

           /* ドル価格に変換して計算  */
            $foreign_unit_price   = round($data['EstimateDtlTrn']['sales_price'] / $sales_rate,2);
            $foreign_amount_price = $foreign_unit_price * $num;
            $foreign_domestic_pay = $foreign_amount_price;
            $foreign_unit_cost    = round($data['EstimateDtlTrn']['sales_cost'] / $cost_rate,2);
            $foreign_cost         = $foreign_unit_cost * $num;
            $foreign_domestic_pay_cost = $foreign_cost ;
            $foreign_net          = $foreign_amount_price - $foreign_cost;
            $foreign_aw_share     = $foreign_net * $aw_rate;
            $foreign_rw_share     = $foreign_net * $rw_rate;
            //利益率
            if($foreign_amount_price != 0){
            	$foreign_profit_rate = round($foreign_net / $foreign_amount_price * 100);
            }
       }

       return array("unit_price"  => $unit_price,
                    "amount_price"=> $amount_price,
                    "unit_cost"   => $unit_cost,
                    "cost"        => $cost,
                    "net"         => $net,
                    "profit_rate" => $profit_rate,
                    "aw_share"    => $aw_share,
                    "rw_share"    => $rw_share,
                    "domestic_pay"=> $domestic_pay,
                    "domestic_pay_cost"=> $domestic_pay_cost,
                    "foreign_unit_price"  => $foreign_unit_price,
                    "foreign_amount_price"=> $foreign_amount_price,
                    "foreign_unit_cost"   => $foreign_unit_cost,
                    "foreign_cost"        => $foreign_cost,
                    "foreign_net"         => $foreign_net,
                    "foreign_profit_rate" => $foreign_profit_rate,
                    "foreign_aw_share"    => $foreign_aw_share,
                    "foreign_rw_share"    => $foreign_rw_share,
                    "foreign_domestic_pay" => $foreign_domestic_pay,
                    "foreign_domestic_pay_cost" => $foreign_domestic_pay_cost,
       		        "sales_rate"=>$sales_rate,
       		        "cost_rate"=>$cost_rate
       );
  }

  /**
   *
   * 明細行の合計計算
   * @param $data
   */
  function _calculateSubTotal($data,$remittance_exchange_rate){

     $foreign_amount_price = 0;
 	 $amount_price = 0;
 	 $foreign_cost = 0;
 	 $cost = 0;
 	 $net = 0;
 	 $profit_rate = 0;
 	 $aw_share = 0;
 	 $foreign_aw_share = 0;
 	 $rw_share = 0;
 	 $foreign_rw_share = 0;
 	 $domestic_pay_amount = 0;
 	 $domestic_pay_cost_amount = 0;
 	 $foreign_domestic_pay_amount = 0;
 	 $foreign_domestic_pay_cost_amount = 0;
     $credit_domestic_pay_amount = 0;
     $credit_aboard_pay_amount = 0;
     $foreign_credit_domestic_pay_amount = 0;
     $foreign_credit_aboard_pay_amount = 0;
     $sales_rate = 0;
     $cost_rate = 0;

 	 for($i=0;$i < count($data);$i++)
     {
 	     $ret = $this->_calculateLine($data[$i]);
 	     //総代価 (外貨)
 	     $foreign_amount_price += $ret["foreign_amount_price"];
 	     //総代価 (円貨)
 	     $amount_price += $ret["amount_price"];

 	     //国内クレジット払い
 	     if($data[$i]['EstimateDtlTrn']['payment_kbn_id'] == PC_DOMESTIC_CREDIT_PAY){
 	     	$credit_domestic_pay_amount += $ret["amount_price"];
 	     	$foreign_credit_domestic_pay_amount += $ret["foreign_amount_price"];
 	     }

 	     //海外クレジット払い
 	     if($data[$i]['EstimateDtlTrn']['payment_kbn_id'] == PC_CREDIT_ABOARD_PAY){
 	     	$credit_aboard_pay_amount += $ret["amount_price"];
 	     	$foreign_credit_aboard_pay_amount += $ret["foreign_amount_price"];
 	     }

 	     //総原価 (外貨)
         if($data[$i]['EstimateDtlTrn']['payment_kbn_id'] != PC_CREDIT_ABOARD_PAY   &&
            $data[$i]['EstimateDtlTrn']['payment_kbn_id'] != PC_DOMESTIC_DIRECT_PAY &&
    	    $data[$i]['EstimateDtlTrn']['payment_kbn_id'] != PC_DOMESTIC_CREDIT_PAY){

   	          $foreign_cost += $ret["foreign_cost"];
   	     }
   	     //総原価 (円貨)
   	     if($data[$i]['EstimateDtlTrn']['payment_kbn_id'] != PC_CREDIT_ABOARD_PAY   &&
   	        $data[$i]['EstimateDtlTrn']['payment_kbn_id'] != PC_DOMESTIC_DIRECT_PAY &&
    	    $data[$i]['EstimateDtlTrn']['payment_kbn_id'] != PC_DOMESTIC_CREDIT_PAY){

   	          $cost += $ret["cost"];
   	     }
 	     //利益(円貨)
 	     $net         += $ret["net"];
 	     //利益率(円貨)
 	 	 $profit_rate += $ret["profit_rate"];

 	 	 //awシェア (円貨)
         if($data[$i]['EstimateDtlTrn']['payment_kbn_id'] != PC_DOMESTIC_DIRECT_PAY &&
            $data[$i]['EstimateDtlTrn']['payment_kbn_id'] != PC_DOMESTIC_CREDIT_PAY){

   	          $aw_share += $ret["aw_share"];
   	      }

        //awシェア (外貨)
         if($data[$i]['EstimateDtlTrn']['payment_kbn_id'] != PC_DOMESTIC_DIRECT_PAY &&
            $data[$i]['EstimateDtlTrn']['payment_kbn_id'] != PC_DOMESTIC_CREDIT_PAY &&
   	        $remittance_exchange_rate != ""  &&
   	      	number_format($remittance_exchange_rate) != "0"){

               $foreign_aw_share += $ret["aw_share"] / $remittance_exchange_rate;
   	      }
   	    //rwシェア(円貨)
 	    $rw_share += $ret["rw_share"];
 	    //rwシェア(外貨)
 	    $foreign_rw_share += $ret["foreign_rw_share"];
 	    //国内払いの金額合計(円貨)
 	    $domestic_pay_amount += $ret["domestic_pay"];
 	    $domestic_pay_cost_amount += $ret["domestic_pay_cost"];
 	    //国内払いの金額合計(外貨)
 	    $foreign_domestic_pay_amount += $ret["foreign_domestic_pay"];
 	    $foreign_domestic_pay_cost_amount += $ret["foreign_domestic_pay_cost"];

 	    $sales_rate = $ret["sales_rate"];
 	    $cost_rate = $ret["cost_rate"];
     }

 	 return array("amount_price"         => $amount_price,
 	              "foreign_amount_price" => $foreign_amount_price,
 	              "foreign_cost"         => $foreign_cost,
 	              "cost"                 => $cost,
 	              "net"                  => $net,
 	              "profit_rate"          => $profit_rate,
 	              "aw_share"             => $aw_share,
 	              "foreign_aw_share"     => $foreign_aw_share,
 	              "rw_share"             => $rw_share,
 	              "foreign_rw_share"     => $foreign_rw_share,
 	              "domestic_pay_amount"  => $domestic_pay_amount,
 	 	     	  "domestic_pay_cost_amount"  => $domestic_pay_cost_amount,
 	 	    	  "foreign_domestic_pay_amount"  => $foreign_domestic_pay_amount,
 	 		      "foreign_domestic_pay_cost_amount"  => $foreign_domestic_pay_cost_amount,
 	 		      "credit_domestic_pay_amount" => $credit_domestic_pay_amount,
 	 		      "credit_aboard_pay_amount" => $credit_aboard_pay_amount,
 	 		      "foreign_credit_domestic_pay_amount" => $foreign_credit_domestic_pay_amount,
 	 		      "foreign_credit_aboard_pay_amount" => $foreign_credit_aboard_pay_amount,
 	 		      "sales_rate"=>$sales_rate,
 	 		      "cost_rate"=>$cost_rate
 	 );
  }

  /**
   *
   * 送金金額の計算
   * @param $estimate_id
   * @param $user
   */
  function calculate($estimate_id,$user){

  	 App::import("Model", "EstimateTrn");
  	 $estimate = new EstimateTrn();

  	 App::import("Model", "EstimateDtlTrn");
  	 $estimate_dtl = new EstimateDtlTrn();

  	 App::import("Model", "RemittanceTrnView");
  	 $remittance = new RemittanceTrnView();

  	 //見積ヘッダを取得
  	 $estimate_data = $estimate->findById($estimate_id);
  	 //見積明細を取得
 	 $estimate_dtl_data = $estimate_dtl->find('all',array('conditions'=>array('estimate_id'=>$estimate_id),'order'=>'no'));
  	 //送金IDを取得
 	 $remittance_data = $remittance->find('all',array('conditions'=>array('estimate_id'=>$estimate_id)));

 	 $hawaii_tax_rate        = $estimate_data["EstimateTrn"]["hawaii_tax_rate"];
 	 $service_rate           = $estimate_data["EstimateTrn"]["service_rate"];
 	 $discount_rate          = $estimate_data["EstimateTrn"]["discount_rate"];
 	 $discount_fee2          = $estimate_data["EstimateTrn"]["discount"];
 	 $discount_exchange_rate = $estimate_data["EstimateTrn"]["discount_exchange_rate"];
 	 $discount_aw_share      = $estimate_data["EstimateTrn"]["discount_aw_share"];
 	 $discount_rw_share      = $estimate_data["EstimateTrn"]["discount_rw_share"];
 	 $remittance_exchange_rate = $estimate_data["EstimateTrn"]["remittance_exchange_rate"];

 	 $ret = $this->_calculateSubTotal($estimate_dtl_data,$remittance_exchange_rate);

        /* 邦貨合計計算 */
        //TAX
        $tax = $hawaii_tax_rate  *  $ret["amount_price"];
        //手数料
        $service = $service_rate  * $ret["amount_price"];
        //代価合計(TAX・手数料込)
        $mid_total_amount_price = $ret["amount_price"] + $tax + $service;

        //割引料
        $discount_fee1 = $mid_total_amount_price  *  $discount_rate;
        //割引額
        //$discount_currency = $discount;
        //代価合計(TAX・手数料・割引料込)
        $total_amount_price = $mid_total_amount_price - $discount_fee1 - $discount_fee2;
        //原価総合計
        $total_cost = $ret["cost"];

        //利益（代価-原価)
        $sub_total_net = $ret["net"];
        //利益（代価(TAX・手数料込)-原価)
        $mid_total_net = $mid_total_amount_price -  $total_cost;
        //利益（代価(TAX・手数料・割引料込)
        $total_net = $total_amount_price - $total_cost;

        //業者配分総合計
        $mid_total_aw = $ret["aw_share"];
        //RW配分合計(手数料込)
        $mid_total_rw = $service + $ret["rw_share"];
        //AW配分割引料
        $total_aw_with_discount = $discount_fee1  * $discount_aw_share;
        $total_aw_with_discount_currency = $discount_fee2 * $discount_aw_share;

        //RW配分割引料
        $total_rw_with_discount = $discount_fee1 * $discount_rw_share;
        $total_rw_with_discount_currency = $discount_fee2 * $discount_rw_share;
        //RW配分総合計(手数料・割引料込)
        $total_rw =  $mid_total_rw - $total_rw_with_discount -  $total_rw_with_discount_currency;
        //AW配分総合計(手数料・割引料込)
        $total_aw =  $mid_total_aw - $total_aw_with_discount - $total_aw_with_discount_currency;
        //小計利益率
        if($ret["amount_price"] == 0)
        {
           $sub_total_profit_rate = 0;
        }
        else
        {
           $sub_total_profit_rate = round(($sub_total_net  /  $ret["amount_price"]) * 100);
        }
        //中計利益率
        if($mid_total_amount_price == 0)
        {
           $mid_total_profit_rate = 0;
        }
        else
        {
           $mid_total_profit_rate = round(($mid_total_net  /  $mid_total_amount_price) * 100);
        }
        //総合計利益率
        if($total_amount_price == 0)
        {
           $total_profit_rate = 0;
        }
        else
        {
            $total_profit_rate = round(($total_net  / $total_amount_price) *100);
        }

     /* 外貨合計計算 */
        //TAX(小計から国内支払商品の合計を除いてからTAXを求める)
        $foreign_tax     = $hawaii_tax_rate  *  ($ret["foreign_amount_price"] - $ret["domestic_pay_amount"]);
        //手数料
        $foreign_service = $service_rate  *  $ret["foreign_amount_price"];
        //代価合計(TAX・手数料込)
        $mid_total_amount_foreign_price = $ret["foreign_amount_price"] + $foreign_tax + $foreign_service;
        //割引率からの割引料
        $foreign_discount = $mid_total_amount_foreign_price *  $discount_rate;
        //割引額からの割引料
        if($discount_exchange_rate == "0" || $discount_exchange_rate =="0.00"){
          $foreign_discount_currency = 0;
        }else{
          $foreign_discount_currency = $discount_fee2 / $discount_exchange_rate;
        }
        //代価合計(TAX・手数料・割引料込)
        $total_amount_foreign_price = $mid_total_amount_foreign_price - $foreign_discount - $foreign_discount_currency;

        //原価総合計
        $total_foreign_cost = $ret["foreign_cost"];
        //利益（代価-原価)
        $sub_total_foreign_net = $ret["foreign_amount_price"] - $total_foreign_cost;
        //利益（代価(TAX・手数料込)-原価)
        $mid_total_foreign_net = $mid_total_amount_foreign_price - $total_foreign_cost;
        //利益（代価(TAX・手数料・割引料込)
        $total_foreign_net = $total_amount_foreign_price - $total_foreign_cost;
        //業者配分小計
        $mid_total_foreign_aw = $ret["foreign_aw_share"];
        //RW配分合計(手数料込)
        $mid_total_foreign_rw  = $foreign_service + $ret["foreign_rw_share"];
        //AW配分割引料
        $total_foreign_aw_with_discount = $foreign_discount * $discount_aw_share;
        $total_foreign_aw_with_discount_currency = $foreign_discount_currency * $discount_aw_share;
        //RW配分割引料
        $total_foreign_rw_with_discount = $foreign_discount * $discount_rw_share;
        $total_foreign_rw_with_discount_currency = $foreign_discount_currency  * $discount_rw_share;
        //RW配分総合計(手数料・割引料込)
        $total_foreign_rw = $mid_total_foreign_rw -  $total_foreign_rw_with_discount - $total_foreign_rw_with_discount_currency;
        //AW配分総合計(手数料・割引料込)
        $total_foreign_aw = $mid_total_foreign_aw -  $total_foreign_aw_with_discount - $total_foreign_aw_with_discount_currency;

        //小計利益率
        if($ret["foreign_amount_price"] == 0)
        {
           $sub_total_foreign_profit_rate = 0;
        }
        else
        {
           $sub_total_foreign_profit_rate = round(($sub_total_foreign_net / $ret["foreign_amount_price"]) * 100);
        }
        //中計利益率
        if($mid_total_amount_foreign_price == 0)
        {
           $mid_total_foreign_profit_rate = 0;
        }
        else
        {
         $mid_total_foreign_profit_rate = round(($mid_total_foreign_net  /  $mid_total_amount_foreign_price) * 100);
        }
        //総合計利益率
        if($total_amount_foreign_price == 0)
        {
           $total_foreign_profit_rate = 0;
        }
        else
        {
             $total_foreign_profit_rate = round(($total_foreign_net /  $total_amount_foreign_price) * 100);
        }

 	  //現地支払合計
 	  $params['vendor_total_cost'] = round($total_foreign_cost,2);
 	  //PD手配料合計
 	  $params['aw_total_cost'] =   round($total_foreign_aw,2); //round($total_foreign_aw,2);
 	  //州税合計
 	  $params['total_tax'] = round(($params['vendor_total_cost'] + $params['aw_total_cost']) * $hawaii_tax_rate,2);
 	  $params['upd_nm'] = $user;
 	  $params['upd_dt'] = date('Y-m-d H:i:s');

 	  $this->id = $remittance_data[0]['RemittanceTrnView']['id'];
 	  if($this->save($params,false)==false){
 	     return array('result'=>false,'message'=>"送金金額計算結果の作成に失敗しました。",'reason'=>$this->getDbo()->error."[".date('Y-m-d H:i:s')."]");
 	  }
  	 return array('result'=>true);
   }

   /**
    * 売上管理メニューのための計算
    * @param unknown $estimate_id
    * @param unknown $user
    * @return multitype:number NULL unknown
    */
  function calculateForSales($estimate_id){

  	App::import("Model", "EstimateTrn");
  	$estimate = new EstimateTrn();

   	App::import("Model", "EstimateDtlTrn");
   	$estimate_dtl = new EstimateDtlTrn();

   	//見積ヘッダを取得
   	$estimate_data = $estimate->findById($estimate_id);
   	//見積明細を取得
   	$estimate_dtl_data = $estimate_dtl->find('all',array('conditions'=>array('estimate_id'=>$estimate_id),'order'=>'no'));

   	$hawaii_tax_rate        = $estimate_data["EstimateTrn"]["hawaii_tax_rate"];
   	$service_rate           = $estimate_data["EstimateTrn"]["service_rate"];
   	$discount_rate          = $estimate_data["EstimateTrn"]["discount_rate"];
   	$discount_fee2          = $estimate_data["EstimateTrn"]["discount"];
   	$discount_exchange_rate = $estimate_data["EstimateTrn"]["discount_exchange_rate"];
   	$discount_aw_share      = $estimate_data["EstimateTrn"]["discount_aw_share"];
   	$discount_rw_share      = $estimate_data["EstimateTrn"]["discount_rw_share"];
   	$remittance_exchange_rate = $estimate_data["EstimateTrn"]["remittance_exchange_rate"];

   	$ret = $this->_calculateSubTotal($estimate_dtl_data,$remittance_exchange_rate);

   	/* 外貨合計計算 */
   	//TAX(小計から国内支払商品の合計を除いてからTAXを求める)
   	$foreign_tax     = $hawaii_tax_rate  *  ($ret["foreign_amount_price"] - $ret["foreign_domestic_pay_amount"]);
   	//手数料
   	$foreign_service = $service_rate  *  $ret["foreign_amount_price"];
   	//代価合計(TAX・手数料込)
   	$mid_total_amount_foreign_price = $ret["foreign_amount_price"] + $foreign_tax + $foreign_service;
   	//割引率からの割引料
   	$foreign_discount = $mid_total_amount_foreign_price *  $discount_rate;
   	//割引額からの割引料
   	if($discount_exchange_rate == "0" || $discount_exchange_rate =="0.00"){
   		$foreign_discount_currency = 0;
   	}else{
   		$foreign_discount_currency = $discount_fee2 / $discount_exchange_rate;
   	}
   	//代価合計(TAX・手数料・割引料込)
   	$total_amount_foreign_price = $mid_total_amount_foreign_price - $foreign_discount - $foreign_discount_currency;

   	//原価総合計
   	$total_foreign_cost = $ret["foreign_cost"];
   	//利益（代価-原価)
   	$sub_total_foreign_net = $ret["foreign_amount_price"] - $total_foreign_cost;
   	//利益（代価(TAX・手数料込)-原価)
   	$mid_total_foreign_net = $mid_total_amount_foreign_price - $total_foreign_cost;
   	//利益（代価(TAX・手数料・割引料込)
   	$total_foreign_net = $total_amount_foreign_price - $total_foreign_cost;
   	//業者配分小計
   	$mid_total_foreign_aw = $ret["foreign_aw_share"];
   	//RW配分合計(手数料込)
   	$mid_total_foreign_rw  = $foreign_service + $ret["foreign_rw_share"];
   	//RW配分合計(手数料含まない) ＋ 国内販売分
   	$mid_total_foreign_rw_without_service  = $ret["foreign_rw_share"] + ($ret["foreign_domestic_pay_amount"]-$ret["foreign_domestic_pay_cost_amount"]);
   	//AW配分割引料
   	$total_foreign_aw_with_discount = $foreign_discount * $discount_aw_share;
   	$total_foreign_aw_with_discount_currency = $foreign_discount_currency * $discount_aw_share;
   	//RW配分割引料
   	$total_foreign_rw_with_discount = $foreign_discount * $discount_rw_share;
   	$total_foreign_rw_with_discount_currency = $foreign_discount_currency  * $discount_rw_share;
   	//RW配分総合計(手数料・割引料込)
   	$total_foreign_rw = $mid_total_foreign_rw -  $total_foreign_rw_with_discount - $total_foreign_rw_with_discount_currency;
   	//RW配分総合計(手数料・割引料含まない)
   	$total_foreign_rw_without_service = $mid_total_foreign_rw_without_service;
   	//AW配分総合計(手数料・割引料込)
   	$total_foreign_aw = $mid_total_foreign_aw -  $total_foreign_aw_with_discount - $total_foreign_aw_with_discount_currency;
   	//AW配分総合計(手数料・割引料含まない)
   	$total_foreign_aw_without_discount = $mid_total_foreign_aw;


   	/* 邦貨合計計算 */
   	//TAX(小計から国内支払商品の合計を除いてからTAXを求める)
   	$tax     =  $hawaii_tax_rate  *  ($ret["amount_price"] - $ret["domestic_pay_amount"]);
   	//手数料
   	$service = $service_rate  *  $ret["amount_price"];
   	//代価合計(TAX・手数料込)
   	$mid_total_amount_price = $ret["amount_price"] + $tax + $service;
   	//割引率からの割引料
   	$discount = $mid_total_amount_price *  $discount_rate;
   	//割引額からの割引料
   	if($discount_exchange_rate == "0" || $discount_exchange_rate =="0.00"){
   		$discount_currency = 0;
   	}else{
   		$discount_currency = $discount_fee2;
   	}
   	//代価合計(TAX・手数料・割引料込)
   	$total_amount_price = $mid_total_amount_price - $discount - $discount_currency;

   	//原価総合計
   	$total_cost = $ret["cost"];
   	//利益（代価-原価)
   	$sub_total_net = $ret["amount_price"] - $total_cost;
   	//利益（代価(TAX・手数料込)-原価)
   	$mid_total_net = $mid_total_amount_price - $total_cost;
   	//利益（代価(TAX・手数料・割引料込)
   	$total_net = $total_amount_price - $total_cost;
   	//業者配分小計
   	$mid_total_aw = $ret["aw_share"];
   	//RW配分合計(手数料込)
   	$mid_total_rw  = $service + $ret["rw_share"];
   	//RW配分合計(手数料含まない) + 国内販売分
   	$mid_total_rw_without_service  = $ret["rw_share"] + ($ret["domestic_pay_amount"] - $ret["domestic_pay_cost_amount"]);
   	//AW配分割引料
   	$total_aw_with_discount = $discount * $discount_aw_share;
   	$total_aw_with_discount_currency = $discount_currency * $discount_aw_share;
   	//RW配分割引料
   	$total_rw_with_discount = $discount * $discount_rw_share;
   	$total_rw_with_discount_currency = $discount_currency  * $discount_rw_share;
   	//RW配分総合計(手数料・割引料込)
   	$total_rw = $mid_total_rw -  $total_rw_with_discount - $total_rw_with_discount_currency;
   	//RW配分総合計(手数料・割引料含まない)
   	$total_rw_without_service = $mid_total_rw_without_service;
   	//AW配分総合計(手数料・割引料込)
   	$total_aw = $mid_total_aw -  $total_aw_with_discount - $total_aw_with_discount_currency;
   	//AW配分総合計(手数料・割引料含まない)
   	$total_aw_without_discount = $mid_total_aw;


   	return array('foreign_total'=>$total_amount_foreign_price,
   		   	     'foreign_service_fee'=>$foreign_service,
   			     'foreign_hi_total'=>$total_foreign_aw_without_discount,
   			     'foreign_rw_total'=>$total_foreign_rw_without_service,
   			     'foreign_hawaii_tax'=>$foreign_tax,
   			     'foreign_remittance_hawaii_tax'=>round(($total_foreign_cost + $total_foreign_aw)*$hawaii_tax_rate,2),
   		   	     'foreign_rw_discount'=>$total_foreign_rw_with_discount + $total_foreign_rw_with_discount_currency,
   			     'foreign_total_discount'=>$foreign_discount + $foreign_discount_currency,
   			     'foreign_rw_sum'=>($foreign_service + $total_foreign_rw_without_service + $foreign_tax)-
   			                       ($total_foreign_rw_with_discount + $total_foreign_rw_with_discount_currency)-
   			                       round(($total_foreign_cost + $total_foreign_aw)*$hawaii_tax_rate,2),
   			     'foreign_rw_total_rate'=>round(((($foreign_service + $total_foreign_rw_without_service + $foreign_tax)-
   			     		                          ($total_foreign_rw_with_discount + $total_foreign_rw_with_discount_currency)-
   			     		                           round(($total_foreign_cost + $total_foreign_aw)*$hawaii_tax_rate,2))/
   			     	                               $total_amount_foreign_price) *100,2),
   			     'foreign_gross_total'=>($foreign_service + $total_foreign_aw_without_discount + $total_foreign_rw_without_service + $foreign_tax)-
   			                            ($foreign_discount + $foreign_discount_currency),
   			     'foreign_gross_total_rate'=>round(((($foreign_service + $total_foreign_aw_without_discount + $total_foreign_rw_without_service + $foreign_tax)-
   			     		                             ($foreign_discount + $foreign_discount_currency))/$total_amount_foreign_price)*100,2),

   	             'total'=>$total_amount_price,
   			     'service_fee'=>$service,
   			     'hi_total'=>$total_aw_without_discount,
   			     'rw_total'=>$total_rw_without_service,
   			     'hawaii_tax'=>$tax,
   		 	     'remittance_hawaii_tax'=>round(($total_cost + $total_aw)*$hawaii_tax_rate),
   			     'rw_discount'=>$total_rw_with_discount + $total_rw_with_discount_currency,
   			     'total_discount'=>$discount + $discount_currency,
   			     'rw_sum'=>($service + $total_rw_without_service + $tax)-($total_rw_with_discount + $total_rw_with_discount_currency)-round(($total_cost + $total_aw)*$hawaii_tax_rate),
   			     'rw_total_rate'=>round(((($service + $total_rw_without_service + $tax)-($total_rw_with_discount + $total_rw_with_discount_currency)-round(($total_cost + $total_aw)*$hawaii_tax_rate))/
   					$total_amount_price) *100,2),
   				 'gross_total'=>($service + $total_aw_without_discount + $total_rw_without_service + $tax)-($discount + $discount_currency),
   				 'gross_total_rate'=>round(((($service + $total_aw_without_discount + $total_rw_without_service + $tax)-($discount + $discount_currency))/$total_amount_price)*100,2),
   			     "credit_domestic_pay_amount" => $ret['credit_domestic_pay_amount'],
   			     "credit_aboard_pay_amount" => $ret['credit_aboard_pay_amount'],
   			     "foreign_credit_domestic_pay_amount" => $ret['foreign_credit_domestic_pay_amount'],
   			     "foreign_credit_aboard_pay_amount" => $ret['foreign_credit_aboard_pay_amount'],
   			     "sales_rate"=>$ret['sales_rate'],
   			     "cost_rate"=>$ret['cost_rate']
   	    );
   }
}
?>