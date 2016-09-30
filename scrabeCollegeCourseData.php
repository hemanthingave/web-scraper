<?php
	set_time_limit(0); 
	ini_set('memory_limit', '30240M'); //MAX limit is 3GB 
	include_once('cfg.php');
	include_once('simplehtmldom_1_5/simple_html_dom.php');
	
	function scrap_code_of_institute($InstituteCode=''){
		$html=$course_str1=$college_str1="";
		// create HTML link
		$html = file_get_html('http://www.dtemaharashtra.gov.in/approvedinstitues/StaticPages/frmInstituteSummary.aspx?InstituteCode='.$InstituteCode);
		//$html = file_get_html('http://localhost:8088/hemant/scrab_dte/approvedinstitues/StaticPages/frmInstituteSummary8452.html');
		
		// Find all <td> in <table> which class=AppFormTable 
		$tds = $html->find('table.AppFormTable td');
		$i=0;
		
		foreach($tds as $td_single) {
			// skip heading
			if($i%2==0) {$i++; continue;}
			//Replace comma(,) by &#44; &#44 is html code for comma
			$college_str1 .= trim(str_replace(',','&#44;',$td_single->plaintext)) .","; //
			$i++;
		}
		$college_str1 = str_replace(array("\r\n", "\r"), "\n", $college_str1);// remove new line
		$college_str1 = $college_str1.PHP_EOL; // add end of line
		
		//course details
		$h_tds = $html->find('table.AppFormTableNew th');
		$h_arr=array();
		foreach($h_tds as $h_td) {
			//Replace , by &#44; html code for comma
			$h_arr[] = trim(str_replace(',','&#44;',$h_td->plaintext)); //
		}
		
		$ct=0;
		if(count($h_arr)>0){
			foreach($html->find('table.DataGrid') as $tables)  //process 1 Tables
			{	
				$course_str_rows='';
				foreach($tables->find('tr') as $inner_tr)    // Process 1 row
				{
					$inner_td_str="";
					foreach($inner_tr->find('td.Item') as $inner_td) // Process 1 td
					{
						$inner_td_str .= trim(str_replace(',','&#44;',$inner_td->plaintext)).','; 
					}
					if($inner_td_str!=""){
						$course_str_rows.=$InstituteCode.','.$h_arr[$ct].','.trim($inner_td_str,',').PHP_EOL;
					}
				} 
				if($course_str_rows!=""){
					$course_str1.=$course_str_rows;
				}
				$ct++;
			}
		}
		// clean up memory
		$html->clear();
		$ret[0]=$college_str1;
		$ret[1]=$course_str1;
		unset($html);
		unset($college_str1);
		unset($course_str1);
		return $ret;
	}
	
	
	// Fetch institute id from db
	$institutes_ids = array();
	$result = $conn->query("SELECT * FROM `col_college_t_mst` WHERE `I_CODE` in (6532,6535)  ");
	while($row = $result->fetch_assoc()) {
		$institutes_ids[] = $row["I_CODE"];
	}
	$conn->close();
	//var_dump($institutes_ids);die;
	$csv_clg_header = 'InstituteCode,AICTECurrentApplicationID,PermanentAICTEApplicationNo,UniversityApplicationNo,InstituteName,DTERegion,RegionType,Address,District,Taluka,Pincode,STDCode,YearofEstablishment,WebAddress,EMailAddress,NearestRailwayStation,Distance_km_railway,NearestBusStand,Distance_bus_km,NearestAirport,Distance_KM_airport,BoysTotal,GirlsTotal,Boys1stYear,Girls1stYear,Name_principal,OfficePhoneNo,PersonalPhoneNo,ResidentialPhoneNo,FaxNo,Name_Registrar,GovtApprovalLetterNo,GovtApprovalDate,DTEApprovalLetterNo,DTEApprovalDate,AICTEApprovalLetterNo,AICTEApprovalDate,Status1,Status2,Status3,Remark'.PHP_EOL;
	$csv_cor_header = 'InstituteId,mainCourse,ChoiceCode,CourseName,CourseType,GenderStatus,Accreditation,StartYear,Intake'.PHP_EOL;
	$clg_file = fopen("college_mst.csv", "w");
	$course_file = fopen("course_mst.csv", "w");
	fwrite($clg_file, $csv_clg_header);
	fwrite($course_file, $csv_cor_header);
	//$institutes_ids = array('1003');
	//echo "..................Process Start...............</br>";
	foreach($institutes_ids AS $institutes_id){
		$ret_data = scrap_code_of_institute($institutes_id);
		if(!empty($ret_data[0])){
			fwrite($clg_file, $ret_data[0]);
		}
		if(!empty($ret_data[1])){
			fwrite($course_file, $ret_data[1]);
		}
		//echo "Data fetch for id $institutes_id</br>";
		//Sleep for 15 to 30 sec
		sleep(rand (15,30));
	}
	fclose($clg_file);
	fclose($course_file); 
	echo "...................Process end..................</br>";
?>