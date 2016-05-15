#!/bin/bash
echo "Vocabulaire Enseignement"
xsltproc --timing --param vocabnumber "'015'" --param vocabtitle "'Enseignement'" xsl/buildJsonHirarchyFromSkos.xsl scolomfr/skos/scolomfr.skos > cache/ens.txt
echo "Vocabulaire Compétences du socle commun"
xsltproc --timing --param vocabnumber "'016'" --param vocabtitle "'Compétences du socle commun'" xsl/buildJsonHirarchyFromSkos.xsl scolomfr/skos/scolomfr.skos > cache/scc.txt
echo "Vocabulaire Public cible détaillé"
xsltproc --timing --param vocabnumber "'021'" --param vocabtitle "'Public cible détaillé'" xsl/buildJsonHirarchyFromSkos.xsl scolomfr/skos/scolomfr.skos > cache/pcd.txt
echo "Vocabulaire Niveau éducatif détaillé"
xsltproc --timing --param vocabnumber "'022'" --param vocabtitle "'Niveau éducatif détaillé'" xsl/buildJsonHirarchyFromSkos.xsl scolomfr/skos/scolomfr.skos > cache/ned.txt
echo "Vocabulaire Diplômes"
xsltproc --timing --param vocabnumber "'029'" --param vocabtitle "'Diplômes'" xsl/buildJsonHirarchyFromSkos.xsl scolomfr/skos/scolomfr.skos > cache/dip.txt
echo "Vocabulaire Cadre pédagogique"
xsltproc --timing --param vocabnumber "'040'" --param vocabtitle "'Cadre pédagogique'" xsl/buildJsonHirarchyFromSkos.xsl scolomfr/skos/scolomfr.skos > cache/cap.txt
echo "Vocabulaire Type de déficience"
xsltproc --timing --param vocabnumber "'041'" --param vocabtitle "'Type de déficience'" xsl/buildJsonHirarchyFromSkos.xsl scolomfr/skos/scolomfr.skos > cache/def.txt
echo "Vocabulaire Cadre européen commun de référence pour les langues"
xsltproc --timing --param vocabnumber "'042'" --param vocabtitle "'Cadre européen commun de référence pour les langues'" xsl/buildJsonHirarchyFromSkos.xsl scolomfr/skos/scolomfr.skos > cache/cecrl.txt
echo "Vocabulaire Support"
xsltproc --timing --param vocabnumber "'043'" --param vocabtitle "'Support'" xsl/buildJsonHirarchyFromSkos.xsl scolomfr/skos/scolomfr.skos > cache/sup.txt
