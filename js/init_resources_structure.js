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
				startCollapsed : false

			}).disableSelection();
	$("ol#selected-resources-for-structure").nestedSortable("option",
			"connectWith", "ol#resource-hierarchy");
	$('ol.sortable .deleteMenu').click(function() {
		var id = $(this).attr('data-id');
		$('#' + id).remove();
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
}
function getDisplayParameters() {
	return "";
}