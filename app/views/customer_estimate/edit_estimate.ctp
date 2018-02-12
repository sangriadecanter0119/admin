<?php
  //テーブル項目入れ替えようプラグイン
  echo $html->script('jquery/jquery.tablednd_0_5.js',false);
  //商品カテゴリ取得URL
  $goods_ctg_url = $html->url('feed/goods_ctg_list');
  //商品区分取得URL
  $goods_kbn_url = $html->url('feed/goods_kbn_list');
  //商品取得URL
  $goods_url = $html->url('feed/goods');

  $goods_form_url = $html->url('goodsDetailForm');
  $edit_estimate_url = $html->url('editEstimate');
  $template_form_url = $html->url('templateForm');
  $main_url = $html->url('.');

  //明細行数
  $count = count($data);
  //初期読み込み時に税金が入力マスクで消去されてしまうので保持しておく
  $tax_rate = $data[0]['EstimateDtlTrnView']['hawaii_tax_rate']*100;
 //支払区分
  $aboard_indirect_pay =  PC_INDIRECT_ABOARD_PAY;
  $aboard_direct_pay   =  PC_DIRECT_ABOARD_PAY;
  $aboard_credit_pay   =  PC_CREDIT_ABOARD_PAY;
  $domestic_direct_pay =  PC_DOMESTIC_DIRECT_PAY;
  $domestic_credit_pay =  PC_DOMESTIC_CREDIT_PAY;
  //セット商品区分
  $set_goods_kbn = SET_GOODS;
  $confirm_image_path = $html->webroot("/images/confirm_result.png");
  $error_image_path = $html->webroot("/images/error_result.png");

  //採用見積
  $adopt_flg = $data[0]['EstimateDtlTrnView']['adopt_flg'] == ESTIMATE_ADOPTED ? 1 : 0;

