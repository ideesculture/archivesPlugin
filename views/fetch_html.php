<?php
	$template = $this->getVar("template");
	$result_rows = $this->getVar("result_rows");
	$level = intval($this->getVar("level"));
    $printLevel = $this->getVar("printLevel");
    if ($printLevel == "all") $printLevel=$level;

	$i=0;
	foreach($result_rows as $row) {
		$object_id = $row['object_id'];
		$t_item2 = new ca_objects($object_id);

		$row_template = $template;
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
            $printButton = "<a href='/gestion/index.php/archives/archives/Export/id/".$object_id."'><i class=\"fa fa-print\" aria-hidden=\"true\"></i></a>";
        } else {
            $printButton = "";
        }

        $row_template = str_replace("^printButton",$printButton,$row_template);

		$result = $t_item2->getWithTemplate($row_template);
		print $result."\n";
		print "<div id='hierarchyFor".$object_id."' class='hierarchyFor'></div>";
		$i++;
	}
