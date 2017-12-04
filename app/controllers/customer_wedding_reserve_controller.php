<?php
class CustomerWeddingReserveController extends AppController
{

 public $name = 'CustomerWeddingReserve';
 public $uses = array('FinalSheetService',
                      'CustomerMst','CustomerMstView',
                      'ContractTrn','ContractTrnView',
                      'CoordinatorMenuTrnView','CoordinatorTimeTrnView',
                      'CeremonyTrnView','CeremonyTrn','CeremonyBrideMadeTrn','CeremonyGroomMadeTrn','CeremonyFlowerTrn','CeremonyRingTrn',
                      'CeremonyOptionTrnView','CeremonyOptionTrn','CeremonyOptionDtlTrn',
                      'HairmakeCplMenuTrnView','HairmakeCplTimeTrnView','HairmakeGuestTrnView',
                      'TransCplTrnView','TransGuestTrnView',
                      'FlowerTrnView',
                      'PhotographerMenuTrnView','PhotographerTimeTrnView',
                      'VideographerMenuTrnView','VideographerTimeTrnView',
                      'EntertainmentTrnView',
                      'MinisterTrn','MinisterTrnView',
                      'ReceptionTrnView',
                      'PartyOptionTrnView',
                      'LinenTrnView',
                      'AvTrnView',
                      'AlbumTrnView',
                      'PaperTrnView',
                      'McTrn','McTrnView',
                      'HouseWeddingTrn','HouseWeddingTrnView',
                      'TransRecepTrnView',
                      'CakeTrnView',
                      'TravelTrnView',
                      'FinalSheetTrn',
                      'FileMst',
                      'EstimateTrn','EstimateDtlTrnView');
 public $layout = 'cust_indivisual_info_main_tab';
 public $components = array('Auth','RequestHandler');   //,'DebugKit.Toolbar');
 public $helpers = array('Html','common','Javascript');

 /**
  *
  * ファイナルシート履歴一覧画面表示
  */
 function index()
 {
 	$customer_id = $this->Session->read('customer_id');
 	$customer = $this->CustomerMstView->findById($customer_id);
    $this->set(	"customer",$customer);

    $final = $this->FinalSheetTrn->find('all',array('conditions'=>array('customer_id'=>$customer_id),'order'=>array('reg_dt desc','upd_dt desc')));
    /* ファイナルシートテーブルが存在しなければ未成約なので見積画面にリダイレクトする  */
    if(count($final) == 0){ $this->redirect('https://'.$_SERVER['HTTP_HOST'].'/admin/customer_estimate'); }

    //新郎新婦の名前をセット
    $this->set(	"broom",($customer['CustomerMstView']['prm_lastname_flg'] == 0 ? $customer['CustomerMstView']['grmls_kj'] : $customer['CustomerMstView']['brdls_kj']).$customer['CustomerMstView']['grmfs_kj'] );
    $this->set(	"bride",$customer['CustomerMstView']['brdfs_kj']);

 	$this->set("menu_customers","");
 	$this->set("menu_customer","current");
 	$this->set("menu_fund","");

 	$this->set("sub_menu_customer_info","");
 	$this->set("sub_menu_customer_meeting","");
 	$this->set("sub_menu_customer_wedding_reserve","current");
 	$this->set("sub_menu_customer_contact","");
 	//$this->set("sub_menu_customer_schedule","");
 	$this->set("sub_menu_customer_estimate","");

 	$this->set("data",$final);
 	$this->set("sub_title","挙式予約状況履歴");
 	$this->set("user",$this->Auth->user());
 }

