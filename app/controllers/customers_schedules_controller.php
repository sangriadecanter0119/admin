<?php
class CustomersSchedulesController extends AppController
{

 public $name = 'CustomersSchedules';
 public $uses = array("CustomerScheduleTrn","CustomerScheduleTrnView","ContractTrn","ContractTrnView","CustomerMst","User","EstimateTrn","EstimateDtlTrnView","EstimateDtlTrn","TravelTrn");
 public $layout = null;
 public $components = array('Auth','RequestHandler');
 public $helpers = array("Javascript");

 function index()
 {
 	//$this->redirect(array('controller' => "CustomersSchedules", 'action' => "calendar"));
 	$this->redirect('https://'.$_SERVER['HTTP_HOST'].'/admin/CustomersSchedules/calendar');
 }

 function feed()
 {
  //1. Transform request parameters to MySQL datetime format.
  $mysqlstart = date( 'Y-m-d H:i:s', $this->params['url']['start']);
  $mysqlend = date('Y-m-d H:i:s', $this->params['url']['end']);

  //2. Get the events corresponding to the time range
  $conditions = array('CustomerScheduleTrnView.start_dt BETWEEN ? AND ?' => array($mysqlstart,$mysqlend));
  $events = $this->CustomerScheduleTrnView->find('all',array('conditions' =>$conditions));

  //3. Create the json array
  $rows = array();
   for ($a=0; count($events)> $a; $a++)
   {
      //Is it an all day event?
      $all = ($events[$a]['CustomerScheduleTrnView']['allday'] == 1);

      //Create an event entry
      $rows[] = array('id'    => $events[$a]['CustomerScheduleTrnView']['id'],
                      'title'  => date('H:i',strtotime($events[$a]['CustomerScheduleTrnView']['start_dt'])).":".
                                  $events[$a]['CustomerScheduleTrnView']['grmls_kj']."様：".
                                  $events[$a]['CustomerScheduleTrnView']['title'],
                      'start'  => date('Y-m-d H:i', strtotime($events[$a]['CustomerScheduleTrnView']['start_dt'])),
                      'end'    => date('Y-m-d H:i',strtotime($events[$a]['CustomerScheduleTrnView']['end_dt'])),
                      'allDay' => $all,
                      'eventType'=>'');
    }

   /* 予定自動取得処理 */
   //挙式1ヶ月前
   $conditions = array('(ContractTrnView.wedding_dt - interval 1 month) BETWEEN ? AND ?' => array($mysqlstart,$mysqlend));
   $events = $this->ContractTrnView->find('all',array('conditions' =>$conditions));
   for ($a=0; count($events)> $a; $a++)
   {
   	  $estimate_data = $this->EstimateDtlTrnView->find('all',array('conditions' =>array('estimate_id'=>$events[$a]['ContractTrnView']['estimate_id'])));
      $church_code =  $this->EstimateDtlTrn->findChurchCode($estimate_data);

      $rows[] = array('id'     => -1,
                      'title'  => date('mdY',strtotime($events[$a]['ContractTrnView']['wedding_dt']))." ".
                                  $church_code.' '.
                                  $events[$a]['ContractTrnView']['grmls_kj'].'様',
                      'start'  => date('Y-m-d H:i', strtotime($events[$a]['ContractTrnView']['wedding_dt'].'- 1 month')),
                      'end'    => date('Y-m-d H:i', strtotime($events[$a]['ContractTrnView']['wedding_dt'].'- 1 month')),
                      'allDay' => true,
                      'eventType'=>'monthBeforeWedding');
    }

   //日本出発日
   $sql = "SELECT trv.arrival_dt , cust.id , cust.grmls_kj ".
            "FROM travel_trns trv " .
       "LEFT JOIN customer_mst_views cust ON cust.id = trv.customer_id ".
           "WHERE (cust.status_id >= ? AND cust.status_id <= ?) AND trv.arrival_dt BETWEEN ? AND ?";

   $events = $this->TravelTrn->query($sql,array(CS_CONTRACT,CS_WEDDING,$mysqlstart,$mysqlend));
   for ($a=0; count($events)> $a; $a++)
   {
   	  $estimate_id = $this->EstimateTrn->GetAdoptedEstimateIdByCustomerId($events[$a]['cust']['id']);
   	  if($estimate_id != null){
   	    $estimate_data = $this->EstimateDtlTrnView->find('all',array('conditions' =>array('estimate_id'=>$estimate_id )));
        $church_code =  $this->EstimateDtlTrn->findChurchCode($estimate_data);

        $rows[] = array('id'    => -1,
                        'title'  => date('mdY',strtotime($this->ContractTrn->GetWeddingDateByCustomerId($events[$a]['cust']['id'])))." ".
                                    $church_code.' '.
                                    $events[$a]['cust']['grmls_kj'].'様',
                        'start'  => date('Y-m-d H:i', strtotime($events[$a]['trv']['arrival_dt'])),
                        'end'    => date('Y-m-d H:i',strtotime($events[$a]['trv']['arrival_dt'])),
                        'allDay' => true,
                        'eventType'=>'homeDepartureDate');
   	  }
    }

   //挙式日
   $conditions = array('ContractTrnView.wedding_dt BETWEEN ? AND ?' => array($mysqlstart,$mysqlend));
   $events = $this->ContractTrnView->find('all',array('conditions' =>$conditions));
   for ($a=0; count($events)> $a; $a++)
   {
      $estimate_data = $this->EstimateDtlTrnView->find('all',array('conditions' =>array('estimate_id'=>$events[$a]['ContractTrnView']['estimate_id'])));
      $church_code =  $this->EstimateDtlTrn->findChurchCode($estimate_data);

      $rows[] = array('id'    => -1,
                      'title'  => date('mdY',strtotime($events[$a]['ContractTrnView']['wedding_dt']))." ".
                                  $church_code.' '.
                                  $events[$a]['ContractTrnView']['grmls_kj'].'様',
                      'start'  => date('Y-m-d H:i', strtotime($events[$a]['ContractTrnView']['wedding_dt'])),
                      'end'    => date('Y-m-d H:i',strtotime($events[$a]['ContractTrnView']['wedding_dt'])),
                      'allDay' => true,
                      'eventType'=>'weddingDate');
    }

   //現地出発日
    $sql = "SELECT trv.departure_dt , cust.id , cust.grmls_kj ".
            "FROM travel_trns trv " .
       "LEFT JOIN customer_mst_views cust ON cust.id = trv.customer_id ".
           "WHERE (cust.status_id >= ? AND cust.status_id <= ?) AND trv.departure_dt BETWEEN ? AND ?";

   $events = $this->TravelTrn->query($sql,array(CS_CONTRACT,CS_WEDDING,$mysqlstart,$mysqlend));
   for ($a=0; count($events)> $a; $a++)
   {
   	  $estimate_id = $this->EstimateTrn->GetAdoptedEstimateIdByCustomerId($events[$a]['cust']['id']);
   	  if($estimate_id != null){
   	    $estimate_data = $this->EstimateDtlTrnView->find('all',array('conditions' =>array('estimate_id'=>$estimate_id )));
        $church_code =  $this->EstimateDtlTrn->findChurchCode($estimate_data);

        $rows[] = array('id'    => -1,
                        'title'  => date('mdY',strtotime($this->ContractTrn->GetWeddingDateByCustomerId($events[$a]['cust']['id'])))." ".
                                    $church_code.' '.
                                    $events[$a]['cust']['grmls_kj'].'様',
                        'start'  => date('Y-m-d H:i', strtotime($events[$a]['trv']['departure_dt'])),
                        'end'    => date('Y-m-d H:i',strtotime($events[$a]['trv']['departure_dt'])),
                        'allDay' => true,
                        'eventType'=>'abroadDepartureDate');
   	  }
    }

   //4. Return as a json array
   Configure::write('debug', 0);
   $this->autoRender = false;
   $this->autoLayout = false;
   $this->header('Content-Type: application/json');
   echo json_encode($rows);
 }

