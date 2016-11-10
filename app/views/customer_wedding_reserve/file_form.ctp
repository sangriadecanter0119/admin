<div id="file_list_dialog">
<script type='text/javascript'>
 $(function(){


	  /* 確認用ダイアログ */
	    $("#partial_confirm_dialog").dialog({
	             buttons: [{
	                 text: "OK",
	                 click: function () {
	                     $("#partial_confirm_dialog").dialog('close');
	                     $(this).simpleLoading('show');
			    	   		var ids = $("#file_list").getGridParam('selarrrow');

		               		var params ="";
	                		for(var i=0;i < ids.length;i++){
	             	  		 // var rowData = jQuery('#file_list').jqGrid('getRowData', ids[i]);
	             	  		  //params += "data[file]["+i+"]="+rowData.url +"&";
	             	  	 	 // params += "data[file]["+i+"]="+rowData.FullUrl +"&";
                                params += "data[file]["+i+"]="+ids[i]+"&";
	                		}

			    	    	$.post(<?php echo "'".$html->url('deleteFile').'/'.$category_nm."'"?>,params,function(result){

		  		    		  $(this).simpleLoading('hide');
			    		      var obj = null;
	     	                  try {
	                             obj = $.parseJSON(result);
	                          } catch(e) {

	             	            obj = {};
	                            obj.result = false;
	     		                obj.message = "致命的なエラーが発生しました。";
	     		                obj.reason  = "このダイアログを閉じた後、画面のスクリーンショットを保存して管理者にお問い合わせ下さい。";
	     		                $("#critical_error").text(result);
	                          }

	     		             if(obj.result == true){
	     		               $("#partial_result_message img").attr('src',<?php echo "'".$html->webroot("/images/confirm_result.png")."'" ?>);
	     		               $("#partial_result_message").attr("name",true);
	     		             }else{
	     		               $("#partial_result_message img").attr('src',<?php echo "'".$html->webroot("/images/error_result.png")."'" ?>);
	     		              $("#partial_result_message").attr("name",false);
	     		             }
	     	                   $("#partial_result_message span").text(obj.message);
	     	                   $("#partial_error_reason").text(obj.reason);
	                           $("#partial_result_dialog").dialog('open');
	      		            });
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

	  /* 処理結果用ダイアログ */
	   $("#partial_result_dialog").dialog({
	             buttons: [{
	                 text: "OK",
	                 click: function () {

	            	     $("#partial_result_dialog").dialog('close');
	            	     $("#file_list_dialog").remove();

	            	     if($("#partial_result_message").attr("name")=="true"){
	            	    	 /* フォームの再読み込み */
	                         $(this).simpleLoading('show');
	                         $.post("<?php echo $html->url('fileForm').'/'.$category_nm ?>",function(html){
	                            $('body').append(html);
	                            $(this).simpleLoading('hide');
	                         });
		            	 }
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

	 /* ファイル編集ダイアログ */
 	 $("#file_list_dialog").dialog({
 	             buttons: [{
 	                 text: "スライドショー",
 	                 click: function () {
 	               	   $("#gallery li:nth-child(1) a").click();
 	                 }
 	             },
 	      	     {
 	                 text: "追加",
 	                 click: function () {

 	            	    $(this).simpleLoading('show');
		    	        $.get(<?php echo "'".$html->url('fileUploadForm')."/".$category_nm."'" ?>,function(html){
		    	   	       $(this).simpleLoading('hide');
     		               $("#file_addition_dialog_wrapper").html(html);
       		            });
 	                 }
 	             },
 	             {
	                 text: "削除",
	                 click: function () {
 	            	      $("#partial_confirm_dialog").dialog('open');
           		     }
	             },
	             {
	                 text: "出力有無更新",
	                 click: function () {
	                	 $(this).simpleLoading('show');
			    	     var ids = $("#file_list").getGridParam('selarrrow');

		                 var params ="";
	                	    for(var i=0;i < ids.length;i++){
                             params += "data[file]["+i+"]="+ids[i]+"&";
	                		}

			    	    	$.post(<?php echo "'".$html->url('updateFinalSheetOutputFlg')."'"?>,params,function(result){

		  		    		  $(this).simpleLoading('hide');
			    		      var obj = null;
	     	                  try {
	                             obj = $.parseJSON(result);
	                          } catch(e) {

	             	            obj = {};
	                            obj.result = false;
	     		                obj.message = "致命的なエラーが発生しました。";
	     		                obj.reason  = "このダイアログを閉じた後、画面のスクリーンショットを保存して管理者にお問い合わせ下さい。";
	     		                $("#critical_error").text(result);
	                          }

	     		             if(obj.result == true){
	     		               $("#partial_result_message img").attr('src',<?php echo "'".$html->webroot("/images/confirm_result.png")."'" ?>);
	     		               $("#partial_result_message").attr("name",true);
	     		             }else{
	     		               $("#partial_result_message img").attr('src',<?php echo "'".$html->webroot("/images/error_result.png")."'" ?>);
	     		              $("#partial_result_message").attr("name",false);
	     		             }
	     	                   $("#partial_result_message span").text(obj.message);
	     	                   $("#partial_error_reason").text(obj.reason);
	                           $("#partial_result_dialog").dialog('open');
	      		            });
	                 }
	             },
 	             {
 	                 text: "CANCEL",
 	                 click: function () {
 	                     $("#file_list_dialog").dialog('close');
 	                 }
 	             }],
 	             beforeClose:function(){
 	                $("#file_list_dialog").remove();
 	             },
 	             draggable: false,
 	             autoOpen: true,
 	             resizable: false,
 	             zIndex: 2000,
 	             width:630,
 	             height:430,
 	             position:[($(window).width() / 2) -  (630 / 2) ,($(window).height() / 2) -  (430 / 2)],
 	             modal: true,
 	             title: "ファイル編集"
 	 });

     var mydata=  [<?php echo $file_list; ?>];

	 /*  ファイル選択用テーブル
      --------------------------------------------------------*/
      jQuery("#file_list").jqGrid({
          //url: <?php echo "'".$html->url('fileList').'/'.$category_nm."'" ?>,
          //datatype: 'json',
          //mtype: 'POST',
          datatype: 'local',
          data: mydata,
          colNames: ['FS出力','カテゴリ名','ファイル名', 'サイズ','ダウンロード','url','完全url'],
          colModel: [
                     { name: 'OutputFlg'    , width: 20 ,align:'center' },
                     { name: 'CategoryName' , width: 40},
                     { name: 'FileName' , width: 130},
                     { name: 'FileSize' , width: 20  ,align:'right' },
                     { name: 'FileLink' , width: 30  ,align:'center' },
                     { name: 'url'      , width: 0  ,hidden:true},
                     { name: 'FullUrl'  , width: 0  ,hidden:true},
                    ],
          onSelectRow: function (id) {
             if (id !== null) { }
          },
          gridComplete: function (data) {

              var root = "<?php echo $html->webroot() ?>";

        	  $("#gallery").children().remove();
        	  var ids = $("#file_list").jqGrid('getGridParam','_index');
        	  for (id in ids) {
            	   if (ids.hasOwnProperty(id)) {
            		   var rowData = jQuery('#file_list').jqGrid('getRowData', id);
            		   /* 画像ファイルのみスライドショーの対象とする */
            		   if(rowData.FullUrl != ""){
            		     $("#gallery").append("<li><a href='" + root + rowData.FullUrl + "' rel='prettyPhoto[temp]' >" + root + rowData.FullUrl + "</a></li>");
            		   }
               	    }
              }

      		  /*  画像スライドショープラグインの設定  */
              $("a[rel^='prettyPhoto']").prettyPhoto({
      			animation_speed: 'fast', /* fast/slow/normal */
      			slideshow: 5000, /* false OR interval time in ms */
      			autoplay_slideshow: false, /* true/false */
      			opacity: 0.80, /* Value between 0 and 1 */
      			show_title: true, /* true/false */
      			allow_resize: true, /* Resize the photos bigger than viewport. true/false */
      			default_width: 500,
      			default_height: 344,
      			counter_separator_label: '/', /* The separator for the gallery counter 1 "of" 2 */
      			theme: 'pp_default', /* light_rounded / dark_rounded / light_square / dark_square / facebook */
      			horizontal_padding: 20, /* The padding on each side of the picture */
      			hideflash: false, /* Hides all the flash object on a page, set to TRUE if flash appears over prettyPhoto */
      			wmode: 'opaque', /* Set the flash wmode attribute */
      			autoplay: false, /* Automatically start videos: True/False */
      			modal: true, /* If set to true, only the close button will close the window */
      			deeplinking: true, /* Allow prettyPhoto to update the url to enable deeplinking. */
      			overlay_gallery: true, /* If set to true, a gallery will overlay the fullscreen image on mouse over */
      			keyboard_shortcuts: true, /* Set to false if you open forms inside prettyPhoto */
      			changepicturecallback: function(){}, /* Called everytime an item is shown/changed */
      			callback: function(){}, /* Called when prettyPhoto is closed */
      			ie6_fallback: true,
      			social_tools: false/* html or false to disable */
      		});
          },
          loadError: function(xhr, status, error) {
              alert("データの読み込みに失敗しました。 "+error);
          },
          pager: $('#file_list_pager'),
          viewrecords: true,
          rowNum: 20,
          rowList: [10,20,30,40,50],
          //Sort、Paging、Search機能をローカルのみで行う
          //loadonce: true,
          emptyrecords: "NO RECORDS HERE",
          //Colunmの移動 *少しソート機能に影響がでる？
         // sortable: true,
          viewrecords: true,
          imgpath: 'themes/basic/images',
          //autowidth: true,
          width : '590',
          height: '240',
       	  multiselect:true
          //rownumbers: true
      });

      $("#file_list").navGrid('#file_list_pager',{refresh:true,edit:false,add:false,del:false,search:false});

 });
</script>

 <table id="file_list"        ></table>
 <div   id="file_list_pager"  style="text-align:center;"></div>
 <div id="partial_result_dialog"  style="display:none"><p id="partial_result_message" name=""><img src="#" alt="" /><span></span></p><p id="partial_error_reason"></p></div>
 <div id="partial_confirm_dialog" style="display:none"><p><img src="<?php echo $html->webroot("/images/warning_result.png") ?>" alt="" />ファイルを削除しますががよろしいですか？</p></div>
 <div id="partial_critical_error"></div>
 <div id="file_addition_dialog_wrapper"></div>

  <!-- 画像スライドショーの画像ファイル -->
  <ul id="gallery" style="display:none" ></ul>
</div>