$this->addScript($javascript->codeBlock( <<<JSPROG
  var table_counter=$count;
$(function(){

    var current_line_no;

    //入力マスク
    $("#taxRate").mask("9.999");
    $("#taxRate").val("$tax_rate");
    $(".datepicker" ).mask("9999/99/99");
    $("#tts_rate").mask("999.99");

    /* Enterキー押下時に行追加されるのを防止する */
     $(this).keydown(function(e){
       if(e.keyCode == 13 && $(":focus").attr("id") == undefined){ return false; }
     });
     $("#invoice_table").keydown(function(e){
       if(e.keyCode == 13 && $(":focus").attr("id") == undefined){ return false; }
     });
     $(":text").keydown(function(e){
       if(e.keyCode == 13){  return false;  }
     });

    //日付入力補助のプラグイン
   $( ".datepicker" ).datepicker({
       dateFormat: 'yy/mm/dd',
       showOtherMonths: true,
       selectOtherMonths: true,
       numberOfMonths:3,
       beforeShow : function(){ $('#ui-datepicker-div').css( 'font-size', '90%' );}
   });

    $("input:submit").button();

    //税金が変更されたので再計算
    $("#taxRate").change(function(){recalculate();});
    //割引率が変更されたので再計算
    $("#discountRate").change(function(){recalculate();});
    //割引額が変更されたので再計算
    $("#discount").change(function(){recalculate();});
    //サービス料が変更されたので再計算
    $("#serviceRate").change(function(){recalculate();});
    //割引額為替レートが変更されたので再計算
    $("#discount_exchange_rate").change(function(){recalculate();});
    //AWの割引配分率が変更されたので再計算
    $("#discount_aw_share").change(function(){recalculate();});
    //RWの割引配分率が変更されたので再計算
    $("#discount_rw_share").change(function(){recalculate();});

    //個別商品適用の販売為替レートが変更
    $(".salesExchangeRate").change(function(){
        recalculateLine(getGoodsNo($(this).attr('name')));
    	ChangeSalesExchangeRateColorIfDifferent();
    });
    //個別商品適用のコスト為替レートが変更
    $(".costExchangeRate").change(function(){
        recalculateLine(getGoodsNo($(this).attr('name')));
    });
     //AWレートが変更
    $(".awRate").change(function(){
        recalculateRate(getGoodsNo($(this).attr('name')));
    });
    //RWレートが変更
    $(".rwRate").change(function(){
        recalculateRate(getGoodsNo($(this).attr('name')));
    });

    /*
    *  shareレート変更再計算
    */
   function recalculateRate(current_line_no)
   {
      /* 邦貨 */
	  var aw_share = Common.removeComma($("#net" + current_line_no).text()) * ($("#aw_rate" + current_line_no).val() /100);
	  var rw_share = Common.removeComma($("#net" + current_line_no).text()) * ($("#rw_rate" + current_line_no).val() /100);
	  $("#aw_share" + current_line_no).text(Common.addDollarComma(aw_share));
	  $("#rw_share" + current_line_no).text(Common.addDollarComma(rw_share));

	  /* 外貨 */
	  var foreign_aw_share = Common.removeDollarComma($("#foreign_net" + current_line_no).text()) * ($("#aw_rate" + current_line_no).val() /100);
	  var foreign_rw_share = Common.removeDollarComma($("#foreign_net" + current_line_no).text()) * ($("#rw_rate" + current_line_no).val() /100);
	  $("#foreign_aw_share" + current_line_no).text(Common.addDollarComma(foreign_aw_share));
	  $("#foreign_rw_share" + current_line_no).text(Common.addDollarComma(foreign_rw_share));

       recalculate();
   }

    //明細1行の再計算
    function recalculateLine(current_line_no)
    {
       //現在の個別表示販売為替レート
       var sales_rate = $("#sales_exchange_rate" + current_line_no).val();
       //現在の個別表示コスト為替レート
       var cost_rate = $("#cost_exchange_rate" + current_line_no).val();
	   //現在の数量
       var num = $("[name='data[EstimateDtlTrn][" + current_line_no + "][num]']").val();

       //通貨区分が外貨の場合は邦貨の金額を再計算
       if($("#currency_kbn" + current_line_no).val() == 0)
       {
          //単価(邦貨)
	      var unit_price = Common.removeDollarComma($("#foreign_unit_price"+ current_line_no).val()) * sales_rate;
	      //全価(邦貨)
	      var amount_price = unit_price * num;
	      //原価(邦貨)
	      var unit_cost = Common.removeDollarComma($("#foreign_unit_cost"+ current_line_no).val()) * cost_rate;
	      //全原価(邦貨)
	      var amount_cost = unit_cost * num;
	      //利益(邦貨)
	      var net = amount_price - amount_cost;
	      //aw share(邦貨)
	      var aw_share = net * ($("#aw_rate" + current_line_no).val() /100);
	      //rw share(邦貨)
	      var rw_share = net * ($("#rw_rate" + current_line_no).val() /100);

	      $("#unit_price" + current_line_no).val(Common.addYenComma(unit_price));
	      $("#amount_price" + current_line_no).text(Common.addYenComma(amount_price));
	      $("#unit_cost" + current_line_no).val(Common.addYenComma(unit_cost));
	      $("#cost" + current_line_no).text(Common.addYenComma(amount_cost));
	      $("#net" + current_line_no).text(Common.addYenComma(net));
	      if(amount_price == 0)
	      {
	        $("#profit_rate" + current_line_no).text(0);
	      }
	      else
	      {
	        $("#profit_rate" + current_line_no).text(Math.round(net / amount_price * 100)+ "%");
	      }

	      $("#aw_share" + current_line_no).text(Common.addYenComma(aw_share));
	      $("#rw_share" + current_line_no).text(Common.addYenComma(rw_share));
       }
       //通貨区分が邦貨の場合は外貨の金額を再計算
       else
       {
          //単価(外貨)
	      var unit_price = Common.removeComma($("#unit_price" + current_line_no).val()) / sales_rate;
	      //全価(外貨)
	      var amount_price = unit_price * num;
	      //原価(外貨)
	      var unit_cost = Common.removeComma($("#unit_cost"+ current_line_no).val()) / cost_rate;
	      //全原価(邦貨)
	      var amount_cost = unit_cost * num;
	      //利益(外貨)
	      var net = amount_price -amount_cost;
	      //aw share(外貨)
	      var aw_share = net * ($("#aw_rate" + current_line_no).val() /100);
	      //rw share(外貨)
	      var rw_share = net * ($("#rw_rate" + current_line_no).val() /100);

	      $("#foreign_unit_price" + current_line_no).val(Common.addDollarComma(unit_price));
	      $("#foreign_amount_price" + current_line_no).text(Common.addDollarComma(amount_price));
	      $("#foreign_unit_cost" + current_line_no).val(Common.addDollarComma(unit_cost));
	      $("#foreign_cost" + current_line_no).text(Common.addDollarComma(amount_cost));
	      $("#foreign_net" + current_line_no).text(Common.addDollarComma(net));
	      if(amount_price == 0)
	      {
	        $("#profit_rate" + current_line_no).text(0);
	      }
	      else
	      {
	        $("#foreign_profit_rate" + current_line_no).text(Math.round(net / amount_price * 100)+ "%");
	      }

	      $("#foreign_aw_share" + current_line_no).text(Common.addDollarComma(aw_share));
	      $("#foreign_rw_share" + current_line_no).text(Common.addDollarComma(rw_share));
       }
       recalculate();
    }

    //商品分類が選択されたら属する商品区分リストを表示する
    $(".goods_ctg").change(function() {
          updateGoodsCtg($(this));
	});

	function updateGoodsCtg(e){

	   $("#goods_ctg_indicator").css("display","inline");
	      current_line_no = getGoodsKbnNo($(e).attr('name'));

		  $.get("$goods_kbn_url/" + $(e).val(), function(data) {

		  $("[name='goodsKbn_" + current_line_no + "']").html(data);

		   //他の値の初期化
		   $("[name='data[EstimateDtlTrn][" + current_line_no +"][sales_goods_nm]']").val("");
		   $("[name='data[EstimateDtlTrn][" + current_line_no +"][num]']").val(1);

		   $("#vendor_nm" + current_line_no).text("");

		   $("#unit_price" + current_line_no).val("0");
	       $("#amount_price" + current_line_no).text("0");
	       $("#unit_cost" + current_line_no).val("0");
	       $("#cost" + current_line_no).text("0");
	       $("#net" + current_line_no).text("0");
	       $("#profit_rate" + current_line_no).text("0");
	       $("#aw_share" + current_line_no).text("0");
	       $("#rw_share" + current_line_no).text("0");

	       $("#foreign_unit_price" + current_line_no).val("0");
	       $("#foreign_amount_price" + current_line_no).text("0");
	       $("#foreign_unit_cost" + current_line_no).val("0");
	       $("#foreign_cost" + current_line_no).text("0");
	       $("#foreign_net" + current_line_no).text("0");
	       $("#foreign_profit_rate" + current_line_no).text("0");
	       $("#foreign_aw_share" + current_line_no).text("0");
	       $("#foreign_rw_share" + current_line_no).text("0");

	       $("#aw_rate" + current_line_no).val("0");
		   $("#rw_rate" + current_line_no).val("0");

		   recalculate();
		   $("#goods_ctg_indicator").css("display","none");
		});
	}

    //商品区分が選択されたら属する商品リストを表示する
	$(".goods_kbn").change(function() {
	      updateGoodsKbn($(this));
	});

	function updateGoodsKbn(e)
	{
	   $(this).simpleLoading('show');

	   //商品区分selectタグのサイズ調整
	   $("#hidden").text($(e).children(':selected').text());
       $(e).css("width",$("#hidden").width()+7);

	   //現在の処理中の行番号を保存
	   current_line_no = getGoodsKbnNo($(e).attr('name'));

	   /* 商品リストフォームデータの取得 */
	   $.get("$goods_form_url"+"/" +  $("#goodsCtg_"+current_line_no).val() + "/" +$("#goodsKbn_"+current_line_no).val() + "/" + current_line_no,function(data){

	       $(this).simpleLoading('hide');
	       $("body").append(data);
	   });
	}

	//行追加
	$(".add_row").click(function(){
	   //ヘッダ分のTRがあるのでカウンター+1になる
	   var cloned = $(".list tr:nth-child("+(table_counter+1)+")");
	   $(cloned).clone(true).insertAfter(cloned);
	   table_counter++;

	   var new_row = $(".list tr:nth-child("+(table_counter+1)+")");
	   var td_delete = $("td:nth-child(1)" ,new_row);
	   var td_toiawase = $("td:nth-child(2)" ,new_row);
	   var td_money_receive = "";
	   var td_category = $("td:nth-child(3)" ,new_row);
	   var td_kbn = $("td:nth-child(4)" ,new_row);
	   var td_goods = $("td:nth-child(5)" ,new_row);
	   var td_vendor = $("td:nth-child(6)" ,new_row);
	   var td_qty = $("td:nth-child(7)" ,new_row);
       var td_total_price = $("td:nth-child(8)",new_row);
	   var td_unit = $("td:nth-child(9)" ,new_row);
	   var td_cost = $("td:nth-child(10)" ,new_row);

	   var td_total_cost = $("td:nth-child(11)",new_row);
	   var td_profit = $("td:nth-child(12)",new_row);
	   var td_profit_rate = $("td:nth-child(13)",new_row);
	   var td_aw_share = $("td:nth-child(14)",new_row);
	   var td_rw_share = $("td:nth-child(15)",new_row);
	   var td_f_total_price = $("td:nth-child(16)",new_row);
	   var td_f_unit = $("td:nth-child(17)",new_row);
	   var td_f_cost = $("td:nth-child(18)",new_row);
	   var td_f_total_cost = $("td:nth-child(19)",new_row);
	   var td_f_profit = $("td:nth-child(20)",new_row);
	   var td_f_profit_rate = $("td:nth-child(21)",new_row);
	   var td_f_aw_share = $("td:nth-child(22)",new_row);
	   var td_f_rw_share = $("td:nth-child(23)",new_row);
	   var td_aw_rate = $("td:nth-child(24)",new_row);
	   var td_rw_rate = $("td:nth-child(25)",new_row);
	   var td_sales_ex_rate = $("td:nth-child(26)",new_row);
	   var td_cost_ex_rate = $("td:nth-child(27)",new_row);
	   var td_payment_kbn = $("td:nth-child(28)",new_row);

	   if("$adopt_flg" == 1){
	      new_row = $(".list tr:nth-child("+(table_counter+1)+")");
	      td_delete = $("td:nth-child(1)" ,new_row);
	      td_toiawase = $("td:nth-child(2)" ,new_row);
	      td_money_receive = $("td:nth-child(3)",new_row);
	      td_category = $("td:nth-child(4)" ,new_row);
	      td_kbn = $("td:nth-child(5)" ,new_row);
	      td_goods = $("td:nth-child(6)" ,new_row);
	      td_vendor = $("td:nth-child(7)" ,new_row);
	      td_qty = $("td:nth-child(8)" ,new_row);
	      td_total_price = $("td:nth-child(9)",new_row);
		  td_unit = $("td:nth-child(10)" ,new_row);
	      td_cost = $("td:nth-child(11)" ,new_row);

	      td_total_cost = $("td:nth-child(12)",new_row);
	      td_profit = $("td:nth-child(13)",new_row);
	      td_profit_rate = $("td:nth-child(14)",new_row);
	      td_aw_share = $("td:nth-child(15)",new_row);
	      td_rw_share = $("td:nth-child(16)",new_row);
	      td_f_total_price = $("td:nth-child(17)",new_row);
		  td_f_unit = $("td:nth-child(18)",new_row);
	      td_f_cost = $("td:nth-child(19)",new_row);
	      td_f_total_cost = $("td:nth-child(20)",new_row);
	      td_f_profit = $("td:nth-child(21)",new_row);
	      td_f_profit_rate = $("td:nth-child(22)",new_row);
	      td_f_aw_share = $("td:nth-child(23)",new_row);
	      td_f_rw_share = $("td:nth-child(24)",new_row);
	      td_aw_rate = $("td:nth-child(25)",new_row);
	      td_rw_rate = $("td:nth-child(26)",new_row);
	      td_sales_ex_rate = $("td:nth-child(27)",new_row);
	      td_cost_ex_rate = $("td:nth-child(28)",new_row);
	      td_payment_kbn = $("td:nth-child(29)",new_row);
       }

	   //新規作成した行のIDを設定
	   $(new_row).attr("id","row"+table_counter);
	   //削除ボタンの属性[name]を設定
	   $("a",td_delete).attr("name","row"+table_counter);
	   //明細IDの属性を設定
	   $("input",td_delete).attr("id","estimate_dtl_id_"+table_counter);
	   $("input",td_delete).attr("name","data[EstimateDtlTrn][" + table_counter + "][id]");
	     //明細IDを初期化
	   $("#estimate_dtl_id_"+table_counter).val("");
	   //新規追加の行は問合せボタンは無効にする
	   $(td_toiawase).html("<span class='disabled'>問</span>");

  	   //入金ボタンの属性[name]を設定
		if("$adopt_flg" == 1){
	      $("input",td_money_receive).attr("name","data[EstimateDtlTrn][" + table_counter + "][money_received_flg]");
	      $("input",td_money_receive).val(1);
	      $("input",td_money_receive).attr("checked",false);
        }

	   //商品分類のタグを設定
	   $(td_category).html( "<select class='goods_ctg' id='goodsCtg_" + table_counter + "' name='goodsCtg_" + table_counter + "'></select>");
	   $.get("$goods_ctg_url/", function(data) {
	       $("select",td_category).html(data);
	   });
	   //動的にタグを作成したらイベントを追加しないと動作しない
	    $("select",td_category).bind("change",function(){
		                                             updateGoodsCtg($("select",td_category));
		                                             });

	   //商品区分のタグを設定(selectタグごと切り替えないと後でoptionを追加しようとしても上手く追加できない)
	   $(td_kbn).html( "<select class='goods_kbn' id='goodsKbn_" + table_counter + "' name='goodsKbn_" + table_counter + "'><option></option></select>");
	      //動的にタグを作成したらイベントを追加しないと動作しない
	     $("select",td_kbn).bind("change",function(){
		                                             updateGoodsKbn($("select",td_kbn));
		                                             });

       //商品の属性を設定
	    $("input:nth-child(1)",td_goods).attr("name", "data[EstimateDtlTrn][" + table_counter + "][goods_id]");
	    $("input:nth-child(1)",td_goods).attr("id", "goods_id" + table_counter);
        $("textarea",td_goods).attr("name", "data[EstimateDtlTrn][" + table_counter + "][sales_goods_nm]");
        $("textarea",td_goods).attr("id", "sales_goods_nm" + table_counter);
        $("textarea",td_goods).attr("rows","1");
        $("textarea",td_goods).val("");

        $("textarea",td_goods).bind("keyup",function(){
                                                       resizeTextarea($(this));
                                                      });
          //クローンの行からクラスを引き継いでしまっている場合は取り除く
	      if($("textarea",td_goods).hasClass("changedField"))
	      {
	        $("textarea",td_goods).removeClass("changedField");
	        $("textarea",td_goods).addClass("focusField");
	      }
        $("input:nth-child(3)",td_goods).attr("id", "original_goods_nm" + table_counter);
        $("input:nth-child(3)",td_goods).val("");

       //ベンダー名
       $(td_vendor).attr('id','vendor_nm' + table_counter);

	   //数量の属性[name]を設定
	   $("select",td_qty).attr("name","data[EstimateDtlTrn][" + table_counter + "][num]");
	   $("select",td_qty).val(1);

	   //単価の属性[name]を設定
	   $("input:nth-child(1)",td_unit).attr("name","data[EstimateDtlTrn][" + table_counter + "][sales_price]");
	   $("input:nth-child(1)",td_unit).attr("id"  ,"unit_price" + table_counter);
	   $("input:nth-child(1)",td_unit).val("0");
	       //クローンの行からクラスを引き継いでしまっている場合は取り除く
	      if($("input:nth-child(1)",td_unit).hasClass("changedField"))
	      {
	        $("input:nth-child(1)",td_unit).removeClass("changedField");
	        $("input:nth-child(1)",td_unit).addClass("focusField");
	      }
	   //単価オリジナルの属性[id]を設定
	   $("input:nth-child(2)",td_unit).attr("id","original_unit_price" + table_counter);
	   $("input:nth-child(2)",td_unit).val("0");
	    //編集不可になっている場合があるので解除する
	    $("#unit_price" + table_counter).removeClass("inputdisable");
	    $("#unit_price" + table_counter).attr("disabled",false);
	    $("#unit_price" + table_counter).attr("readonly",false);
	   //原価の属性[id]を設定
	   $("input:nth-child(1)",td_cost).attr("name","data[EstimateDtlTrn][" + table_counter + "][sales_cost]");
	   $("input:nth-child(1)",td_cost).attr("id"  ,"unit_cost" + table_counter);
	   $("input:nth-child(1)",td_cost).val("0");
	     //クローンの行からクラスを引き継いでしまっている場合は取り除く
	     if($("input:nth-child(1)",td_cost).hasClass("changedField"))
	     {
	        $("input:nth-child(1)",td_cost).removeClass("changedField");
	        $("input:nth-child(1)",td_cost).addClass("focusField");
	     }
	   //原価オリジナルの属性[id]を設定
	   $("input:nth-child(2)",td_cost).attr("id","original_unit_cost" + table_counter);
	   $("input:nth-child(2)",td_cost).val("0");
	     //編集不可になっている場合があるので解除する
	    $("#unit_cost" + table_counter).removeClass("inputdisable");
	    $("#unit_cost" + table_counter).attr("disabled",false);
	    $("#unit_cost" + table_counter).attr("readonly",false);
	   //総全価の属性[id]を設定
	   $(td_total_price).attr("id","amount_price" + table_counter);
	   $(td_total_price).text("0");
	   //総原価の属性[id]を設定
	   $(td_total_cost).attr("id","cost" + table_counter);
	   $(td_total_cost).text("0");
	   //利益の属性[id]を設定
	   $(td_profit).attr("id","net" + table_counter);
	   $(td_profit).text("0");
	   //利益率の属性[id]を設定
	   $(td_profit_rate).attr("id","profit_rate" + table_counter);
	   $(td_profit_rate).text("0");
	   //awシェアの属性[id]を設定
	   $(td_aw_share).attr("id","aw_share" + table_counter);
	   $(td_aw_share).text("0");
	   //rwシェアの属性[id]を設定
	   $(td_rw_share).attr("id","rw_share" + table_counter);
	   $(td_rw_share).text("0");

	   //単価(外貨)の属性[name]を設定
	   $("input:nth-child(1)",td_f_unit).attr("name","data[EstimateDtlTrn][" + table_counter + "][foreign_sales_price]");
	   $("input:nth-child(1)",td_f_unit).attr("id"  ,"foreign_unit_price" + table_counter);
	   $("input:nth-child(1)",td_f_unit).val("0");
	     //クローンの行からクラスを引き継いでしまっている場合は取り除く
	     if($("input:nth-child(1)",td_f_unit).hasClass("changedField"))
	     {
	        $("input:nth-child(1)",td_f_unit).removeClass("changedField");
	        $("input:nth-child(1)",td_f_unit).addClass("focusField");
	     }
	   //単価(外貨)オリジナルの属性[id]を設定
	   $("input:nth-child(2)",td_f_unit).attr("id","foreign_original_unit_price" + table_counter);
	   $("input:nth-child(2)",td_f_unit).val("0");
	     //編集不可になっている場合があるので解除する
	    $("#foreign_unit_price" + table_counter).removeClass("inputdisable");
	    $("#foreign_unit_price" + table_counter).attr("disabled",false);
	    $("#foreign_unit_price" + table_counter).attr("readonly",false);
       //原価(外貨)の属性[id]を設定
	   $("input:nth-child(1)",td_f_cost).attr("name","data[EstimateDtlTrn][" + table_counter + "][foreign_sales_cost]");
	   $("input:nth-child(1)",td_f_cost).attr("id"  ,"foreign_unit_cost" + table_counter);
	   $("input:nth-child(1)",td_f_cost).val("0");
	      //クローンの行からクラスを引き継いでしまっている場合は取り除く
	     if($("input:nth-child(1)",td_f_cost).hasClass("changedField"))
	     {
	        $("input:nth-child(1)",td_f_cost).removeClass("changedField");
	        $("input:nth-child(1)",td_f_cost).addClass("focusField");
	     }
	   //原価(外貨)オリジナルの属性[id]を設定
	   $("input:nth-child(2)",td_f_cost).attr("id","foreign_original_unit_cost" + table_counter);
	   $("input:nth-child(2)",td_f_cost).val("0");
	     //編集不可になっている場合があるので解除する
	    $("#foreign_unit_cost" + table_counter).removeClass("inputdisable");
	    $("#foreign_unit_cost" + table_counter).attr("disabled",false);
	    $("#foreign_unit_cost" + table_counter).attr("readonly",false);
	   //総代価(外貨)の属性[name]を設定
	   $(td_f_total_price).attr("id","foreign_amount_price" + table_counter);
	   $(td_f_total_price).text("0");
	   //総原価(外貨)の属性[id]を設定
	   $(td_f_total_cost).attr("id","foreign_cost" + table_counter);
	   $(td_f_total_cost).text("0");
	   //利益(外貨)の属性[id]を設定
	   $(td_f_profit).attr("id","foreign_net" + table_counter);
	   $(td_f_profit).text("0");
	   //利益率(外貨)の属性[id]を設定
	   $(td_f_profit_rate).attr("id","foreign_profit_rate" + table_counter);
	   $(td_f_profit_rate).text("0");
	   //awシェア(外貨)の属性[id]を設定
	   $(td_f_aw_share).attr("id","foreign_aw_share" + table_counter);
	   $(td_f_aw_share).text("0");
	   //rwシェア(外貨)の属性[id]を設定
	   $(td_f_rw_share).attr("id","foreign_rw_share" + table_counter);
	   $(td_f_rw_share).text("0");
	   //awレートの属性[id]を設定
	   $("input:nth-child(1)",td_aw_rate).attr("name","data[EstimateDtlTrn][" + table_counter + "][aw_share]");
	   $("input:nth-child(1)",td_aw_rate).attr("id"  ,"aw_rate" + table_counter);
	   $("input:nth-child(1)",td_aw_rate).val("0");
	   //rwレートの属性[id]を設定
	   $("input:nth-child(1)",td_rw_rate).attr("name","data[EstimateDtlTrn][" + table_counter + "][rw_share]");
	   $("input:nth-child(1)",td_rw_rate).attr("id"  ,"rw_rate" + table_counter);
	   $("input:nth-child(1)",td_rw_rate).val("0");

	   //販売為替レートの属性[name]を設定
	   $("input",td_sales_ex_rate).attr("name","data[EstimateDtlTrn][" + table_counter + "][sales_exchange_rate]");
	   $("input",td_sales_ex_rate).attr("id","sales_exchange_rate" + table_counter);
	  // $("input",td_sales_ex_rate).val($("#exchangeRate").text());
	   $("input",td_sales_ex_rate).val("");

	   //通貨区分の[id]と[name]設定
	   $("input:nth-child(2)",td_sales_ex_rate).attr("id"  ,"currency_kbn" + table_counter);
	   $("input:nth-child(2)",td_sales_ex_rate).attr("name","data[EstimateDtlTrn][" + table_counter + "][currency_kbn]");
	    //コスト為替レートの属性[name]を設定
	   $("input",td_cost_ex_rate).attr("name","data[EstimateDtlTrn][" + table_counter + "][cost_exchange_rate]");
	   $("input",td_cost_ex_rate).attr("id","cost_exchange_rate" + table_counter);
	   $("input",td_cost_ex_rate).val($("#costExchangeRate").text());

	   //支払区分の属性[name]を設定
	   $("select",td_payment_kbn).attr("id","payment_kbn"+table_counter);
	   $("select",td_payment_kbn).attr("name","data[EstimateDtlTrn][" + table_counter + "][payment_kbn_id]");
	   return false;
	});

    //行削除
    $(".delete").click(function(){
	    //一番初めの行は常に残す
        if(table_counter > 1){

        //name属性が"row"+番号になっているので"row"を除いた文字列を取得する
        var removed_line_no = $(this).attr("name").substr(3);

	       $("#"+$(this).attr("name")).remove();
	       table_counter--;
	       ReorderTable(removed_line_no);
	    }
	});

	//テーブル行の入れ替え
   $('#invoice_table').tableDnD({
        onDrop: function(table, row) {
            ReorderTable();
        }
    });

    //テーブル行の再設定
    function ReorderTable(removed_line_no)
    {

      //各行のIDを再設定する
      for(i=removed_line_no;i <= table_counter;i++){

       var td = $("#invoice_table tr:nth-child("+(parseInt(i)+parseInt(1))+")");

	   var td_delete = $("td:nth-child(1)" ,td);
	   var td_toiawase = $("td:nth-child(2)" ,td);
	   var td_money_receive = "";
	   var td_category = $("td:nth-child(3)" ,td);
	   var td_kbn = $("td:nth-child(4)" ,td);
	   var td_goods = $("td:nth-child(5)" ,td);
	   var td_vendor = $("td:nth-child(6)" ,td);
	   var td_qty = $("td:nth-child(7)" ,td);
	   var td_total_price = $("td:nth-child(8)",td);
       var td_unit = $("td:nth-child(9)" ,td);
	   var td_cost = $("td:nth-child(10)" ,td);

	   var td_total_cost = $("td:nth-child(11)",td);
	   var td_profit = $("td:nth-child(12)",td);
	   var td_profit_rate = $("td:nth-child(13)",td);
	   var td_aw_share = $("td:nth-child(14)",td);
	   var td_rw_share = $("td:nth-child(15)",td);
       var td_f_total_price = $("td:nth-child(16)",td);
	   var td_f_unit = $("td:nth-child(17)",td);
	   var td_f_cost = $("td:nth-child(18)",td);
	   var td_f_total_cost = $("td:nth-child(19)",td);
	   var td_f_profit = $("td:nth-child(20)",td);
	   var td_f_profit_rate = $("td:nth-child(21)",td);
	   var td_f_aw_share = $("td:nth-child(22)",td);
	   var td_f_rw_share = $("td:nth-child(23)",td);
	   var td_aw_rate = $("td:nth-child(24)",td);
	   var td_rw_rate = $("td:nth-child(25)",td);
	   var td_sales_ex_rate = $("td:nth-child(26)",td);
	   var td_cost_ex_rate = $("td:nth-child(27)",td);
	   var td_payment_kbn = $("td:nth-child(28)",td);

      if("$adopt_flg" == 1){
	   td_delete = $("td:nth-child(1)" ,td);
	   td_toiawase = $("td:nth-child(2)" ,td);
	   td_money_receive = $("td:nth-child(3)",td);
	   td_category = $("td:nth-child(4)" ,td);
	   td_kbn = $("td:nth-child(5)" ,td);
	   td_goods = $("td:nth-child(6)" ,td);
	   td_vendor = $("td:nth-child(7)" ,td);
	   td_qty = $("td:nth-child(8)" ,td);
	   td_total_price = $("td:nth-child(9)",td);
	   td_unit = $("td:nth-child(10)" ,td);
	   td_cost = $("td:nth-child(11)" ,td);

	   td_total_cost = $("td:nth-child(12)",td);
	   td_profit = $("td:nth-child(13)",td);
	   td_profit_rate = $("td:nth-child(14)",td);
	   td_aw_share = $("td:nth-child(15)",td);
	   td_rw_share = $("td:nth-child(16)",td);
	   td_f_total_price = $("td:nth-child(17)",td);
	   td_f_unit = $("td:nth-child(18)",td);
	   td_f_cost = $("td:nth-child(19)",td);
	   td_f_total_cost = $("td:nth-child(20)",td);
	   td_f_profit = $("td:nth-child(21)",td);
	   td_f_profit_rate = $("td:nth-child(22)",td);
	   td_f_aw_share = $("td:nth-child(23)",td);
	   td_f_rw_share = $("td:nth-child(24)",td);
	   td_aw_rate = $("td:nth-child(25)",td);
	   td_rw_rate = $("td:nth-child(26)",td);
	   td_sales_ex_rate = $("td:nth-child(27)",td);
	   td_cost_ex_rate = $("td:nth-child(28)",td);
	   td_payment_kbn = $("td:nth-child(29)",td);
      }

	       //TRのID設定
	       $(td).attr("id","row"+i);

	       //削除ボタンの属性[name]を設定
	       $("a",td_delete).attr("name","row"+i);
	       //明細IDの属性を設定
	       $("input",td_delete).attr({id  :'estimate_dtl_id_'+i,
	                            name:'data[EstimateDtlTrn][' + i + '][id]'
	                           });

	       //問合せボタンの属性[name]を設定
	       $("a",td_toiawase).attr("name","row"+i);

		   //入金ボタンの属性[name]を設定
		   if("$adopt_flg" == 1){
	         $("input",td_money_receive).attr("name","data[EstimateDtlTrn][" + i + "][money_received_flg]");
           }

	       //商品分類の属性[name]を設定
	       $("select",td_category).attr({name:'goodsCtg_' + i,
	                             id  :'goodsCtg_' + i
	                            });
	       //商品区分の属性[name]を設定
	       $("select",td_kbn).attr({name :'goodsKbn_' + i,
	                             id   :'goodsKbn_' + i
	                            });
	        //商品の属性[name]を設定
	       $("input:nth-child(1)" ,td_goods).attr("name","data[EstimateDtlTrn][" + i + "][goods_id]");
	       $("input:nth-child(1)" ,td_goods).attr("id","goods_id" + i);
	       $("textarea" ,td_goods).attr("name","data[EstimateDtlTrn][" + i + "][sales_goods_nm]");
	       $("textarea" ,td_goods).attr("id","sales_goods_nm" + i);
           $("input:nth-child(3)" ,td_goods).attr("id","original_goods_nm" + i);

           //ベンダー名
           $(td_vendor).attr('id','vendor_nm' + i);

           //数量の属性[name]を設定
	       $("select",td_qty).attr("name","data[EstimateDtlTrn][" + i + "][num]");

	       //単価の属性[name]を設定
	       $("input:nth-child(1)",td_unit).attr({name :'data[EstimateDtlTrn][' + i + '][sales_price]',
	                                          id   :'unit_price' + i
	                                          });
	       //単価オリジナルの属性[id]を設定
           $("input:nth-child(2)",td_unit).attr("id" ,"original_unit_price" + i);
	       //原価の属性[id]を設定
	       $("input:nth-child(1)",td_cost).attr({name : 'data[EstimateDtlTrn][' + i + '][sales_cost]',
	                                          id   : 'unit_cost' + i
	                                         });
	       //原価オリジナルの属性[id]を設定
           $("input:nth-child(2)",td_cost).attr("id" ,"original_unit_cost" + i);
	       //総代価の属性[id]を設定
	       $(td_total_price).attr("id","amount_price" + i);
	       //総原価の属性[id]を設定
	       $(td_total_cost).attr("id","cost" + i);
	       //利益の属性[id]を設定
	       $(td_profit).attr("id","net" + i);
	       //利益率の属性[id]を設定
	       $(td_profit_rate).attr("id","profit_rate" + i);
	       //awシェアの属性[id]を設定
	       $(td_aw_share).attr("id","aw_share" + i);
	       //rwシェアの属性[id]を設定
	       $(td_rw_share).attr("id","rw_share" + i);

	       //単価(外貨)の属性[name]を設定
	       $("input:nth-child(1)",td_f_unit).attr({name : 'data[EstimateDtlTrn][' + i + '][foreign_sales_price]',
	                                          id   : 'foreign_unit_price' + i
	                                          });
	       //単価(外貨)オリジナルの属性[id]を設定
           $("input:nth-child(2)",td_f_unit).attr("id" ,"foreign_original_unit_price" + i);
           //原価(外貨)の属性[id]を設定
	       $("input:nth-child(1)",td_f_cost).attr({name : 'data[EstimateDtlTrn][' + i + '][foreign_sales_cost]',
	                                          id   : 'foreign_unit_cost' + i
	                                         });
	       //原価(外貨)オリジナルの属性[id]を設定
           $("input:nth-child(2)",td_f_cost).attr("id" ,"foreign_original_unit_cost" + i);
	       //総代価(外貨)の属性[id]を設定
	       $(td_f_total_price).attr("id","foreign_amount_price" + i);
	       //総原価(外貨)の属性[id]を設定
	       $(td_f_total_cost).attr("id","foreign_cost" + i);
	       //利益(外貨)の属性[id]を設定
	       $(td_f_profit).attr("id","foreign_net" + i);
	       //利益率(外貨)の属性[id]を設定
	       $(td_f_profit_rate).attr("id","foreign_profit_rate" + i);
	       //awシェア(外貨)の属性[id]を設定
	       $(td_f_aw_share).attr("id","foreign_aw_share" + i);
	       //rwシェア(外貨)の属性[id]を設定
	       $(td_f_rw_share).attr("id","foreign_rw_share" + i);
	       //awレートの属性[id]を設定
	       $("input:nth-child(1)",td_aw_rate).attr({name : 'data[EstimateDtlTrn][' + i + '][aw_share]',
	                                          id   : 'aw_rate' + i
	                                          });
	       //rwレートの属性[id]を設定
	       $("input:nth-child(1)",td_rw_rate).attr({name : 'data[EstimateDtlTrn][' + i + '][rw_share]',
	                                          id   : 'rw_rate' + i
	                                          });

	       //販売為替レートの属性[name]を設定
	       $("input",td_sales_ex_rate).attr({name: 'data[EstimateDtlTrn][' + i + '][sales_exchange_rate]',
	                            id  : 'sales_exchange_rate' + i
	                           });
	       //通貨区分の設定
	       $("input:nth-child(2)",td_sales_ex_rate).attr({name:'data[EstimateDtlTrn][' + i + '][currency_kbn]',
	                                         id  :'currency_kbn' + i
	                                         });
	       //コスト為替レートの属性[name]を設定
	       $("input",td_cost_ex_rate).attr({name:'data[EstimateDtlTrn][' + i + '][cost_exchange_rate]',
	                            id  :'cost_exchange_rate' + i
	                            });

	       //支払区分の属性[name]を設定
	       $("select",td_payment_kbn).attr("id","payment_kbn" + i);
	       $("select",td_payment_kbn).attr("name","data[EstimateDtlTrn][" + i + "][payment_kbn_id]");

	    }
	       recalculate();
    }

	//数量変更
	$(".num").change(function()
	{
	  current_line_no = getGoodsNo($(this).attr('name'));

	  //単価(邦貨)
	  var sales_price = Common.removeComma($("[name='data[EstimateDtlTrn][" + current_line_no + "][sales_price]']").val());
	  //全価(邦貨)
	  var amount_price = sales_price * $(this).val();
	  //原価(邦貨)
	  var cost = Common.removeComma($("#unit_cost"+ current_line_no).val()) * $(this).val();
	  //利益(邦貨)
	  var net = amount_price -cost;
	  //aw share(邦貨)
	  var aw_share = net * ($("#aw_rate" + current_line_no).val() /100);
	  //rw share(邦貨)
	  var rw_share = net * ($("#rw_rate" + current_line_no).val() /100);

	  $("#amount_price" + current_line_no).text(Common.addYenComma(amount_price));
	  $("#cost" + current_line_no).text(Common.addYenComma(cost));
	  $("#net" + current_line_no).text(Common.addYenComma(net));
	  $("#profit_rate" + current_line_no).text(Math.round(net / amount_price *100)+ "%");
	  $("#aw_share" + current_line_no).text(Common.addYenComma(aw_share));
	  $("#rw_share" + current_line_no).text(Common.addYenComma(rw_share));

	  //単価(外貨)
	  sales_price = Common.removeDollarComma($("[name='data[EstimateDtlTrn][" + current_line_no + "][foreign_sales_price]']").val());
	  //全価(外貨)
	  amount_price = sales_price * $(this).val();
	  //原価(外貨)
	  cost = Common.removeDollarComma($("#foreign_unit_cost"+ current_line_no).val() * $(this).val());
	  //利益(外貨)
	  net = amount_price -cost;
	  //aw share(外貨)
	  aw_share = net * ($("#aw_rate" + current_line_no).val() /100);
	  //rw share(外貨)
	  rw_share = net * ($("#rw_rate" + current_line_no).val() /100);

	  $("#foreign_amount_price" + current_line_no).text(Common.addDollarComma(amount_price));
	  $("#foreign_cost" + current_line_no).text(Common.addDollarComma(cost));
	  $("#foreign_net" + current_line_no).text(Common.addDollarComma(net));
	  $("#foreign_profit_rate" + current_line_no).text(Math.round(net / amount_price *100)+ "%");
	  $("#foreign_aw_share" + current_line_no).text(Common.addDollarComma(aw_share));
	  $("#foreign_rw_share" + current_line_no).text(Common.addDollarComma(rw_share));

	  recalculate();
	});

	//単価又は原価変更(邦貨)
	$(".unitPrice,.unitCost").change(function()
	{
	  recalculateLineOnDomesticPriceChanged($(this));
	});

	//邦貨の単価が変更されたので明細行を再計算
    function recalculateLineOnDomesticPriceChanged(e)
    {
	  current_line_no = getGoodsNo($(e).attr('name'));
	  //現在の表示販売為替レート
      var sales_rate = $("#sales_exchange_rate" + current_line_no).val();
      //現在の表示コスト為替レート
      var cost_rate = $("#cost_exchange_rate" + current_line_no).val();
	  //現在の数量
      var num = $("[name='data[EstimateDtlTrn][" + current_line_no + "][num]']").val();

      //単価(邦貨)
	  var unit_price = Common.removeComma($("#unit_price" + current_line_no).val());
	  //全価(邦貨)
	  var amount_price = unit_price * num;
      //原価(邦貨)
	  var unit_cost = Common.removeComma($("#unit_cost"+ current_line_no).val());
	  //総原価(邦貨)
	  var amount_cost = unit_cost * num;
	  //利益(邦貨)
	  var net = amount_price - amount_cost;
	  //aw share(邦貨)
	  var aw_share = net * ($("#aw_rate" + current_line_no).val() /100);
	  //rw share(邦貨)
	  var rw_share = net * ($("#rw_rate" + current_line_no).val() /100);

	  $("#unit_price" + current_line_no).val(Common.addYenComma(unit_price));
	  $("#amount_price" + current_line_no).text(Common.addYenComma(amount_price));
	  $("#unit_cost" + current_line_no).val(Common.addYenComma(unit_cost));
	  $("#cost" + current_line_no).text(Common.addYenComma(amount_cost));
	  $("#net" + current_line_no).text(Common.addYenComma(net));
	  if(amount_price == 0)
	  {
	    $("#profit_rate" + current_line_no).text(0);
	  }
	  else
	  {
	   $("#profit_rate" + current_line_no).text(Math.round(net / amount_price *100)+ "%");
	  }
	  $("#aw_share" + current_line_no).text(Common.addYenComma(aw_share));
	  $("#rw_share" + current_line_no).text(Common.addYenComma(rw_share));

	  //単価がオリジナルと違う場合は表示を変える
	  if(unit_price != $("#original_unit_price"+ current_line_no).val())
	  {
	     $("#unit_price"+ current_line_no).removeClass("focusField");
	     $("#unit_price"+ current_line_no).addClass("changedField");
	  }
	  else
	  {
	     $("#unit_price"+ current_line_no).addClass("focusField");
	     $("#unit_price"+ current_line_no).removeClass("changedField");
	  }
	  //原価がオリジナルと違う場合は表示を変える
	  if(unit_cost != $("#original_unit_cost"+ current_line_no).val())
	  {
	     $("#unit_cost"+ current_line_no).removeClass("focusField");
	     $("#unit_cost"+ current_line_no).addClass("changedField");
	  }
	  else
	  {
	     $("#unit_cost"+ current_line_no).addClass("focusField");
	     $("#unit_cost"+ current_line_no).removeClass("changedField");
	  }

	  //単価(外貨)
	  unit_price = unit_price / sales_rate;
	  //全価(外貨)
	  amount_price = unit_price * num;
	  //原価(外貨)
	  unit_cost = unit_cost / cost_rate;
	  //総原価(外貨)
	  amount_cost = unit_cost * num;
	  //利益(外貨)
	  net = amount_price - amount_cost;
	  //aw share(外貨)
	  aw_share = net * ($("#aw_rate" + current_line_no).val() /100);
	  //rw share(外貨)
	  rw_share = net * ($("#rw_rate" + current_line_no).val() /100);

	  $("#foreign_unit_price" + current_line_no).val(Common.addDollarComma(unit_price));
	  $("#foreign_amount_price" + current_line_no).text(Common.addDollarComma(amount_price));
	  $("#foreign_unit_cost" + current_line_no).val(Common.addDollarComma(unit_cost));
	  $("#foreign_cost" + current_line_no).text(Common.addDollarComma(amount_cost));
	  $("#foreign_net" + current_line_no).text(Common.addDollarComma(net));
	  if(amount_price == 0)
	  {
	    $("#foreign_profit_rate" + current_line_no).text(0);
	  }
	  else
	  {
	   $("#foreign_profit_rate" + current_line_no).text(Math.round(net / amount_price *100)+ "%");
	  }
	  $("#foreign_aw_share" + current_line_no).text(Common.addDollarComma(aw_share));
	  $("#foreign_rw_share" + current_line_no).text(Common.addDollarComma(rw_share));

	  recalculate();
	 }

	 //単価又は原価変更(外貨)
	$(".unitForeignPrice,.unitForeignCost").change(function()
	 {
	    recalculateLineOnForeignPriceChanged($(this))
	 });

	 //外貨の単価が変更されたので明細行を再計算
	 function recalculateLineOnForeignPriceChanged(e)
	 {
	  current_line_no = getGoodsNo($(e).attr('name'));
	  //現在の表示販売為替レート
      var sales_rate = $("#sales_exchange_rate" + current_line_no).val();
      //現在の表示コスト為替レート
      var cost_rate = $("#cost_exchange_rate" + current_line_no).val();
	  //現在の数量
       var num = $("[name='data[EstimateDtlTrn][" + current_line_no + "][num]']").val();

	  //単価(外貨)
	  var unit_price = Common.removeDollarComma($("#foreign_unit_price" + current_line_no).val());
	  //全価(外貨)
	  var amount_price = unit_price * num;
	  //原価(外貨)
	  var unit_cost = Common.removeDollarComma($("#foreign_unit_cost"+ current_line_no).val());

	  //全原価(外貨)
	  var amount_cost = unit_cost * num;
	  //利益(外貨)
	  var net = amount_price - amount_cost;
	  //aw share(外貨)
	  var aw_share = net * ($("#aw_rate" + current_line_no).val() /100);
	  //rw share(外貨)
	  var rw_share = net * ($("#rw_rate" + current_line_no).val() /100);

	  $("#foreign_unit_price" + current_line_no).val(Common.addDollarComma(unit_price));
	  $("#foreign_amount_price" + current_line_no).text(Common.addDollarComma(amount_price));
	  $("#foreign_unit_cost" + current_line_no).val(Common.addDollarComma(unit_cost));
	  $("#foreign_cost" + current_line_no).text(Common.addDollarComma(amount_cost));
	  $("#foreign_net" + current_line_no).text(Common.addDollarComma(net));
	   if(amount_price == 0)
	  {
	    $("#foreign_profit_rate" + current_line_no).text(0);
	  }
	  else
	  {
	   $("#foreign_profit_rate" + current_line_no).text(Math.round(net / amount_price *100)+ "%");
	  }
	  $("#foreign_aw_share" + current_line_no).text(Common.addDollarComma(aw_share));
	  $("#foreign_rw_share" + current_line_no).text(Common.addDollarComma(rw_share));

	  //単価がオリギナルと違う場合は表示を変える
	  if(unit_price != $("#foreign_original_unit_price"+ current_line_no).val())
	  {
	     $("#foreign_unit_price"+ current_line_no).removeClass("focusField");
	     $("#foreign_unit_price"+ current_line_no).addClass("changedField");
	  }
	  else
	  {
	     $("#foreign_unit_price"+ current_line_no).addClass("focusField");
	     $("#foreign_unit_price"+ current_line_no).removeClass("changedField");
	  }
	  //原価がオリジナルと違う場合は表示を変える
	  if(unit_cost != $("#foreign_original_unit_cost"+ current_line_no).val())
	  {
	     $("#foreign_unit_cost"+ current_line_no).removeClass("focusField");
	     $("#foreign_unit_cost"+ current_line_no).addClass("changedField");
	  }
	  else
	  {
	     $("#foreign_unit_cost"+ current_line_no).addClass("focusField");
	     $("#foreign_unit_cost"+ current_line_no).removeClass("changedField");
	  }

	  //単価(邦貨) *単価をここで四捨五入する
	  unit_price = Common.removeComma(Common.addYenComma(unit_price * sales_rate));
	  //全価(邦貨)
	  amount_price = unit_price * num;

	  //原価(邦貨) *原価をここで四捨五入する
	  unit_cost =   Common.removeComma(Common.addYenComma(unit_cost * cost_rate));
	  //全原価(邦貨)
	  amount_cost = unit_cost * num;

	  //利益(邦貨)
	  net = amount_price - amount_cost;
	  //aw share(邦貨)
	  aw_share = net * ($("#aw_rate" + current_line_no).val() /100);
	  //rw share(邦貨)
	  rw_share = net * ($("#rw_rate" + current_line_no).val() /100);

	  $("#unit_price" + current_line_no).val(Common.addYenComma(unit_price));
	  $("#amount_price" + current_line_no).text(Common.addYenComma(amount_price));
	  $("#unit_cost" + current_line_no).val(Common.addYenComma(unit_cost));
	  $("#cost" + current_line_no).text(Common.addYenComma(amount_cost));
	  $("#net" + current_line_no).text(Common.addYenComma(net));
	  if(amount_price == 0)
	  {
	    $("#profit_rate" + current_line_no).text(0);
	  }
	  else
	  {
	    $("#profit_rate" + current_line_no).text(Math.round(net / amount_price *100)+ "%");
	  }
	  $("#aw_share" + current_line_no).text(Common.addYenComma(aw_share));
	  $("#rw_share" + current_line_no).text(Common.addYenComma(rw_share));

	  recalculate();
	}

	 // 表示形式の選択
	$("[name='view']").click(function(){
        ChangeDisplayStyle($(this).val());
	});
	    ChangeDisplayStyle("short");

	//テキストエリアの大きさの自動調整
	$(".sales_goods_nm").height(30);//init
    $(".sales_goods_nm").css("lineHeight","20px");//init

    $(".sales_goods_nm").each(function(){
      resizeTextarea($(this));
    });

    $(".sales_goods_nm").on("input",function(){
      resizeTextarea($(this));
   });


    //ajaxの時の待ち画像
	$("#goods_ctg_indicator").css("display","none");
	$("#goods_kbn_indicator").css("display","none");
	$("#goods_indicator").css("display","none");
	//$("#showing_year_indicator").css("display","none");

	//テーブル行の入れ替え
    $('#invoice_table').tableDnD({
        onDrop: function(table, row) {
            ReorderTable(1);
        }
    });

     /* 処理結果用ダイアログ */
    $("#result_dialog").dialog({
             buttons: [{
                 text: "OK",
                 click: function () {
                     $("#result_dialog").dialog('close');

                     /* 正常登録なら見積一覧画面に戻る */
                     if($("#result_dialog").data("action").toUpperCase() != "COPY" ){
                        if($("#result_dialog").data("status").toUpperCase() == "TRUE"){
                           location.href = "$main_url";
                        }
                     }
                 }
             }],
              beforeClose: function (event, ui) {
                  $("#result_message span").text("");
		          $("#error_reason").text("");
             },
             draggable: false,
             autoOpen: false,
             resizable: false,
             zIndex: 2000,
             modal: true,
             title: "登録結果"
    });

    /* 確認用ダイアログ */
    $("#confirm_dialog").dialog({
             buttons: [{
                 text: "OK",
                 click: function () {
                     $("#confirm_dialog").dialog('close');
                     StartSubmit();
                 }
             },
             {
                 text: "CANCEL",
                 click: function () {
                     $("#confirm_dialog").dialog('close');
                 }
             }],
             draggable: false,
             autoOpen: false,
             resizable: false,
             zIndex: 2000,
             width:"350px",
             modal: true,
             title: "確認"
     });

	/* 送信前に押下されたボタンの種類を保存しておく */
	$(".inputbutton").click(function(){
	  $("#result_dialog").data("action",$(this).attr("name"));
	});

	//フォーム送信前操作
	$("#formID").submit(function(){

	    if($("#result_dialog").data("action").toUpperCase() == "DELETE"){
	        $("#confirm_dialog").dialog('open');
	    }else{
	       StartSubmit();
	    }
		return false;
	});

    /* 更新処理開始  */
	function StartSubmit(){

	    if($("#result_dialog").data("action").toUpperCase() != "DELETE" &&
	       CheckSalesExchangeRate()==false){
            alert("販売為替レートが未入力です。");
	        return false;
	    }

	    if( $("#formID").validationEngine('validate')==false){ return false; }
	      $(".unitCost").each(function(){
                $(this).attr("disabled",false);
          });
          $(".unitForeignCost").each(function(){
               $(this).attr("disabled",false);
          });
          $(".unitPrice").each(function(){
               $(this).attr("disabled",false);
          });
          $(".unitForeignPrice").each(function(){
               $(this).attr("disabled",false);
          });

	    for(var i=1;i <= table_counter;i++)
	    {
	       //クレジットフラグの設定
	       var tag = "[name='credit_flg" + i + "']";
	       var value = "data[EstimateDtlTrn][" + i + "][credit_flg]"
		   $(tag).attr('name',value);
		}

		/* 登録開始 */
		$(this).simpleLoading('show');
		var formData = $("#formID").serialize() + "&submit=" + $("#result_dialog").data("action");
		$.post("$edit_estimate_url",formData , function(result) {

		  $(this).simpleLoading('hide');

		  var obj = null;
	      try {
            obj = $.parseJSON(result);
          } catch(e) {
            obj = {};
            obj.result = false;
		    obj.message = "致命的なエラーが発生しました。";
		    obj.reason  = "このダイアログを閉じた後、画面のスクリーンショットを保存して管理者にお問い合わせ下さい。";
		    $("#result_dialog").data("status","false");
		    $("#critical_error").text(result);
          }

		  if(obj.result == true){
		     $("#result_message img").attr('src',"$confirm_image_path");
		      $("#result_dialog").data("status","true");
		  }else{
		     $("#result_message img").attr('src',"$error_image_path");
		      $("#result_dialog").data("status","false");
		  }
		    $("#result_message span").text(obj.message);
		    $("#error_reason").text(obj.reason);
            $("#result_dialog").dialog('open');
        });
		return false;
	}

	/**
	*    オリジナル商品名をどこで保持するか未決定のためまだ実装していない
	*/
	//商品名変更
	$(".sales_goods_nm").change(function(){

	  current_line_no = getGoodsNo($(this).attr('name'));

	  //商品名がオリジナルと違う場合は表示を変える
	  if($(this).val() != $("#original_goods_nm"+ current_line_no).val())
	  {
	    // $("#sales_goods_nm"+ current_line_no).removeClass("focusField");
	    // $("#sales_goods_nm"+ current_line_no).addClass("changedField");
	  }
	  else
	  {
	    // $("#sales_goods_nm"+ current_line_no).addClass("focusField");
	    // $("#sales_goods_nm"+ current_line_no).removeClass("changedField");
	  }
	});

	//価格及び原価がオリジナルと違う時は表示
	for(var k=1; k <= table_counter;k++)
	{
	  if($("#currency_kbn" + k).val()==0)
	  {
	     if(Common.removeComma($("#foreign_unit_price"+ k).val()) != Common.removeComma($("#foreign_original_unit_price"+ k).val()))
	     {
	       $("#foreign_unit_price"+ k).removeClass("focusField");
	       $("#foreign_unit_price"+ k).addClass("changedField");
	     }
	     if(Common.removeComma($("#foreign_unit_cost"+ k).val()) != Common.removeComma($("#foreign_original_unit_cost"+ k).val()))
	     {
	       $("#foreign_unit_cost"+ k).removeClass("focusField");
	       $("#foreign_unit_cost"+ k).addClass("changedField");
	     }
	  }
	  else
	  {
	     if(Common.removeComma($("#unit_price"+ k).val()) != Common.removeComma($("#original_unit_price"+ k).val()))
	     {
	       $("#unit_price"+ k).removeClass("focusField");
	       $("#unit_price"+ k).addClass("changedField");
	     }
	    //原価がオリジナルと違う場合は表示を変える
	     if(Common.removeComma($("#unit_cost"+ k).val()) != Common.removeComma($("#original_unit_cost"+ k).val()))
	     {
	       $("#unit_cost"+ k).removeClass("focusField");
	       $("#unit_cost"+ k).addClass("changedField");
	     }
	  }
	}

	 /* テンプレート見積フォーム表示 */
     $("#template").click(function(){

        $(this).simpleLoading('show');
	    $.post("$template_form_url" , function(html) {
		   $(this).simpleLoading('hide');
	  	   $("body").append(html);
        });
       return false;
     });

    /* 請求書発行の事前チェック処理 */
    $(".export_invoice").click(function(){
       var deadline = $("#invoice_deadline").val();
       if(deadline == ""){
         alert("振込期限を入力して下さい。");
         return false;
       }

       //「未定」ベンダー存在チェック
       var hasNoVender = false;
       for(var i=1;i <= table_counter;i++){
         if($("#vendor_nm"+i).text()=="未定"){
           hasNoVender = true;
           break;
         }
       }
       if(hasNoVender){ alert("ベンダーが未定の明細がありますので注意してください。");}

       deadline = deadline.split("/");
       var url = $(this).attr("href") + "/" + $("#credit_amount").text().split(",").join("") + "/" + deadline[0]+deadline[1]+deadline[2];

       $(this).attr("href",url);
    });

    /* 見積書発行の事前チェック処理 */
    $(".export_estimate").click(function(){
       var url = $(this).attr("href") + "/" + $("#credit_amount").text().split(",").join("") + "/dummy/" + $("#pdf_note").val();
       $(this).attr("href",url);
    });

    /* 内金請求書発行の事前チェック処理 */
    $(".export_credit").click(function(){

       var credit_amount = $("#credit_invoice_amount").val().split(",").join("");
       if(credit_amount == ""){
         alert("内金額を入力して下さい。");
         return false;
       }

       var deadline = $("#credit_deadline").val();
       if(deadline == ""){
         alert("内金期限を入力して下さい。");
         return false;
       }
       deadline = deadline.split("/");
       var url = $(this).attr("href") + "/" + credit_amount + "/" + deadline[0]+deadline[1]+deadline[2];

       $(this).attr("href",url);
    });

    /* 支払区分が変更されたら再計算する(国内払いの場合は合計に州税を含めないため） */
    $(".payment_kbn").change(function(){
         recalculate();
    });

    /* 問い合わせボタン押下 */
    $(".question").click(function(){

       $(this).simpleLoading('show');

       var href = $(this).attr('href');
       $.post(href,function(data){
          $("body").append(data);
          $(this).simpleLoading('hide');
       });

       return false;
    });

    /* 販売為替レートを一括変換する
    -----------------------------------------------------------*/
    $("#sales_exchange_rate_update_button").click(function(){

      var new_rate = $("#sales_exchange_update_rate").val();

      if($("input:radio[name='sales_exchange_update_rate_chk']:checked").val().toUpperCase()=="ALL"){

		 for(var i=1;i <= table_counter;i++){
            $("#sales_exchange_rate"+i).val(new_rate);
            recalculateLine(getGoodsNo($("#sales_exchange_rate"+i).attr('name')));
         }
      }else{
         for(var i=1;i <= table_counter;i++){
		     if($("#sales_exchange_rate"+i).val() == ""){
               $("#sales_exchange_rate"+i).val(new_rate);
               recalculateLine(getGoodsNo($("#sales_exchange_rate"+i).attr('name')));
   		     }
         }
      }
		ChangeSalesExchangeRateColorIfDifferent();
    });

    /* 原価為替レートを一括変換する
    -----------------------------------------------------------*/
    $("#cost_exchange_rate_update_button").click(function(){

         var new_rate = $("#cost_exchange_update_rate").val();
         for(var i=1;i <= table_counter;i++){
            $("#cost_exchange_rate"+i).val(new_rate);
            recalculateLine(getGoodsNo($("#cost_exchange_rate"+i).attr('name')));
         }
    });

    //ページを読み込んだ時点で総計の計算
	recalculate();
    //テーブルサイズの調整
	ResizeTable();
	$(window).resize(function(){
       ResizeTable();
    });

    //異なる販売為替レートの色を変える
    ChangeSalesExchangeRateColorIfDifferent();
});

/* 見積もり金額が変更されたらサマリーを変更する
--------------------------------------------------------------*/
function changeEstimateSummary(){
    $("#total_amount_price_summary span").text($("#total_amount_price").text());
    $("#total_net_summary span").text($("#total_net").text());
    $("#total_profit_rate_summary span").text($("#total_profit_rate").text());
}

/* 商品ﾃｷｽﾄエリアの高さ調整
-------------------------------------------------------*/
 function resizeTextarea(e){
	 if($(e).prop("scrollHeight") > $(e).prop("offsetHeight")){
        $(e).height($(e).prop("scrollHeight"));
     }
     else{
        var lineHeight = Number($(e).css("lineHeight").split("px")[0]);
        while (true){
          $(e).height($(e).height() - lineHeight);
          if($(e).prop("scrollHeight") > $(e).prop("offsetHeight")){
              $(e).height($(e).prop("scrollHeight"));
              break;
          }
       }
    }
  }

/* 価格などの合計の再計算
-------------------------------------------------------*/
function recalculate()
{
      //邦貨初期化
      $("#sub_total_amount_price").text(0);
      $("#sub_total_amount_price_with_tax").text(0);
      $("#sub_total_amount_price_with_arrange").text(0);
      $("#mid_total_amount_price").text(0);
      $("#total_amount_price_with_discount").text(0);
      $("#total_amount_price_with_discount_currency").text(0);
      $("#total_amount_price").text(0);

      $("#sub_total_cost").text(0);
      $("#mid_total_cost").text(0);
      $("#total_cost").text(0);

      $("#sub_total_net").text(0);
      $("#mid_total_net").text(0);
      $("#total_net").text(0);

      $("#sub_total_aw").text(0);
      $("#mid_total_aw").text(0);
      $("#total_aw").text(0);

      $("#sub_total_rw").text(0);
      $("#sub_total_rw_with_arrange").text(0);
      $("#mid_total_rw").text(0);
      $("#total_rw").text(0);

      //外貨初期化
      $("#sub_total_amount_foreign_price").text(0);
      $("#sub_total_foreign_amount_price_with_tax").text(0);
      $("#sub_total_foreign_amount_price_with_arrange").text(0);
      $("#mid_total_foreign_amount_price").text(0);
      $("#total_foreign_amount_price_with_discount").text(0);
      $("#total_foreign_amount_price_with_discount_currency").text(0);
      $("#total_foreign_amount_price").text(0);

      $("#sub_total_foreign_cost").text(0);
      $("#mid_total_foreign_cost").text(0);
      $("#total_foreign_cost").text(0);

      $("#sub_total_foreign_net").text(0);
      $("#mid_total_foreign_net").text(0);
      $("#total_foreign_net").text(0);

      $("#sub_total_foreign_aw").text(0);
      $("#mid_total_foreign_aw").text(0);
      $("#total_foreign_aw").text(0);

      $("#sub_total_foreign_rw").text(0);
      $("#sub_total_foreign_rw_with_arrange").text(0);
      $("#mid_total_foreign_rw").text(0);
      $("#total_foreign_rw").text(0);

      //国内払い用の商品代価合計
      var internal_total_price=0;
      //国内払い用の商品代価合計(外貨)
      var internal_total_foreign_price=0;
      /* 商品価格と原価の集計 */
      for(i=1;i <= table_counter;i++)
      {
        /* 邦貨計算 */
        //代価合計
        $("#sub_total_amount_price").text(Common.addYenComma(Common.removeComma($("#amount_price" + i).text()) +  Common.removeComma($("#sub_total_amount_price").text())));
        //原価合計
        $("#sub_total_cost").text(Common.addYenComma(Common.removeComma($("#cost" + i).text()) +  Common.removeComma($("#sub_total_cost").text())));
        //業者配分合計
        $("#sub_total_aw").text(Common.addYenComma(Common.removeComma($("#aw_share" + i).text()) + Common.removeComma($("#sub_total_aw").text())));
        //RW配分合計
        $("#sub_total_rw").text(Common.addYenComma(Common.removeComma($("#rw_share" + i).text()) + Common.removeComma($("#sub_total_rw").text())));

        /* 外貨計算 */
        //代価合計
        $("#sub_total_amount_foreign_price").text(Common.addDollarComma(Common.removeDollarComma($("#foreign_amount_price" + i).text()) +  Common.removeDollarComma($("#sub_total_amount_foreign_price").text())));
        //原価合計
        $("#sub_total_foreign_cost").text(Common.addDollarComma(Common.removeDollarComma($("#foreign_cost" + i).text()) +  Common.removeDollarComma($("#sub_total_foreign_cost").text())));
        //業者配分合計
        $("#sub_total_foreign_aw").text(Common.addDollarComma(Common.removeDollarComma($("#foreign_aw_share" + i).text()) + Common.removeDollarComma($("#sub_total_foreign_aw").text())));
        //RW配分合計
        $("#sub_total_foreign_rw").text(Common.addDollarComma(Common.removeDollarComma($("#foreign_rw_share" + i).text()) + Common.removeDollarComma($("#sub_total_foreign_rw").text())));

        //国内払い用の商品
        if($("#payment_kbn" + i).val() == $domestic_direct_pay || $("#payment_kbn" + i).val() == $domestic_credit_pay ){
           internal_total_price         += Common.removeComma($("#amount_price" + i).text());
           internal_total_foreign_price += Common.removeComma($("#foreign_amount_price" + i).text());
        }
      }
        /* 邦貨合計計算 */
        //TAX(小計から国内支払商品の合計を除いてからTAXを求める)
        $("#sub_total_amount_price_with_tax").text(Common.addYenComma($("input[name='data[EstimateTrn][hawaii_tax_rate]']").val() / 100 *  (Common.removeComma($("#sub_total_amount_price").text())-internal_total_price)));
        //手数料
        $("#sub_total_amount_price_with_arrange").text(Common.addYenComma($("input[name='data[EstimateTrn][service_rate]']").val()/ 100 *  Common.removeComma($("#sub_total_amount_price").text())));
        //代価合計(TAX・手数料込)
        $("#mid_total_amount_price").text(Common.addYenComma(Common.removeComma($("#sub_total_amount_price").text()) +
                                                      Common.removeComma($("#sub_total_amount_price_with_tax").text()) +
                                                      Common.removeComma($("#sub_total_amount_price_with_arrange").text())));
        //割引料
        $("#total_amount_price_with_discount").text(Common.addYenComma(Common.removeComma($("#mid_total_amount_price").text()) *  ($("input[name='data[EstimateTrn][discount_rate]']").val() / 100)));
        $("#total_amount_price_with_discount_currency").text(Common.addYenComma($("input[name='data[EstimateTrn][discount]']").val()));
        //代価合計(TAX・手数料・割引料込)
        $("#total_amount_price").text(Common.addYenComma(Common.removeComma($("#mid_total_amount_price").text()) -  Common.removeComma($("#total_amount_price_with_discount").text()) - Common.removeComma($("#total_amount_price_with_discount_currency").text())));
        //原価小計
        $("#mid_total_cost").text(Common.addYenComma(Common.removeComma($("#sub_total_cost").text())));
        //原価総合計
        $("#total_cost").text(Common.addYenComma(Common.removeComma($("#sub_total_cost").text())));
        //利益（代価-原価)
        $("#sub_total_net").text(Common.addYenComma(Common.removeComma($("#sub_total_amount_price").text()) -  Common.removeComma($("#total_cost").text())));
        //利益（代価(TAX・手数料込)-原価)
        $("#mid_total_net").text(Common.addYenComma(Common.removeComma($("#mid_total_amount_price").text()) -  Common.removeComma($("#total_cost").text())));
        //利益（代価(TAX・手数料・割引料込)
        $("#total_net").text(Common.addYenComma(Common.removeComma($("#total_amount_price").text()) -  Common.removeComma($("#total_cost").text())));
        //業者配分小計
        $("#mid_total_aw").text(Common.addYenComma(Common.removeComma($("#sub_total_aw").text())));
        //業者配分総合計
        $("#total_aw").text(Common.addYenComma(Common.removeComma($("#sub_total_aw").text())));
        //RW手数料
        $("#sub_total_rw_with_arrange").text(Common.addYenComma(Common.removeComma($("#sub_total_amount_price_with_arrange").text())));
        //RW配分合計(手数料込)
        $("#mid_total_rw").text(Common.addYenComma(Common.removeComma($("#sub_total_rw_with_arrange").text()) + Common.removeComma($("#sub_total_rw").text())));
        //AW配分割引料
        $("#total_aw_with_discount").text(Common.addYenComma(Common.removeComma($("#total_amount_price_with_discount").text()) * ($("#discount_aw_share").val() / 100)));
        $("#total_aw_with_discount_currency").text(Common.addYenComma(Common.removeComma($("#total_amount_price_with_discount_currency").text()) * ($("#discount_aw_share").val() / 100)));
        //RW配分割引料
        $("#total_rw_with_discount").text(Common.addYenComma(Common.removeComma($("#total_amount_price_with_discount").text()) * ($("#discount_rw_share").val() / 100)));
        $("#total_rw_with_discount_currency").text(Common.addYenComma(Common.removeComma($("#total_amount_price_with_discount_currency").text()) * ($("#discount_rw_share").val() / 100)));
        //RW配分総合計(手数料・割引料込)
        $("#total_rw").text(Common.addYenComma(Common.removeComma($("#mid_total_rw").text()) -  Common.removeComma($("#total_rw_with_discount").text()) -  Common.removeComma($("#total_rw_with_discount_currency").text())));
        //AW配分総合計(手数料・割引料込)
        $("#total_aw").text(Common.addYenComma(Common.removeComma($("#mid_total_aw").text()) -  Common.removeComma($("#total_aw_with_discount").text()) -  Common.removeComma($("#total_aw_with_discount_currency").text())));
        //小計利益率
        if(Common.removeComma($("#sub_total_amount_price").text()) == 0)
        {
           $("#sub_total_profit_rate").text("0%");
        }
        else
        {
           $("#sub_total_profit_rate").text(Math.round(Common.removeComma($("#sub_total_net").text())  /  Common.removeComma($("#sub_total_amount_price").text())*100) + "%");
        }
        //中計利益率
        if(Common.removeComma($("#mid_total_amount_price").text()) == 0)
        {
           $("#mid_total_profit_rate").text("0%");
        }
        else
        {
           $("#mid_total_profit_rate").text(Math.round(Common.removeComma($("#mid_total_net").text())  /  Common.removeComma($("#mid_total_amount_price").text()) *100)+ "%");
        }
        //総合計利益率
        if(Common.removeComma($("#total_amount_price").text()) == 0)
        {
           $("#total_profit_rate").text("0%");
        }
        else
        {
            $("#total_profit_rate").text(Math.round(Common.removeComma($("#total_net").text())  /  Common.removeComma($("#total_amount_price").text()) *100)+ "%");
        }

     /* 外貨合計計算 */
        //TAX(小計から国内支払商品の合計を除いてからTAXを求める)
        $("#sub_total_amount_foreign_price_with_tax").text(Common.addDollarComma($("input[name='data[EstimateTrn][hawaii_tax_rate]']").val() / 100 *  (Common.removeDollarComma($("#sub_total_amount_foreign_price").text())-internal_total_foreign_price)));
        //手数料
        $("#sub_total_amount_foreign_price_with_arrange").text(Common.addDollarComma($("input[name='data[EstimateTrn][service_rate]']").val()/ 100 *  Common.removeDollarComma($("#sub_total_amount_foreign_price").text())));
        //代価合計(TAX・手数料込)
        $("#mid_total_amount_foreign_price").text(Common.addDollarComma(Common.removeDollarComma($("#sub_total_amount_foreign_price").text()) +
                                                      Common.removeDollarComma($("#sub_total_amount_foreign_price_with_tax").text()) +
                                                      Common.removeDollarComma($("#sub_total_amount_foreign_price_with_arrange").text())));
        //割引料
        $("#total_amount_foreign_price_with_discount").text(Common.addDollarComma(Common.removeDollarComma($("#mid_total_amount_foreign_price").text()) *  ($("input[name='data[EstimateTrn][discount_rate]']").val()/100)));
        $("#total_amount_foreign_price_with_discount_currency").text(Common.addDollarComma($("input[name='data[EstimateTrn][discount]']").val() / $("#discount_exchange_rate").val()));
        //代価合計(TAX・手数料・割引料込)
        $("#total_amount_foreign_price").text(Common.addDollarComma(Common.removeDollarComma($("#mid_total_amount_foreign_price").text()) -  Common.removeDollarComma($("#total_amount_foreign_price_with_discount").text())- Common.removeDollarComma($("#total_amount_foreign_price_with_discount_currency").text())));
        //原価小計
        $("#mid_total_foreign_cost").text(Common.addDollarComma(Common.removeDollarComma($("#sub_total_foreign_cost").text())));
        //原価総合計
        $("#total_foreign_cost").text(Common.addDollarComma(Common.removeDollarComma($("#sub_total_foreign_cost").text())));
        //利益（代価-原価)
        $("#sub_total_foreign_net").text(Common.addDollarComma(Common.removeDollarComma($("#sub_total_amount_foreign_price").text()) -  Common.removeDollarComma($("#total_foreign_cost").text())));
        //利益（代価(TAX・手数料込)-原価)
        $("#mid_total_foreign_net").text(Common.addDollarComma(Common.removeDollarComma($("#mid_total_amount_foreign_price").text()) -  Common.removeDollarComma($("#total_foreign_cost").text())));
        //利益（代価(TAX・手数料・割引料込)
        $("#total_foreign_net").text(Common.addDollarComma(Common.removeDollarComma($("#total_amount_foreign_price").text()) -  Common.removeDollarComma($("#total_foreign_cost").text())));
        //業者配分小計
        $("#mid_total_foreign_aw").text(Common.addDollarComma(Common.removeDollarComma($("#sub_total_foreign_aw").text())));
        //業者配分総合計
        $("#total_foreign_aw").text(Common.addDollarComma(Common.removeDollarComma($("#sub_total_foreign_aw").text())));
        //RW手数料
        $("#sub_total_foreign_rw_with_arrange").text(Common.addDollarComma(Common.removeDollarComma($("#sub_total_amount_foreign_price_with_arrange").text())));
        //RW配分合計(手数料込)
        $("#mid_total_foreign_rw").text(Common.addDollarComma(Common.removeDollarComma($("#sub_total_foreign_rw_with_arrange").text()) + Common.removeDollarComma($("#sub_total_foreign_rw").text())));
        //AW配分割引料
        $("#total_foreign_aw_with_discount").text(Common.addDollarComma(Common.removeDollarComma($("#total_amount_foreign_price_with_discount").text()) * ($("#discount_aw_share").val() / 100)));
        $("#total_foreign_aw_with_discount_currency").text(Common.addDollarComma(Common.removeDollarComma($("#total_amount_foreign_price_with_discount_currency").text()) * ($("#discount_aw_share").val() / 100)));
        //RW配分割引料
        $("#total_foreign_rw_with_discount").text(Common.addDollarComma(Common.removeDollarComma($("#total_amount_foreign_price_with_discount").text()) * ($("#discount_rw_share").val() / 100)));
        $("#total_foreign_rw_with_discount_currency").text(Common.addDollarComma(Common.removeDollarComma($("#total_amount_foreign_price_with_discount_currency").text()) * ($("#discount_rw_share").val() / 100)));
        //RW配分総合計(手数料・割引料込)
        $("#total_foreign_rw").text(Common.addDollarComma(Common.removeDollarComma($("#mid_total_foreign_rw").text()) -  Common.removeDollarComma($("#total_foreign_rw_with_discount").text()) - Common.removeDollarComma($("#total_foreign_rw_with_discount_currency").text())));
        //AW配分総合計(手数料・割引料込)
        $("#total_foreign_aw").text(Common.addDollarComma(Common.removeDollarComma($("#mid_total_foreign_aw").text()) -  Common.removeDollarComma($("#total_foreign_aw_with_discount").text()) - Common.removeDollarComma($("#total_foreign_aw_with_discount_currency").text())));
        //小計利益率
        if(Common.removeComma($("#sub_total_amount_price").text()) == 0)
        {
           $("#sub_total_foreign_profit_rate").text("0%");
        }
        else
        {
           $("#sub_total_foreign_profit_rate").text(Math.round(Common.removeDollarComma($("#sub_total_foreign_net").text())  /  Common.removeDollarComma($("#sub_total_amount_foreign_price").text()) * 100)+ "%")
        }
        //中計利益率
        if(Common.removeComma($("#mid_total_amount_price").text()) == 0)
        {
           $("#mid_total_foreign_profit_rate").text("0%");
        }
        else
        {
         $("#mid_total_foreign_profit_rate").text(Math.round(Common.removeDollarComma($("#mid_total_foreign_net").text())  /  Common.removeDollarComma($("#mid_total_amount_foreign_price").text()) * 100)+ "%")
        }
        //総合計利益率
        if(Common.removeComma($("#total_amount_price").text()) == 0)
        {
           $("#total_foreign_profit_rate").text("0%");
        }
        else
        {
             $("#total_foreign_profit_rate").text(Math.round(Common.removeDollarComma($("#total_foreign_net").text())  /  Common.removeDollarComma($("#total_amount_foreign_price").text()) * 100)+ "%")
        }

        changeEstimateSummary();
    }

function getGoodsKbnNo(str){
      var arr = str.split('_');
      return arr[1];
}
function getGoodsNo(str){
      var arr = str.split('[');
      return arr[2].split(']')[0];
}

/* 行コピー
---------------------------------------------------------------*/
function copyRow(){

	   //ヘッダ分のTRがあるのでカウンター+1になる
	   var cloned = $(".list tr:nth-child("+(table_counter+1)+")");
	   $(cloned).clone(true).insertAfter(cloned);
	   table_counter++;

	   var btd_category = $("td:nth-child(3)",cloned);
	   var btd_kbn = $("td:nth-child(4)",cloned);

	   var new_row = $(".list tr:nth-child("+(table_counter+1)+")");
	   var td_delete = $("td:nth-child(1)" ,new_row);
	   var td_toiawase = $("td:nth-child(2)" ,new_row);
	   var td_money_receive = "";
	   var td_category = $("td:nth-child(3)" ,new_row);
	   var td_kbn = $("td:nth-child(4)" ,new_row);
	   var td_goods = $("td:nth-child(5)" ,new_row);
	   var td_vendor = $("td:nth-child(6)" ,new_row);
	   var td_qty = $("td:nth-child(7)" ,new_row);
       var td_total_price = $("td:nth-child(8)",new_row);
	   var td_unit = $("td:nth-child(9)" ,new_row);
	   var td_cost = $("td:nth-child(10)" ,new_row);

	   var td_total_cost = $("td:nth-child(11)",new_row);
	   var td_profit = $("td:nth-child(12)",new_row);
	   var td_profit_rate = $("td:nth-child(13)",new_row);
	   var td_aw_share = $("td:nth-child(14)",new_row);
	   var td_rw_share = $("td:nth-child(15)",new_row);
	   var td_f_total_price = $("td:nth-child(16)",new_row);
	   var td_f_unit = $("td:nth-child(17)",new_row);
	   var td_f_cost = $("td:nth-child(18)",new_row);
	   var td_f_total_cost = $("td:nth-child(19)",new_row);
	   var td_f_profit = $("td:nth-child(20)",new_row);
	   var td_f_profit_rate = $("td:nth-child(21)",new_row);
	   var td_f_aw_share = $("td:nth-child(22)",new_row);
	   var td_f_rw_share = $("td:nth-child(23)",new_row);
	   var td_aw_rate = $("td:nth-child(24)",new_row);
	   var td_rw_rate = $("td:nth-child(25)",new_row);
	   var td_sales_ex_rate = $("td:nth-child(26)",new_row);
	   var td_cost_ex_rate = $("td:nth-child(27)",new_row);
	   var td_payment_kbn = $("td:nth-child(28)",new_row);

	   if("$adopt_flg" == 1){
	      btd_category = $("td:nth-child(4)",cloned);
	      btd_kbn = $("td:nth-child(5)",cloned);

	      new_row = $(".list tr:nth-child("+(table_counter+1)+")");
	      td_delete = $("td:nth-child(1)" ,new_row);
	      td_toiawase = $("td:nth-child(2)" ,new_row);
	      td_money_receive = $("td:nth-child(3)",new_row);
	      td_category = $("td:nth-child(4)" ,new_row);
	      td_kbn = $("td:nth-child(5)" ,new_row);
	      td_goods = $("td:nth-child(6)" ,new_row);
	      td_vendor = $("td:nth-child(7)" ,new_row);
	      td_qty = $("td:nth-child(8)" ,new_row);
	      td_total_price = $("td:nth-child(9)",new_row);
		  td_unit = $("td:nth-child(10)" ,new_row);
	      td_cost = $("td:nth-child(11)" ,new_row);

	      td_total_cost = $("td:nth-child(12)",new_row);
	      td_profit = $("td:nth-child(13)",new_row);
	      td_profit_rate = $("td:nth-child(14)",new_row);
	      td_aw_share = $("td:nth-child(15)",new_row);
	      td_rw_share = $("td:nth-child(16)",new_row);
	      td_f_total_price = $("td:nth-child(17)",new_row);
		  td_f_unit = $("td:nth-child(18)",new_row);
	      td_f_cost = $("td:nth-child(19)",new_row);
	      td_f_total_cost = $("td:nth-child(20)",new_row);
	      td_f_profit = $("td:nth-child(21)",new_row);
	      td_f_profit_rate = $("td:nth-child(22)",new_row);
	      td_f_aw_share = $("td:nth-child(23)",new_row);
	      td_f_rw_share = $("td:nth-child(24)",new_row);
	      td_aw_rate = $("td:nth-child(25)",new_row);
	      td_rw_rate = $("td:nth-child(26)",new_row);
	      td_sales_ex_rate = $("td:nth-child(27)",new_row);
	      td_cost_ex_rate = $("td:nth-child(28)",new_row);
	      td_payment_kbn = $("td:nth-child(29)",new_row);
       }

	   //新規作成した行のIDを設定
	   $(new_row).attr("id","row"+table_counter);
	   //削除ボタンの属性[name]を設定
	   $("a",td_delete).attr("name","row"+table_counter);
	   //明細IDの属性を設定
	   $("input",td_delete).attr("id","estimate_dtl_id_"+table_counter);
	   $("input",td_delete).attr("name","data[EstimateDtlTrn][" + table_counter + "][id]");
	     //明細IDを初期化
	   $("#estimate_dtl_id_"+table_counter).val("");
	   //新規追加の行は問合せボタンは無効にする
	   $(td_toiawase).html("<span class='disabled'>問</span>");

  	   //入金ボタンの属性[name]を設定
		if("$adopt_flg" == 1){
	      $("input",td_money_receive).attr("name","data[EstimateDtlTrn][" + table_counter + "][money_received_flg]");
	      $("input",td_money_receive).val(1);
	      $("input",td_money_receive).attr("checked",false);
        }

	   //商品分類のタグを設定
	   $(td_category).html( "<select class='goods_ctg' id='goodsCtg_" + table_counter + "' name='goodsCtg_" + table_counter + "'>" + $("select",btd_category).html() + "</select>");
	   //動的にタグを作成したらイベントを追加しないと動作しない
	   $("select",td_category).bind("change",function(){
		                                             updateGoodsCtg($("select",td_category));
		                                             });
	   //前の商品分類の値を引き継ぐ
	   $("select",td_category).val($("select",btd_category).val());

	   //商品区分のタグを設定(selectタグごと切り替えないと後でoptionを追加しようとしても上手く追加できない)
	   $(td_kbn).html( "<select class='goods_kbn' id='goodsKbn_" + table_counter + "' name='goodsKbn_" + table_counter + "'>" + $("select",btd_kbn).html() + "</select>");
	   //動的にタグを作成したらイベントを追加しないと動作しない
	   $("select",td_kbn).bind("change",function(){
		                                             updateGoodsKbn($("select",td_kbn));
		                                             });
       //前の商品区分の値を引き継ぐ
	   $("select",td_kbn).val($("select",btd_kbn).val());

       //商品の属性を設定
	    $("input:nth-child(1)",td_goods).attr("name", "data[EstimateDtlTrn][" + table_counter + "][goods_id]");
	    $("input:nth-child(1)",td_goods).attr("id", "goods_id" + table_counter);
        $("textarea",td_goods).attr("name", "data[EstimateDtlTrn][" + table_counter + "][sales_goods_nm]");
        $("textarea",td_goods).attr("id", "sales_goods_nm" + table_counter);
        $("textarea",td_goods).attr("rows","1");
        $("textarea",td_goods).val("");

        $("textarea",td_goods).bind("keyup",function(){
                                                       resizeTextarea($(this));
                                                      });
          //クローンの行からクラスを引き継いでしまっている場合は取り除く
	      if($("textarea",td_goods).hasClass("changedField"))
	      {
	        $("textarea",td_goods).removeClass("changedField");
	        $("textarea",td_goods).addClass("focusField");
	      }
        $("input:nth-child(3)",td_goods).attr("id", "original_goods_nm" + table_counter);
        $("input:nth-child(3)",td_goods).val("");

       //ベンダー名
       $(td_vendor).attr('id','vendor_nm' + table_counter);

	   //数量の属性[name]を設定
	   $("select",td_qty).attr("name","data[EstimateDtlTrn][" + table_counter + "][num]");
	   $("select",td_qty).val(1);

	   //単価の属性[name]を設定
	   $("input:nth-child(1)",td_unit).attr("name","data[EstimateDtlTrn][" + table_counter + "][sales_price]");
	   $("input:nth-child(1)",td_unit).attr("id"  ,"unit_price" + table_counter);
	   $("input:nth-child(1)",td_unit).val("0");
	       //クローンの行からクラスを引き継いでしまっている場合は取り除く
	      if($("input:nth-child(1)",td_unit).hasClass("changedField"))
	      {
	        $("input:nth-child(1)",td_unit).removeClass("changedField");
	        $("input:nth-child(1)",td_unit).addClass("focusField");
	      }
	   //単価オリジナルの属性[id]を設定
	   $("input:nth-child(2)",td_unit).attr("id","original_unit_price" + table_counter);
	   $("input:nth-child(2)",td_unit).val("0");
	    //編集不可になっている場合があるので解除する
	    $("#unit_price" + table_counter).removeClass("inputdisable");
	    $("#unit_price" + table_counter).attr("disabled",false);
	    $("#unit_price" + table_counter).attr("readonly",false);
	   //原価の属性[id]を設定
	   $("input:nth-child(1)",td_cost).attr("name","data[EstimateDtlTrn][" + table_counter + "][sales_cost]");
	   $("input:nth-child(1)",td_cost).attr("id"  ,"unit_cost" + table_counter);
	   $("input:nth-child(1)",td_cost).val("0");
	     //クローンの行からクラスを引き継いでしまっている場合は取り除く
	     if($("input:nth-child(1)",td_cost).hasClass("changedField"))
	     {
	        $("input:nth-child(1)",td_cost).removeClass("changedField");
	        $("input:nth-child(1)",td_cost).addClass("focusField");
	     }
	   //原価オリジナルの属性[id]を設定
	   $("input:nth-child(2)",td_cost).attr("id","original_unit_cost" + table_counter);
	   $("input:nth-child(2)",td_cost).val("0");
	     //編集不可になっている場合があるので解除する
	    $("#unit_cost" + table_counter).removeClass("inputdisable");
	    $("#unit_cost" + table_counter).attr("disabled",false);
	    $("#unit_cost" + table_counter).attr("readonly",false);
	   //総全価の属性[id]を設定
	   $(td_total_price).attr("id","amount_price" + table_counter);
	   $(td_total_price).text("0");
	   //総原価の属性[id]を設定
	   $(td_total_cost).attr("id","cost" + table_counter);
	   $(td_total_cost).text("0");
	   //利益の属性[id]を設定
	   $(td_profit).attr("id","net" + table_counter);
	   $(td_profit).text("0");
	   //利益率の属性[id]を設定
	   $(td_profit_rate).attr("id","profit_rate" + table_counter);
	   $(td_profit_rate).text("0");
	   //awシェアの属性[id]を設定
	   $(td_aw_share).attr("id","aw_share" + table_counter);
	   $(td_aw_share).text("0");
	   //rwシェアの属性[id]を設定
	   $(td_rw_share).attr("id","rw_share" + table_counter);
	   $(td_rw_share).text("0");

	   //単価(外貨)の属性[name]を設定
	   $("input:nth-child(1)",td_f_unit).attr("name","data[EstimateDtlTrn][" + table_counter + "][foreign_sales_price]");
	   $("input:nth-child(1)",td_f_unit).attr("id"  ,"foreign_unit_price" + table_counter);
	   $("input:nth-child(1)",td_f_unit).val("0");
	     //クローンの行からクラスを引き継いでしまっている場合は取り除く
	     if($("input:nth-child(1)",td_f_unit).hasClass("changedField"))
	     {
	        $("input:nth-child(1)",td_f_unit).removeClass("changedField");
	        $("input:nth-child(1)",td_f_unit).addClass("focusField");
	     }
	   //単価(外貨)オリジナルの属性[id]を設定
	   $("input:nth-child(2)",td_f_unit).attr("id","foreign_original_unit_price" + table_counter);
	   $("input:nth-child(2)",td_f_unit).val("0");
	     //編集不可になっている場合があるので解除する
	    $("#foreign_unit_price" + table_counter).removeClass("inputdisable");
	    $("#foreign_unit_price" + table_counter).attr("disabled",false);
	    $("#foreign_unit_price" + table_counter).attr("readonly",false);
       //原価(外貨)の属性[id]を設定
	   $("input:nth-child(1)",td_f_cost).attr("name","data[EstimateDtlTrn][" + table_counter + "][foreign_sales_cost]");
	   $("input:nth-child(1)",td_f_cost).attr("id"  ,"foreign_unit_cost" + table_counter);
	   $("input:nth-child(1)",td_f_cost).val("0");
	      //クローンの行からクラスを引き継いでしまっている場合は取り除く
	     if($("input:nth-child(1)",td_f_cost).hasClass("changedField"))
	     {
	        $("input:nth-child(1)",td_f_cost).removeClass("changedField");
	        $("input:nth-child(1)",td_f_cost).addClass("focusField");
	     }
	   //原価(外貨)オリジナルの属性[id]を設定
	   $("input:nth-child(2)",td_f_cost).attr("id","foreign_original_unit_cost" + table_counter);
	   $("input:nth-child(2)",td_f_cost).val("0");
	     //編集不可になっている場合があるので解除する
	    $("#foreign_unit_cost" + table_counter).removeClass("inputdisable");
	    $("#foreign_unit_cost" + table_counter).attr("disabled",false);
	    $("#foreign_unit_cost" + table_counter).attr("readonly",false);
	   //総代価(外貨)の属性[name]を設定
	   $(td_f_total_price).attr("id","foreign_amount_price" + table_counter);
	   $(td_f_total_price).text("0");
	   //総原価(外貨)の属性[id]を設定
	   $(td_f_total_cost).attr("id","foreign_cost" + table_counter);
	   $(td_f_total_cost).text("0");
	   //利益(外貨)の属性[id]を設定
	   $(td_f_profit).attr("id","foreign_net" + table_counter);
	   $(td_f_profit).text("0");
	   //利益率(外貨)の属性[id]を設定
	   $(td_f_profit_rate).attr("id","foreign_profit_rate" + table_counter);
	   $(td_f_profit_rate).text("0");
	   //awシェア(外貨)の属性[id]を設定
	   $(td_f_aw_share).attr("id","foreign_aw_share" + table_counter);
	   $(td_f_aw_share).text("0");
	   //rwシェア(外貨)の属性[id]を設定
	   $(td_f_rw_share).attr("id","foreign_rw_share" + table_counter);
	   $(td_f_rw_share).text("0");
	   //awレートの属性[id]を設定
	   $("input:nth-child(1)",td_aw_rate).attr("name","data[EstimateDtlTrn][" + table_counter + "][aw_share]");
	   $("input:nth-child(1)",td_aw_rate).attr("id"  ,"aw_rate" + table_counter);
	   $("input:nth-child(1)",td_aw_rate).val("0");
	   //rwレートの属性[id]を設定
	   $("input:nth-child(1)",td_rw_rate).attr("name","data[EstimateDtlTrn][" + table_counter + "][rw_share]");
	   $("input:nth-child(1)",td_rw_rate).attr("id"  ,"rw_rate" + table_counter);
	   $("input:nth-child(1)",td_rw_rate).val("0");

	   //販売為替レートの属性[name]を設定
	   $("input",td_sales_ex_rate).attr("name","data[EstimateDtlTrn][" + table_counter + "][sales_exchange_rate]");
	   $("input",td_sales_ex_rate).attr("id","sales_exchange_rate" + table_counter);
	  // $("input",td_sales_ex_rate).val($("#exchangeRate").text());
	   $("input",td_sales_ex_rate).val("");

	   //通貨区分の[id]と[name]設定
	   $("input:nth-child(2)",td_sales_ex_rate).attr("id"  ,"currency_kbn" + table_counter);
	   $("input:nth-child(2)",td_sales_ex_rate).attr("name","data[EstimateDtlTrn][" + table_counter + "][currency_kbn]");
	    //コスト為替レートの属性[name]を設定
	   $("input",td_cost_ex_rate).attr("name","data[EstimateDtlTrn][" + table_counter + "][cost_exchange_rate]");
	   $("input",td_cost_ex_rate).attr("id","cost_exchange_rate" + table_counter);
	   $("input",td_cost_ex_rate).val($("#costExchangeRate").text());

	   //支払区分の属性[name]を設定
	   $("select",td_payment_kbn).attr("id","payment_kbn"+table_counter);
	   $("select",td_payment_kbn).attr("name","data[EstimateDtlTrn][" + table_counter + "][payment_kbn_id]");
	}

 /* 商品詳細の再表示
 ---------------------------------------------------------------*/
 function updateGoodsDetail(goods_id,current_line_no){

	    $(this).simpleLoading('show');
		$.get("$goods_url" +"/"+ goods_id, function(data) {

	    var obj=eval("("+data+")");

	  	//現在の表示販売為替レート
		var sales_rate = $("#exchangeRate").text();
		//現在の表示原価為替レート
		var cost_rate = $("#costExchangeRate").text();

		//数量
		var tag = "[name='data[EstimateDtlTrn][" + current_line_no + "][num]']";
		$(tag).val(1);
		//通貨区分
		$("#currency_kbn" + current_line_no).val(obj.GoodsMstView.currency_kbn);

		//商品
        $("#goods_id" + current_line_no).val(obj.GoodsMstView.id);
        $("#sales_goods_nm" + current_line_no).val(obj.GoodsMstView.goods_nm);
        $("#original_goods_nm" + current_line_no).val(obj.GoodsMstView.goods_nm);

        //ベンダー名
        $("#vendor_nm" + current_line_no).text(obj.GoodsMstView.vendor_nm);

        /* 国内支払の場合は支払区分を国内に変更する */
        /*
        if(obj.GoodsMstView.internal_pay_flg == 1){
         	  $("[name='data[EstimateDtlTrn][" + current_line_no + "][payment_kbn_id]']").val($domestic_direct_pay);
        }else{
              $("[name='data[EstimateDtlTrn][" + current_line_no + "][payment_kbn_id]']").val($aboard_indirect_pay);
        }*/

        $("[name='data[EstimateDtlTrn][" + current_line_no + "][payment_kbn_id]']").val(obj.GoodsMstView.payment_kbn_id);

		//テキストエリアの幅調整
		resizeTextarea($("#sales_goods_nm" + current_line_no));
	    $("#sales_goods_nm" + current_line_no).bind("keyup",function(){ resizeTextarea($(this)); });

	    //円ベース
		if(obj.GoodsMstView.currency_kbn == 1)
		{
		  //価格（邦貨）
	      $("#unit_price" + current_line_no).val(Common.addYenComma(obj.GoodsMstView.price));
		  //編集可にする
		  $("#unit_price" + current_line_no).removeClass("inputdisable");
		  $("#unit_price" + current_line_no).attr("disabled",false);
		  //価格オリジナル（邦貨）
		  $("#original_unit_price" + current_line_no).val(obj.GoodsMstView.price);
		  //全価（邦貨）
		  $("#amount_price" + current_line_no).text(Common.addYenComma(obj.GoodsMstView.price));
		  //単価原価（邦貨）
		  $("#unit_cost" + current_line_no).val(Common.addYenComma(obj.GoodsMstView.cost));
		  //セット商品の場合は編集不可とする
          if(obj.GoodsMstView.set_goods_kbn == $set_goods_kbn){
              $("#unit_cost" + current_line_no).addClass("inputdisable");
		      $("#unit_cost" + current_line_no).attr("readonly",true);
          }else{
		    $("#unit_cost" + current_line_no).removeClass("inputdisable");
		    $("#unit_cost" + current_line_no).attr("disabled",false);
		  }
		  //単価原価オリジナル(邦貨)
		  $("#original_unit_cost" + current_line_no).val(obj.GoodsMstView.cost);
		  //原価（邦貨）
		  $("#cost" + current_line_no).text(Common.addYenComma(obj.GoodsMstView.cost));
		  //利益（邦貨）
		  $("#net" + current_line_no).text(Common.addYenComma(obj.GoodsMstView.price -obj.GoodsMstView.cost));
		  //利益率（邦貨）
		  if(Common.removeComma($("#amount_price" + current_line_no).text()) == 0)
		  {
  		    $("#profit_rate" + current_line_no).text(0);
		  }
		  else
		  {
		    $("#profit_rate" + current_line_no).text(Math.round(Common.removeComma($("#net" + current_line_no).text()) / Common.removeComma($("#amount_price" + current_line_no).text()) * 100)+ "%");
		  }
		  //awシェア（邦貨）
		  $("#aw_share" + current_line_no).text(Common.addYenComma((obj.GoodsMstView.price -obj.GoodsMstView.cost)* obj.GoodsMstView.aw_share));
		  //rwシェア（邦貨）
		  $("#rw_share" + current_line_no).text(Common.addYenComma((obj.GoodsMstView.price -obj.GoodsMstView.cost)* obj.GoodsMstView.rw_share));

		  //価格(外貨)
		  $("#foreign_unit_price" + current_line_no).val(Common.addDollarComma(obj.GoodsMstView.price / sales_rate));
          //編集不可にする
		  $("#foreign_unit_price" + current_line_no).addClass("inputdisable");
		  $("#foreign_unit_price" + current_line_no).attr("disabled",true);
		  //全価(外貨)
		  $("#foreign_amount_price" + current_line_no).text(Common.addDollarComma(obj.GoodsMstView.price / sales_rate));
          //単価原価(外貨)
		  $("#foreign_unit_cost" + current_line_no).val(Common.addDollarComma(obj.GoodsMstView.cost / cost_rate));
		  //編集不可にする
		  $("#foreign_unit_cost" + current_line_no).addClass("inputdisable");
		  $("#foreign_unit_cost" + current_line_no).attr("disabled",true);

		  //全原価(外貨)
		  $("#foreign_cost" + current_line_no).text(Common.addDollarComma(obj.GoodsMstView.cost / cost_rate));
		  //利益(外貨)
		  $("#foreign_net" + current_line_no).text(Common.addDollarComma((obj.GoodsMstView.price / sales_rate) - (obj.GoodsMstView.cost / cost_rate)));
		  //利益率(外貨)
		  if(Common.removeComma($("#foreign_amount_price" + current_line_no).text()) == 0)
		  {
  		    $("#foreign_profit_rate" + current_line_no).text(0);
		  }
		  else
		  {
		    $("#foreign_profit_rate" + current_line_no).text(Math.round(Common.removeComma($("#foreign_net" + current_line_no).text()) / Common.removeComma($("#foreign_amount_price" + current_line_no).text()) * 100)+ "%");
		  }
		    //awシェア(外貨)
		    $("#foreign_aw_share" + current_line_no).text(Common.addDollarComma(((obj.GoodsMstView.price / sales_rate) - (obj.GoodsMstView.cost / cost_rate)) * obj.GoodsMstView.aw_share));
		    //ｒｗシェア(外貨)
		    $("#foreign_rw_share" + current_line_no).text(Common.addDollarComma(((obj.GoodsMstView.price / sales_rate) - (obj.GoodsMstView.cost / cost_rate)) * obj.GoodsMstView.rw_share));
		  }
		 //ドルベース
		else
		{
		  //価格（邦貨）
		  $("#unit_price" + current_line_no).val(Common.addYenComma(obj.GoodsMstView.price * sales_rate));
		  //編集不可にする
		  $("#unit_price" + current_line_no).addClass("inputdisable");
		  $("#unit_price" + current_line_no).attr("disabled",true);
		  //全価（邦貨）
		  $("#amount_price" + current_line_no).text(Common.addYenComma(obj.GoodsMstView.price * sales_rate));
		  //単価原価（邦貨）
		  $("#unit_cost" + current_line_no).val(Common.addYenComma(obj.GoodsMstView.cost * cost_rate));
          //編集不可にする
		  $("#unit_cost" + current_line_no).addClass("inputdisable");
		  $("#unit_cost" + current_line_no).attr("disabled",true);
		  //原価（邦貨）
		  $("#cost" + current_line_no).text(Common.addYenComma(obj.GoodsMstView.cost * cost_rate))
		  //利益（邦貨）
		  $("#net" + current_line_no).text(Common.addYenComma((obj.GoodsMstView.price * sales_rate)-(obj.GoodsMstView.cost * cost_rate)));
		  //利益率（邦貨）
		  //利益率(邦貨)
		  if(Common.removeComma($("#amount_price" + current_line_no).text()) == 0)
		  {
  		    $("#profit_rate" + current_line_no).text(0);
		  }
		  else
		  {
		    $("#profit_rate" + current_line_no).text(Math.round(Common.removeComma($("#net" + current_line_no).text()) / Common.removeComma($("#amount_price" + current_line_no).text()) * 100)+ "%");
		  }

		  //awシェア（邦貨）
		  $("#aw_share" + current_line_no).text(Common.addYenComma(((obj.GoodsMstView.price * sales_rate)-(obj.GoodsMstView.cost * cost_rate)) * obj.GoodsMstView.aw_share));
		  //rwシェア（邦貨）
		  $("#rw_share" + current_line_no).text(Common.addYenComma(((obj.GoodsMstView.price * sales_rate)-(obj.GoodsMstView.cost * cost_rate)) * obj.GoodsMstView.rw_share));

	      //価格(外貨)
		  $("#foreign_unit_price" + current_line_no).val(Common.addDollarComma(obj.GoodsMstView.price));
		  //編集可にする
		  $("#foreign_unit_price" + current_line_no).removeClass("inputdisable");
		  $("#foreign_unit_price" + current_line_no).attr("disabled",false);
		  //価格オリジナル(外貨)
		  $("#foreign_original_unit_price" + current_line_no).val(obj.GoodsMstView.price);
		  //全価(外貨)
		  $("#foreign_amount_price" + current_line_no).text(Common.addDollarComma(obj.GoodsMstView.price));
          //単価原価(外貨)
		  $("#foreign_unit_cost" + current_line_no).val(Common.addDollarComma(obj.GoodsMstView.cost));
		  //セット商品の場合は編集不可とする
          if(obj.GoodsMstView.set_goods_kbn == $set_goods_kbn){
              $("#foreign_unit_cost" + current_line_no).addClass("inputdisable");
		      $("#foreign_unit_cost" + current_line_no).attr("readonly",true);
          }else{
		    $("#foreign_unit_cost" + current_line_no).removeClass("inputdisable");
		    $("#foreign_unit_cost" + current_line_no).attr("disabled",false);
		  }
		  //単価原価オリジナル(外貨)
		  $("#foreign_original_unit_cost" + current_line_no).val(obj.GoodsMstView.cost);
		  //原価(外貨)
		  $("#foreign_cost" + current_line_no).text(Common.addDollarComma(obj.GoodsMstView.cost));
          //利益(外貨)
		  $("#foreign_net" + current_line_no).text(Common.addDollarComma(obj.GoodsMstView.price-obj.GoodsMstView.cost));
		  //利益率(外貨)
		  if(Common.removeComma($("#foreign_amount_price" + current_line_no).text()) == 0)
		  {
  		    $("#foreign_profit_rate" + current_line_no).text(0);
		  }
		  else
		  {
		    $("#foreign_profit_rate" + current_line_no).text(Math.round(Common.removeComma($("#foreign_net" + current_line_no).text()) / Common.removeComma($("#foreign_amount_price" + current_line_no).text()) * 100)+ "%");
		  }

		  //awシェア(外貨)
		  $("#foreign_aw_share" + current_line_no).text(Common.addDollarComma((obj.GoodsMstView.price-obj.GoodsMstView.cost) * obj.GoodsMstView.aw_share));
		  //ｒｗシェア(外貨)
		  $("#foreign_rw_share" + current_line_no).text(Common.addDollarComma((obj.GoodsMstView.price-obj.GoodsMstView.cost) * obj.GoodsMstView.rw_share));
		}
		  //awレイト
		  $("#aw_rate" + current_line_no).val(obj.GoodsMstView.aw_share*100);
		  //rwレイト
		  $("#rw_rate" + current_line_no).val(obj.GoodsMstView.rw_share*100);
		  //合計値の再計算
		  recalculate()

		  $(this).simpleLoading('hide');
		});
}

function CheckSalesExchangeRate(){

  for(var i=1;i <= table_counter;i++){
     if($("#sales_exchange_rate"+i).val()== ""){ return false;}
  }
  return true;
}

/* 表示形式の選択  [display='']は初期値に戻すという意味なのでブラウザ毎の表示差異を吸収できる
-------------------------------------------------------------------------*/
function ChangeDisplayStyle(type){

	switch(type.toUpperCase()){
      case "YEN": $(".yen").css("display","");
	              $(".dollar").css("display","none");
		          break;
	  case "DOLLAR": $(".yen").css("display","none");
	                 $(".dollar").css("display","");
		             break;
	  case "SHORT": $(".yen").css("display","none");
		          $(".dollar").css("display","none");
	              $(".short").css("display","");
		          break;
      default: $(".yen").css("display","");
	           $(".dollar").css("display","");
    }
}

/* テーブルのサイズ変更
----------------------------------------------------------------------*/
function ResizeTable(){

   $("#invoice_table_wrapper").height($(window).height() - 340);
   $("#invoice_table_wrapper").width('100%');
}
/* 異なる販売為替レートに色を設定する
-----------------------------------------------------------------------*/
function ChangeSalesExchangeRateColorIfDifferent(){

	var color = ["red","blue","green","brown","orange"];
    var current_color_index = 0;
    var obj = [];
	var base_rate = $("#sales_exchange_rate1").val();

	/* 全商品の販売為替レートを調べる */
    for(var i =2; i <= table_counter;i++){

		var current_line = "#sales_exchange_rate" + i;
		var current_rate = $(current_line).val();

	 if(current_rate != ""){
		/* 1番初めの商品と2番目以降の商品の販売為替レートを比較して異なれば色を変える*/
		if(base_rate != current_rate){

		   /* 既出の１番目と異なる販売為替レートが存在するかチェック */
		   var found = false;
		   for(var j=0;j < obj.length;j++){

		     //既出の販売為替レートの場合は同じ色を設定
		     if(obj[j].rate == current_rate){
                $(current_line).css("color",color[obj[j].color]);
		        found=true;
		        break;
             }
           }
		   //初出の販売為替レートの場合は異なる色を設定
		   if(found == false){
		        //初めて異なる販売為替レートが出現
		        if(obj.length == 0){
                   obj[0] = { rate:$(current_line).val() , color:0};
                   $(current_line).css("color",color[0]);
		           current_color_index++;

		        //色の種類の上限をお超えていないなら次の色を設定
                }else if(color.length != obj.length){
                   obj[obj.length] = { rate:$(current_line).val() , color:current_color_index };
                   $(current_line).css("color",color[current_color_index]);
                   current_color_index++;

		        //色の種類の上限を超えている
                }else{
                   alert("異なる販売為替レートの上限[" + color.length + "個]を超えました。");
		           break;
                }
           }
		//基準為替と同じなら黒に戻す
        }else{
            $(current_line).css("color",$("#sales_exchange_rate1").css("color"));
        }
     }
  }
}
JSPROG
)) ?>


