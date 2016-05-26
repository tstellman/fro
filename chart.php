<?php
ob_start();

include('fusioncharts.php');

function loadChart($type, $startDate, $endDate, $AgencyNoOption, $DonorNoOption, $PickupType, $PickupTypeOption, $initAgencyNo){

echo("<script src='fusioncharts/js/fusioncharts.js'></script>");


if (!empty($AgencyNoOption)){
	$AgencyNo = "AND ph.ProgramNumber = '" . $AgencyNoOption . "'";
}
if (!empty($DonorNoOption)){
	$DonorNo = "AND ph.DonorNumber = '" . $DonorNoOption . "'";
}


if ($type == "Donor"){
		$url = "programreport.php?sd=" . $startDate . "&ed=" . $endDate;
		if (checkAdmin()){
		echo "<a class='chartBack' href='$url'>back</a>";
		}

		/*--- BEGIN Total Pounds by Donor ---*/
		/* This query returns Total Pounds by Donor based on GRID filters*/
		$chartResult = mysql_query("SELECT ph.PickupId, p.ProgramNumber, p.ProgramName, pt.SUMTotalAmount, pt.DonorNumber, pt.PickupType
										FROM PickupHeader ph
										JOIN Program p
											ON p.ProgramNumber = ph.ProgramNumber,
										(SELECT ph.DonorNumber as 'DonorNumber', SUM(ph.TotalAmount) as 'SUMTotalAmount', ph.PickupType as 'PickupType'
										FROM PickupHeader ph
										WHERE (ph.PickupDate BETWEEN '".$startDate."' AND '".$endDate."')" . $initAgencyNo . $AgencyNo . $DonorNo . $PickupType ."
										GROUP BY ph.DonorNumber) as pt
									WHERE (ph.PickupDate BETWEEN '".$startDate."' AND '".$endDate."')" . $initAgencyNo . $AgencyNo . $DonorNo . $PickupType ."
									GROUP BY pt.DonorNumber");
						

		if ($chartResult) {
			$arrData = array(
							"chart" => array(
											"caption"=> "Total Pounds by Donor",
											"xaxisname"=> "Donor No.",
        									"yaxisname"=> "Pounds",
        									"showvalues"=> "1",
        									"placeValuesInside"=> "1",
        									"rotateValues"=> "1",
        									"valueFontColor"=> "#ffffff",
        									"baseFontColor"=> "#333333",
        									"baseFont"=> "Helvetica Neue,Arial",
        									"captionFontSize"=> "14",
        									"subcaptionFontSize"=> "14",
        									"subcaptionFontBold"=> "0",
        									"showborder"=> "0",
        									"paletteColors"=> "#538A21,#1B310A,#E5DBA9,#E0A367,#B6B114,#C52C1D",
        									"bgcolor"=> "#FFFFFF",
        									"showalternatehgridcolor"=> "0",
        									"showplotborder"=> "0",
        									"labeldisplay"=> "rotate",
											"slantlabels"=> "1",
        									"divlinecolor"=> "#CCCCCC",
        									"showcanvasborder"=> "0",
        									"linethickness"=> "3",
        									"plotfillalpha"=> "100",
									        "plotgradientcolor"=> "",
        									"numVisiblePlot"=> "12",
        									"divlineAlpha"=> "100",
        									"divlineColor"=> "#999999",
        									"divlineThickness"=> "1",
        									"divLineDashed"=> "1",
        									"divLineDashLen"=> "1",
        									"divLineGapLen"=> "1",
        									"scrollheight"=> "10",
        									"flatScrollBars"=> "1",
        									"scrollShowButtons"=> "0",
        									"scrollColor"=> "#cccccc",
											"showlegend"=> "1",
        									"showHoverEffect"=> "1"
											)
							);
			$arrData["data"] = array();
			
			while($cRow = mysql_fetch_array($chartResult)) {
				if($cRow['PickupType'] == 1){
					$link = "programreport.php?t=Cat&sd=" . $startDate . "&ed=" . $endDate . "&an=" . $cRow['ProgramNumber'] . "&dn=" . $cRow['DonorNumber'];
				}else{
					$link = "";
				}
				array_push($arrData["data"], array(
					"label" => $cRow['DonorNumber'],
					"value" => $cRow['SUMTotalAmount'],
					"link" => $link
					)
				);
			}
			
		$jsonEncodedData = json_encode($arrData);
		
		$columnChart = new FusionCharts("column2d", "TotalPoundsbyDonorChart" , 800, 300, "TotalPoundsbyDonor", "json", $jsonEncodedData);
		
		$columnChart->render();
		
		#echo("<tr><td>Hello" . $type . $startDate . $endDate . $AgencyNo . $DonorNo . "</td></tr>");
		echo("<tr><td><div id='TotalPoundsbyDonor' align='center'><!-- Fusion Charts will render here--></div></td></tr>");
		}
		/*--- END Total Pounds by Donor ---*/
		
}elseif ($type == "Cat"){		
		/*--- BEGIN Total Pounds by Category ---*/
		/* This query returns Total Pounds by Category based on GRID filters*/
		$chartResult = mysql_query("SELECT  bt.Bakery as 'BakeryTotal', 
										    dt.Dairy as 'DairyTotal',
											pt.Produce as 'ProduceTotal',
											mt.Meat as 'MeatTotal',
											mit.Misc as 'Misc Total',
											tt.Trash as 'TrashTotal',
											gt.Grocery as 'GroceryTotal'
									FROM 
										(SELECT SUM(Quantity) AS 'Bakery' FROM PickupDetail pd, PickupHeader ph WHERE ph.PickupId = pd.PickupId AND LineNumber = 1 AND (PickupDate BETWEEN '".$startDate."' AND '".$endDate."')" . $initAgencyNo . $AgencyNo . $DonorNo . $PickupType . ") as bt,
										(SELECT SUM(Quantity) AS 'Dairy' FROM PickupDetail pd, PickupHeader ph WHERE ph.PickupId = pd.PickupId AND LineNumber = 2 AND (PickupDate BETWEEN '".$startDate."' AND '".$endDate."')" . $initAgencyNo . $AgencyNo . $DonorNo . $PickupType . ") as dt,
										(SELECT SUM(Quantity) AS 'Produce' FROM PickupDetail pd, PickupHeader ph WHERE ph.PickupId = pd.PickupId AND LineNumber = 3 AND (PickupDate BETWEEN '".$startDate."' AND '".$endDate."')" . $initAgencyNo . $AgencyNo . $DonorNo . $PickupType . ") as pt,
										(SELECT SUM(Quantity) AS 'Meat' FROM PickupDetail pd, PickupHeader ph WHERE ph.PickupId = pd.PickupId AND LineNumber = 4 AND (PickupDate BETWEEN '".$startDate."' AND '".$endDate."')" . $initAgencyNo . $AgencyNo . $DonorNo . $PickupType . ") as mt,
										(SELECT SUM(Quantity) AS 'Misc' FROM PickupDetail pd, PickupHeader ph WHERE ph.PickupId = pd.PickupId AND LineNumber = 5 AND (PickupDate BETWEEN '".$startDate."' AND '".$endDate."')" . $initAgencyNo . $AgencyNo . $DonorNo . $PickupType . ") as mit,
										(SELECT SUM(Quantity) AS 'Trash' FROM PickupDetail pd, PickupHeader ph WHERE ph.PickupId = pd.PickupId AND LineNumber = 6 AND (PickupDate BETWEEN '".$startDate."' AND '".$endDate."')" . $initAgencyNo . $AgencyNo . $DonorNo . $PickupType . ") as tt,
										(SELECT SUM(Quantity) AS 'Grocery' FROM PickupDetail pd, PickupHeader ph WHERE ph.PickupId = pd.PickupId AND LineNumber = 7 AND (PickupDate BETWEEN '".$startDate."' AND '".$endDate."')" . $initAgencyNo . $AgencyNo . $DonorNo . $PickupType . ") as gt
										");
				

		if ($chartResult) {
			$arrData = array(
							"chart" => array(
											"caption"=> "Total Pounds by Category",
											"startingangle"=> "120",
											"showlabels"=> "1",
											"showvalues"=> "1",
											"showlegend"=> "1",
											"enablemultislicing"=> "1",
											"slicingdistance"=> "15",
											"showpercentvalues"=> "0",
											"showpercentintooltip"=> "1",
        									"paletteColors"=> "#1B310A,#E5DBA9,#538A21,#E0A367,#B6B114,#C52C1D",
											"showHoverEffect"=> "1",
											"bgAlpha"=> "0",
											"borderAlpha"=> "0"
        									)
							);
			$arrData["data"] = array();
			
			while($cRow = mysql_fetch_array($chartResult)) {
				array_push($arrData["data"], array(
					"label" => 'Bakery Total',
					"value" => $cRow['BakeryTotal']
					)
				);
				array_push($arrData["data"], array(
					"label" => 'Dairy Total',
					"value" => $cRow['DairyTotal']
					)
				);
				array_push($arrData["data"], array(
					"label" => 'Produce Total',
					"value" => $cRow['ProduceTotal']
					)
				);
				array_push($arrData["data"], array(
					"label" => 'Meat Total',
					"value" => $cRow['MeatTotal']
					)
				);
				array_push($arrData["data"], array(
					"label" => 'Grocery Total',
					"value" => $cRow['GroceryTotal']
					)
				);
				array_push($arrData["data"], array(
					"label" => 'Misc Total',
					"value" => $cRow['MiscTotal']
					)
				);
				array_push($arrData["data"], array(
					"label" => 'Trash Total',
					"value" => $cRow['TrashTotal']
					)
				);
				
			}
			
		$jsonEncodedData = json_encode($arrData);
		
		$columnChart = new FusionCharts("pie2d", "TotalPoundsbyCategoryChart" , 800, 400, "TotalPoundsbyCategory", "json", $jsonEncodedData);
		
		$columnChart->render();
		
		#echo("<tr><td>Hello" . $type . $startDate . $endDate . $AgencyNo . $DonorNo . "</td></tr>");
		echo("<tr><td><div id='TotalPoundsbyCategory' align='center'><!-- Fusion Charts will render here--></div></td></tr>");
		}
		/*--- END Total Pounds by Category ---*/

}else{		
		/*--- BEGIN Total Pounds by Program ---*/
		/* This query returns Total Pounds by Donor based on GRID filters*/
		$chartResult = mysql_query("SELECT ph.PickupId, p.ProgramNumber, p.ProgramName, pt.SUMTotalAmount, pt.ProgramNumber
										FROM PickupHeader ph
										JOIN Program p
											ON p.ProgramNumber = ph.ProgramNumber,
										(SELECT ph.ProgramNumber as 'ProgramNumber', SUM(ph.TotalAmount) as 'SUMTotalAmount'
										FROM PickupHeader ph
										WHERE (ph.PickupDate BETWEEN '".$startDate."' AND '".$endDate."')" . $initAgencyNo . $AgencyNo . $DonorNo . $PickupType ."
										GROUP BY ph.ProgramNumber) as pt
									WHERE (ph.PickupDate BETWEEN '".$startDate."' AND '".$endDate."')" . $initAgencyNo . $AgencyNo . $DonorNo . $PickupType ."
									GROUP BY pt.ProgramNumber");
						

		if ($chartResult) {
			$arrData = array(
							"chart" => array(
											"caption"=> "Total Pounds by Agency",
        									"xaxisname"=> "Agency No.",
        									"yaxisname"=> "Pounds",
        									"showvalues"=> "1",
        									"placeValuesInside"=> "1",
        									"rotateValues"=> "1",
        									"valueFontColor"=> "#ffffff",
        									"baseFontColor"=> "#333333",
        									"baseFont"=> "Helvetica Neue,Arial",
        									"captionFontSize"=> "14",
        									"subcaptionFontSize"=> "14",
        									"subcaptionFontBold"=> "0",
        									"showborder"=> "0",
        									"paletteColors"=> "#538A21,#1B310A,#E5DBA9,#E0A367,#B6B114,#C52C1D",
        									"bgcolor"=> "#FFFFFF",
        									"showalternatehgridcolor"=> "0",
        									"showplotborder"=> "0",
        									"labeldisplay"=> "rotate",
											"slantlabels"=> "1",
        									"divlinecolor"=> "#CCCCCC",
        									"showcanvasborder"=> "0",
        									"linethickness"=> "3",
        									"plotfillalpha"=> "100",
									        "plotgradientcolor"=> "",
        									"numVisiblePlot"=> "12",
        									"divlineAlpha"=> "100",
        									"divlineColor"=> "#999999",
        									"divlineThickness"=> "1",
        									"divLineDashed"=> "1",
        									"divLineDashLen"=> "1",
        									"divLineGapLen"=> "1",
        									"scrollheight"=> "10",
        									"flatScrollBars"=> "1",
        									"scrollShowButtons"=> "0",
        									"scrollColor"=> "#cccccc",
        									"showHoverEffect"=> "1"
											)
							);
			$arrData["data"] = array();
			
			while($cRow = mysql_fetch_array($chartResult)) {
				array_push($arrData["data"], array(
					"label" => $cRow['ProgramNumber'],
					"value" => $cRow['SUMTotalAmount'],
					"link" => "programreport.php?t=Donor&sd=" . $startDate . "&ed=" . $endDate . "&an=" . $cRow['ProgramNumber']
					)
				);
			}
			
		$jsonEncodedData = json_encode($arrData);
		
		$columnChart = new FusionCharts("column2d", "TotalPoundsbyProgramChart" , 800, 300, "TotalPoundsbyProgram", "json", $jsonEncodedData);
		
		$columnChart->render();
		
		#echo("<tr><td>Hello" . $type . $startDate . $endDate . $AgencyNo . $DonorNo . "</td></tr>");
		echo("<tr><td><div id='TotalPoundsbyProgram' align='center'><!-- Fusion Charts will render here--></div></td></tr>");		
		}
		/*--- END Total Pounds by Program ---*/
	}
}

?>