 /**
  *
  * ファイナルシート詳細画面表示
  */
 function detail($final_sheet_id)
 {
 	$customer_id = $this->Session->read('customer_id');
 	$customer = $this->CustomerMst->findById($customer_id);
    $this->set(	"customer",$customer);

    $this->set("final_sheet_id",$final_sheet_id);

    $contract = $this->ContractTrnView->find('all',array('conditions'=>array('customer_id'=>$customer_id)));
    /* 契約テーブルが存在すれば成約済みなので各商品カテゴリデータの有無を取得する  */
    if(count($contract) > 0 && $final_sheet_id > 0)
    {
      $this->set("contract",$contract[0]);
      /* 各カテゴリのデータが存在するかチェック */
      $this->set('ceremony',$this->CeremonyTrn->find('count',array('conditions'=>array('final_sheet_id'=>$final_sheet_id))));
      $this->set('ceremony_option',$this->CeremonyOptionTrnView->find('count',array('conditions'=>array('final_sheet_id'=>$final_sheet_id))));
      $this->set('coordinator',$this->CoordinatorMenuTrnView->find('count',array('conditions'=>array('final_sheet_id'=>$final_sheet_id))));
      $this->set('hairmake_cpl',$this->HairmakeCplMenuTrnView->find('count',array('conditions'=>array('final_sheet_id'=>$final_sheet_id))));
      $this->set('hairmake_gst',$this->HairmakeGuestTrnView->find('count',array('conditions'=>array('final_sheet_id'=>$final_sheet_id))));
      $this->set('trans_cpl',$this->TransCplTrnView->find('count',array('conditions'=>array('final_sheet_id'=>$final_sheet_id))));
      $this->set('trans_gst',$this->TransGuestTrnView->find('count',array('conditions'=>array('final_sheet_id'=>$final_sheet_id))));
      $this->set('flower',$this->FlowerTrnView->find('count',array('conditions'=>array('final_sheet_id'=>$final_sheet_id))));
      $this->set('photographer',$this->PhotographerMenuTrnView->find('count',array('conditions'=>array('final_sheet_id'=>$final_sheet_id))));
      $this->set('videographer',$this->VideographerMenuTrnView->find('count',array('conditions'=>array('final_sheet_id'=>$final_sheet_id))));
      $this->set('entertainment',$this->EntertainmentTrnView->find('count',array('conditions'=>array('final_sheet_id'=>$final_sheet_id))));
      $this->set('minister',$this->MinisterTrnView->find('count',array('conditions'=>array('final_sheet_id'=>$final_sheet_id))));
      $this->set('reception',$this->ReceptionTrnView->find('count',array('conditions'=>array('final_sheet_id'=>$final_sheet_id))));
      $this->set('party_option',$this->PartyOptionTrnView->find('count',array('conditions'=>array('final_sheet_id'=>$final_sheet_id))));
      $this->set('linen',$this->LinenTrnView->find('count',array('conditions'=>array('final_sheet_id'=>$final_sheet_id))));
      $this->set('av',$this->AvTrnView->find('count',array('conditions'=>array('final_sheet_id'=>$final_sheet_id))));
      $this->set('paper',$this->PaperTrnView->find('count',array('conditions'=>array('final_sheet_id'=>$final_sheet_id))));
      $this->set('mc',$this->McTrnView->find('count',array('conditions'=>array('final_sheet_id'=>$final_sheet_id))));
      $this->set('house_wedding',$this->HouseWeddingTrnView->find('count',array('conditions'=>array('final_sheet_id'=>$final_sheet_id))));
      $this->set('trans_recep',$this->TransRecepTrnView->find('count',array('conditions'=>array('final_sheet_id'=>$final_sheet_id))));
      $this->set('cake',$this->CakeTrnView->find('count',array('conditions'=>array('final_sheet_id'=>$final_sheet_id))));
      $this->set('album',$this->AlbumTrnView->find('count',array('conditions'=>array('final_sheet_id'=>$final_sheet_id))));
      $this->set('travel',$this->TravelTrnView->find('count',array('conditions'=>array('final_sheet_id'=>$final_sheet_id))));

      /* 保存ファイルの取得 */
      /*
      $this->set('basicInfo_files',$this->_downloadImage("basicInfo"));
      $this->set('personalInfo_files',$this->_downloadImage("personalInfo"));
      $this->set('travel_files',$this->_downloadImage("travel"));
      $this->set('coordinator_files',$this->_downloadImage("coordinator"));
      $this->set('ceremony_files',$this->_downloadImage("ceremony"));
      $this->set('ceremonyOption_files',$this->_downloadImage("ceremonyOption"));
      $this->set('transportationCpl_files',$this->_downloadImage("transportationCpl"));
      $this->set('transportationGst_files',$this->_downloadImage("transportationGst"));
      $this->set('hairmake_files',$this->_downloadImage("hairMake"));
      $this->set('photo_files',$this->_downloadImage("photo"));
      $this->set('video_files',$this->_downloadImage("video"));
      $this->set('flower_files',$this->_downloadImage("flower"));
      $this->set('reception_files',$this->_downloadImage("reception"));
      $this->set('receptionTransportation_files',$this->_downloadImage("receptionTransportation"));
      $this->set('cake_files',$this->_downloadImage("cake"));
      $this->set('entertainment_files',$this->_downloadImage("entertainment"));
      $this->set('av_files',$this->_downloadImage("av"));
      $this->set('album_files',$this->_downloadImage("album"));
      $this->set('linen_files',$this->_downloadImage("linen"));
      $this->set('paper_files',$this->_downloadImage("paper"));
      $this->set('mc_files',$this->_downloadImage("mc"));
      $this->set('minister_files',$this->_downloadImage("minister"));
      $this->set('partyOption_files',$this->_downloadImage("partyOption"));
      $this->set('houseWedding_files',$this->_downloadImage("houseWedding"));
      */
    }
    else
   {
      //$this->redirect('/customer_estimate');
      $this->redirect('https://'.$_SERVER['HTTP_HOST'].'/admin/customer_estimate');
    }

    //新郎新婦の名前をセット
   $this->set("broom",($customer['CustomerMst']['prm_lastname_flg'] == 0 ? $customer['CustomerMst']['grmls_kj'] : $customer['CustomerMst']['brdls_kj']).$customer['CustomerMst']['grmfs_kj'] );
   $this->set("bride",$customer['CustomerMst']['brdfs_kj']);

    $this->set("menu_customers","");
 	$this->set("menu_customer","current");
 	$this->set("menu_fund","");

 	$this->set("sub_menu_customer_info","");
 	$this->set("sub_menu_customer_meeting","");
 	$this->set("sub_menu_customer_wedding_reserve","current");
 	$this->set("sub_menu_customer_contact","");
 	//$this->set("sub_menu_customer_schedule","");
 	$this->set("sub_menu_customer_estimate","");

 	$this->set("sub_title","挙式予約状況");
 	$this->set("user",$this->Auth->user());
 }

 /**
  *
  * [AJAX]ファイナルシートの更新処理
  */
 function edit()
 {
 	if (!$this->RequestHandler->isAjax()){ $this->cakeError('error404'); }

 	$this->layout = '';
 	$this->autoRender =false;
 	configure::write('debug', 0);

 	$ret =  $this->FinalSheetService->updateFinalSheet($this->data['Category']['id'],$this->data,$this->Auth->user('username'));
 	if($ret['result']==false){	return json_encode($ret); }

    return json_encode(array('result'=>true,'message'=>'処理完了しました。'));
 }

/**
  *
  * [AJAX]ファイナルシートのスナップショット
  */
 function snapshot($final_sheet_id)
 {
 	if (!$this->RequestHandler->isAjax()){ $this->cakeError('error404'); }

 	$this->layout = '';
 	$this->autoRender =false;
 	configure::write('debug', 0);

 	$ret =  $this->FinalSheetService->snapshotFinalSheet($final_sheet_id,$this->Auth->user('username'));
 	if($ret['result']==false){	return json_encode($ret); }

    return json_encode(array('result'=>true,'message'=>'処理完了しました。'));
 }

 /**
  *
  * [AJAX]引数のカテゴリのファイルアップロード画面を表示する
  * @param $category_nm
  */
 function fileUploadForm($category_nm)
 {
    if (!$this->RequestHandler->isAjax()){ $this->cakeError('error404'); }
	configure::write('debug', 0);
	$this->layout = '';
	$this->set("customer_id", $this->Session->read('customer_id'));
	$this->set("category_nm",$category_nm);
 }