 function add($allday=null,$day=null,$month=null,$year=null,$hour=null,$min=null)
   {
   	App::import('Sanitize');

    if (empty($this->data))
     {
        //Set default duration: 1hr and format to a leading zero.
        $hourPlus=intval($hour)+1;
        if (strlen($hourPlus)==1)
        {
            $hourPlus = '0'.$hourPlus;
        }

        //Create a time string to display in view. The time string
        //is either  "Fri 26 / Mar, 09 : 00 a?” 10 : 00" or
        //"All day event: (Fri 26 / Mar)"
        if ($allday=='true')
        {
            $event['CustomerScheduleTrn']['allday'] = 1;
            $displayTime = date('Y',strtotime($day.'/'.$month.'/'.$year)).' / '.
                           date("m", mktime(0, 0, 0, $month, 10)).' / '. $day.' ('.
                           date('D',strtotime($day.'/'.$month.'/'.$year)).')';
        }
         else
       {
            $event['CustomerScheduleTrn']['allday'] = 0;
            $displayTime = date('Y',strtotime($day.'/'.$month.'/'.$year)).' / '.
                           date("m", mktime(0, 0, 0, $month, 10)).' / '. $day.
                           ', '.$hour.' : '.$min.' &mdash; '.$hourPlus.' : '.$min;
        }
        $this->set("displayTime",$displayTime);

        //Populate the event fields for the add form
        $event['CustomerScheduleTrn']['title'] = 'Event description';
        $event['CustomerScheduleTrn']['start'] = $year.'-'.$month.'-'.$day.' '.$hour.':'.$min.':00';
        $event['CustomerScheduleTrn']['end']   = $year.'-'.$month.'-'.$day.' '.$hourPlus.':'.$min.':00';
        $this->set('event',$event);

        $this->set("customer_list",$this->CustomerMst->find('all'));
        $this->set("user_list",$this->User->find('all'));
        //Do not use a view template.
        $this->layout="";
    }
    else
   {
   	      $start_dt = date('Y',strtotime($this->data['CustomerScheduleTrn']['start_dt'])).'-'.
                      date("m",strtotime($this->data['CustomerScheduleTrn']['start_dt'])).'-'.
                      date("d",strtotime($this->data['CustomerScheduleTrn']['start_dt'])).' '.
                      $this->data['Tmp']['start_hour'].':'.
                      $this->data['Tmp']['start_min'].':00';

          $end_dt =   date('Y',strtotime($this->data['CustomerScheduleTrn']['end_dt'])).'-'.
                      date("m",strtotime($this->data['CustomerScheduleTrn']['end_dt'])).'-'.
                      date("d",strtotime($this->data['CustomerScheduleTrn']['end_dt'])).' '.
                      $this->data['Tmp']['end_hour'].':'.
                      $this->data['Tmp']['end_min'].':00';


        //Create and save the new event in the table.
        //Event type is set to editable - because this is a user event.
        $this->CustomerScheduleTrn->create();
        $this->data['CustomerScheduleTrn']['start_dt'] = $start_dt;
        $this->data['CustomerScheduleTrn']['end_dt'] = $end_dt;
      //サニタイズすると日本語が認識されない
      //  $this->data['CustomerScheduleTrn']['title'] = Sanitize::paranoid($this->data["CustomerScheduleTrn"]["title"], array('!','\'','?','_','.',' ','-'));
        $this->data['CustomerScheduleTrn']['editable']='1';
        $this->data['CustomerScheduleTrn']['reg_nm']=$this->Auth->user('username');
        $this->data['CustomerScheduleTrn']['reg_dt']=date('Y/m/d h:i:s');
        $this->CustomerScheduleTrn->save($this->data);
        //$this->redirect(array('controller' => "CustomersSchedules", 'action' => "index"));
        $this->redirect('https://'.$_SERVER['HTTP_HOST'].'/admin/CustomersSchedules/index');
    }
  }

