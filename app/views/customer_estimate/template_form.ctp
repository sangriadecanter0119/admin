<script type='text/javascript'>
$(function(){
    $("input:submit").button();  

    /* テンプレートダイアログ */
	 $("#template_dialog").dialog({
	             buttons: [{
                   text:"適用",
                   id:"update_button",
                   click:function(){
	            	//更新開始 
                    //StartSubmit("UPDATE",$("#template_list").getGridParam('selrow'));
                 	  location.href ="addEstimate?id=" + $("#template_list").getGridParam('selrow');                	 
	               }
		         },
		         {
	                text:"削除",
	                id:"delete_button",
	                click:function(){
                         $("#partial_confirm_dialog").dialog('open');
		            }
			     },
	             {
	                 text: "CANCEL",
	                 click: function () {                     
	                     $("#template_dialog").dialog('close');
	                 }
	             }],
	             beforeClose:function(){
	                $("#template_dialog").remove();
	             },
	             draggable: false,
	             autoOpen: true,
	             resizable: false,
	             zIndex: 2000,
	             width:650,
	             height:488,
	             position:[($(window).width() / 2) -  (650 / 2) ,($(window).height() / 2) -  (488 / 2)],
	             modal: true,
	             title: "見積テンプレートフォーム"
	 });
	 
     /* 処理結果用ダイアログ */
    $("#partial_result_dialog").dialog({
             buttons: [{
                 text: "OK",
                 click: function () {                     
            	   //再度読み込む      
	               $("#template_list").jqGrid("setGridParam", {datatype: 'json'}).trigger("reloadGrid");     
                   $("#partial_result_dialog").dialog('close');                   
                 }
             }],            
              beforeClose: function (event, ui) {
                  $("#partial_result_message span").text("");    
		          $("#partial_error_reason").text("");    
             },
             draggable: false,
             autoOpen: false,
             resizable: false,
             zIndex: 2000,
             modal: true,
             title: "処理結果"
    });

    /* 確認用ダイアログ */ 
    $("#partial_confirm_dialog").dialog({
             buttons: [{
                 text: "OK",
                 click: function () {                  
                     $("#partial_confirm_dialog").dialog('close');
                     //削除開始 
                     StartSubmit("DELETE",$("#template_list").getGridParam('selrow'));                    
                 }
             },
             {
                 text: "CANCEL",
                 click: function () {                     
                     $("#partial_confirm_dialog").dialog('close');
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

    /* 見積名入力ダイアログ */ 
    $("#partial_input_dialog").dialog({
             buttons: [{
                 text: "OK",
                 click: function () {                  
                     $("#partial_input_dialog").dialog('close');
                     //追加開始 
                     StartSubmit("CREATE",$("#new_template_name").val());                    
                 }
             },
             {
                 text: "CANCEL",
                 click: function () {                     
                     $("#partial_input_dialog").dialog('close');
                 }
             }],
             draggable: false,
             autoOpen: false,
             resizable: false,
             zIndex: 2000,
             width:"350px",
             modal: true,
             title: "入力フォーム"
   });
  
    $("#template_type").change(function(){
        
        if($(this).val() == "create"){
           $("#template_name").attr("disabled",false);
            $("#template_name").removeClass("inputdisable");
        }else{
           $("#template_name").attr("disabled",true);
           $("#template_name").addClass("inputdisable");
        }
      
      });

    /*  テンプレート選択用テーブル
    --------------------------------------------------------*/
    jQuery("#template_list").jqGrid({
        url: <?php echo "'".$html->url('feed').'/template_list'."'" ?>,
        datatype: 'json',
        mtype: 'POST',
        colNames: ['見積テンプレート名', '登録日','登録者','更新日','更新者'],
        colModel: [
                   { name: 'TemplateName' , index: 'TemplateName' , width: 200 },                      
                   { name: 'RegDate'      , index: 'RegDate'      , width: 130 },
                   { name: 'RegName'      , index: 'RegName'      , width: 130 },   
                   { name: 'UpdDate'      , index: 'UpdDate'      , width: 130 },   
                   { name: 'UpdName'      , index: 'updName'      , width: 130 },                
                  ],         
        onSelectRow: function (id) {              
            if (id !== null) {
            	$("#feed_button").attr("disabled",false);
            	//見積追加モードの時のみ採用ボタンを有効にする
            	if($("#estimate_mode").hasClass("add_mode")){
                   $("#update_button").attr("disabled",false);
            	}
                $("#delete_button").attr("disabled",false);              
            }             
        },
        loadComplete: function (data) {           
        },
        loadError: function(xhr, status, error) {
            alert("データの読み込みに失敗しました。 "+error);
        },        
        pager: $('#template_list_pager'),
        viewrecords: true,
        rowNum: 20,
        rowList: [10, 20, 30, 40, 50],        
        //Sort、Paging、Search機能をローカルのみで行う
        loadonce: true,
        emptyrecords: "NO RECORDS HERE",       
        //Colunmの移動 *少しソート機能に影響がでる？
       // sortable: true,
        viewrecords: true,
        imgpath: 'themes/basic/images',
        autowidth: true,      
        height: '290'
        //rownumbers: true
    });

    $("#template_list")
    .navGrid('#template_list_pager',{refresh:false,edit:false,add:false,del:false,search:false})
    .jqGrid(
  		    'navButtonAdd',
  		    '#template_list_pager',
  		    {
  		        caption: "見積追加",
  		        buttonicon: "ui-icon-plus",    		      	        
  		        onClickButton: function () {
  		    	    $("#partial_input_dialog").dialog('open');
  		        }
  		    }
     );

    $("#feed_button").attr("disabled",true);
    $("#update_button").attr("disabled",true);
    $("#delete_button").attr("disabled",true);
});

function StartSubmit(type,id){

	$(this).simpleLoading('show');

	$("#feed_button").attr("disabled",true);
    $("#update_button").attr("disabled",true);
    $("#delete_button").attr("disabled",true);
    
	var formData = null;
    if(type == "CREATE" || type=="UPDATE"){
         formData = $("#formID").serialize();
         formData += "&" + encodeURI("data[EstimateTrn][template_nm]=" + $("#new_template_name").val());
    }

    $.post(<?php echo "'".$html->url('templateForm')."'" ?> + "/" + type + "/" + id , formData , function(result) {
	
        $(this).simpleLoading('hide');

	    var obj = null;
        try {
           obj = $.parseJSON(result);    
         } catch(e) {  
   	
   	       obj = {};
           obj.result = false;
	       obj.message = "致命的なエラーが発生しました。";
	       obj.reason  = "このダイアログを閉じた後、画面のスクリーンショットを保存して管理者にお問い合わせ下さい。";
	       $("#partial_critical_error").text(result);
         }
   
	     if(obj.result == true){		   
	       $("#partial_result_message img").attr('src',<?php echo "'".$html->webroot("/images/confirm_result.png")."'" ?>);	 		  	  		
	     }else{
	       $("#partial_result_message img").attr('src',<?php echo "'".$html->webroot("/images/error_result.png")."'" ?>);	  
	     }    
           $("#partial_result_message span").text(obj.message);    
           $("#partial_error_reason").text(obj.reason);     
           $("#partial_result_dialog").dialog('open');          
   });	
}
</script>

<div id="template_dialog">
  <table id="template_list"        ></table> 
  <div   id="template_list_pager"  style="text-align:center;"></div>
 
  <div id="partial_result_dialog"  style="display:none"><p id="partial_result_message"><img src="#" alt="" /><span></span></p><p id="partial_error_reason"></p></div>
  <div id="partial_critical_error"></div>
  <div id="partial_confirm_dialog" style="display:none"><p><img src="<?php echo $html->webroot("/images/warning_result.png") ?>" alt="" />データを削除しますがよろしいですか？</p></div>
  <div id="partial_input_dialog" style="display:none"><p>見積テンプレート名を入力して下さい。</p><input type="text" id="new_template_name" style="width:300px"/></div>
</div>