<ul class="operate" style="margin-bottom: :10px;">
	<li><a href="<?php echo $html->url('.') ?>">戻る</a></li>
	<li><a href="<?php echo $html->url('export/excel').'/'.$data[0]['EstimateDtlTrnView']['estimate_id'] ?>" id="export_estimate_excel">EXCEL出力</a></li>

	<?php
      if($customer_status < CS_CONTRACTED){
   	       echo "<li><a href='".$html->url('export/credit_yen').'/'.$data[0]['EstimateDtlTrnView']['estimate_id']."' class='export_credit'>内金請求書出力</a></li>";
	  }
   	?>

	<li><a href="<?php echo $html->url('export/estimate_yen').'/'.$data[0]['EstimateDtlTrnView']['estimate_id'] ?>"  class="export_estimate">見積書出力【円ベース】</a></li>
	<li><a href="<?php echo $html->url('export/estimate_dollar').'/'.$data[0]['EstimateDtlTrnView']['estimate_id'] ?>"  class="export_estimate">見積書出力【ドルベース】</a></li>
	<?php
	   if($data[0]['EstimateDtlTrnView']['adopt_flg'] == ESTIMATE_ADOPTED){
         if($customer_status == CS_CONTRACTED || $customer_status == CS_INVOICED || $customer_status == CS_UNPAIED || $customer_status == CS_PAIED){
   	       echo "<li><a href='".$html->url('export/invoice_yen').'/'.$data[0]['EstimateDtlTrnView']['estimate_id']."' id='export_invoice' class='export_invoice'>請求書出力【円ベース】</a></li>";
   	       echo "<li><a href='".$html->url('export/invoice_dollar').'/'.$data[0]['EstimateDtlTrnView']['estimate_id']."' id='export_invoice_dollar' class='export_invoice'>請求書出力【ドルベース】</a></li>";
	     }
	   }
   	?>
