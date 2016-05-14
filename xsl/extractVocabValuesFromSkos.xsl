<?xml version="1.0" encoding="UTF-8"?>
<xsl:stylesheet version="1.0"
	xmlns="http://apiscol.crdp.ac-versailles.fr/2016" xmlns:apiscol="http://apiscol.crdp.ac-versailles.fr/2016"
	xmlns:rdf="http://www.w3.org/1999/02/22-rdf-syntax-ns#" xmlns:xs="http://www.w3.org/2001/XMLSchema"
	xmlns:xsl="http://www.w3.org/1999/XSL/Transform" xmlns:skos="http://www.w3.org/2004/02/skos/core#">
	<xsl:param name="prefix" select="/" />
	<xsl:param name="url" />
	<xsl:output method="xml" omit-xml-declaration="no" encoding="UTF-8"
		indent="yes" />
	<xsl:strip-space elements="*" />

	<xsl:template match="/">
		<vocabs>
			<xsl:call-template name="findvocablist">
				<xsl:with-param name="number" select="'003'" />
			</xsl:call-template>
			<xsl:call-template name="findvocablist">
				<xsl:with-param name="number" select="'005'" />
			</xsl:call-template>
			<xsl:call-template name="findvocablist">
				<xsl:with-param name="number" select="'008'" />
			</xsl:call-template>
			<xsl:call-template name="findvocablist">
				<xsl:with-param name="number" select="'010'" />
			</xsl:call-template>
			<xsl:call-template name="findvocablist">
				<xsl:with-param name="number" select="'011'" />
			</xsl:call-template>
			<xsl:call-template name="findvocablist">
				<xsl:with-param name="number" select="'017'" />
			</xsl:call-template>
			<xsl:call-template name="findvocablist">
				<xsl:with-param name="number" select="'018'" />
			</xsl:call-template>
			<xsl:call-template name="findvocablist">
				<xsl:with-param name="number" select="'019'" />
			</xsl:call-template>
			<xsl:call-template name="findvocablist">
				<xsl:with-param name="number" select="'025'" />
			</xsl:call-template>
		</vocabs>

	</xsl:template>
	<xsl:template name="findvocablist">
		<xsl:param name="number"></xsl:param>
		<xsl:variable name="vocaburi"
			select="concat('http://data.education.fr/voc/scolomfr/scolomfr-voc-',$number)">
		</xsl:variable>
		<vocab>
			<xsl:attribute name="uri"><xsl:value-of select="$vocaburi" /></xsl:attribute>

			<xsl:for-each
				select="//rdf:RDF/rdf:Description[@rdf:about=$vocaburi]/skos:member">
				<xsl:variable name="termuri" select="@rdf:resource">
				</xsl:variable>
				<xsl:call-template name="findterm">
					<xsl:with-param name="termuri" select="$termuri" />
				</xsl:call-template>
			</xsl:for-each>
		</vocab>
	</xsl:template>
	<xsl:template name="findterm">
		<xsl:param name="termuri"></xsl:param>
		<term>
			<id>
				<xsl:value-of select="$termuri" />
			</id>
			<label>
				<xsl:value-of
					select="//rdf:RDF/rdf:Description[@rdf:about=$termuri]/skos:prefLabel">

				</xsl:value-of>
			</label>
			<comment>
				<xsl:value-of
					select="//rdf:RDF/rdf:Description[@rdf:about=$termuri]/skos:scopeNote">

				</xsl:value-of>
			</comment>
		</term>
	</xsl:template>
</xsl:stylesheet>
