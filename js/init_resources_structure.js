var activeLabel;
var putBlocked;
var buttonOriginalText;
var saveApiscolLinkHtml;
var submitButton;
var displayConsole;
var scanIsRunning = false;
var awaitedRefreshProcessReports = new Array();
function secundaryInit() {
	displayConsole = $("div.pane div.console-area", "div#refresh.detail-pane");
	var headerHeight = $("html>body>header.ui-helper-clearfix").height();
	var navHeight = $("div#tabs").height();
	var availableHeight = $(window).height() - headerHeight - navHeight - 20;

	$("#structure").height(availableHeight).layout({
		defaults : {
			applyDemoStyles : true
		},
		west : {
			resizable : true,
			size : "20%"
		}
	});
	$("div.pane ul li.resource-selected a.resource-detail-link",
			"div#structure").button({
		icons : {
			primary : "ui-icon-extlink"
		},
		text : false
	});

	$("ol#resource-hierarchy,ol#selected-resources-for-structure")
			.nestedSortable({
				// protectRoot : true,
				forcePlaceholderSize : true,
				handle : 'div',
				helper : 'clone',
				items : 'li',
				opacity : .6,
				placeholder : 'placeholder',
				revert : 250,
				tabSize : 25,
				tolerance : 'pointer',
				toleranceElement : '> div',
				maxLevels : 4,
				isTree : true,
				expandOnHover : 700,
				startCollapsed : false,
				relocate : function(event, ui) {
					var $container = ui.item.parent().closest("li");
					if ($container.length == 0) {
						return false;
					}
					updateDiscloseIcon();
				}

			}).disableSelection();
	$("ol#selected-resources-for-structure").nestedSortable("option",
			"connectWith", "ol#resource-hierarchy");
	$("ol#selected-resources-for-structure").nestedSortable("option",
			"maxLevels", 1);
	$("ol#resource-hierarchy").nestedSortable("option", "maxLevels", 0);
	$('ol.sortable .deleteMenu').click(
			function() {
				var id = $(this).attr('data-id');

				var $element = $('#' + id);
				if ($element.closest("ol#resource-hierarchy").length > 0) {
					recursivelyReturnToSelectedResourcesRepository($element);
				} else {
					var selectedMetadata = [ false ];
					var selectedMetadataId = [ id ];
					sendSelectedResourcesList(selectedMetadataId,
							selectedMetadata, function() {
								$element.remove();
							});
				}
				updateDiscloseIcon();
			});

	$('ol.sortable .disclose').on(
			'click',
			function() {
				$(this).closest('li').toggleClass(
						'mjs-nestedSortable-collapsed').toggleClass(
						'mjs-nestedSortable-expanded');
				$(this).toggleClass('ui-icon-plusthick').toggleClass(
						'ui-icon-minusthick');
			});

	$('ol.sortable .expandEditor, ol.sortable .itemTitle').click(
			function() {
				var id = $(this).attr('data-id');
				$('#menuEdit' + id).toggle();
				$(this).toggleClass('ui-icon-triangle-1-n').toggleClass(
						'ui-icon-triangle-1-s');
			});
	var $selectedResourcesContainer = $("div.selected-resources-container",
			"div#structure");
	var $title = $selectedResourcesContainer.prev("h2");
	var selectedResourcesContainerHeight = $selectedResourcesContainer.parent()
			.height()
			- $title.height();
	$selectedResourcesContainer.height(selectedResourcesContainerHeight)
			.perfectScrollbar();
	var $editedResourcesContainer = $("div.edited-resources-container",
			"div#structure");
	var $title = $editedResourcesContainer.prev("h2");
	var editedResourcesContainerHeight = $editedResourcesContainer.parent()
			.height()
			- $title.height();
	$editedResourcesContainer.height(editedResourcesContainerHeight)
			.perfectScrollbar();
	updateDiscloseIcon();
}
function recursivelyReturnToSelectedResourcesRepository($element) {
	$element.find("li").each(function(index, elem) {
		recursivelyReturnToSelectedResourcesRepository($(elem));
	})
	$element.removeClass("mjs-nestedSortable-branch").addClass(
			"mjs-nestedSortable-leaf").prependTo(
			"ol#selected-resources-for-structure");
	$element.find(">.menuDiv>span.disclose").css("visibility", "hidden");
}
function updateDiscloseIcon() {
	var $disclose, $elem;
	$(".mjs-nestedSortable-branch").each(
			function(index, elem) {
				$elem = $(elem);
				$disclose = $elem.find(">.menuDiv>span.disclose");
				if ($elem.find("ol").length == 0
						|| $elem.find("ol").find("li").length == 0)
					$disclose.css("visibility", "hidden");
				else
					$disclose.css("visibility", "visible");
			});
}
function getDisplayParameters() {
	return "";
}