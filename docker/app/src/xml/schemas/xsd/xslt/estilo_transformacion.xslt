<?xml version="1.0" encoding="UTF-8"?>
<xsl:stylesheet version="1.0"
  xmlns:xsl="http://www.w3.org/1999/XSL/Transform">

  <xsl:output method="html" encoding="UTF-8" indent="yes"/>

  <xsl:template match="/recursos">
    <html>
      <head>
        <title>Listado de Recursos</title>
        <style>
          table { border-collapse: collapse; width: 80%; margin: 20px auto; }
          th, td { border: 1px solid black; padding: 8px; text-align: center; }
          th { background-color: #dddddd; }
        </style>
      </head>
      <body>
        <h2 style="text-align: center;">Listado de Recursos</h2>
        <table>
          <tr>
            <th>ID</th>
            <th>Nombre</th>
            <th>Precio</th>
            <th>Estado</th>
            <th>Ubicación</th>
          </tr>
          <xsl:for-each select="recurso">
            <tr>
              <td><xsl:value-of select="recurso_id"/></td>
              <td><xsl:value-of select="recurso_nombre"/></td>
              <td><xsl:value-of select="recurso_precio"/></td>
              <td><xsl:value-of select="recurso_estado"/></td>
              <td><xsl:value-of select="recurso_ubicacion"/></td>
            </tr>
          </xsl:for-each>
        </table>
      </body>
    </html>
  </xsl:template>

</xsl:stylesheet>