</ul>

<form id="formID" class="content" method="post" name="estimate" action="">
<table cellspacing="5px">
<tr>
    <td rowspan="2">
        <fieldset style="display:inline;padding:10px;margin-top:-10px;"><legend style="margin-bottom:3px;"><?php echo $html->image('yen.png')?>見積要約</legend>
          <p id="total_amount_price_summary"  style="display:inline;margin-right:15px;">総代価<span style="padding-left:5px;"></span></p>
          <p id="total_net_summary"           style="display:inline;margin-right:15px;">利益   <span style="padding-left:5px;"></span></p>
          <p id="total_profit_rate_summary"   style="display:inline;margin-right:15px;">利益率<span style="padding-left:5px;"></span></p>
        </fieldset>
    </td>
    <td><label for="data[EstimateTrn][sales_exchange_rate]"> 販売為替レート: </label></td>
    <!-- <td><label id="exchangeRate"> <?php echo $env_data['EnvMst']['exchange_rate'] ?> </label></td>-->
    <td><input id="sales_exchange_update_rate" type="text" style="width:80px" />
        <input type="radio" id="sales_exchange_update_rate_all"  name="sales_exchange_update_rate_chk" value="all" /><label for="sales_exchange_update_rate_all" style="margin-left: :3px;margin-right:5px;">すべて</label>
        <input type="radio" id="sales_exchange_update_rate_part" name="sales_exchange_update_rate_chk" value="part" checked /><label for="sales_exchange_update_rate_part" style="margin-left: :3px;margin-right:5px;">空白行のみ</label>
        <input id="sales_exchange_rate_update_button" type="button"  value="変換"/>
    </td>

    <td style="padding-left:30px">特記事項:</td>
    <td><textarea id="special_note" class="small-inputcomment " name="data[EstimateTrn][note]" style="height:20px;" ><?php echo $data[0]["EstimateDtlTrnView"]["header_note"] ?></textarea></td>
     <td style="padding-left:20px"><label for="yen"> 【邦貨</label><input type="radio"     name="view" value="yen" />
        <label for="dollar"> 外貨</label><input type="radio"  name="view" value="dollar" />
        <label for="dollar"> 概略</label><input type="radio"  name="view" value="short" checked />
        <label for="dollar"> 全貨</label><input type="radio"  name="view" value="all" />】</td>
