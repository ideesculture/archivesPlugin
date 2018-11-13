<?php
	$id = $this->getVar("object_id");
	if($id) {
		$item = $this->getVar("item");
	}
	$template = $this->getVar("template");
	error_reporting(E_ERROR);
	?>
<h1>Archives</h1>
<div id="hierarchy<?php print ($id ? "For".$id : "Root"); ?>">

</div>

<style>
	.archiveHierarchyOffset0 {width:75%;padding-left:0;}
	.archiveHierarchyOffset1 {width:72%;padding-left:3%;}
	.archiveHierarchyOffset2 {width:69%;padding-left:6%;}
	.archiveHierarchyOffset3 {width:66%;padding-left:9%;}
	.archiveHierarchyOffset4 {width:63%;padding-left:12%;}
	.hierarchyFor {
		margin-bottom:5px;
	}
	div[class^=archiveHierarchyOffset] {
		margin-bottom:5px;
		display:inline-block;
	}
	a[id^="expandButton"], a[id^="shrinkButton"], a[id^="exportButton"], a[id^="printButton"] {
		color:black;
		cursor:pointer;
		text-decoration: none;
	}
</style>
<script>
	jQuery("document").ready(function() {
		var url = "<?php print __CA_URL_ROOT__; ?>/index.php/archives/archives/Fetch";
		console.log(url);
		jQuery.get(url, function(data){
			jQuery('#hierarchyRoot').html(data);
		}, "html");

	});
	var expandHierarchy = function(object_id,level) {
		jQuery("#expandButton"+object_id).parent().prepend("<a onClick='shrinkHierarchy("+object_id+","+level+");' id='shrinkButton"+object_id+"'><i class='fa fa-minus-square-o'></i></a>");
		jQuery("#expandButton"+object_id).remove();
		//jQuery("#expandButton"+object_id+" i.fa").addClass("fa-square-o");
		//jQuery("#expandButton"+object_id+" i.fa").removeClass("fa-plus-square-o");
		var url = "<?php print __CA_URL_ROOT__; ?>/index.php/archives/archives/Fetch/id/"+object_id+"/level/"+level;
		console.log(url);
		jQuery.get(url, function(data){
			jQuery("#hierarchyFor"+object_id).html(data);
		}, "html");

	}
	var shrinkHierarchy = function(object_id,level) {
		jQuery("#shrinkButton"+object_id).parent().prepend("<a onClick='expandHierarchy("+object_id+","+level+");' id='expandButton"+object_id+"'><i class='fa fa-plus-square-o'></i></a>");
		jQuery("#shrinkButton"+object_id).remove();
		jQuery("#hierarchyFor"+object_id).html("");
	}
	
	var generateArchivesPdf = function(object_id) {
		jQuery("#pdfButton"+object_id+" i").removeClass("fa-file-pdf-o").addClass("fa-spinner fa-pulse fa-fw");
		url='/gestion/index.php/archives/archives/Pdf/id/'+object_id;
		var that=object_id;
		jQuery.getJSON(url, function(data) {
			console.log(data);
			jQuery("#pdfButton"+that).prop('onclick',null).off('click');
			jQuery("#pdfButton"+that+" i").removeClass("fa-spinner fa-pulse fa-fw").addClass("fa-file-pdf-o");
			jQuery("#pdfButton"+that+" a").attr("href", data.URL);
			jQuery("#pdfButton"+that+" a").css("color", "#1ab3c8a");
			
		})
	}
</script>
