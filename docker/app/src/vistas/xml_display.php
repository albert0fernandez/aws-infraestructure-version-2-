<div class="container is-fluid mb-6">
    <h1 class="title">Recursos</h1>
    <h2 class="subtitle">Visualizar fichero guardado</h2>
</div>

<div class="container pb-6 pt-6">  

    <div class="form-rest mb-6 mt-6"></div>

    <?php    
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $xml_nombre = $_POST['xml_nombre'];

            $root_path = $_SERVER['DOCUMENT_ROOT'] . '/IESAlisal24RetaCantabriaASIR1-Solucion/INVENTARIO-main/';
            $xml_path = $root_path.'/xml/consultas/'.$xml_nombre;
            $xslt_path = $root_path.'/schemas/estilo_transformacion.xsl';

            // Aplicar XSLT al archivo XML
            $xml = new DOMDocument;
            $xml->load($xml_path);

            $xsl = new DOMDocument;
            $xsl->load($xslt_path);

            $proc = new XSLTProcessor;
            $proc->importStyleSheet($xsl);

            $result = $proc->transformToXML($xml);
            echo '<div>'.$result.'</div>';
        }
    ?>
</div>