</tr>
<tr>
    <td><label for="data[EstimateTrn][cost_exchange_rate]"> 原価為替レート: </label></td>
    <!-- <td><label id="costExchangeRate"> <?php echo $env_data['EnvMst']['cost_exchange_rate'] ?> </label></td> -->
    <td><input id="cost_exchange_update_rate" type="text" style="width:80px;margin-right:10px;" /><input id="cost_exchange_rate_update_button" type="button"  value="変換" /></td>
    <td style="padding-left:30px">注意事項:</td>
    <td><textarea id="pdf_note" class="small-inputcomment " name="data[EstimateTrn][pdf_note]" style="height:20px;" ><?php echo $data[0]["EstimateDtlTrnView"]["pdf_note"] ?></textarea></td>
    <td></td>
</tr>
</table>
<div class="submit">
<input type="submit"  class="add_row inputbutton" value=" 項目追加 " />

    <div style='display:inline;margin-left:5px;'><label>TTSレート適用日付：</label><input type='text' id='tts_rate_dt' name="data[EstimateTrn][tts_rate_dt]" class='datepicker' style="width:90px" value='<?php echo $data[0]['EstimateDtlTrnView']['tts_rate_dt'] ?>' /></div>
    <div style='display:inline;margin-left:5px;'><label>TTSレート：</label><input type='text' id='tts_rate' name="data[EstimateTrn][tts_rate]" style="width:80px" value='<?php echo $data[0]['EstimateDtlTrnView']['tts_rate'] ?>' /></div>
    <div style='display:inline;margin-left:5px;'><label>入金金額：</label><span  id='credit_amount'><?php echo number_format($credit_amount) ?></span></div>

     <?php
      if($customer_status < CS_CONTRACTED){
         echo "<div style='display:inline;margin-left:5px;'><label>内金額：  </label><input type='text'  id='credit_invoice_amount' style='width:90px;text-align:right' value='' /></div>";
   	     echo "<div style='display:inline;margin-left:5px;'><label>内金期日：</label><input type='text'  id='credit_deadline' class='datepicker' style='width:90px' value='' /></div>";
	  }
   	 ?>

     <?php
      if($data[0]['EstimateDtlTrnView']['adopt_flg'] == ESTIMATE_ADOPTED){
      	 echo "<div style='display:inline;margin-left:5px;'><label>振込期日：</label><input type='text'  id='invoice_deadline' class='datepicker' style='width:90px' value='' /></div>";
      }
     ?>

     <?php
      if($customer_status >= CS_INVOICED){
      	 echo "<div style='display:inline;margin-left:5px;'><label>請求書発行日：</label>{$data[0]['EstimateDtlTrnView']['invoice_issued_dt']}</div>";
      	 echo "<input type='hidden' name='data[EstimateTrn][invoice_issued_dt]' value='{$data[0]['EstimateDtlTrnView']['invoice_issued_dt']}'>";
      }
     ?>
    <a href="#" id="template" style="padding-left:10px;">見積テンプレート</a>