 /**
  *
  * ファイルリスト画面の表示
  * @param $category_nm
  */
 function fileForm($category_nm)
 {
 	if (!$this->RequestHandler->isAjax()){ $this->cakeError('error404'); }

 	$this->layout = '';
 	configure::write('debug', 0);

 	switch (strtoupper($category_nm)) {
 		case "COMMON":  $this->set("file_list",$this->_getAllFileList());
 	            	    break;
 		default:$this->set("file_list",$this->_getFileList($category_nm));
 		        break;
 	}
 	 $this->set("category_nm",$category_nm);
 }

 /**
  *
  * 引数のカテゴリのファイルリストをJSONデータで取得する
  * @param $category_nm
  */
 /* function _getFileList($category_nm)
 {

    $customer_id = $this->Session->read('customer_id');
 	$targetPath = "uploads/".$customer_id."/".$category_nm."/";

 	$file_list = null;
 	if(is_dir($targetPath)){

        $handle=opendir($targetPath);
        $counter =1;
 	    while($filename=readdir($handle))
 	    {
 	      if($filename != "." and $filename != "..")
 	      {
 	      	 画像ファイル
 	      	$type = exif_imagetype("$targetPath/$filename");
 	        if($type == IMAGETYPE_GIF || $type == IMAGETYPE_JPEG || $type == IMAGETYPE_BMP || $type == IMAGETYPE_PNG ){

 	        	$file_list .= "{id:".$counter.",CategoryName:'".$category_nm."',FileName:'".mb_convert_encoding("$filename", "UTF-8", "SJIS")."',FileSize:'".round((filesize("$targetPath/$filename")/1024))."KB',FileLink:"."\""."<a href='$targetPath".urlencode(mb_convert_encoding("$filename", "SJIS", "AUTO"))."' target='_blank'><img src='./images/link.png' ></a>"."\"".",url:'".urlencode(mb_convert_encoding($filename, "SJIS", "AUTO"))."',FullUrl:'".$targetPath.urlencode(mb_convert_encoding("$filename", "SJIS", "AUTO"))."'},";

                              画像ファイル以外
 	      	}else{
 	      		$file_list .= "{id:".$counter.",CategoryName:'".$category_nm."',FileName:'".mb_convert_encoding("$filename", "UTF-8", "SJIS")."',FileSize:'".round((filesize("$targetPath/$filename")/1024))."KB',FileLink:"."\""."<a href='$targetPath".urlencode(mb_convert_encoding("$filename", "SJIS", "AUTO"))."' target='_blank'><img src='./images/link.png' ></a>"."\"".",url:'".urlencode(mb_convert_encoding($filename, "SJIS", "AUTO"))."',FullUrl:''},";

 	        }
 	      	 $counter++;
 	      }
 	 	}
     }
    //  return $file_list;
    return $file_list == null ? "{}" :substr($file_list,0,strlen($file_list)-1);
 } */

 /**
  * カテゴリのファイルリストをJSONデータで取得する
  * @param unknown $category_nm
  * @return string
  */
 function _getFileList($category_nm)
 {
    $customer_id = $this->Session->read('customer_id');
    $file_list = null;

    $data = $this->FileMst->getFileInfoOfCustomerByFolder($customer_id,$category_nm);

    for($i=0; $i < count($data);$i++){
       $attr = $data[$i]["FileMst"];
       $output = $attr['output_flg'] == 0 ? "" : "<img src='".Router::url("/images/good.png")."'>";
       $targetPath =  Router::url("/".$attr['root_path']."/".$customer_id."/".$attr['folder_nm']."/",true);
       $filename = mb_convert_encoding($attr['file_nm'], "UTF-8", "SJIS");
       $file_list .= "{id:".$attr['id'].",OutputFlg:'".$output."',CategoryName:'".$category_nm."',FileName:'".$filename.
                     "',FileSize:'".round($attr['file_size']/1024)."KB',FileLink:"."\"".
                     "<a href='$targetPath".urlencode($filename)."' target='_blank'><img src='".Router::url("/images/link.png")."' ></a>"."\"".
                     ",url:'".urlencode($filename)."',FullUrl:'".$targetPath.urlencode($filename)."'},";
    }
    return $file_list == null ? "{}" :substr($file_list,0,strlen($file_list)-1);
 }

 /**
  * 全てののファイルリストをJSONデータで取得する
  * @return string
  */
 function _getAllFileList()
 {
    $customer_id = $this->Session->read('customer_id');
    $file_list = null;

    $data = $this->FileMst->getFileInfoOfCustomer($customer_id);

    for($i=0; $i < count($data);$i++){

      $attr = $data[$i]["FileMst"];
      $output = $attr['output_flg'] == 0 ? "" : "<img src='".Router::url("/images/good.png")."'>";
      $targetPath =  Router::url("/".$attr['root_path']."/".$customer_id."/".$attr['folder_nm']."/",true);
      $filename = mb_convert_encoding($attr['file_nm'], "UTF-8", "SJIS");
      $file_list .= "{id:".$attr['id'].",OutputFlg:'".$output."',CategoryName:'".$attr['folder_nm']."',FileName:'".$filename.
                    "',FileSize:'".round($attr['file_size']/1024)."KB',FileLink:"."\"".
                    "<a href='$targetPath".urlencode($filename)."' target='_blank'><img src='".Router::url("/images/link.png")."' ></a>"."\"".
                    ",url:'".urlencode($filename)."',FullUrl:'".$targetPath.urlencode($filename)."'},";
    }
    return $file_list == null ? "{}" :substr($file_list,0,strlen($file_list)-1);
 }

/**
  *
  * 全てののファイルリストをJSONデータで取得する
  *
  */