 function edit($id=null)
   {
    if (empty($this->data))
    {
        if ($id==null)
        {
            //fail gracefully in case of error
            return;
        }

        $ev = $this->CustomerScheduleTrnView->findById($id);
       // $ev['CustomerScheduleTrnView']['start_dt']=date('Y-m-d h:i:s',strtotime($ev['CustomerScheduleTrnView']['start_dt']));
       // $ev['CustomerScheduleTrnView']['end_dt']=date('Y-m-d h:i:s',strtotime($ev['CustomerScheduleTrnView']['end_dt']));
        $this->set("event",$ev);
        if ($ev['CustomerScheduleTrnView']['allday']=='1')
        {
        	$displayTime = date('Y',strtotime($ev['CustomerScheduleTrnView']['start_dt'])).' / '.
                           date('m',strtotime($ev['CustomerScheduleTrnView']['start_dt'])).' / '.
                           date('d',strtotime($ev['CustomerScheduleTrnView']['start_dt'])).' ('.
                           date('D',strtotime($ev['CustomerScheduleTrnView']['start_dt'])).')';
        }
        else
       {
            $displayTime = date('D M d, H:i',strtotime($ev['CustomerScheduleTrnView']['start_dt'])) . '&mdash;' . date('H:i',strtotime($ev['CustomerScheduleTrnView']['end_dt']));
        }
        $this->set('displayTime',$displayTime);
        $this->set("user_list",$this->User->find('all'));
        $this->layout="";
    }
    else
    {
    	 $start_dt =  date('Y',strtotime($this->data['CustomerScheduleTrn']['start_dt'])).'-'.
                      date("m",strtotime($this->data['CustomerScheduleTrn']['start_dt'])).'-'.
                      date("d",strtotime($this->data['CustomerScheduleTrn']['start_dt'])).' '.
                      $this->data['Tmp']['start_hour'].':'.
                      $this->data['Tmp']['start_min'].':00';

          $end_dt =   date('Y',strtotime($this->data['CustomerScheduleTrn']['end_dt'])).'-'.
                      date("m",strtotime($this->data['CustomerScheduleTrn']['end_dt'])).'-'.
                      date("d",strtotime($this->data['CustomerScheduleTrn']['end_dt'])).' '.
                      $this->data['Tmp']['end_hour'].':'.
                      $this->data['Tmp']['end_min'].':00';

        $this->CustomerScheduleTrn->updateAll(array('CustomerScheduleTrn.start_dt'=>  "'{$start_dt}'",
                                                      'CustomerScheduleTrn.end_dt'  =>  "'{$end_dt}'",
                                                      'CustomerScheduleTrn.title'   => "'{$this->data['CustomerScheduleTrn']['title']}'",
                                                      'CustomerScheduleTrn.note'    => "'{$this->data['CustomerScheduleTrn']['note']}'",
                                                      'CustomerScheduleTrn.attend_id' => "'{$this->data['CustomerScheduleTrn']['attend_id']}'",
                                                      'CustomerScheduleTrn.upd_nm'  => "'{$this->Auth->user('username')}'",
                                                      'CustomerScheduleTrn.upd_dt'  =>   "'".date('Y/m/d h:i:s')."'"),
                                               array('CustomerScheduleTrn.id =' => $this->data['CustomerScheduleTrn']['id']));


        /*$this->redirect(array('controller' => "CustomersSchedules", 'action' => "calendar",
             substr($this->data['CustomerScheduleTrn']['start_dt'],0,4),
             substr($this->data['CustomerScheduleTrn']['start_dt'],5,2),
             substr($this->data['CustomerScheduleTrn']['start_dt'],8,2)));*/
        $this->redirect('https://'.$_SERVER['HTTP_HOST'].'/admin/CustomersSchedules/calendar/'.
        		substr($this->data['CustomerScheduleTrn']['start_dt'],0,4).'/'.
        		substr($this->data['CustomerScheduleTrn']['start_dt'],5,2).'/'.
        		substr($this->data['CustomerScheduleTrn']['start_dt'],8,2));
    }
  }