</div>


<div id="invoice_table_wrapper" style="overflow:auto;padding:0px 0px 15px 0px;" >
<table id="invoice_table" class="list" cellspacing="0">

    <tr class="nodrag nodrop">
	    <th>削除</th>
	    <th>問</th>
	    <?php  if($data[0]['EstimateDtlTrnView']['adopt_flg'] == ESTIMATE_ADOPTED){ echo "<th>支払</th>";} ?>
	    <th>商品分類&nbsp;&nbsp;<?php echo $html->image('loading.gif',array('id'=>'goods_ctg_indicator')); ?></th>
	    <th>商品区分&nbsp;&nbsp;<?php echo $html->image('loading.gif',array('id'=>'goods_kbn_indicator')); ?></th>
	    <th>商品名&nbsp;&nbsp;<?php echo $html->image('loading.gif',array('id'=>'goods_indicator')); ?></th>
	    <th>ベンダー</th>
	    <th>数量</th>

	    <th class="yen short">総代価<?php echo $html->image('yen.png')?></th>
        <th class="yen short">単価<?php echo $html->image('yen.png')?></th>
        <th class="yen short">原価<?php echo $html->image('yen.png')?></th>
        <th class="yen">総原価<?php echo $html->image('yen.png')?></th>
        <th class="yen">利益<?php echo $html->image('yen.png')?></th>
        <th class="yen">利益率<?php echo $html->image('yen.png')?></th>
        <th class="yen">HI<?php echo $html->image('yen.png')?></th>
        <th class="yen">RW<?php echo $html->image('yen.png')?></th>

        <th class="dollar">総代価<?php echo $html->image('dollar.png')?></th>
        <th class="dollar short">単価<?php echo $html->image('dollar.png')?></th>
        <th class="dollar short">原価<?php echo $html->image('dollar.png')?></th>
        <th class="dollar">総原価<?php echo $html->image('dollar.png')?></th>
        <th class="dollar">利益<?php echo $html->image('dollar.png')?></th>
        <th class="dollar short">利益率<?php echo $html->image('dollar.png')?></th>
        <th class="dollar">HI<?php echo $html->image('dollar.png')?></th>
        <th class="dollar">RW<?php echo $html->image('dollar.png')?></th>
        <th>HI/SH</th>
        <th>RW/SH</th>
        <th>販売為替レート</th>
	    <th>原価為替レート</th>
        <th>支払区分</th>

    </tr>
