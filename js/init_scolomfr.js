var ned = {
	key : "ned",
	title : "Niveau éducatif détaillé",
	standard : "SCOLOMFRv2.0",
	purpose : "http://data.education.fr/voc/scolomfr/concept/educational_level",
	source : "scolomfr-voc-022",
	help : "Précisez à quel(s) niveau(x) de classes la séquence se destine.",
	mandatory : true
};
var ens = {
	key : "ens",
	title : "Domaines d'enseignement",
	standard : "SCOLOMFRv2.0",
	purpose : "http://data.education.fr/voc/scolomfr/concept/scolomfr-voc-028-num-003",
	source : "scolomfr-voc-015",
	help : "Précisez la discipline pour laquelle la séquence est adaptée.",
	mandatory : true
};
var scc = {
	key : "scc",
	title : "Compétences du socle commun",
	standard : "SCOLOMFRv2.0",
	purpose : "http://data.education.fr/voc/scolomfr/concept/competency",
	source : "scolomfr-voc-016",
	help : "Précisez les compétences du socle commun." ,
	mandatory : true
};
var dip = {
	key : "dip",
	title : "Diplomes",
	standard : "SCOLOMFRv2.0",
	purpose : "http://data.education.fr/voc/scolomfr/concept/scolomfr-voc-028-num-016",
	source : "scolomfr-voc-029",
	help : "Précisez le diplôme.",
	mandatory : true
};
var pcd = {
	key : "pcd",
	title : "Public cible détaillé",
	standard : "SCOLOMFRv2.0",
	purpose : "http://data.education.fr/voc/scolomfr/concept/scolomfr-voc-028-num-011",
	source : "scolomfr-voc-021",
	help : "Précisez le public cible détaillé.",
	mandatory : true
};
var cap = {
	key : "cap",
	title : "Cadre pédagogique.",
	standard : "SCOLOMFRv2.0",
	purpose : "http://data.education.fr/voc/scolomfr/concept/competency",
	source : "scolomfr-voc-040",
	help : "Précisez le cadre pédagogique.",
	mandatory : true
};
var cecrl = {
	key : "cecrl",
	title : "Cadre européen commun de référence pour les langues",
	standard : "SCOLOMFRv2.0",
	purpose : "http://data.education.fr/voc/scolomfr/concept/scolomfr-voc-028-num-019",
	source : "scolomfr-voc-042",
	help : "Précisez le cadre européen commun de référence pour les langues.",
	mandatory : true
};
var sup = {
	key : "sup",
	title : "Support",
	standard : "SCOLOMFRv2.0",
	purpose : "http://data.education.fr/voc/scolomfr/concept/scolomfr-voc-028-num-023",
	source : "scolomfr-voc-043",
	help : "Précisez le Support.",
	mandatory : true
};
var def = {
	key : "def",
	title : "Support",
	standard : "SCOLOMFRv2.0",
	purpose : "http://data.education.fr/voc/scolomfr/concept/scolomfr-voc-028-num-017",
	source : "scolomfr-voc-041",
	help : "Précisez le type de déficience.",
	mandatory : true
};
var classifications;
var dataSource = $("input#prefix").val() + "/data.php";
var trees = new Object();
var disableDynatreeOnSelect;
var submitButton;
var initialization = true;