 function delete($id=null)
   {
        if ($id==null)
        {
            //fail gracefully in case of error
            return;
        }

        $this->CustomerScheduleTrn->delete($id);
        //$this->redirect(array('controller' => "CustomersSchedules", 'action' => "index"));
        $this->redirect('https://'.$_SERVER['HTTP_HOST'].'/admin/CustomersSchedules"/index');
  }

 function move ($id=null,$dayDelta,$minDelta,$allDay)
  {
     if ($id!=null)
      {
         $ev = $this->CustomerScheduleTrn->findById($id);  //1 - locate the event in the DB
         if ($allDay=='true')
         { //2- handle all day events
            $ev['CustomerScheduleTrn']['allday'] = 1;
         }
         else
        {
            $ev['CustomerScheduleTrn']['allday'] = 0;
         }
            //3 - Start
        $ev['CustomerScheduleTrn']['end_dt']=date('Y-m-d H:i:s',strtotime(''.$dayDelta.' days '.$minDelta.' minutes',strtotime($ev['CustomerScheduleTrn']['end_dt'])));
        $ev['CustomerScheduleTrn']['start_dt']=date('Y-m-d H:i:s',strtotime(''.$dayDelta.' days '.$minDelta.' minutes',strtotime($ev['CustomerScheduleTrn']['start_dt'])));

        $this->CustomerScheduleTrn->save($ev); //4 - Save the event with the new data
        //5 - redirect and reload
        /*$this->redirect(array('controller' => "CustomersSchedules", 'action' => "calendar",
             substr($ev['CustomerScheduleTrn']['start_dt'],0,4),
             substr($ev['CustomerScheduleTrn']['start_dt'],5,2),
             substr($ev['CustomerScheduleTrn']['start_dt'],8,2)));*/
        $this->redirect('https://'.$_SERVER['HTTP_HOST'].'/admin/CustomersSchedules/calendar/'.
        		substr($this->data['CustomerScheduleTrn']['start_dt'],0,4).'/'.
        		substr($this->data['CustomerScheduleTrn']['start_dt'],5,2).'/'.
        		substr($this->data['CustomerScheduleTrn']['start_dt'],8,2));
      }
  }

 function calendar($year=null,$month=null,$day=null)
  {
    if ($year!=null)
    {
        $this->set('openYear',$year);
        if ($month!=null)
        {
            $month = ltrim($month,'0');
            $month = $month-1;
            $this->set('openMonth',$month);
        }
        if ($day!=null)
        {
            $day = ltrim($day,'0');
            $this->set('openDay',$day);
        }
    }

    $this->set("menu_customers","current");
 	$this->set("menu_customer","disable");
 	$this->set("menu_fund","");

 	$this->set("sub_menu_customers_list","");
 	$this->set("sub_menu_customers_company_contact","");
 	$this->set("sub_menu_customers_schedules","current");
 	$this->set("sub_menu_customers_contract_list","");
 	$this->set("sub_menu_attendant_state","");
 	$this->set("sub_menu_wedding_reservations","");

 	$this->set("sub_title","顧客スケジュール");
 	$this->set("user",$this->Auth->user());
  }
}
?>