 /**
  * [AJAX]ファイルの削除
  */
 function deleteFile()
 {
   if (!$this->RequestHandler->isAjax()){ $this->cakeError('error404'); }

   $this->layout = '';
   $this->autoRender =false;
   configure::write('debug', 0);

   if(!empty($this->data)){

      /* ファイルのDB情報の削除 */
      $tr = ClassRegistry::init('TransactionManager');
      $tr->begin();

      $path_array = array();
      for($i=0;$i < count($this->data['file']);$i++){
        //ファイルのパスを保存しておく
        $path_array[] = "./".$this->FileMst->getFilePathById($this->data['file'][$i]);
        $ret = $this->FileMst->deleteFileInfoById($this->data['file'][$i]);
        if($ret['result']==false){ return json_encode($ret);}
      }
      $tr->commit();

      /* ファイルの物理削除 */
      for($i=0;$i < count($path_array);$i++){
         //unlink(mb_convert_encoding($targetPath.$this->data['file'][$i], "SJIS", "AUTO"));
         unlink($path_array[$i]);
      }
         return json_encode(array('result'=>true,'message'=>"ファイルを削除しました。",'reason'=>""));
   }else{
         return json_encode(array('result'=>false,'message'=>"ファイルが選択されていません。",'reason'=>""));
   }
 }

/**
  *
  * カテゴリ毎のファイルをアップロードする
  * @param $category_nm
  */
 function uploadImage($category_nm)
 {
 	$this->layout = "";

    if (is_uploaded_file($this->data['ImgForm']['ImgFile']['tmp_name'])) {

     $customer_id = $this->Session->read('customer_id');
     // 格納先フォルダ
     $des_dir = "uploads".DS.$customer_id;
     // テンプレートフォルダ
     $src_dir = "uploads".DS."temp";

     // フォルダが存在するかチェックし、なければテンプレートフォルダを元に作成
     if(!is_dir($des_dir)){

     	//顧客用の基本フォルダを作成
     	mkdir($des_dir);
        $handle=opendir($src_dir);
 	    while($filename=readdir($handle))
 	    {
 	    	if(!is_dir("$des_dir/$filename")){
 		       mkdir("$des_dir/$filename");
 	    	}
 	 	}
     }

    $targetPath = "uploads".DS.$customer_id.DS.$category_nm.DS;
	$tempFile   = $this->data['ImgForm']['ImgFile']['tmp_name'];
	//ファイル名の文字化け対策
	$targetFile =  mb_convert_encoding($targetPath.$this->data['ImgForm']['ImgFile']['name'], "SJIS", "AUTO");

	if(is_file($targetFile)){
		$this->set("msg",json_encode(array('data'=>array('isSuccess'=>"false", 'message'=>"既に同じファイル名が存在します。"))));
		return;
	}

	//ファイル情報をDBに保存
    $db_ret = $this->FileMst->registerFileInfo( array(
                  "root_path"=>'uploads',
                  "folder_nm"=>$category_nm,
                  "file_nm"=>basename($targetFile),
                  "customer_id"=>$customer_id,
                  "file_size"=>filesize($tempFile),
                  "username"=>$this->Auth->user('username')
                  ));
    if($db_ret['result']==false){ return json_encode($ret); }

    chmod($targetPath, 0777);

	//ファイル保存
    move_uploaded_file($tempFile,$targetFile);
    //エラー判定
    switch ($this->data['ImgForm']['ImgFile']['error'])
    {
        case 0:
              $msg = "アップロードが完了しました。";
             break;
        case 1:
              $msg = "The file is bigger than this PHP installation allows";
              break;
        case 2:
              $msg = "The file is bigger than this form allows";
              break;
        case 3:
              $msg = "Only part of the file was uploaded";
              break;
        case 4:
             $msg = "No file was uploaded";
              break;
        case 6:
             $msg = "Missing a temporary folder";
              break;
        case 7:
             $msg = "Failed to write file to disk";
             break;
        case 8:
             $msg = "File upload stopped by extension";
             break;
        default:
             $msg = "unknown error ".$this->data['ImgForm']['ImgFile']['error'];
             break;
    }
      $this->set("msg",json_encode(array('data'=>array('isSuccess'=> $this->data['ImgForm']['ImgFile']['error']==0 ? "true":"false", 'message'=>$msg))));
   }else{
      $this->set("msg",json_encode(array('data'=>array('isSuccess'=>"false", 'message'=> "ファイルが指定されていません。　　ファイルサイズの上限は128Mです。"))));
   }
 }