function initScolomFr() {

	classifications = new Object();
	$("#ned_tree").append(ajouterChampProgramme(ned));
	$("#ens_tree").append(ajouterChampProgramme(ens));
	$("#scc_tree").append(ajouterChampProgramme(scc));
	$("#dip_tree").append(ajouterChampProgramme(dip));
	$("#pcd_tree").append(ajouterChampProgramme(pcd));
	$("#cap_tree").append(ajouterChampProgramme(cap));
	$("#cecrl_tree").append(ajouterChampProgramme(cecrl));
	$("#sup_tree").append(ajouterChampProgramme(sup));
	$("#def_tree").append(ajouterChampProgramme(def));
	addSubmitButton();

	$("form#formulaire_scolomfr").attr("action",
			$("form#formulaire_scolomfr").attr("action") + "/async").ajaxForm({
		dataType : 'xml',
		cache : true,
		beforeSend : function() {
			activateSubmitButton(false);
			putWaiterOnSubmitButton(true);
		},
		complete : function(xhr) {
			activateSubmitButton(false);
			putWaiterOnSubmitButton(false);
			console.log(xhr.responseXML);
		}
	});
	$("textarea#general-description").add($("input#general-title")).add(
			$("textarea#educational-description")).add(
			$("textarea#general-coverage")).bind("keyup change", function() {
		activateSubmitButton(true);
	});
	$("ul", "#keyword-container").tagit({
		itemName : 'general-keywords',
		fieldName : 'general-keyword[]',
		placeholderText : 'Saisissez puis "entrée"',
		allowSpaces : true,
		afterTagAdded : function() {
			if (!initialization)
				activateSubmitButton(true);
		},
		afterTagRemoved : function() {
			activateSubmitButton(true);
		}
	});

	initializeSelect("generalResourceType", "general-generalResourceType");
	initializeSelect("learningResourceType", "educational-learningResourceType");
	initializeSelect("place", "educational-place");
	initializeSelect("educationalMethod", "educational-educationalMethod");
	initializeSelect("activity", "educational-activity");
	initializeSelect("intendedEndUserRole", "educational-intendedEndUserRole");
	$("div#difficulty-container.element div select").change(function() {
		activateSubmitButton(true);
	});
	$("div#aggregationLevel-container.element div select").change(function() {
		activateSubmitButton(true);
	});
	handleContributors();
	initialization = false;

}

function initializeSelect(key, name) {
	var tagitContainer = $("div#" + key
			+ "-container.element div.entries-container div.elt_champ_form ul",
			"form#formulaire_scolomfr");
	$("div#" + key + "-container.element span.register-entry",
			"form#formulaire_scolomfr")
			.button({
				icons : {
					primary : "ui-icon-add"
				},
				text : true
			})
			.click(
					function() {
						var value = $(
								"div#" + key + "-container.element div select")
								.val();
						var label = $(
								"div#"
										+ key
										+ "-container.element div select option[value='"
										+ value + "']").text()
						addEntryListInput(tagitContainer, value, label, key,
								name);
					});
	tagitContainer.tagit({
		itemName : name,
		fieldName : name + '[]',
		allowSpaces : true,
		afterTagRemoved : function() {
			activateSubmitButton(true);
		}

	});
	tagitContainer.find("input.ui-widget-content").attr("disabled", "disabled");
}

function addEntryListInput(tagitContainer, value, label, key, name) {
	var yetPresent = false;
	$("div#" + key + "-container.element span.tagit-label").each(
			function(i, e) {
				if ($(e).closest("li").hasClass(value))
					yetPresent = true;
			});
	$("span.ui-state-error.duplicate-alert", "div#" + key + "-container")
			.toggle(yetPresent).effect(yetPresent ? "pulsate" : "");
	if (yetPresent) {
		return;
	}
	tagitContainer.tagit("createTag", label + " (" + value + ") ", value);
	activateSubmitButton(true);
}

function addSubmitButton() {
	submitButton = $(document.createElement("span")).addClass("submit-button")
			.attr("title", "enregistrer");
	$("h2.submit-button-container", "div#edit.detail-pane")
			.append(submitButton);
	$("div.cadre summary", "div#visualisation").append(submitButton.clone());
	$("span.submit-button").button({
		icons : {
			primary : "ui-icon-save"
		},
		text : false
	}).click(function() {
		$("form#formulaire_scolomfr").submit();
		return false;
	});
	activateSubmitButton(false);
	putWaiterOnSubmitButton(false);
}

