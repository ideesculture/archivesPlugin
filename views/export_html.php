<header>
	<meta charset="UTF-8">
</header>
<body onload='window.print()'>
<?php
error_reporting(E_ERROR);
	$id = $this->getVar("object_id");
	$item = $this->getVar("item");
	$template = $this->getVar("template");
	$inner_template = $this->getVar("inner_template");
	$templateH1 = $this->getVar("templateH1");
	$db = new Db();
	error_reporting(E_ERROR);

	function printItemWithTemplate ($item, $template) {
		print $item->getWithTemplate($template);
	}


	function printHierarchyFromItemWithTemplate($db, $id, $template, $inner_template, $level) {
		$item = new ca_objects($id);
		print "<blockquote>";
		if($level==1) {
			printItemWithTemplate($item, "<h1>".$template."</h1>");
		} elseif($level<5) {
			//printItemWithTemplate($item, "<h$level>".$template."</h$level>");
			printItemWithTemplate($item, "<h3>".$template."</h3>");
		} else {
			printItemWithTemplate($item, "<p>".$template."</p>");
		}
		printItemWithTemplate($item, $inner_template);
		$num_children = reset($item->getHierarchyChildCountsForIDs([$id]));
		if($num_children) {
			$children = $item->getHierarchyChildren($id);
			foreach($children as $child) {
				printHierarchyFromItemWithTemplate($db, $child["object_id"], $template, $inner_template, $level+1);
			}
		}
		print "</blockquote>";
	}

	//printItemWithTemplate($item, $templateH1);
	printHierarchyFromItemWithTemplate($db, $id, $template, $inner_template, 1);
?>
<style>
body {
	font-size:10px;
	font-family: "DIN-Regular", Arial, Helvetica, sans-serif;
}
h1, h2, h3, h4, h5, h6 {
	font-weight: bold;
	font-family: "DIN-Regular", Arial, Helvetica, sans-serif;

}
	blockquote blockquote {
		border-left:1px solid lightgray;
		padding-left:10px;
		border-top:1px solid lightgray;
	}
</style>
</body>
