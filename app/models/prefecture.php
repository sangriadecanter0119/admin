<?php 
class Prefecture extends AppModel {
    var $useTable = false;
    var $prefectures = array(
     '0'=>array('北海道地方'=>array('北海道')),
     '1'=>array('東北地方'=>array('青森県','秋田県','岩手県','福島県','宮城県','山形県')),
     '2'=>array('関東甲信'=>array('東京都','神奈川県','埼玉県','栃木県','茨城県','千葉県','群馬県','山梨県','長野県')),
     '3'=>array('東海地方'=>array('静岡県','岐阜県','愛知県','三重県')),
     '4'=>array('北陸地方'=>array('新潟県','福井県','富山県','石川県')),
     '5'=>array('近畿地方'=>array('京都府','大阪府','滋賀県','奈良県','和歌山県','兵庫県')),
     '6'=>array('中国地方'=>array('広島県','鳥取県','島根県','岡山県','山口県')),
     '7'=>array('四国地方'=>array('愛媛県','徳島県','高知県','香川県')),
     '8'=>array('九州地方'=>array('福岡県','大分県','熊本県','長崎県','宮崎県','鹿児島県','佐賀県')),
     '9'=>array('沖縄地方'=>array('沖縄県'))   
    );   
    
    function GetListOfDivisions(){

    	$divisions = array();
    	for($i=0;$i < count($this->prefectures);$i++){
    	   $keys = array_keys( $this->prefectures[$i]);
    	   $divisions[$i] = $keys[0];
    	}
    	return $divisions;
    }
    
    function GetPrefecturesByDivision($division_id)
    {
    	$division = $this->prefectures[$division_id];
    	$keys = array_keys($division);
       	return $division[$keys[0]];
    }
}
?>