 /**
  *
  * ファイナルシート出力
  * @param $file_type
  */
 function export($file_type,$final_sheet_id)
 {
    $save_filename = null;
    $temp_filename = null;
    $render_name = null;

    $customer_id = $this->Session->read('customer_id');
 	$customer = $this->CustomerMstView->findById($customer_id);
    $this->set(	"customer",$customer);
    $contract = $this->ContractTrnView->find('all',array('conditions'=>array('customer_id'=>$customer_id)));

   /* 保存ファイル名作成*/
   $file_name = null;
   $file_name = date('mdY',strtotime($contract[0]['ContractTrnView']['wedding_dt'])).mb_convert_encoding($contract[0]['ContractTrnView']['grmls_kj'], "SJIS", "AUTO").'('.$contract[0]['ContractTrnView']['grmls_rm'].')';

   switch (strtoupper($file_type)) {
   	case "EXCEL_BUSINESS":
                 $temp_filename = "final_template.xlsx";
   		         $save_filename = "Final".$file_name.".xlsx";
                 $render_name = "excel";
   	       	     break;
   	case "EXCEL_CUSTOMER":
                 $temp_filename = "final_customer_template.xlsx";
   		         $save_filename = "FinalCustomer".$file_name.".xlsx";
                 $render_name = "excel_customer";
   	       	     break;
        case "EXCEL_BUSINESS_TEST":
   	  	     	 $temp_filename = "final_test_template.xlsx";
   	       	     $save_filename = "Final_Test_".$file_name.".xlsx";
   	       	     $render_name = "excel_test";
   	       	     break;
   	default:
   		    $this->cakeError("error", array("message" => "予期しないファイルタイプ[{$file_type}]です。"));
       	    break;
   }


    /* 契約テーブルが存在すれば成約済みなので各商品カテゴリデータの有無を取得する  */
    if(count($contract) > 0)
    {
      $this->set("contract",$contract[0]);
      /* 各カテゴリのデータが存在するかチェック */
      $ceremony = $this->CeremonyTrn->find('all',array('conditions'=>array('final_sheet_id'=>$final_sheet_id)));
      $this->set('ceremony',$ceremony);
      if(!empty($ceremony)){
          $this->set('ceremony_ring',$this->CeremonyRingTrn->find('all',array('conditions'=>array('ceremony_id'=>$ceremony[0]['CeremonyTrn']['id']))));
          $this->set('ceremony_flower',$this->CeremonyFlowerTrn->find('all',array('conditions'=>array('ceremony_id'=>$ceremony[0]['CeremonyTrn']['id']))));
          $this->set('ceremony_bride_made',$this->CeremonyBrideMadeTrn->find('all',array('conditions'=>array('ceremony_id'=>$ceremony[0]['CeremonyTrn']['id']))));
          $this->set('ceremony_groom_made',$this->CeremonyGroomMadeTrn->find('all',array('conditions'=>array('ceremony_id'=>$ceremony[0]['CeremonyTrn']['id']))));
      }

      $this->set('ceremony_option',$this->CeremonyOptionTrnView->find('all',array('conditions'=>array('final_sheet_id'=>$final_sheet_id))));
      $this->set('hairmake_cpl'      ,$this->HairmakeCplMenuTrnView->find('all',array('conditions'=>array('final_sheet_id'=>$final_sheet_id),'order'=>'id')));
      $this->set('hairmake_cpl_time' ,$this->HairmakeCplTimeTrnView->find('all',array('conditions'=>array('final_sheet_id'=>$final_sheet_id),'order'=>'id')));
      $this->set('hairmake_gst',$this->HairmakeGuestTrnView->find('all',array('conditions'=>array('final_sheet_id'=>$final_sheet_id),'order'=>'id,hairmake_guest_sub_id,no')));
      $this->set('trans_cpl',$this->TransCplTrnView->find('all',array('conditions'=>array('final_sheet_id'=>$final_sheet_id),'order'=>'id,trans_cpl_sub_id,no')));
      $this->set('trans_gst',$this->TransGuestTrnView->find('all',array('conditions'=>array('final_sheet_id'=>$final_sheet_id),'order'=>'id,trans_guest_sub_id,no')));
      $this->set('travel',$this->TravelTrnView->find('all',array('conditions'=>array('final_sheet_id'=>$final_sheet_id),'order'=>'id')));
      $this->set('coordinator',$this->CoordinatorMenuTrnView->find('all',array('conditions'=>array('final_sheet_id'=>$final_sheet_id),'order'=>'id')));
      $this->set('coordinator_time',$this->CoordinatorTimeTrnView->find('all',array('conditions'=>array('final_sheet_id'=>$final_sheet_id),'order'=>'id')));
      $this->set('flower',$this->FlowerTrnView->find('all',array('conditions'=>array('final_sheet_id'=>$final_sheet_id))));
      $this->set('photographer'     ,$this->PhotographerMenuTrnView->find('all',array('conditions'=>array('final_sheet_id'=>$final_sheet_id),'order'=>'id')));
      $this->set('photographer_time',$this->PhotographerTimeTrnView->find('all',array('conditions'=>array('final_sheet_id'=>$final_sheet_id),'order'=>'id')));
      $this->set('videographer'     ,$this->VideographerMenuTrnView->find('all',array('conditions'=>array('final_sheet_id'=>$final_sheet_id),'order'=>'id')));
 	  $this->set('videographer_time',$this->VideographerTimeTrnView->find('all',array('conditions'=>array('final_sheet_id'=>$final_sheet_id),'order'=>'id')));
      $this->set('entertainment',$this->EntertainmentTrnView->find('all',array('conditions'=>array('final_sheet_id'=>$final_sheet_id))));
      $this->set('minister',$this->MinisterTrnView->find('all',array('conditions'=>array('final_sheet_id'=>$final_sheet_id))));
      $this->set('mc',$this->McTrnView->find('all',array('conditions'=>array('final_sheet_id'=>$final_sheet_id))));
      $this->set('house_wedding',$this->HouseWeddingTrnView->find('all',array('conditions'=>array('final_sheet_id'=>$final_sheet_id))));
      $this->set('reception',$this->ReceptionTrnView->find('all',array('conditions'=>array('final_sheet_id'=>$final_sheet_id))));
      $this->set('trans_recep',$this->TransRecepTrnView->find('all',array('conditions'=>array('final_sheet_id'=>$final_sheet_id),'order'=>'id')));
      $this->set('party_option',$this->PartyOptionTrnView->find('all',array('conditions'=>array('final_sheet_id'=>$final_sheet_id))));
      $this->set('cake',$this->CakeTrnView->find('all',array('conditions'=>array('final_sheet_id'=>$final_sheet_id))));
      $this->set('linen',$this->LinenTrnView->find('all',array('conditions'=>array('final_sheet_id'=>$final_sheet_id))));
      $this->set('av',$this->AvTrnView->find('all',array('conditions'=>array('final_sheet_id'=>$final_sheet_id))));
      $this->set('album',$this->AlbumTrnView->find('all',array('conditions'=>array('final_sheet_id'=>$final_sheet_id),'order'=>'id,album_dtl_id')));
      $this->set('paper',$this->PaperTrnView->find('all',array('conditions'=>array('final_sheet_id'=>$final_sheet_id))));
       //画像ファイルとベンダーリストの取得
      if(strtoupper($file_type) == "EXCEL_BUSINESS"){
        $this->set('customer_files',$this->_getAllFileByCustomerId());
        $this->set("vendor_list", $this->FinalSheetService->getVendorList($final_sheet_id));
      }
      elseif(strtoupper($file_type) == "EXCEL_BUSINESS_TEST"){
      	$this->set('customer_files',$this->_getAllFileByCustomerId());
      	$this->set("vendor_list", $this->FinalSheetService->getVendorList($final_sheet_id));

      	$estimate = $this->EstimateTrn->find('first',array('conditions'=>array('customer_id'=>$customer_id,'adopt_flg'=>1,'del_kbn'=>0)));
      	$this->set("estimate_dtl", $this->EstimateDtlTrnView->find('all',array('conditions'=>array('estimate_id'=>$estimate['EstimateTrn']['id']),'order'=>array('no'=>'asc'))));
      }
    }
   $this->layout = false;

   $this->set( "sheet_name", "FinalSheet" );
   $this->set( "filename", $save_filename );
   $this->set( "template_file", $temp_filename);
   $this->render($render_name);
 }

