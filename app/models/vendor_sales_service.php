<?php
class VendorSalesService extends AppModel {
	var $useTable = false;

	/**
	 * ベンダー売上詳細一覧を取得する
	 * @param unknown $wedding_dt
	 * @return multitype:unknown
	 */
	function GetVendorSalesDetailList($start_wedding_dt,$end_wedding_dt){

		$sql = "SELECT
 					 ctg.id            			AS goods_ctg_id
				   	,ctg.goods_ctg_nm  			AS goods_ctg_nm
					,dtl.vendor_id     			AS vendor_id
					,ven.vendor_nm     			AS vendor_nm
					,dtl.goods_id      			AS goods_id
					,gds.goods_cd      			AS goods_cd
					,gds.goods_nm      			AS goods_nm
					,dtl.num           			AS num
					,CASE WHEN dtl.currency_kbn = 0 THEN
    					dtl.sales_price * dtl.sales_exchange_rate
 					 ELSE
    					dtl.sales_price
					 END                        AS sales_price
					,CASE WHEN dtl.currency_kbn = 0 THEN
    					dtl.sales_cost * dtl.cost_exchange_rate
 					 ELSE
    					dtl.sales_cost
 					 END                        AS sales_cost
					,cus.id                     AS customer_id
					,CONCAT(cus.grmls_kj,cus.grmfs_kj) 	AS customer_nm
					,con.wedding_dt    			AS wedding_dt
					,cus.status_id     			AS status_id
				  FROM estimate_dtl_trns dtl
			INNER JOIN estimate_trns est
        			ON est.id = dtl.estimate_id
			INNER JOIN customer_msts cus
        			ON cus.id = est.customer_id
			INNER JOIN contract_trns con
        			ON con.customer_id = cus.id
			 LEFT JOIN goods_msts gds
       				ON gds.id = dtl.goods_id
			 LEFT JOIN goods_kbn_msts kbn
       				ON kbn.id =  gds.goods_kbn_id
			 LEFT JOIN goods_ctg_msts ctg
       				ON ctg.id = kbn.goods_ctg_id
			 LEFT JOIN vendor_msts ven
       				ON ven.id = dtl.vendor_id
			WHERE dtl.set_goods_kbn = 0
  			  AND est.adopt_flg = 1
  			  AND cus.status_id IN ('7','8')
  			  AND SUBSTR(con.wedding_dt,1,7) >='".$start_wedding_dt."'
  			  AND SUBSTR(con.wedding_dt,1,7) <='".$end_wedding_dt."'
		    UNION ALL
  			  	SELECT
 					 ctg.id            			AS goods_ctg_id
				   	,ctg.goods_ctg_nm  			AS goods_ctg_nm
					,sgd.vendor_id     			AS vendor_id
					,ven.vendor_nm     			AS vendor_nm
					,sgd.goods_id      			AS goods_id
					,gds.goods_cd      			AS goods_cd
					,gds.goods_nm      			AS goods_nm
					,dtl.num * sgd.num 			AS num
			  		,CASE WHEN dtl.currency_kbn = 0 THEN
    					(dtl.num * sgd.sales_cost) * dtl.cost_exchange_rate
 					 ELSE
    					dtl.num * sgd.sales_cost
 					 END                        AS sales_price
					,CASE WHEN dtl.currency_kbn = 0 THEN
    					(dtl.num * sgd.sales_cost) * dtl.cost_exchange_rate
 					 ELSE
    					dtl.num * sgd.sales_cost
 					 END                        AS sales_cost
					,cus.id                     AS customer_id
					,CONCAT(cus.grmls_kj,cus.grmfs_kj) 	AS customer_nm
					,con.wedding_dt    			AS wedding_dt
					,cus.status_id     			AS status_id
				  FROM estimate_dtl_trns dtl
		    INNER JOIN set_goods_estimate_dtl_trns sgd
       				ON sgd.estimate_dtl_id =  dtl.id
			INNER JOIN estimate_trns est
        			ON est.id = dtl.estimate_id
			INNER JOIN customer_msts cus
        			ON cus.id = est.customer_id
			INNER JOIN contract_trns con
        			ON con.customer_id = cus.id
			 LEFT JOIN goods_msts gds
       				ON gds.id = sgd.goods_id
			 LEFT JOIN goods_kbn_msts kbn
       				ON kbn.id =  gds.goods_kbn_id
			 LEFT JOIN goods_ctg_msts ctg
       				ON ctg.id = kbn.goods_ctg_id
			 LEFT JOIN vendor_msts ven
       				ON ven.id = sgd.vendor_id
			WHERE dtl.set_goods_kbn = 1
  			  AND est.adopt_flg = 1
  			  AND cus.status_id IN ('7','8')
  			  AND SUBSTR(con.wedding_dt,1,7) >='".$start_wedding_dt."'
  			  AND SUBSTR(con.wedding_dt,1,7) <='".$end_wedding_dt."'";
//debug($this->query($sql));
		return $this->query($sql);
	}