<?php
   for($i=0;$i < count($data);$i++)
   {
	echo "<tr id='row".($i+1)."'>".
	    "<!--  削除ボタン -->".
	    "<td><a href='#'  class='delete' name='row".($i+1)."'>削除</a>".
	        "<input type='hidden' id='estimate_dtl_id_".($i+1)."' name='data[EstimateDtlTrn][".($i+1)."][id]' value='{$data[$i]['EstimateDtlTrnView']['id']}' />".
	    "</td>".
	    "<!--  問合せボタン -->";
	    if($data[$i]['EstimateDtlTrnView']['contact_flg']==1)
	    {
	    	echo "<td><a href='".$html->url('contactForm')."/".$data[$i]['EstimateDtlTrnView']['id']."/".($i+1)."' class='question' name='question_".($i+1)."' rel='contactPopin' >問</a>".$html->image('ok.png')."</td>";
	    }
	    else
	    {
	    	echo "<td><a href='".$html->url('contactForm')."/".$data[$i]['EstimateDtlTrnView']['id']."/".($i+1)."' class='question' name='question_".($i+1)."' rel='contactPopin' >問</a>".$html->image('ok.png',array('style'=>'display:none'))."</td>";
	    }

	 echo "<!-- 入金 -->";
	    if($data[0]['EstimateDtlTrnView']['adopt_flg'] == ESTIMATE_ADOPTED){
	      if($data[$i]['EstimateDtlTrnView']['money_received_flg']==1){
            echo "<td><input type='checkbox' name='data[EstimateDtlTrn][".($i+1)."][money_received_flg]' value='1' checked /></td>";
          }else{
            echo "<td><input type='checkbox' name='data[EstimateDtlTrn][".($i+1)."][money_received_flg]' value='1' /></td>";
          }
        }

     echo "<!--  商品分類 -->".
        "<td>";
               for($j=0;$j < count($goods_ctg_list);$j++)
               {
                  $atr = $goods_ctg_list[$j];
                  if($data[$i]['EstimateDtlTrnView']['goods_ctg_id'] == $atr['GoodsCtgMst']['id']){
                  	 echo $atr['GoodsCtgMst']['goods_ctg_nm'];
                  }
                }
    echo "</td>".

         "<!--  商品区分 -->".
         "<td>".$data[$i]['EstimateDtlTrnView']['goods_kbn_nm']."</td>".

        "<!--  商品名 -->".
         "<td>".
             "<input    type='hidden' class='goods'  id='goods_id".($i+1)."' name='data[EstimateDtlTrn][".($i+1)."][goods_id]' value='{$data[$i]['EstimateDtlTrnView']['goods_id']}' />".
             "<textarea  class='validate[required maxSize[500]] small-inputcomment sales_goods_nm' id='sales_goods_nm".($i+1)."' name='data[EstimateDtlTrn][".($i+1)."][sales_goods_nm]' wrap='soft'>{$data[$i]['EstimateDtlTrnView']['sales_goods_nm']}</textarea>".
             "<input type='hidden' id='original_goods_nm".($i+1)."' value='' />".
        "</td>".
       "<!--  ベンダー  -->".
        "<td id='vendor_nm".($i+1)."'>{$data[$i]['EstimateDtlTrnView']['vendor_nm']}</td>".
        "<!--  数量 -->".
        "<td>".
         "<select class='num' name='data[EstimateDtlTrn][".($i+1)."][num]'>";
	         for($j=1;$j < 100;$j++)
             {
               if($data[$i]['EstimateDtlTrnView']['num']==$j)
               {
              	echo "<option value='$j' selected='selected'>$j</option>";
               }
               else
               {
              	echo "<option value='$j'>$j</option>";
               }
             }
  echo  "</select>".
        "</td>";

        //価格計算等の準備
         $rate = $data[$i]['EstimateDtlTrnView']['sales_exchange_rate'];
         $cost_rate = $data[$i]['EstimateDtlTrnView']['cost_exchange_rate'];
         $num = $data[$i]['EstimateDtlTrnView']['num'];
         $aw_rate = $data[$i]['EstimateDtlTrnView']['aw_share'];
         $rw_rate = $data[$i]['EstimateDtlTrnView']['rw_share'];
         $unit_price=0;
         $amount_price=0;
         $unit_cost=0;
         $cost=0;
         $net=0;
         $profit_rate=0;
         $aw_share=0;
         $rw_share=0;
         $foreign_unit_price=0;
         $foreign_amount_price=0;
         $foreign_unit_cost=0;
         $foreign_cost=0;
         $foreign_net=0;
         $foreign_profit_rate=0;
         $foreign_aw_share=0;
         $foreign_rw_share=0;

         //ドルベース
         if($data[$i]['EstimateDtlTrnView']['currency_kbn']==0)
         {
         	$foreign_unit_price = $data[$i]['EstimateDtlTrnView']['sales_price'];
            $foreign_amount_price = $foreign_unit_price * $num;
            $foreign_unit_cost = $data[$i]['EstimateDtlTrnView']['sales_cost'];
            $foreign_cost = $foreign_unit_cost * $num;
            $foreign_net = $foreign_amount_price - $foreign_cost;
            $foreign_aw_share = $foreign_net * $aw_rate;
            $foreign_rw_share = $foreign_net * $rw_rate;
            if($foreign_amount_price != 0){
            	$foreign_profit_rate = round($foreign_net / $foreign_amount_price * 100);
            }

            $unit_price = round($data[$i]['EstimateDtlTrnView']['sales_price'] * $rate);
            $amount_price = $unit_price * $num;
            $unit_cost = round($data[$i]['EstimateDtlTrnView']['sales_cost'] * $cost_rate);
            $cost = $unit_cost * $num;
            $net = $amount_price - $cost;
            $aw_share = $net * $aw_rate;
            $rw_share = $net * $rw_rate;
            if($amount_price != 0){
            	$profit_rate = round($net / $amount_price * 100);
            }
         }
         //円ベース
         else
         {
         	$unit_price = round($data[$i]['EstimateDtlTrnView']['sales_price']);
            $amount_price = $unit_price * $num;
            $unit_cost = round($data[$i]['EstimateDtlTrnView']['sales_cost']);
            $cost = $unit_cost * $num;
            $net = $amount_price - $cost;
            $aw_share = $net * $aw_rate;
            $rw_share = $net * $rw_rate;
            if($amount_price != 0){
            	$profit_rate = round($net / $amount_price * 100);
            }

            $foreign_unit_price = round($data[$i]['EstimateDtlTrnView']['sales_price'] / $rate,2);
            $foreign_amount_price = $foreign_unit_price * $num;
            $foreign_unit_cost = round($data[$i]['EstimateDtlTrnView']['sales_cost'] / $cost_rate,2);
            $foreign_cost = $foreign_unit_cost * $num;
            $foreign_net = $foreign_amount_price - $foreign_cost;
            $foreign_aw_share = $foreign_net * $aw_rate;
            $foreign_rw_share = $foreign_net * $rw_rate;
            if($foreign_amount_price != 0){
            	$foreign_profit_rate = round($foreign_net / $foreign_amount_price * 100);
            }
         }

         echo   "<!-- 総代価 -->".
         		"<td id='amount_price".($i+1)."' class='yen short''>".number_format($amount_price)."</td>";

         //外貨
         if($data[$i]['EstimateDtlTrnView']['currency_kbn'] == 0)
         {
             echo   "<!-- 単価 -->".
                    "<td class='yen short'>".
                        "<input type='text'   id='unit_price".($i+1)."' class='validate[required,custom[onlyInteger]] inputdisable inputnumeric unitPrice' name='data[EstimateDtlTrn][".($i+1)."][sales_price]'  value='".number_format($unit_price)."' disabled />".
                        "<input type='hidden' id='original_unit_price".($i+1)."' value='' />".
                    "</td>".
                    "<!-- 原価 -->".
                    "<td class='yen short'>".
                       "<input type='text''  id='unit_cost".($i+1)."' class='validate[required,custom[onlyInteger]] inputdisable inputnumeric unitCost' name='data[EstimateDtlTrn][".($i+1)."][sales_cost]'  value='".number_format($unit_cost)."' disabled />".
                       "<input type='hidden' id='original_unit_cost".($i+1)."' value='' />".
                    "</td>";
         }
         //邦貨
         else
         {
         	  echo   "<!-- 単価 -->".
         	         "<td class='yen short'>".
         	            "<input type='text'   id='unit_price".($i+1)."' class='validate[required,custom[onlyNumber]] inputnumeric unitPrice' name='data[EstimateDtlTrn][".($i+1)."][sales_price]'  value='".number_format($unit_price)."' />".
                        "<input type='hidden' id='original_unit_price".($i+1)."' value='".$data[$i]['EstimateDtlTrnView']['original_sales_price']."' />".
                     "</td>".
         	         "<!-- 原価 -->";
         	    /* セット商品の場合は原価変更不可とする */
              if($data[$i]['EstimateDtlTrnView']['set_goods_kbn'] == SET_GOODS){
                 echo   "<td class='yen short'>".
                          "<input type='text''  id='unit_cost".($i+1)."' class='inputdisable validate[required,custom[onlyNumber]] inputnumeric unitCost' name='data[EstimateDtlTrn][".($i+1)."][sales_cost]'  value='".number_format($unit_cost)."' readonly  />".
                          "<input type='hidden' id='original_unit_cost".($i+1)."' value='".$data[$i]['EstimateDtlTrnView']['original_sales_cost']."' />".
                        "</td>";
              }else{
              	 echo   "<td class='yen short'>".
                          "<input type='text''  id='unit_cost".($i+1)."' class='validate[required,custom[onlyNumber]] inputnumeric unitCost' name='data[EstimateDtlTrn][".($i+1)."][sales_cost]'  value='".number_format($unit_cost)."' />".
                          "<input type='hidden' id='original_unit_cost".($i+1)."' value='".$data[$i]['EstimateDtlTrnView']['original_sales_cost']."' />".
                        "</td>";
              }
         }

 echo   "<!-- 総原価 -->".
        "<td id='cost".($i+1)."' class='yen'>".number_format($cost)."</td>".
        "<!-- 利益 -->".
        "<td id='net".($i+1)."' class='yen'>".number_format($net)."</td>".
        "<!-- 利益率 -->".
        "<td id='profit_rate".($i+1)."' class='yen'>".number_format($profit_rate)."%</td>".
        "<!-- awシェア-->".
        "<td id='aw_share".($i+1)."' class='yen'>".number_format($aw_share)."</td>".
        "<!-- rwシェア -->".
        "<td id='rw_share".($i+1)."' class='yen'>".number_format($rw_share)."</td>";

         echo  "<!-- 総代価 (外貨)-->".
 		       "<td id='foreign_amount_price".($i+1)."' class='dollar'>".number_format($foreign_amount_price,2)."</td>";
         //外貨
         if($data[$i]['EstimateDtlTrnView']['currency_kbn'] == 0)
         {
             echo   "<!-- 単価(外貨) -->".
                    "<td class='dollar short'>".
                        "<input type='text'   id='foreign_unit_price".($i+1)."' class='inputnumeric unitForeignPrice' name='data[EstimateDtlTrn][".($i+1)."][foreign_sales_price]'  value='".number_format($foreign_unit_price,2)."' />".
                        "<input type='hidden' id='foreign_original_unit_price".($i+1)."' value='".$data[$i]['EstimateDtlTrnView']['original_sales_price']."' />".
                    "</td>".
                    "<!-- 原価(外貨) -->";
             /* セット商品の場合は原価変更不可とする */
             if($data[$i]['EstimateDtlTrnView']['set_goods_kbn'] == SET_GOODS){

                echo "<td class='dollar short'>".
                       "<input type='text''  id='foreign_unit_cost".($i+1)."' class='inputdisable inputnumeric unitForeignCost' name='data[EstimateDtlTrn][".($i+1)."][foreign_sales_cost]'  value='".number_format($foreign_unit_cost,2)."' readonly/>".
                       "<input type='hidden' id='foreign_original_unit_cost".($i+1)."'  value='".$data[$i]['EstimateDtlTrnView']['original_sales_cost']."' />".
                     "</td>";
             }else{
                echo "<td class='dollar short'>".
                       "<input type='text''  id='foreign_unit_cost".($i+1)."' class='inputnumeric unitForeignCost' name='data[EstimateDtlTrn][".($i+1)."][foreign_sales_cost]'  value='".number_format($foreign_unit_cost,2)."' />".
                       "<input type='hidden' id='foreign_original_unit_cost".($i+1)."'  value='".$data[$i]['EstimateDtlTrnView']['original_sales_cost']."' />".
                     "</td>";
             }
         }
         //邦貨
         else
         {
             echo   "<!-- 単価(外貨) -->".
                    "<td class='dollar short'>".
                        "<input type='text'   id='foreign_unit_price".($i+1)."' class='inputdisable inputnumeric unitForeignPrice' name='data[EstimateDtlTrn][".($i+1)."][foreign_sales_price]'  value='".number_format($foreign_unit_price,2)."' disabled />".
                        "<input type='hidden' id='foreign_original_unit_price".($i+1)."' value='' />".
                    "</td>".
                    "<!-- 原価(外貨) -->".
                    "<td class='dollar short'>".
                       "<input type='text''  id='foreign_unit_cost".($i+1)."' class='inputdisable inputnumeric unitForeignCost' name='data[EstimateDtlTrn][".($i+1)."][foreign_sales_cost]'  value='".number_format($foreign_unit_cost,2)."' disabled />".
                       "<input type='hidden' id='foreign_original_unit_cost".($i+1)."' value='' />".
                    "</td>";
         }
echo    "<!-- 総原価 (外貨)-->".
        "<td id='foreign_cost".($i+1)."' class='dollar'>".number_format($foreign_cost,2)."</td>".
        "<!-- 利益(外貨)-->".
        "<td id='foreign_net".($i+1)."' class='dollar'>".number_format($foreign_net,2)."</td>".
        "<!-- 利益率(外貨) -->".
        "<td id='foreign_profit_rate".($i+1)."' class='dollar short'>".number_format($foreign_profit_rate,2)."%</td>".
        "<!-- awシェア (外貨)-->".
        "<td id='foreign_aw_share".($i+1)."' class='dollar'>".number_format($foreign_aw_share,2)."</td>".
        "<!-- rwシェア(外貨)-->".
        "<td id='foreign_rw_share".($i+1)."' class='dollar'>".number_format($foreign_rw_share,2)."</td>".

       "<!-- awレート-->".
        "<td><input type='text'' id='aw_rate".($i+1)."' class='inputnumeric awRate' name='data[EstimateDtlTrn][".($i+1)."][aw_share]' value='".($data[$i]['EstimateDtlTrnView']['aw_share']*100)."' />%</td>".
        "<!-- rwレート-->".
        "<td><input type='text'' id='rw_rate".($i+1)."' class='inputnumeric rwRate' name='data[EstimateDtlTrn][".($i+1)."][rw_share]' value='".($data[$i]['EstimateDtlTrnView']['rw_share']*100)."' />%</td>".

      "<!-- 販売為替レート-->".
        "<td><input type='text' id='sales_exchange_rate".($i+1)."' class='inputnumeric salesExchangeRate'".
                   "name='data[EstimateDtlTrn][".($i+1)."][sales_exchange_rate]'  value='".$data[$i]['EstimateDtlTrnView']['sales_exchange_rate']."' />".
            "<input type='hidden' id='currency_kbn".($i+1)."' name='data[EstimateDtlTrn][".($i+1)."][currency_kbn]'  value='".$data[$i]['EstimateDtlTrnView']['currency_kbn']."' />".
        "</td>".
      "<!-- 原価為替レート-->".
        "<td><input type='text' id='cost_exchange_rate".($i+1)."' class='validate[required] inputnumeric costExchangeRate'".
                   "name='data[EstimateDtlTrn][".($i+1)."][cost_exchange_rate]'  value='".$data[$i]['EstimateDtlTrnView']['cost_exchange_rate']."' />".
        "</td>".

      "<!--  支払区分 -->".
         "<td>".
             "<select id='payment_kbn".($i+1)."' class='payment_kbn' name='data[EstimateDtlTrn][".($i+1)."][payment_kbn_id]'>";
                 for($payment_kbn_index=0;$payment_kbn_index < count($payment_kbn_list);$payment_kbn_index++){
                   $atr = $payment_kbn_list[$payment_kbn_index];
                   if($data[$i]['EstimateDtlTrnView']['payment_kbn_id'] == $atr['PaymentKbnMst']['id']){
                      echo "<option value='{$atr['PaymentKbnMst']['id']}' selected>{$atr['PaymentKbnMst']['payment_kbn_nm']}</option>";
                   }else{
                   	  echo "<option value='{$atr['PaymentKbnMst']['id']}'>{$atr['PaymentKbnMst']['payment_kbn_nm']}</option>";
                   }
                 }
    echo    "</select>".
         "</td>".
    "</tr>";
  }
