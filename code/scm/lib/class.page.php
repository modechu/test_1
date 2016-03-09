<?php
class Page {


    ###################################################################
	#			search->get_full_page()
	####### 取得檢索結果總頁數	2006.11.14
	
	function get_max_page()
	{
		$total_count =$this->max_no;
		$row_per_page = $this->row_per_page;
		$full_page = $total_count / $row_per_page;
		settype($full_page, 'integer');
		if (($total_count % $row_per_page) != 0) {$full_page=$full_page+1;}
		return $full_page;		
	}
	
	###################################################################
	#			search->get_full_page()
	####### 分頁頁碼顯示	2006.11.14
	function get_page($now_page,$pagesize=10)
	{
		$row_per_page = $this->row_per_page;
//		$pagesize=10;
		if(substr($now_page,0,1)=="p")
		{		
			$now_page=substr($now_page,1);
			$now_page=$now_page-$pagesize;
		}elseif(substr($now_page,0,1)=="n"){			
			    $now_page=substr($now_page,1);
			    $now_page=$now_page+1;				    		    			
		}
		$this->now_pp = $now_page;
		$g_page = ($now_page / $pagesize );		
		settype($g_page, 'integer');
		if (($now_page%$pagesize)==0) $g_page=$g_page-1;
        $g_page=$g_page*$pagesize+1;
        $j=0;
		for($i=$g_page; $i< $g_page +$pagesize; $i++)
			{
				$pages[$j]=$i;
				$j++;				
			}	
			$limit_page= ($now_page-1)*$row_per_page;
			$this->add_limit_condition($limit_page);
			return $pages;
		}
		
		
		
		
		
		
}
?>    
    
    