 /**
  *
  * 各カテゴリのフォームを取得する
  * @param $type
  * @param $category_id
  * @param $final_sheet_id
  * @param $sub_type
  * @param $table_counter
  * @param $sub_counter
  * @param $dtl_counter
  * @param $no
  */
 function feed($type,$category_id,$final_sheet_id,$sub_type=null,$table_counter=null,$sub_counter=null,$dtl_counter=null,$no=1){

  if (!$this->RequestHandler->isAjax()){ $this->cakeError('error404'); }

     $this->layout = '';

     switch (strtoupper($type)) {
     	case "ALL": $this->_feedAll($category_id,$final_sheet_id);
     	            break;
     	case "PART":
     		        $this->_feedPart($category_id,$sub_type,$table_counter,$sub_counter,$dtl_counter,$no);
     		        break;
     	default:
     		       throw new Exception("予期しないフォームタイプ[{$type}]です。");
       	           break;
     }
 }


 /**
  *
  * 引数のカテゴリIDに該当するファイナルシートのデータを取得して、HTML構造で出力する
  * @param $category_id
  */
 function _feedAll($category_id,$final_sheet_id){

 	 // $customer_id = $this->Session->read('customer_id');
 	  switch ($category_id){

     	   	   case GC_WEDDING:      $ceremony = $this->CeremonyTrn->find('all',array('conditions'=>array('final_sheet_id'=>$final_sheet_id)));
                                     $this->set('ceremony',$ceremony);
                                     if(!empty($ceremony)){
                                        $this->set('ceremony_ring',$this->CeremonyRingTrn->find('all',array('conditions'=>array('ceremony_id'=>$ceremony[0]['CeremonyTrn']['id']))));
                                        $this->set('ceremony_flower',$this->CeremonyFlowerTrn->find('all',array('conditions'=>array('ceremony_id'=>$ceremony[0]['CeremonyTrn']['id']))));
                                        $this->set('ceremony_bride_made',$this->CeremonyBrideMadeTrn->find('all',array('conditions'=>array('ceremony_id'=>$ceremony[0]['CeremonyTrn']['id']))));
                                        $this->set('ceremony_groom_made',$this->CeremonyGroomMadeTrn->find('all',array('conditions'=>array('ceremony_id'=>$ceremony[0]['CeremonyTrn']['id']))));
                                     }
                                      $this->render("ceremony");
     	   		                      break;
     	   	   case GC_CEREMONY_OPTION: $this->set('ceremony_option',$this->CeremonyOptionTrnView->find('all',array('conditions'=>array('final_sheet_id'=>$final_sheet_id))));
     	   	                            $this->render("ceremony_option");
     	   		                        break;
     	   	   case GC_HAIR_MAKE:     $this->set('hairmake_cpl'      ,$this->HairmakeCplMenuTrnView->find('all',array('conditions'=>array('final_sheet_id'=>$final_sheet_id),'order'=>'id')));
     	   	                          $this->set('hairmake_cpl_time' ,$this->HairmakeCplTimeTrnView->find('all',array('conditions'=>array('final_sheet_id'=>$final_sheet_id),'order'=>'id')));
                                      $this->set('hairmake_gst',$this->HairmakeGuestTrnView->find('all',array('conditions'=>array('final_sheet_id'=>$final_sheet_id))));
                                      $this->render("hairmake");
     	   		                      break;
     	   	   case GC_TRANS_CPL:      $this->set('trans_cpl',$this->TransCplTrnView->find('all',array('conditions'=>array('final_sheet_id'=>$final_sheet_id),'order'=>'id,trans_cpl_sub_id,no')));
                                       $this->render("transportation_cpl");
     	   		                       break;
     	   	   case GC_TRANS_GST:      $this->set('trans_gst',$this->TransGuestTrnView->find('all',array('conditions'=>array('final_sheet_id'=>$final_sheet_id),'order'=>'id,trans_guest_sub_id,no')));
                                       $this->render("transportation_gst");
     	   		                       break;
     	   	   case GC_TRAVEL        : $this->set('travel',$this->TravelTrnView->find('all',array('conditions'=>array('final_sheet_id'=>$final_sheet_id),'order'=>'id')));
     	   	                           $this->render("travel");
     	   		                       break;
     	   	   case GC_COORDINATOR:   $this->set('coordinator',$this->CoordinatorMenuTrnView->find('all',array('conditions'=>array('final_sheet_id'=>$final_sheet_id),'order'=>'id')));
     	   	                          $this->set('coordinator_time',$this->CoordinatorTimeTrnView->find('all',array('conditions'=>array('final_sheet_id'=>$final_sheet_id),'order'=>'id')));
     	   	                          $this->render("coordinator");
     	   		                      break;
     	   	   case GC_FLOWER:        $this->set('flower',$this->FlowerTrnView->find('all',array('conditions'=>array('final_sheet_id'=>$final_sheet_id))));
     	   	                          $this->render("flower");
     	   		                      break;
     	   	   case GC_PHOTO:         $this->set('photographer'     ,$this->PhotographerMenuTrnView->find('all',array('conditions'=>array('final_sheet_id'=>$final_sheet_id),'order'=>'id')));
                                      $this->set('photographer_time',$this->PhotographerTimeTrnView->find('all',array('conditions'=>array('final_sheet_id'=>$final_sheet_id),'order'=>'id')));
                                      $this->render("photographer");
     	   	   	                      break;
     	   	   case GC_VIDEO:         $this->set('videographer'     ,$this->VideographerMenuTrnView->find('all',array('conditions'=>array('final_sheet_id'=>$final_sheet_id),'order'=>'id')));
 	                                  $this->set('videographer_time',$this->VideographerTimeTrnView->find('all',array('conditions'=>array('final_sheet_id'=>$final_sheet_id),'order'=>'id')));
     	   	                          $this->render("videographer");
     	   		                      break;
     	       case GC_ENTERTAINMENT: $this->set('entertainment',$this->EntertainmentTrnView->find('all',array('conditions'=>array('final_sheet_id'=>$final_sheet_id))));
     	                              $this->render("entertainment");
     	   		                      break;
     	   	   case GC_MINISTER:      $this->set('minister',$this->MinisterTrnView->find('all',array('conditions'=>array('final_sheet_id'=>$final_sheet_id))));
     	   	                          $this->render("minister");
     	   		                      break;
     	   	   case GC_MC:            $this->set('mc',$this->McTrnView->find('all',array('conditions'=>array('final_sheet_id'=>$final_sheet_id))));
     	   	                          $this->render("mc");
     	   		                      break;
     	   	   case GC_HOUSE_WEDDING :$this->set('house_wedding',$this->HouseWeddingTrnView->find('all',array('conditions'=>array('final_sheet_id'=>$final_sheet_id))));
     	   	                          $this->render("house_wedding");
     	   		                      break;
     	   	   case GC_RECEPTION:     $this->set('reception',$this->ReceptionTrnView->find('all',array('conditions'=>array('final_sheet_id'=>$final_sheet_id))));
     	   	                          $this->render("reception");
     	   		                      break;
     	   	   case GC_RECEPTION_TRANS: $this->set('trans_recep',$this->TransRecepTrnView->find('all',array('conditions'=>array('final_sheet_id'=>$final_sheet_id),'order'=>'id')));
     	   	                            $this->render("reception_transportation");
     	   		                        break;
     	   	   case GC_PARTY_OPTION:  $this->set('party_option',$this->PartyOptionTrnView->find('all',array('conditions'=>array('final_sheet_id'=>$final_sheet_id))));
     	   	                          $this->render("party_option");
     	   		                      break;
     	   	   case GC_CAKE:          $this->set('cake',$this->CakeTrnView->find('all',array('conditions'=>array('final_sheet_id'=>$final_sheet_id),'order'=>'id,cake_menu_id')));
     	   	                          $this->render("cake");
     	   		                      break;
     	   	   case GC_LINEN:         $this->set('linen',$this->LinenTrnView->find('all',array('conditions'=>array('final_sheet_id'=>$final_sheet_id))));
     	   	                          $this->render("linen");
     	   		                      break;
     	   	   case GC_AV:            $this->set('av',$this->AvTrnView->find('all',array('conditions'=>array('final_sheet_id'=>$final_sheet_id))));
     	   	                          $this->render("av");
     	   		                      break;
     	   	   case GC_ALBUM:         $this->set('album',$this->AlbumTrnView->find('all',array('conditions'=>array('final_sheet_id'=>$final_sheet_id),'order'=>'id,album_dtl_id')));
     	   	                          $this->render("album");
     	   		                      break;
     	   	   case GC_PAPER:         $this->set('paper',$this->PaperTrnView->find('all',array('conditions'=>array('final_sheet_id'=>$final_sheet_id))));
     	   	                          $this->render("paper");
     	   		                      break;
     	   	   /*
     	   	   default:               throw new Exception("予期しない商品カテゴリID[{$goods_data['GoodsMstView']['goods_ctg_id']}]です。");
     	   	   	                      break;
     	   	   */
    }
 }