?>
        <tr class="nodrag nodrop">
            <td>&nbsp;</td>
            <td>&nbsp;</td>
            <td>&nbsp;</td>
            <?php  if($data[0]['EstimateDtlTrnView']['adopt_flg'] == ESTIMATE_ADOPTED){ echo "<td>&nbsp;</td>";} ?>

            <td align="right">SUBTOTAL</td>
            <td>&nbsp;</td>
            <td>&nbsp;</td>
            <td>&nbsp;</td>

            <td class="yen short" id="sub_total_amount_price" >&nbsp;</td>
            <td class="yen short">&nbsp;</td>
            <td class="yen short">&nbsp;</td>
            <td class="yen" id="sub_total_cost" >&nbsp;</td>
            <td class="yen" id="sub_total_net" >&nbsp;</td>
            <td class="yen" id="sub_total_profit_rate" >&nbsp;</td>
            <td class="yen" id="sub_total_aw" >&nbsp;</td>
            <td class="yen" id="sub_total_rw" >&nbsp;</td>

            <td class="dollar" id="sub_total_amount_foreign_price" >&nbsp;</td>
            <td class="dollar short">&nbsp;</td>
            <td class="dollar short">&nbsp;</td>
            <td class="dollar" id="sub_total_foreign_cost" >&nbsp;</td>
            <td class="dollar" id="sub_total_foreign_net" >&nbsp;</td>
            <td class="dollar short" id="sub_total_foreign_profit_rate" >&nbsp;</td>
            <td class="dollar" id="sub_total_foreign_aw" >&nbsp;</td>
            <td class="dollar" id="sub_total_foreign_rw" >&nbsp;</td>

            <td>&nbsp;</td>
            <td>&nbsp;</td>
            <td>&nbsp;</td>
            <td>&nbsp;</td>
            <td>&nbsp;</td>
        </tr>
        <tr class="nodrag nodrop">
            <td>&nbsp;</td>
            <td>&nbsp;</td>
            <td>&nbsp;</td>
            <?php  if($data[0]['EstimateDtlTrnView']['adopt_flg'] == ESTIMATE_ADOPTED){ echo "<td>&nbsp;</td>";} ?>

            <td align="right"><span id=fontSizeTest>ハワイ州税</span></td>
            <td align="left"><input type="text" id="taxRate" class="inputnumeric" name="data[EstimateTrn][hawaii_tax_rate]"  value="<?php echo $data[0]['EstimateDtlTrnView']['hawaii_tax_rate']*100 ?>" />%</td>
            <td>&nbsp;</td>
            <td>&nbsp;</td>

            <td class="yen short" id="sub_total_amount_price_with_tax" >&nbsp;</td>
            <td class="yen short">&nbsp;</td>
            <td class="yen short">&nbsp;</td>
            <td class="yen">&nbsp;</td>
            <td class="yen">&nbsp;</td>
            <td class="yen">&nbsp;</td>
            <td class="yen">&nbsp;</td>
            <td class="yen">&nbsp;</td>

            <td class="dollar" id="sub_total_amount_foreign_price_with_tax" >&nbsp;</td>
            <td class="dollar short">&nbsp;</td>
            <td class="dollar short">&nbsp;</td>
            <td class="dollar">&nbsp;</td>
            <td class="dollar">&nbsp;</td>
            <td class="dollar short">&nbsp;</td>
            <td class="dollar">&nbsp;</td>
            <td class="dollar">&nbsp;</td>

            <td>&nbsp;</td>
            <td>&nbsp;</td>
            <td>&nbsp;</td>
            <td>&nbsp;</td>
            <td>&nbsp;</td>
        </tr>
        <tr class="nodrag nodrop">
            <td>&nbsp;</td>
            <td>&nbsp;</td>
            <td>&nbsp;</td>
            <?php  if($data[0]['EstimateDtlTrnView']['adopt_flg'] == ESTIMATE_ADOPTED){ echo "<td>&nbsp;</td>";} ?>

            <td align="right"><input type="text" id="serviceRateName"  class="" name="data[EstimateTrn][service_rate_nm]"  value="<?php echo $data[0]['EstimateDtlTrnView']['service_rate_nm'] ?>" /></td>
            <td align="left"><input type="text" id="serviceRate" class="validate[required,integer,max[100],min[0]] inputnumeric" name="data[EstimateTrn][service_rate]"  value="<?php echo $data[0]['EstimateDtlTrnView']['service_rate']*100 ?>" />%</td>
            <td>&nbsp;</td>
            <td>&nbsp;</td>

            <td class="yen short" id="sub_total_amount_price_with_arrange" >&nbsp;</td>
            <td class="yen short">&nbsp;</td>
            <td class="yen short">&nbsp;</td>
            <td class="yen">&nbsp;</td>
            <td class="yen">&nbsp;</td>
            <td class="yen">&nbsp;</td>
            <td class="yen">&nbsp;</td>
            <td class="yen" id="sub_total_rw_with_arrange" >&nbsp;</td>

            <td class="dollar" id="sub_total_amount_foreign_price_with_arrange" >&nbsp;</td>
            <td class="dollar short">&nbsp;</td>
            <td class="dollar short">&nbsp;</td>
            <td class="dollar">&nbsp;</td>
            <td class="dollar">&nbsp;</td>
            <td class="dollar short">&nbsp;</td>
            <td class="dollar">&nbsp;</td>
            <td class="dollar" id="sub_total_foreign_rw_with_arrange" >&nbsp;</td>

            <td>&nbsp;</td>
            <td>&nbsp;</td>
            <td>&nbsp;</td>
            <td>&nbsp;</td>
            <td>&nbsp;</td>
        </tr>
        <tr class="nodrag nodrop">
            <td>&nbsp;</td>
            <td>&nbsp;</td>
            <td>&nbsp;</td>
            <?php  if($data[0]['EstimateDtlTrnView']['adopt_flg'] == ESTIMATE_ADOPTED){ echo "<td>&nbsp;</td>";} ?>

            <td align="right">SUBTOTAL</td>
            <td>&nbsp;</td>
            <td>&nbsp;</td>
            <td>&nbsp;</td>

            <td class="yen short" id="mid_total_amount_price" >&nbsp;</td>
            <td class="yen short">&nbsp;</td>
            <td class="yen short">&nbsp;</td>
            <td class="yen" id="mid_total_cost" >&nbsp;</td>
            <td class="yen" id="mid_total_net" >&nbsp;</td>
            <td class="yen" id="mid_total_profit_rate" >&nbsp;</td>
            <td class="yen" id="mid_total_aw" >&nbsp;</td>
            <td class="yen" id="mid_total_rw" >&nbsp;</td>

            <td class="dollar" id="mid_total_amount_foreign_price" >&nbsp;</td>
            <td class="dollar short">&nbsp;</td>
            <td class="dollar short">&nbsp;</td>
            <td class="dollar" id="mid_total_foreign_cost" >&nbsp;</td>
            <td class="dollar" id="mid_total_foreign_net" >&nbsp;</td>
            <td class="dollar short" id="mid_total_foreign_profit_rate" >&nbsp;</td>
            <td class="dollar" id="mid_total_foreign_aw" >&nbsp;</td>
            <td class="dollar" id="mid_total_foreign_rw" >&nbsp;</td>

            <td>&nbsp;</td>
            <td>&nbsp;</td>
            <td>&nbsp;</td>
            <td>&nbsp;</td>
            <td>&nbsp;</td>
        </tr>
        <tr class="nodrag nodrop">
            <td>&nbsp;</td>
            <td>&nbsp;</td>
            <td>&nbsp;</td>
            <?php  if($data[0]['EstimateDtlTrnView']['adopt_flg'] == ESTIMATE_ADOPTED){ echo "<td>&nbsp;</td>";} ?>

            <td align="right"><input type="text" id="discountRateName"  class="" name="data[EstimateTrn][discount_rate_nm]"
                                     value="<?php echo $data[0]['EstimateDtlTrnView']['discount_rate_nm'] ?>" /></td>
            <td align="left"><input type="text" id="discountRate" class="validate[required,integer,max[100],min[0]] inputnumeric" name="data[EstimateTrn][discount_rate]"  value="<?php echo $data[0]['EstimateDtlTrnView']['discount_rate']*100 ?>" />%</td>
            <td>&nbsp;</td>
            <td>&nbsp;</td>

            <td class="yen short minus" id="total_amount_price_with_discount" >&nbsp;</td>
            <td class="yen short">&nbsp;</td>
            <td class="yen short">&nbsp;</td>
            <td class="yen">&nbsp;</td>
            <td class="yen">&nbsp;</td>
            <td class="yen">&nbsp;</td>
            <td class="yen minus" id="total_aw_with_discount">&nbsp;</td>
            <td class="yen minus" id="total_rw_with_discount">&nbsp;</td>

            <td class="dollar minus" id="total_amount_foreign_price_with_discount" >&nbsp;</td>
            <td class="dollar short">&nbsp;</td>
            <td class="dollar short">&nbsp;</td>
            <td class="dollar">&nbsp;</td>
            <td class="dollar">&nbsp;</td>
            <td class="dollar short">&nbsp;</td>
            <td class="dollar minus" id="total_foreign_aw_with_discount">&nbsp;</td>
            <td class="dollar minus" id="total_foreign_rw_with_discount">&nbsp;</td>

            <td rowspan="2"><input type="text" id="discount_aw_share" class="inputnumeric" name="data[EstimateTrn][discount_aw_share]" value="<?php echo $data[0]['EstimateDtlTrnView']['discount_aw_share']*100 ?>" />%</td>
            <td rowspan="2"><input type="text" id="discount_rw_share" class="inputnumeric" name="data[EstimateTrn][discount_rw_share]" value="<?php echo $data[0]['EstimateDtlTrnView']['discount_rw_share']*100 ?>" />%</td>

            <td>&nbsp;</td>
            <td>&nbsp;</td>
            <td>&nbsp;</td>
        </tr>
        <tr class="nodrag nodrop">
            <td>&nbsp;</td>
            <td>&nbsp;</td>
            <td>&nbsp;</td>
            <?php  if($data[0]['EstimateDtlTrnView']['adopt_flg'] == ESTIMATE_ADOPTED){ echo "<td>&nbsp;</td>";} ?>

            <td align="right"><input type="text" id="discountName"  class="" name="data[EstimateTrn][discount_nm]"
                                     value="<?php echo $data[0]['EstimateDtlTrnView']['discount_nm'] ?>" /></td>
            <td align="left"><input type="text" id="discount" class="validate[required,integer] inputnumeric" name="data[EstimateTrn][discount]"  value="<?php echo $data[0]['EstimateDtlTrnView']['discount'] ?>" /></td>
            <td>割引額為替レート</td>
            <td><input type="text" id="discount_exchange_rate" class="inputnumeric" name="data[EstimateTrn][discount_exchange_rate]" value="<?php echo $data[0]['EstimateDtlTrnView']['discount_exchange_rate'] ?>" /></td>
            <td class="yen short minus" id="total_amount_price_with_discount_currency" >&nbsp;</td>
            <td class="yen short">&nbsp;</td>
            <td class="yen short">&nbsp;</td>

            <td class="yen">&nbsp;</td>
            <td class="yen">&nbsp;</td>
            <td class="yen">&nbsp;</td>
            <td class="yen  minus" id="total_aw_with_discount_currency">&nbsp;</td>
            <td class="yen  minus" id="total_rw_with_discount_currency" >&nbsp;</td>

            <td class="dollar minus" id="total_amount_foreign_price_with_discount_currency" >&nbsp;</td>
            <td class="dollar short">&nbsp;</td>
            <td class="dollar short">&nbsp;</td>
            <td class="dollar">&nbsp;</td>
            <td class="dollar">&nbsp;</td>
            <td class="dollar short">&nbsp;</td>
            <td class="dollar minus" id="total_foreign_aw_with_discount_currency" >&nbsp;</td>
            <td class="dollar minus" id="total_foreign_rw_with_discount_currency" >&nbsp;</td>

            <td>&nbsp;</td>
            <td>&nbsp;</td>
            <td>&nbsp;</td>
        </tr>
        <tr class="nodrag nodrop">
            <td>&nbsp;</td>
            <td>&nbsp;</td>
            <td>&nbsp;</td>
           <?php  if($data[0]['EstimateDtlTrnView']['adopt_flg'] == ESTIMATE_ADOPTED){ echo "<td>&nbsp;</td>";} ?>

            <td align="right">TOTAL</td>
            <td>&nbsp;</td>
            <td>&nbsp;</td>
            <td>&nbsp;</td>

            <td class="yen short" id="total_amount_price" >&nbsp;</td>
            <td class="yen short">&nbsp;</td>
            <td class="yen short">&nbsp;</td>
            <td class="yen" id="total_cost" >&nbsp;</td>
            <td class="yen" id="total_net">&nbsp;</td>
            <td class="yen" id="total_profit_rate" >&nbsp;</td>
            <td class="yen" id="total_aw" >&nbsp;</td>
            <td class="yen" id="total_rw" >&nbsp;</td>

            <td class="dollar" id="total_amount_foreign_price" >&nbsp;</td>
            <td class="dollar short">&nbsp;</td>
            <td class="dollar short">&nbsp;</td>
            <td class="dollar" id="total_foreign_cost" >&nbsp;</td>
            <td class="dollar" id="total_foreign_net" >&nbsp;</td>
            <td class="dollar short" id="total_foreign_profit_rate" >&nbsp;</td>
            <td class="dollar" id="total_foreign_aw" >&nbsp;</td>
            <td class="dollar" id="total_foreign_rw" >&nbsp;</td>

            <td>&nbsp;</td>
            <td>&nbsp;</td>
            <td>&nbsp;</td>
            <td>&nbsp;</td>
            <td>&nbsp;</td>
            <td>&nbsp;</td>
        </tr>
</table>
</div>
<div class="submit">
<input type="submit"  class="add_row inputbutton" value=" 項目追加 " />
</div>
    <input type="hidden" name="data[EstimateTrn][id]" value="<?php echo $data[0]['EstimateDtlTrnView']['estimate_id'] ?>" />
    <input type="hidden" name="data[EstimateTrn][customer_id]" value="<?php echo $data[0]['EstimateDtlTrnView']['customer_id'] ?>" />

	<div class="submit">
	    <!-- <input type="submit" class="inputbutton" value=" 複製して登録   "  name="copy" /> -->
          <?php
          if($customer_status == CS_ESTIMATED || $customer_status == CS_CONTRACTING || $customer_status == CS_CONTRACTED ||
             $customer_status == CS_INVOICED || $customer_status ==CS_UNPAIED || $customer_status ==CS_PAIED){

	          if($data[0]['EstimateDtlTrnView']['adopt_flg'] == ESTIMATE_ADOPTED){
                 echo "<input type='submit' class='inputbutton' value=' 更新 '  name='update' /> ";
              }else{
           	     echo "<input type='submit' id='publish' class='inputbutton' value=' 更新 '     name='normal_update' />";
                 echo "<input type='submit'              class='inputbutton' value=' 採用更新 '  name='adopt_update' /> ";
              }

              if($customer_status == CS_ESTIMATED || $customer_status == CS_CONTRACTING){
                 echo "<input type='submit' id='delete' class='inputbutton' value=' 削除'  name='delete' />";
              }
         }
         ?>
         <!-- 商品区分選択人の文字列幅取得用 -->
        <span id="hidden" style="display:none;"></span>
	</div>
</form>
<div id="result_dialog"  style="display:none"><p id="result_message"><img src="#" alt="" /><span></span></p><p id="error_reason"></p></div>
<div id="confirm_dialog" style="display:none"><p><img src="<?php echo $html->webroot("/images/warning_result.png") ?>" alt="" />採用済みの見積もりの場合、関連するデータ(ファイナルシート等)も削除されますがよろしいですか？</p></div>
<div id="critical_error"></div>
<div id="estimate_mode" class="edit_mode" style="display:none;"></div>
