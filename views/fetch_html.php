<?php
	$template = $this->getVar("template");
	//var_dump($template);die();
	$result_rows = $this->getVar("result_rows");
	$level = intval($this->getVar("level"));
    $printLevel = $this->getVar("printLevel");
    if ($printLevel == "all") $printLevel=$level;
    $pdfLevel = $this->getVar("pdfLevel");
    if ($pdfLevel == "all") $pdfLevel=$level;
    $exportLevel = $this->getVar("exportLevel");
    if ($exportLevel == "all") $exportLevel=$level;

	if(!function_exists(getTemplateForItem)) {
	    function getTemplateForItem($item, $template) {
	        $type = $item->get("ca_objects.type_id");
	        if(isset($template[$type]) && trim($template[$type])) {
	            $return = $template[$type];
	        } else {
	            $return = $template["_default"];
	        }
	        return $return;
	    }
	}
	
	$i=0;
	foreach($result_rows as $row) {
		$object_id = $row['object_id'];
		$t_item2 = new ca_objects($object_id);

		$row_template = getTemplateForItem($t_item2, $template);
		if($row["numchildren"]>0) {
			$row_template = str_ireplace(
				"^expandButton",
				"<a onclick='expandHierarchy(".$object_id.",".($level+1).");' id='expandButton".$object_id."'><i class=\"fa fa-plus-square-o\" aria-hidden=\"true\"></i>
</a>",
				$row_template
			);
		} else {
			$row_template = str_replace("^expandButton","<i class='fa fa-square-o'></i>",$row_template);
		}

        $row_template = str_replace("^level",$level,$row_template);

        if($printLevel >= $level) {
            $printButton = "<a href='/gestion/index.php/archives/archives/Export/id/".$object_id."' id='printButton".$object_id."'><i class=\"fa fa-print\" aria-hidden=\"true\"></i></a>";
        } else {
            $printButton = "";
        }
        $row_template = str_replace("^printButton",$printButton,$row_template);

        if($pdfLevel >= $level) {
            $pdfButton = "<span onClick=\"generateArchivesPdf('".$object_id."');\" id='pdfButton".$object_id."'><a style='color:black;'><i class=\"fa fa-file-pdf-o\" aria-hidden=\"true\"></i></a></span>";
        } else {
            $printButton = "";
        }
        $row_template = str_replace("^pdfButton",$pdfButton,$row_template);

        if($exportLevel >= $level) {
            $exportButton = "<a href='/gestion/index.php/archives/archives/Export/id/".$object_id."' id='exportButton".$object_id."'><i class=\"fa fa-print\" aria-hidden=\"true\"></i></a>";
        } else {
            $exportButton = "";
        }
        $row_template = str_replace("^exportButton",$exportButton,$row_template);

        $result = $t_item2->getWithTemplate($row_template);
		print $result."\n";
		print "<div id='hierarchyFor".$object_id."' class='hierarchyFor'></div>";
		$i++;
	}