function ajouterChampProgramme(program) {
	var container = $(document.createElement("div")).addClass(
			"ui-widget-content container-editeur");
	trees[program.key] = $(document.createElement("div")).appendTo(container)
			.dynatree(
					{
						onSelect : function(select, node) {
							if (disableDynatreeOnSelect)
								return;
							var selectedNodes = node.tree.getSelectedNodes();
							updateProgramClassificationInput(selectedNodes,
									program.source, program.purpose,
									program.standard);
							activateSubmitButton(true);

						},
						checkbox : true,

						selectMode : 3,
						initAjax : {

							url : dataSource,
							ajaxDefaults : {
								cache : true,
							},
							data : {
								data : program.key
							}
						},
						onPostInit : function() {
							mettreAJourChampProgramme(program);
							var selectedNodes = trees[program.key]
									.dynatree("getSelectedNodes");
							updateProgramClassificationInput(selectedNodes,
									program.source, program.purpose,
									program.standard);
						}

					});

	return container;
}
function updateProgramClassificationInput(selectedNodes, sourceIdentifier,
		purpose, standard) {
	var classification = getOrCreateClassification(purpose, standard);
	clearTaxonPaths(classification, sourceIdentifier);
	var taxonPath;
	var index = 0;
	var node;
	while (selectedNode = selectedNodes[index]) {
		taxonPath = createTaxonPath(classification, sourceIdentifier);
		node = selectedNodes[index];
		addEntriesToTaxonPath(taxonPath, node);
		index++;
	}
	$("input#classifications").val(JSON.stringify(classifications));
}
function getOrCreateClassification(purpose, standard) {
	var classification = getClassification(purpose);
	if (!classification)
		classification = createClassification(purpose, standard);
	return classification;

}
function getClassification(purpose) {
	var classification = classifications[purpose];
	return classification;
}

