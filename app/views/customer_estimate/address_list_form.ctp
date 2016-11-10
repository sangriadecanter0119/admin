<script type='text/javascript'>
 $(function(){
	 	    
	 /* 電話帳ダイアログ */
 	 $("#address_list_dialog").dialog({
 	             buttons: [{
 	                 text: "OK",
 	                 id:"ConfirmAddressListBotton",
 	                 click: function () {                  

 	              	     var json="";
 	                     var ids = $("#address_list").getGridParam('selarrrow'); 	                   
 	                     for(var i=0;i < ids.length;i++){
 	                    	var rowData = jQuery('#address_list').jqGrid('getRowData', ids[i]); 
 	                    	json += "\""  + rowData.Name + "\"" + "<" + rowData.Mail + ">" + ",";
 	                     }
 	                    //最後のカンマを除く
  	                     json = json.substr(0,json.length-1);   	                 
  	                     $(".selected").val(json);  	                   
  	                     $(".selected").removeClass("selected"); 	                 
 	                     
  		                 $("#address_list_dialog").dialog('close');
 	                 }
 	             },
 	             {
 	                 text: "CANCEL",
 	                 click: function () {                     
 	                     $("#address_list_dialog").dialog('close');
 	                 }
 	             }],
 	             beforeClose:function(){
 	                $("#address_list_dialog").remove();
 	             },
 	             draggable: false,
 	             autoOpen: true,
 	             resizable: false,
 	             zIndex: 2000,
 	             width:760,
 	             height:500,
 	             position:[($(window).width() / 2) -  (740 / 2) ,($(window).height() / 2) -  (500 / 2)],
 	             modal: true,
 	             title: "連絡先アドレス帳"
 	 });
 	 
	 /*  アドレス選択用テーブル
      --------------------------------------------------------*/
      jQuery("#address_list").jqGrid({
          url: <?php echo "'".$html->url('AddressList')."'" ?>,
          datatype: 'json',
          mtype: 'POST',
          colNames: ['名前', 'E-MAIL','区分'],
          colModel: [
                     { name: 'Name' , width: 80  },     
                     { name: 'Mail' , width: 130 },          
                     { name: 'TYPE' , width: 30 },         
                    ],                   
          onSelectRow: function (id) {              
             if (id !== null) {  $("#ConfirmAddressListBotton").attr('disabled',false);  }             
          },
          loadComplete: function (data) {            
          },
          loadError: function(xhr, status, error) {
              alert("データの読み込みに失敗しました。 "+error);
          },        
          pager: $('#address_list_pager'),
          viewrecords: true,
          rowNum: 100,
          rowList: [100],        
          //Sort、Paging、Search機能をローカルのみで行う
          loadonce: true,
          emptyrecords: "NO RECORDS HERE",       
          //Colunmの移動 *少しソート機能に影響がでる？
         // sortable: true,          
          viewrecords: true,
          imgpath: 'themes/basic/images',
          autowidth: true,      
          height: '315',         
       	  multiselect:true
          //rownumbers: true
      });      
	 
      //連絡先選択ボタン
      $("#ConfirmAddressListBotton").attr('disabled',true);
 });		  
</script>

<div id="address_list_dialog">
 <table id="address_list"        ></table> 
 <div   id="address_list_pager"  style="text-align:center;"></div>
</div>

