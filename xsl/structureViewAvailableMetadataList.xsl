<?xml version="1.0" encoding="UTF-8"?>
<xsl:stylesheet version="1.0" xmlns="http://www.w3.org/1999/xhtml"
	xmlns:atom="http://www.w3.org/2005/Atom" xmlns:xsl="http://www.w3.org/1999/XSL/Transform"
	xmlns:apiscol="http://www.crdp.ac-versailles.fr/2012/apiscol"
	exclude-result-prefixes="#default apiscol atom">
	<xsl:param name="prefix" select="/" />
	<xsl:param name="currentPage" select="0" />
	<xsl:param name="rowsPerPage" />
	<xsl:param name="query" />
	<xsl:param name="write_permission" select="false()" />
	<xsl:output method="html" omit-xml-declaration="yes"
		encoding="UTF-8" indent="yes" />
	<xsl:strip-space elements="*" />

	<xsl:template match="/">
		<xsl:apply-templates select="atom:feed"></xsl:apply-templates>
	</xsl:template>
	<xsl:template match="atom:feed">

		<ol id="selected-resources-for-structure"
			class="sortable ui-sortable mjs-nestedSortable-branch mjs-nestedSortable-expanded">
			<xsl:apply-templates select="atom:entry"></xsl:apply-templates>
		</ol>



	</xsl:template>
	<xsl:template match="atom:entry">
		<xsl:variable name="selected">
			<xsl:value-of select="./@selected"></xsl:value-of>
		</xsl:variable>
		<xsl:variable name="mdid">
			<xsl:call-template name="substring-after-last">
				<xsl:with-param name="string" select="atom:id" />
				<xsl:with-param name="delimiter" select="':'" />
			</xsl:call-template>
		</xsl:variable>
		<li class="mjs-nestedSortable-leaf">
			<xsl:attribute name="id"><xsl:value-of select="$mdid"></xsl:value-of></xsl:attribute>
			<div class="menuDiv  ui-helper-clearfix">

				<span title="Click to show/hide item details" class="expandEditor ui-icon ui-icon-triangle-1-n">
					<xsl:attribute name="data-id"><xsl:value-of
						select="$mdid"></xsl:value-of></xsl:attribute>
					<span></span>
				</span>
				<span title="Click to show/hide children" class="disclose ui-icon ui-icon-minusthick">
					<span></span>
				</span>
				<span>
					<span title="Click to delete item." class="deleteMenu ui-icon ui-icon-closethick">
						<xsl:attribute name="data-id"><xsl:value-of
							select="$mdid"></xsl:value-of></xsl:attribute>
						<span></span>
					</span>
					<span class="itemTitle">
						<xsl:value-of select="atom:title"></xsl:value-of>
						<xsl:element name="a">
							<xsl:attribute name="class"> ui-icon ui-icon-extlink
				</xsl:attribute>
							<xsl:attribute name="href"><xsl:value-of
								select="$prefix"></xsl:value-of>/resources/detail/<xsl:call-template name="substring-after-last"><xsl:with-param
								name="string" select="atom:link[@rel='self'][@type='text/html']/@href" /> <xsl:with-param
								name="delimiter" select="'/'" /> </xsl:call-template>/display </xsl:attribute>
							Accéder
						</xsl:element>
					</span>

				</span>
				<div class="menuEdit hidden resource-selected ui-helper-clearfix"
					style="display: none;">
					<xsl:attribute name="id">menuEdit<xsl:value-of
						select="$mdid"></xsl:value-of></xsl:attribute>
					<p>
						<img class="ui-widget-content">
							<xsl:attribute name="src">
					<xsl:value-of select="atom:link[@rel='icon']/@href"></xsl:value-of>
					</xsl:attribute>
						</img>
						<xsl:value-of select="atom:summary"></xsl:value-of>
					</p>
				</div>
			</div>

		</li>
	</xsl:template>
	<!--TODO factoriser -->
	<xsl:template name="substring-after-last">
		<xsl:param name="string" />
		<xsl:param name="delimiter" />
		<xsl:choose>
			<xsl:when test="contains($string, $delimiter)">
				<xsl:call-template name="substring-after-last">
					<xsl:with-param name="string"
						select="substring-after($string, $delimiter)" />
					<xsl:with-param name="delimiter" select="$delimiter" />
				</xsl:call-template>
			</xsl:when>
			<xsl:otherwise>
				<xsl:value-of select="$string" />
			</xsl:otherwise>
		</xsl:choose>
	</xsl:template>


</xsl:stylesheet>