function getTaxonPaths(classification, sourceIdentifier) {
	var taxonPaths = new Array();
	classification.children("taxonPath").each(function(index, elem) {
		var sourceInTaxonPath = $(elem).find("source").find("string").text();
		if (sourceIdentifier == sourceInTaxonPath)
			taxonPaths.push($(elem));
	});
	return taxonPaths;
}
function clearTaxonPaths(classification, sourceIdentifier) {
	if (!classification["taxonPaths"])
		classification["taxonPaths"] = new Array();
	classification["taxonPaths"] = jQuery.grep(classification["taxonPaths"],
			function(elem, index) {
				return (elem != "null" && elem["source"] != sourceIdentifier);
			});
}
function createTaxonPath(classification, sourceIdentifier) {
	var taxonPath = new Object();
	classification["taxonPaths"].push(taxonPath);
	taxonPath["source"] = sourceIdentifier;
	taxonPath["taxons"] = new Array();
	return taxonPath;
}
function clearTaxons(taxonPath) {
	taxonPath.children("taxon").each(function(index, elem) {
		elem.parentNode.removeChild(elem);
	});
}
function createClassification(purpose, standard) {
	classifications[purpose] = new Object();
	classifications[purpose]['standard'] = standard;
	classifications[purpose]['taxonPaths'] = new Array();
	return classifications[purpose];
}
function addEntriesToTaxonPath(taxonPath, selectedNode) {
	addTaxon(taxonPath, selectedNode.data.key, selectedNode.data.title);
	while (typeof selectedNode.getParent == 'function'
			&& selectedNode.getParent()) {
		selectedNode = selectedNode.getParent();
		if (!selectedNode.data.key)
			continue;
		addTaxon(taxonPath, selectedNode.data.key, selectedNode.data.title);
	}
}
function addTaxon(taxonPath, id, title) {
	if (!id || typeof id === undefined || id.match(/^_\d+$/))
		return;
	var taxon = {
		"id" : id,
		"entry" : title
	};
	taxonPath["taxons"].unshift(taxon);
}
function mettreAJourChampProgramme(program) {
	disableDynatreeOnSelect = true;
	trees[program.key].dynatree("getRoot").visit(
			function(node) {
				if (!node.isStatusNode() && !node.hasChildren()) {
					if (hasProgramEntry(node.data.key, program.purpose,
							program.standard, program.source))
						node.select();
				}
			});
	disableDynatreeOnSelect = false;
}
function hasProgramEntry(id, purpose, standard, source) {
	var found = false;
	$("div.purpose").each(
			function(index, elem) {
				if (source.indexOf($(elem).find(".source-data").attr(
						"data-source")) >= 0) {
					$(elem).find(".source-data").find(".entry-data").each(
							function(index, elem) {
								if ($(elem).attr("data-id") == id)
									found = true;
							});
				}
				;
			});
	return found;
}
function displayVcards() {
	$("tr.role td.vcard-string", "div#contributors-container")
			.each(
					function(index, elem) {
						var vcardString = $(elem).text();
						var roleLabel = $(elem).prev("th.role-label").text();
						var date = $(elem).next("td.date-label").text();
						var tr = $(elem).closest("tr");
						var roleId = tr.attr("id");
						tr
								.append(
										$('<input type="hidden" name="lifeCycle-contributor-vcard[]" value="'
												+ vcardString.replace(/§/g, "\n") + '"/>'))
								.append(
										$('<input type="hidden" name="lifeCycle-contributor-role-label[]" value="'
												+ roleLabel + '"/>'))
								.append(
										$('<input type="hidden" name="lifeCycle-contributor-role-id[]" value="'
												+ roleId + '"/>'))
								.append(
										$('<input type="hidden" name="lifeCycle-contributor-date[]" value="'
												+ date + '"/>'));
						var vcardParsed=vCard.initialize(vcardString);
						$(elem).replaceWith(vcardParsed.to_html());
						tr.find("span.delete-button").button({
							icons : {
								primary : "ui-icon-close"
							},
							text : false
						}).click(function() {
							tr.remove();
							activateSubmitButton(true);
						});
					});
}
function handleContributors() {
	displayVcards();
	$("div#contributors-container.element input.date-input",
			"form#formulaire_scolomfr").datepicker();
	var addButton = $("div#contributors-container.element span.register-entry",
			"form#formulaire_scolomfr")
			.button({
				icons : {
					primary : "ui-icon-add"
				},
				text : false
			})
			.click(
					function() {
						var vcard = "BEGIN:VCARD§VERSION:3.0[FN][N][ORG]§END:VCARD";
						var tr = $(this).closest("tr");
						var org = tr.find("input.org-input").val();
						tr.find("input.org-input").val("");
						var date = tr.find("input.date-input").val();
						tr.find("input.date-input").val("");
						var fn = tr.find("input.name-input").val();
						tr.find("input.name-input").val("");
						if (!org.match(/^\s*$/))
							vcard = vcard.replace("[ORG]", "§ORG:" + org);
						else
							vcard = vcard.replace("[ORG]", "");
						if (!fn.match(/^\s*$/)) {
							vcard = vcard.replace("[FN]", "§FN:" + fn);
							vcard = vcard.replace("[N]", "§N:" + fn);
						} else {
							vcard = vcard.replace("[FN]", "");
							vcard = vcard.replace("[N]", "");
						}
						var roleId = $(this).closest("tr").find("select").val();
						var roleLabel = $(this).closest("tr").find(
								"select option[value='" + roleId + "']").text()
						var line = '<tr class="role" id="[ROLE-ID]"><th class="role-label">[ROLE-LABEL]</th><td class="vcard-string">[VCARD]</td><td class="date-label">[DATE]</td><td><span class="delete-button"></span></td></tr>';
						line = line.replace("[ROLE-LABEL]", roleLabel).replace(
								"[ROLE-ID]", roleId).replace("[VCARD]", vcard)
								.replace("[DATE]", inverseDate(date));
						$(line).insertBefore($(this).closest("tr"));
						displayVcards();
						addButton.button("disable");
						activateSubmitButton(true);
					});
	addButton.closest("tr").bind(
			"click change keyup",
			function() {
				var org = $(this).find("input.org-input").val();
				var date = $(this).find("input.date-input").val();
				var fn = $(this).find("input.name-input").val();

				var active = true;
				active = active && (!org.match(/^\s*$/) || !fn.match(/^\s*$/))
						&& !date.match(/^\s*$/);
				addButton.button(active ? "enable" : "disable");
			});
	addButton.button("disable");
}
function inverseDate(dateStr) {
	var tab = dateStr.split("/");
	if (tab.length < 3)
		return dateStr;
	return tab[2] + "-" + tab[0] + "-" + tab[1];
}