	/**
	 * ベンダー売上要約一覧を取得する
	 * @param unknown $wedding_dt
	 * @return multitype:unknown
	 */
	function GetVendorSalesList($start_wedding_dt,$end_wedding_dt){


		$sql = "SELECT
 					 T1.goods_ctg_id       			AS goods_ctg_id
				   	,MAX(T1.goods_ctg_nm) 			AS goods_ctg_nm
				   	,T1.vendor_id     			    AS vendor_id
					,MAX(T1.vendor_nm)    			AS vendor_nm
					,SUM(T1.num)     			    AS sales_num
					,SUM(T1.sales_price * T1.num)	AS sales_price
					,SUM(T1.sales_cost  * T1.num)	AS sales_cost
					,COUNT(DISTINCT T1.customer_id) AS customer_num
				  FROM
  					(SELECT
 						 ctg.id            			AS goods_ctg_id
						,ctg.goods_ctg_nm  			AS goods_ctg_nm
						,dtl.vendor_id     			AS vendor_id
						,ven.vendor_nm     			AS vendor_nm
						,dtl.goods_id      			AS goods_id
						,gds.goods_cd      			AS goods_cd
						,gds.goods_nm      			AS goods_nm
						,dtl.num           			AS num
						,CASE WHEN dtl.currency_kbn = 0 THEN
	    					dtl.sales_price * dtl.sales_exchange_rate
	 					 ELSE
	    					dtl.sales_price
	 					 END                        AS sales_price
						,CASE WHEN dtl.currency_kbn = 0 THEN
  	    					dtl.sales_cost * dtl.cost_exchange_rate
 	 					 ELSE
    	    				dtl.sales_cost
         				 END                        AS sales_cost
						,cus.id                                 AS customer_id
						,CONCAT(cus.grmls_kj,cus.grmfs_kj) 	AS customer_nm
						,con.wedding_dt    			AS wedding_dt
						,cus.status_id     			AS status_id
    				   FROM estimate_dtl_trns dtl
				 INNER JOIN estimate_trns est
        				 ON est.id = dtl.estimate_id
				 INNER JOIN customer_msts cus
        				 ON cus.id = est.customer_id
				 INNER JOIN contract_trns con
        				 ON con.customer_id = cus.id
				 LEFT JOIN goods_msts gds
       					ON gds.id = dtl.goods_id
				 LEFT JOIN goods_kbn_msts kbn
       					ON kbn.id =  gds.goods_kbn_id
				 LEFT JOIN goods_ctg_msts ctg
       					ON ctg.id = kbn.goods_ctg_id
				 LEFT JOIN vendor_msts ven
       					ON ven.id = dtl.vendor_id
   				WHERE dtl.set_goods_kbn = 0
     			  AND est.adopt_flg = 1
     			  AND cus.status_id IN ('7','8')
     			  AND SUBSTR(con.wedding_dt,1,7) >='".$start_wedding_dt."'
   			      AND SUBSTR(con.wedding_dt,1,7)    <='".$end_wedding_dt."'
            UNION ALL
  			  	SELECT
 					 ctg.id            			AS goods_ctg_id
				   	,ctg.goods_ctg_nm  			AS goods_ctg_nm
					,sgd.vendor_id     			AS vendor_id
					,ven.vendor_nm     			AS vendor_nm
					,sgd.goods_id      			AS goods_id
					,gds.goods_cd      			AS goods_cd
					,gds.goods_nm      			AS goods_nm
					,dtl.num * sgd.num 			AS num
   			      	,CASE WHEN dtl.currency_kbn = 0 THEN
    					(dtl.num * sgd.sales_cost) * dtl.cost_exchange_rate
 					 ELSE
    					dtl.num * sgd.sales_cost
 					 END                        AS sales_price
					,CASE WHEN dtl.currency_kbn = 0 THEN
    					(dtl.num * sgd.sales_cost) * dtl.cost_exchange_rate
 					 ELSE
    					dtl.num * sgd.sales_cost
 					 END                        AS sales_cost
					,cus.id                     AS customer_id
					,CONCAT(cus.grmls_kj,cus.grmfs_kj) 	AS customer_nm
					,con.wedding_dt    			AS wedding_dt
					,cus.status_id     			AS status_id
				  FROM estimate_dtl_trns dtl
		    INNER JOIN set_goods_estimate_dtl_trns sgd
       				ON sgd.estimate_dtl_id =  dtl.id
			INNER JOIN estimate_trns est
        			ON est.id = dtl.estimate_id
			INNER JOIN customer_msts cus
        			ON cus.id = est.customer_id
			INNER JOIN contract_trns con
        			ON con.customer_id = cus.id
			 LEFT JOIN goods_msts gds
       				ON gds.id = sgd.goods_id
			 LEFT JOIN goods_kbn_msts kbn
       				ON kbn.id =  gds.goods_kbn_id
			 LEFT JOIN goods_ctg_msts ctg
       				ON ctg.id = kbn.goods_ctg_id
			 LEFT JOIN vendor_msts ven
       				ON ven.id = sgd.vendor_id
			WHERE dtl.set_goods_kbn = 1
  			  AND est.adopt_flg = 1
  			  AND cus.status_id IN ('7','8')
  			  AND SUBSTR(con.wedding_dt,1,7) >='".$start_wedding_dt."'
  			  AND SUBSTR(con.wedding_dt,1,7) <='".$end_wedding_dt."') T1
			GROUP BY T1.goods_ctg_id,T1.vendor_id
			ORDER BY sales_price DESC";

		return $this->query($sql);
	}
}