<?xml version="1.0" encoding="UTF-8"?>
<xsl:stylesheet version="1.0"
	xmlns="http://apiscol.crdp.ac-versailles.fr/2016" xmlns:apiscol="http://apiscol.crdp.ac-versailles.fr/2016"
	xmlns:rdf="http://www.w3.org/1999/02/22-rdf-syntax-ns#" xmlns:xs="http://www.w3.org/2001/XMLSchema"
	xmlns:xsl="http://www.w3.org/1999/XSL/Transform" xmlns:skos="http://www.w3.org/2004/02/skos/core#">
	<xsl:param name="vocabnumber" />
	<xsl:param name="vocabtitle" />
	<xsl:output method="text" omit-xml-declaration="no"
		encoding="UTF-8" indent="no" />
	<xsl:strip-space elements="*" />

	<xsl:template match="/">
		<xsl:call-template name="findmembers">
			<xsl:with-param name="number" select="$vocabnumber" />
			<xsl:with-param name="title" select="$vocabtitle" />
		</xsl:call-template>



	</xsl:template>
	<xsl:template name="findmembers">
		<xsl:param name="number"></xsl:param>
		<xsl:param name="title"></xsl:param>
		<xsl:variable name="vocaburi"
			select="concat('http://data.education.fr/voc/scolomfr/scolomfr-voc-',$number)"/>[ {
		"title" : "<xsl:value-of select="$title"></xsl:value-of>",
		"isFolder" : true,
		"hideCheckbox" : true,
		"children" : [<xsl:for-each
			select="//rdf:RDF/rdf:Description[@rdf:about=$vocaburi]/skos:member">
			<xsl:variable name="termuri" select="@rdf:resource"/>
			<xsl:call-template name="findtopconcept">
				<xsl:with-param name="termuri" select="$termuri" />
			</xsl:call-template>
		</xsl:for-each>]
		}]</xsl:template>
	<xsl:template name="findtopconcept">
		<xsl:param name="termuri"></xsl:param>
		<xsl:if
			test="//rdf:RDF/rdf:Description/skos:hasTopConcept[@rdf:resource=$termuri]">{"title" :"<xsl:call-template name="findpreflabel">
				<xsl:with-param name="termuri" select="$termuri" />
			</xsl:call-template>",
			"key" : "<xsl:value-of select="$termuri"></xsl:value-of>",
			"icon" : false,
			"children" : [<xsl:call-template name="findnarrowerconcept">
				<xsl:with-param name="termuri" select="$termuri" />
			</xsl:call-template>]
			}<xsl:if test="position() != last()">,</xsl:if>
		</xsl:if>


	</xsl:template>
	<xsl:template name="findpreflabel">
		<xsl:param name="termuri"></xsl:param>
		<xsl:value-of
			select="//rdf:RDF/rdf:Description[@rdf:about=$termuri]/skos:prefLabel[@xml:lang='fr']">
		</xsl:value-of>
	</xsl:template>
	<xsl:template name="findnarrowerconcept">
		<xsl:param name="termuri"></xsl:param>
		<xsl:for-each
			select="//rdf:RDF/rdf:Description[@rdf:about=$termuri]/skos:narrower">
			<xsl:variable name="narrowertermuri" select="@rdf:resource" />{
			"title" : "<xsl:call-template name="findpreflabel">
				<xsl:with-param name="termuri" select="$narrowertermuri" />
			</xsl:call-template>","key" : "<xsl:value-of select="$narrowertermuri"></xsl:value-of>",
			"icon" : false,
			"children" : [<xsl:call-template name="findnarrowerconcept">
				<xsl:with-param name="termuri" select="$narrowertermuri" />
			</xsl:call-template>]
			}<xsl:if test="position() != last()">,</xsl:if>
		</xsl:for-each>


	</xsl:template>
</xsl:stylesheet>