 /**
  *
  * 各カテゴリの列データ用フォームを取得する
  * @param $category_id
  * @param $type
  * @param $table_counter
  * @param $sub_counter
  * @param $dtl_counter
  * @param $no
  */
 function _feedPart($category_id,$type,$table_counter=null,$sub_counter=null,$dtl_counter=null,$no){

 	  switch ($category_id){

     	   	   case GC_CEREMONY_RING:
     	   	   	                      if(strtoupper($type) == "ROWUNIT"){
     	   	   	                        $this->set("dtl_counter" ,$dtl_counter);
     	   	   	                        $this->set("no",$no+1);
     	   	   	                        $this->render("ceremony_ring_row");
     	   	   	                      }
     	   		                      break;
     	   	   case GC_CEREMONY_FLOWER:
     	   	   	                      if(strtoupper($type) == "ROWUNIT"){
     	   	   	                        $this->set("dtl_counter" ,$dtl_counter);
     	   	   	                        $this->set("no",$no+1);
     	   	   	                        $this->render("ceremony_flower_row");
     	   	   	                      }
     	   		                      break;
     	   	   case GC_CEREMONY_BRIDE:
     	   	   	                      if(strtoupper($type) == "ROWUNIT"){
     	   	   	                        $this->set("dtl_counter" ,$dtl_counter);
     	   	   	                        $this->set("no",$no+1);
     	   	   	                        $this->render("ceremony_bride_row");
     	   	   	                      }
     	   		                      break;
     	   	   case GC_CEREMONY_GROOM:
     	   	   	                      if(strtoupper($type) == "ROWUNIT"){
     	   	   	                        $this->set("dtl_counter" ,$dtl_counter);
     	   	   	                        $this->set("no",$no+1);
     	   	   	                        $this->render("ceremony_groom_row");
     	   	   	                      }
     	   		                      break;
     	       case GC_TRANS_CPL:
     	       	                      if(strtoupper($type) == "ROWUNIT"){
     	       	                        $this->set("head_counter",$table_counter);
 	                                    $this->set("sub_counter" ,$sub_counter);
     	   	   	                        $this->set("dtl_counter" ,$dtl_counter);
     	   	   	                        $this->set("no",$no+1);
     	   	   	                        $this->render("transportation_cpl_row");
     	   	   	                      }
     	   		                      break;
     	   	   case GC_TRANS_GST:
     	       	                      if(strtoupper($type) == "ROWUNIT"){
     	       	                        $this->set("head_counter",$table_counter);
 	                                    $this->set("sub_counter" ,$sub_counter);
     	   	   	                        $this->set("dtl_counter" ,$dtl_counter);
     	   	   	                        $this->set("no",$no+1);
     	   	   	                        $this->render("transportation_guest_row");
     	   	   	                      }
     	   		                      break;
     	   	  case GC_HAIR_MAKE_CPL:
     	       	                      if(strtoupper($type) == "ROWUNIT"){
     	       	                        $this->set("head_counter",$table_counter);
     	   	   	                        $this->set("dtl_counter" ,$dtl_counter);
     	   	   	                        $this->set("no",$no+1);
     	   	   	                        $this->render("hairmake_cpl_row");
     	   	   	                      }
     	   		                      break;
     	   	   case GC_HAIR_MAKE_GST:
     	       	                      if(strtoupper($type) == "ROWUNIT"){
     	       	                        $this->set("head_counter",$table_counter);
 	                                    $this->set("sub_counter" ,$sub_counter);
     	   	   	                        $this->set("dtl_counter" ,$dtl_counter);
     	   	   	                        $this->set("no",$no+1);
     	   	   	                        $this->render("hairmake_guest_row");
     	   	   	                      }
     	   		                      break;
     	   	   case GC_TRAVEL:
     	   	                          if(strtoupper($type) == "ROWUNIT"){
     	       	                        $this->set("head_counter",$table_counter);
 	                                    $this->set("dtl_counter" ,$dtl_counter);
 	                                    $this->set("no",$no+1);
     	   	   	                        $this->render("travel_row");
     	   	   	                      }
     	   		                      break;
     	   	   case GC_COORDINATOR:
     	   	                          if(strtoupper($type) == "ROWUNIT"){
     	       	                        $this->set("head_counter",$table_counter);
 	                                    $this->set("dtl_counter" ,$dtl_counter);
 	                                    $this->set("no",$no+1);
     	   	   	                        $this->render("coordinator_row");
     	   	   	                      }
     	   		                      break;
     	   	   case GC_PHOTO:
     	   	   	                     if(strtoupper($type) == "ROWUNIT"){
     	       	                        $this->set("head_counter",$table_counter);
 	                                    $this->set("dtl_counter" ,$dtl_counter);
 	                                    $this->set("no",$no+1);
     	   	   	                        $this->render("photographer_row");
     	   	   	                      }
     	   	   	                      break;
     	   	   case GC_VIDEO:
     	   	   	                     if(strtoupper($type) == "ROWUNIT"){
     	       	                        $this->set("head_counter",$table_counter);
 	                                    $this->set("dtl_counter" ,$dtl_counter);
 	                                    $this->set("no",$no+1);
     	   	   	                        $this->render("videographer_row");
     	   	   	                      }
     	   		                      break;
     	   	   case GC_RECEPTION_TRANS:
     	   	                            if(strtoupper($type) == "ROWUNIT"){
     	       	                        $this->set("head_counter",$table_counter);
     	       	                        $this->set("sub_counter" ,$sub_counter);
 	                                    $this->set("dtl_counter" ,$dtl_counter);
 	                                    $this->set("no",$no+1);
     	   	   	                        $this->render("reception_transportation_row");
     	   	   	                      }
     	   		                        break;

     	   	  /*
     	   	   default:               throw new Exception("予期しない商品カテゴリID[{$goods_data['GoodsMstView']['goods_ctg_id']}]です。");
     	   	   	                      break;
     	   	   */
    }
}

/**
 *
 * 現セッションの顧客のファイナル用画像ファイルを全て取得する
 */
 function _getAllFileByCustomerId(){

   $customer_id = $this->Session->read('customer_id');
   $data = $this->FileMst->getFinalSheetOutputFileInfoOfCustomer($customer_id);

   $dir_array = array();
   for($i=0;$i < count($data);$i++){
      $attr = $data[$i]["FileMst"];
      $path = "./".$attr["root_path"]."/".$attr["customer_id"]."/".$attr["folder_nm"]."/".$attr["file_nm"];
      $type = exif_imagetype($path);
      if($type == IMAGETYPE_GIF || $type == IMAGETYPE_JPEG || $type == IMAGETYPE_BMP || $type == IMAGETYPE_PNG ){
        //array_push($file_array, mb_convert_encoding("$path/$filename", "UTF-8", "AUTO"));
        $dir_array[$attr["folder_nm"]] = $path;
      }
   }
   return $dir_array;
}

/**
 *
 * 現セッションの顧客のファイナル用画像ファイルを全て取得する
 */
 function _getAllFileByCustomerId2()
 {
 	$customer_id = $this->Session->read('customer_id');
 	$targetPath = "uploads/".$customer_id;

 	$dir_array = array();
 	if(is_dir($targetPath)){

        $basehandle=dir($targetPath);      //ex upload/79
 	    while($dir=$basehandle->read())
 	    {
 	    	if($dir != "." && $dir != ".."){

 	    		$path = "$targetPath/$dir";  //ex upload/79/basicInfo

 	    		$handle = opendir($path);
 	    		$file_array = array();
 	    		while($filename = readdir($handle)){

 	    			if($filename != "." and $filename != ".."){

 	    				// 画像ファイル
 	              	    $type = exif_imagetype("$path/$filename");
 	              	    if($type == IMAGETYPE_GIF || $type == IMAGETYPE_JPEG || $type == IMAGETYPE_BMP || $type == IMAGETYPE_PNG ){
 	              	    	 //ex array(0=>'xxx.jpg',1=>'yyy.jpg')
 	              	    	 //array_push($file_array, mb_convert_encoding("$path/$filename", "UTF-8", "AUTO"));
 	              	    	 array_push($file_array, "$path/$filename");
 	              	    }
 	    	 	   }
 	    	   }
 	    	 if(!empty($file_array)){ $dir_array[$dir]=$file_array;}
 	       }
 	 	}
     }
     return $dir_array;
  }

  /**
   * [AJAX]ファイルのファイナルシート用出力の有無を更新
   */
  function updateFinalSheetOutputFlg(){

    if (!$this->RequestHandler->isAjax()){ $this->cakeError('error404'); }

    $this->layout = '';
    $this->autoRender =false;
    configure::write('debug', 0);

    $tr = ClassRegistry::init('TransactionManager');
    $tr->begin();

    for($i=0;$i < count($this->data['file']);$i++){

      if($this->FileMst->isSetFinalSheetOutputOn($this->data['file'][$i])){
        $ret = $this->FileMst->setFinalSheetOutputOff($this->data['file'][$i],$this->Auth->user('username'));
        if($ret['result']==false){ return json_encode($ret); }

      }else{
        $ret = $this->FileMst->setFinalSheetOutputOn($this->data['file'][$i],$this->Auth->user('username'));
        if($ret['result']==false){ return json_encode($ret); }
      }
    }

    $tr->commit();
    return json_encode(array('result'=>true,'message'=>'処理完了しました。'));  }
}
?>
