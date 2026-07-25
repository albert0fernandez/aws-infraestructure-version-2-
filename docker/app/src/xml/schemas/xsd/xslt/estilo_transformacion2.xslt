<?xml version="1.0" encoding="UTF-8"?>
<xsl:stylesheet version="1.0"
  xmlns:xsl="http://www.w3.org/1999/XSL/Transform">

  <xsl:output method="html" encoding="UTF-8" indent="yes"/>

  <xsl:template match="/">
    <html>
      <head>
        <title>Listado de Recursos</title>
        <style>
          table {
            border-collapse: collapse;
            width: 60%;
            background-color: #f0f8ff;
          }
          th, td {
            border: 1px solid #000;
            padding: 8px;
            text-align: center;
          }
          th {
            background-color: #6495ed;
            color: white;
          }
        </style>
      </head>
      <body>
        <h2>Recursos del Inventario</h2>
        <table>
          <tr>
            <th>ID</th>
            <th>Nombre</th>
            <th>Precio</th>
            <th>Estado</th>
            <th>Ubicación</th>
          </tr>
          <xsl:for-each select="recursos/recurso">
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




