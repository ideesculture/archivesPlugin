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
		jQuery("#expandButton"+object_id).parent().prepend("<a onClick='shrinkHierarchy("+object_id+","+level+");' id='shrinkButton"+object_id+"'><i class='far fa-minus-square'></i></a>");
		jQuery("#expandButton"+object_id).remove();
		//jQuery("#expandButton"+object_id+" i.fa").addClass("fa-square");
		//jQuery("#expandButton"+object_id+" i.fa").removeClass("fa-plus-square");
		var url = "<?php print __CA_URL_ROOT__; ?>/index.php/archives/archives/Fetch/id/"+object_id+"/level/"+level;
		console.log(url);
		jQuery.get(url, function(data){
			jQuery("#hierarchyFor"+object_id).html(data);
		}, "html");

	}
	var shrinkHierarchy = function(object_id,level) {
		jQuery("#shrinkButton"+object_id).parent().prepend("<a onClick='expandHierarchy("+object_id+","+level+");' id='expandButton"+object_id+"'><i class='far fa-plus-square'></i></a>");
		jQuery("#shrinkButton"+object_id).remove();
		jQuery("#hierarchyFor"+object_id).html("");
	}
	
	var generateArchivesPdf = function(object_id) {
		jQuery("#pdfButton"+object_id+" i").removeClass("far fa-file-pdf").addClass("fas fa-spinner fa-pulse fa-fw");
		url='/gestion/index.php/archives/archives/Pdf/id/'+object_id;
		var that=object_id;
		jQuery.getJSON(url, function(data) {
			jQuery("#pdfButton"+that+" i").removeClass("fas fa-spinner fa-pulse fa-fw").addClass("far fa-file-pdf");
			if (!data || !data.URL) {
				// echec de generation : on garde le bouton actif et on affiche la raison
				jQuery("#pdfButton"+that+" a").attr("title", (data && data.message) ? data.message : "Erreur de génération du PDF").css("color", "#c0392b");
				return;
			}
			jQuery("#pdfButton"+that).prop('onclick',null).off('click');
			jQuery("#pdfButton"+that+" a").attr("href", data.URL).attr("target", "_blank").attr("title", data.message);
			jQuery("#pdfButton"+that+" a").css("color", "#1ab3c8");
		}).fail(function() {
			jQuery("#pdfButton"+that+" i").removeClass("fas fa-spinner fa-pulse fa-fw").addClass("far fa-file-pdf");
			jQuery("#pdfButton"+that+" a").attr("title", "Erreur de génération du PDF").css("color", "#c0392b");
		})
	}
</script>
