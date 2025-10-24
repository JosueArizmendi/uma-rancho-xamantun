<?php
// Incluir archivo de conexión a la base de datos
include('../model/conexion_bd.php');
session_start();

// Obtener datos del usuario actual
$usuario = $_SESSION['usuario'];
$query = "SELECT * FROM usuarios WHERE nom_usuario = ?";
$stmt = $conn->prepare($query);
$stmt->execute([$usuario]);
$user_data = $stmt->fetch(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="es">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet"
    integrity="sha384-1BmE4kWBq78iYhFldvKuhfTAU6auU8tT94WrHftjDbrCEXSU1oBoqyl2QvZ6jIW3" crossorigin="anonymous">
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
  <link rel="stylesheet" href="../CSS/custom-styles.css">
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"
    integrity="sha384-ka7Sk0Gln4gmtz2MlQnikT1wXgYsOg+OMhuP+IlRH9sENBO0LRn5q+8nbTov4+1p"
    crossorigin="anonymous"></script>
  <title>Plan de Trabajo</title>

  <style>
    /* Estilos personalizados */
    .document-container {
      max-width: 700px;
      max-height: 1500px;
      margin: 0 auto;
      padding: 10px;
      border-radius: 8px;
      background-color: #fff;
      box-shadow: 0 0 10px rgba(0,0,0,0.1);
      border: 3px solid #ccc;  /* Borde gris claro */
    }
    
    .document-viewer {
      width: 100%;
      height: 32vh;
      border: 1px solid #cccacaff;
      border-radius: 5px;
      margin-top: 20px;
      padding: 10px; /* Añade esta línea */

    }
    
    .document-title {
      text-align: center;
      margin-bottom: 20px;
      color: #1B396A ;
      font-weight: bold;
    }

    .container {
        padding-top: 15px; /* Añadimos espacio para no solapar con el navbar */
    }
    
    /* Ocultar la barra de traducción */
    .goog-te-banner-frame {
      display: none !important;
    }

    /* Ocultar el logo de Google */
    .goog-logo-link {
      display: none !important;
    }
  
    /* Asegura que el traductor se vea por encima de otros elementos si es necesario */
    #google_translate_element {
      z-index: 9999 !important;
      position: fixed !important;
      top: 0;
      right: 0;
      margin-top: 80px;
    }
    
    /* Estilos para submenús desplegables */
    .dropdown-submenu {
      position: relative;
    }

    .dropdown-submenu .dropdown-menu {
      top: 0;
      left: 100%;
      margin-top: -1px;
      margin-left: .1rem;
    }

    .dropdown-submenu:hover .dropdown-menu {
      display: block;
    }

     body {
      background-color: #f8f9fa;
      font-family: Arial, sans-serif;
      text-align: justify;

    }
    
    .document-container {
      max-width: 800px;
      margin: 80px auto 20px;
      padding: 20px;
      border: 3px solid #ccc;
      border-radius: 8px;
      background-color: #fff;
      box-shadow: 0 0 10px rgba(0,0,0,0.1);
    }
    
    .document-viewer {
      width: 100%;
      height: 145vh;
      overflow-y: auto;
      border: 1px solid #ccc;
      border-radius: 5px;
      padding: 20px;
      background-color: #fff;
    }
    
    .document-title {
      text-align: center;
      margin-bottom: 20px;
      color: #1B396A;
      font-weight: bold;
    }
    
    .page {
      display: none;
      height: 100%;
    }
    
    .page.active {
      display: block;
    }
    
    .page-number {
      text-align: center;
      margin-top: 10px;
      font-size: 14px;
      color: #0a0a0aff;
    }
    
    table {
      width: 100%;
      border-collapse: collapse;
      margin-bottom: 15px;
    }
    
    table, th, td {
      border: 1px solid #171a16ff;
    }
    
    th, td {
      padding: 8px;
      text-align: left;
    }
    
    th {
      background-color: #3dfd2bff;
    }
    
    .navigation-buttons {
      display: flex;
      justify-content: space-between;
      margin-top: 20px;
    }
    
    .toc {
      margin-bottom: 20px;
      padding: 10px;
      background-color: #f8f9fa;
      border-radius: 5px;
    }
    
    .toc ul {
      padding-left: 20px;
    }
    
    .toc li {
      margin-bottom: 5px;
    }
    
    .toc a {
      text-decoration: none;
      color: #1B396A;
    }
    
    .toc a:hover {
      text-decoration: underline;
    }
    
    .header-image {
      text-align: center;
      margin: 15px 0;
    }
    
    .header-image img {
      max-width: 100%;
      height: auto;
    }
    
    .figure-caption {
      text-align: center;
      font-style: italic;
      margin-bottom: 15px;
    }

    /* Justificar todo el texto del documento */
    body, .document-viewer, .page, p, td, li {
        text-align: justify;
        text-justify: inter-word;
    }

    /* Mantener los títulos alineados a la izquierda */
    h1, h2, h3, h4, h5, h6, th {
        text-align: left;
    }

    /* Mantener centrados los elementos que deben estarlo */
    .document-title, .figure-caption, .page-number {
        text-align: center;
    }
  </style>
</head>

<body>

  <header>
    <nav class="navbar navbar-expand-lg fixed-top navbar-dark bg-dark">
      <div class="me-3 ms-2">
        <img src="../img2/rancho.png" class="rounded" width="60" height="56">
      </div>
      <div class="container-fluid">
        <a class="navbar-brand fs-3 text-white" href="template.php">Inicio</a>
        <button class="navbar-toggler" type="button" data-bs-toggle="offcanvas" data-bs-target="#offcanvasNavbar"
          aria-controls="offcanvasDarkNavbar" aria-label="Toggle navigation">
          <span class="navbar-toggler-icon"></span>
        </button>
        <div class="offcanvas offcanvas-end text-bg-dark" tabindex="-1" id="offcanvasNavbar"
          aria-labelledby="offcanvasDarkNavbarLabel">
          <div class="offcanvas-header">
            <h5 class="offcanvas-title" id="offcanvasDarkNavbarLabel">Opciones</h5>
            <button type="button" class="btn-close btn-close-white" data-bs-dismiss="offcanvas"
              aria-label="Close"></button>
          </div>
          <div class="offcanvas-body">
            <ul class="navbar-nav justify-content-start flex-grow-1">
              <li class="nav-item dropdown">
                <a class="nav-link dropdown-toggle text-dark fs-3" href="#" role="button"
                  data-bs-toggle="dropdown">Nosotros</a>
                <ul class="dropdown-menu">
                  <li><a class="dropdown-item" href="historia.php">Historia</a></li>
                  <li><a class="dropdown-item" href="acerca2_de.php">Acerca del Rancho Xamantún</a></li>
                  <li><a class="dropdown-item" href="plan.php">Plan de Manejo</a></li>
                  <li><a class="dropdown-item" href="autorizacion.php">Registro Autorizado por parte de la SEMARNAT</a></li>
                  <li><a class="dropdown-item" href="app.php">Mapa Interactivo</a></li>
                  <li><a class="dropdown-item" href="curriculum.php">Curriculum</a></li> 
                </ul>
              </li>
              <li class="nav-item dropdown">
                <a class="nav-link dropdown-toggle text-dark fs-3" href="#" role="button"
                  data-bs-toggle="dropdown">Fauna</a>
                <ul class="dropdown-menu">
                  <li><a class="dropdown-item" href="especies_fauna.php">Especies</a></li>
                  <li><a class="dropdown-item" href="avistamientos_fauna.php">Avistamientos</a></li>
                </ul>
              </li>
              <li class="nav-item dropdown">
                <a class="nav-link dropdown-toggle text-dark fs-3 d-none d-md-block" href="#" role="button"
                  data-bs-toggle="dropdown">Flora</a>
                <ul class="dropdown-menu">
                  <li><a class="dropdown-item" href="especies_flora.php">Especies</a></li>
                  <li><a class="dropdown-item" href="avistamientos_flora.php">Avistamientos</a></li>
                </ul>
              </li>
              <li class="nav-item dropdown">
                  <a class="nav-link dropdown-toggle text-dark fs-3 d-none d-md-block" href="#" role="button"
                      data-bs-toggle="dropdown">Administración</a>
                  <ul class="dropdown-menu">
                      <li><a class="dropdown-item" href="especie_faunaR.php">Registros especies Fauna</a></li>
                      <li><a class="dropdown-item" href="avistamiento_faunaR.php">Registros avistamientos Fauna</a></li>
                      <li><a class="dropdown-item" href="especie_floraR.php">Registros especies Flora</a></li>
                      <li><a class="dropdown-item" href="avistamiento_floraR.php">Registros avistamientos Flora</a></li>
                      <li class="dropdown-submenu">
                          <a class="dropdown-item dropdown-toggle" href="#">Catálogo de Reg. Especies Fauna</a>
                          <ul class="dropdown-menu">
                              <li><a class="dropdown-item" href="especie_faunaR.php?categoria=Mammalia">Mamíferos</a></li>
                              <li><a class="dropdown-item" href="especie_faunaR.php?categoria=Aves">Aves</a></li>
                              <li><a class="dropdown-item" href="especie_faunaR.php?categoria=Reptilia">Reptiles</a></li>
                              <li><a class="dropdown-item" href="especie_faunaR.php?categoria=Amphibia">Anfibios</a></li>
                              <li><a class="dropdown-item" href="especie_faunaR.php?categoria=Pisces">Peces</a></li>
                              <li><a class="dropdown-item" href="especie_faunaR.php?categoria=Insecta">Insectos</a></li>
                              <li><a class="dropdown-item" href="especie_faunaR.php?categoria=Arachnida">Arácnidos</a></li>
                          </ul>
                      </li>
                      <li class="dropdown-submenu">
                          <a class="dropdown-item dropdown-toggle" href="#">Catálogo de Reg. Especies Flora</a>
                          <ul class="dropdown-menu">
                              <li><a class="dropdown-item" href="especie_floraR.php?categoria=Magnoliopsida">Dicotiledóneas</a></li>
                              <li><a class="dropdown-item" href="especie_floraR.php?categoria=Liliopsida">Monocotiledóneas</a></li>
                              <li><a class="dropdown-item" href="especie_floraR.php?categoria=Gimnospermas">Gimnospermas</a></li>
                              <li><a class="dropdown-item" href="especie_floraR.php?categoria=Pteridopsida">Helechos</a></li>
                          </ul>
                      </li>
                  </ul>
              </li>
              <li class="nav-item">
                <a class="nav-link text-dark fs-3" href="usuarios.php">Usuarios</a>
              </li>
              <li class="nav-item">
                <a class="nav-link text-dark fs-3" href="blogsR.php">Blogs</a>
              </li>
              <!-- Perfil de usuario con imagen -->
              <li class="nav-item dropdown">
                <a class="nav-link dropdown-toggle text-dark fs-3" href="#" role="button" data-bs-toggle="dropdown">
                  <img src="<?php echo !empty($user_data['imagen_perfil']) ? $user_data['imagen_perfil'] : '../imagenes_perfil/vacio.jpg'; ?>" alt="Perfil" class="rounded-circle" width="30" height="30">
                </a>
                <ul class="dropdown-menu">
                  <li><a class="dropdown-item" href="perfil.php">Ver Perfil</a></li>
                  <li><a class="dropdown-item" href="comentarios_blogR.php">Opiniones</a></li>
                </ul>
              </li>
            </ul>
          </div>
        </div>

        <div class="d-flex align-items-center">
          <div class="d-flex justify-content-end">
            <a href="../logout.php" class="btn btn-danger ms-2">Cerrar sesión</a>
          </div>
        </div>
      </div>
    </nav>
  </header>
  
  <div class="container mt-2">
    <div id="google_translate_element" style="text-align: right;"></div>
  </div>

  <script type="text/javascript">
    function googleTranslateElementInit() {
      new google.translate.TranslateElement({
        includedLanguages: 'es,yua,en,fr,de',
        layout: google.translate.TranslateElement.InlineLayout.SIMPLE
      }, 'google_translate_element');
    }
  </script>
  <script type="text/javascript" src="https://translate.google.com/translate_a/element.js?cb=googleTranslateElementInit"></script>

  <div class="container">
    <div class="document-container">
      <div class="navigation-buttons">
        <button class="btn btn-primary" onclick="prevPage()">
          <i class="bi bi-arrow-left"></i> Anterior
        </button>
          <h3 class="document-title">PLAN DE MANEJO - UMA RANCHO XAMANTUN</h3>
        <button class="btn btn-primary" onclick="nextPage()">
          Siguiente <i class="bi bi-arrow-right"></i>
        </button>
      </div>
      
      <div class="document-viewer">
        <!-- Página 1: Índice -->
        <div class="page active" id="page1">
          <h3 class="text-center"><strong>ÍNDICE</strong></h3>
          <div class="toc">
            <ul>
              <li><a href="#" onclick="showPage(2)">DEL PROMOVENTE - Obligaciones y Derechos</a></li>
              <li><a href="#" onclick="showPage(3)">INFORMACIÓN BIOLÓGICA Y ECOLÓGICA</a></li>
              <li><a href="#" onclick="showPage(4)">OBJETIVO GENERAL</a></li>
              <li><a href="#" onclick="showPage(5)">OBJETIVOS ESPECÍFICOS, METAS E INDICADORES - Objetivos Ecológicos</a></li>
              <li><a href="#" onclick="showPage(6)">OBJETIVOS ESPECÍFICOS - Objetivos Sociales y Económicos</a></li>
              <li><a href="#" onclick="showPage(7)">DESCRIPCIÓN FÍSICA - Localización</a></li>
              <li><a href="#" onclick="showPage(8)">DESCRIPCIÓN FÍSICA - Sistema Vegetacional Dominante</a></li>
              <li><a href="#" onclick="showPage(9)">FAUNA PRESENTE EN EL SITIO</a></li>
              <li><a href="#" onclick="showPage(10)">MANEJO Y CONSERVACIÓN DEL HÁBITAT</a></li>
              <li><a href="#" onclick="showPage(11)">MONITOREO DE ESPECIES INDICADORAS</a></li>
              <li><a href="#" onclick="showPage(12)">MONITOREO DE CAMBIO EN LA COBERTURA VEGETAL</a></li>
              <li><a href="#" onclick="showPage(13)">METODOLOGÍA BIOCOMUNI</a></li>
              <li><a href="#" onclick="showPage(14)">MEDIDAS DE CONTINGENCIA</a></li>
              <li><a href="#" onclick="showPage(15)">VIGILANCIA PARTICIPATIVA</a></li>
              <li><a href="#" onclick="showPage(16)">MONITOREO SANITARIO</a></li>
              <li><a href="#" onclick="showPage(17)">MECANISMOS DE VIGILANCIA</a></li>
              <li><a href="#" onclick="showPage(18)">TIPO DE APROVECHAMIENTO</a></li>
              <li><a href="#" onclick="showPage(19)">CALENDARIO DE ACTIVIDADES</a></li>
              <li><a href="#" onclick="showPage(20)">BIBLIOGRAFÍA CONSULTADA</a></li>
              <li><a href="#" onclick="showPage(21)">ANEXOS - Descripción Física y Biológica</a></li>
              <li><a href="#" onclick="showPage(22)">ANEXOS - Características de la UMA</a></li>
              <li><a href="#" onclick="showPage(23)">ANEXOS - Ubicación</a></li>
              <li><a href="#" onclick="showPage(24)">ANEXOS - Disponibilidad de Agua e Instalaciones</a></li>
              <li><a href="#" onclick="showPage(25)">ANEXOS - Recursos Humanos y Especies</a></li>
              <li><a href="#" onclick="showPage(26)">ANEXOS - Protocolo de Incorporación</a></li>
              <li><a href="#" onclick="showPage(27)">ANEXOS - Programa de Educación Ambiental (Parte 1)</a></li>
              <li><a href="#" onclick="showPage(28)">ANEXOS - Programa de Educación Ambiental (Parte 2)</a></li>
              <li><a href="#" onclick="showPage(29)">ANEXOS - Programa de Educación Ambiental (Parte 3)</a></li>
            </ul>
          </div>
          <div class="page-number">Página 1 de 29</div>
        </div>

        <!-- Página 2: Del Promovente -->
        <div class="page" id="page2">
          <h4 class="text-center"><strong>DEL PROMOVENTE</strong></h4>
          <table>
            <tr>
              <th class="text-center">OBLIGACIONES</th>
              <th class="text-center">DERECHOS</th>
            </tr>
            <tr>
              <td>
                <p>Contribuir con la conservación del hábitat natural de la vida silvestre conforme a lo establecido en la ley, <em>(Art. 18 y 63 a 69 de la LGVS).</em></p>
                <p>Informar a la Secretaria a través de la Dirección General de Vida Silvestre en el momento que decidan comenzar con cualquier actividad de conservación en sus predios <em>(Art. 39 de la LGVS y 29 RLGVS).</em></p>
                <p>Presentar la solicitud correspondiente a la actividad que desee realizar en su predio en los formatos que para tal efecto establezca la Secretaría <em>(Art. 12 RLGVS).</em></p>
              </td>
              <td>
                <p>Los legítimos poseedores de predios, tendrán el derecho a realizar un aprovechamiento sustentable conforme a lo establecido en la LGVS; asimismo, podrán transferir esta prerrogativa a terceros, conservando el derecho a participar de los beneficios que se deriven de dicho aprovechamiento <em>(Art. 18 de la LGVS).</em></p>
                <p>Elegir el tipo de manejo en UMA (vida libre o intensivo) y el tipo de aprovechamiento (extractivo, no extractivo y mixto) que deseen implementar en su predio <em>(Art. 23 al 25 del RLGVS).</em></p>
              </td>
            </tr>
            <tr>
              <td>
                <p>La UMA deberá contar con el registro y la autorización del Plan de Manejo ante la SEMARNAT <em>(Art. 39 y 40 de la LGVS, 12, 30 al 32 y 37 al 46 RLGVS).</em></p>
                <p>Los legítimos poseedores de los predios, así como los terceros que realicen el aprovechamiento, serán responsables solidarios de los efectos negativos que éste pudiera tener para la conservación de la vida silvestre y su hábitat <em>(Art. 18 de la LGVS).</em></p>
              </td>
              <td>
                <p>Modificar los datos del registro de UMA <em>(Art. 47 del RLGVS).</em></p>
                <p>Realizar la solicitud correspondiente en los formatos establecidos para cualquier actividad relacionada con el manejo del hábitat, especies, partes o derivados de vida silvestre y que requieran licencia, permiso o autorización de la Secretaría <em>(Art. 12 RLGVS).</em></p>
              </td>
            </tr>
            <tr>
              <td>
                <p>Todos los que manejen vida silvestre fuera de su hábitat, deberán contemplar en sus planes de manejo, aspectos de educación ambiental y de conservación, con especial atención a las especies que se encuentren en alguna categoría de riesgo.</p>
                <p>Manejar ejemplares y poblaciones exóticos exclusivamente en condiciones de confinamiento, de acuerdo con un plan de manejo que deberá ser previamente aprobado por la Secretaría y en el que se establecerán las condiciones de seguridad y contingencia,
                   para evitar efectos negativos que pudieran tener para la conservación de los ejemplares y poblaciones nativas y su hábitat <em> (Art. 27 de la LGVS).</em></p>
              </td>
              <td>
                <p>Participar en la ejecución de los programas de manejo de las ANP dentro de sus predios dando prioridad al aprovechamiento no extractivo cuando se trate de especies o poblaciones amenazadas o en peligro de extinción <em>(Art. 47 de la LGVS).</em></p>
                <p>Participar en el aprovechamiento extractivo de la vida silvestre, en condiciones de sustentabilidad prescritas en la LGVS, que podrán autorizarse para actividades de colecta, captura o caza; con fines de reproducción, restauración, recuperación, repoblación,
                  reintroducción, translocación, con fines económicos o de educación ambiental <em> (Art. 82 al 92 de la LGVS, 91, 98, 99, 101,103, 104, 106 al 113,
                  123, 125, 126 RLGVS).</em>
                </p>
              </td>
            </tr>
            <tr>
              <td>
                <p>Presentar informes periódicos de las actividades realizadas en la UMA, incidencias y contingencia, logros con base en los indicadores de éxito, y en caso de aprovechamiento, datos socioeconómicos <em>(Art. 42, 98 y 103 de la LGVS, 50 al 52, 82, 105 y 127 RLGVS).</em></p>
                <p>En caso de una visita de supervisión técnica, el propietario de la UMA deberá exhibir el registro y la autorización correspondiente de su plan de manejo, en el cual se especifique la(s) especie(s) y actividad(es) autorizada(s) <em>(Art. 43 y 110 de la LGVS, 14 y 33 del RLGVS)</em></p>
              </td>
              <td>
                <p>Realizar el aprovechamiento no extractivo de vida silvestre garantizando el bienestar de los ejemplares, la continuidad de sus poblaciones y la conservación de sus hábitats, <em>(Art. 99 al 103 de la LGVS, 132 y 133 RLGVS).</em></p>
                <p>Acceder a la información que se genere en la Secretaria a través del Sistema Nacional de Información Ambiental y de Recursos Naturales, siempre y 
                  cuando la información no sea susceptible de generar derechos de propiedad intelectual 
                 <em>(Art. 48 y 49 de la LGVS).</em></p>
              </td>
            </tr>
            <tr>
              <td>
                <p>Exhibir los documentos que demuestren la legal procedencia (marca que demuestre que han sido objeto de un aprovechamiento sustentable, tasa de aprovechamiento autorizada y la nota de remisión o factura correspondiente) de los ejemplares,
                  partes y derivados de especies fuera de su hábitat natural para registros, autorizaciones de aprovechamiento, traslado, importación, exportación y reexportación <em> (Art. 50 al 55 de la LGVS, 53 al 56 RLGVS).</em></p>
                <p>11.	Respetar el establecimiento de vedas u otras medidas preventivas para facilitar evaluar los daños ocasionados por desastres naturales o actividades humanas, permitir la recuperación de las poblaciones y evitar riesgos a la salud humana
                 <em>(Art. 71 de la LGVS).</em></p>
                <p>12.	Denunciar ante la PROFEPA daños a la vida silvestre y su hábitat sin necesidad de demostrar que sufre una afectación personal y directa en razón de dichos daños <em> (Art. 107 de LGVS).</em>
                  Cubrir los gastos que se hubieren realizado para la protección, conservación, liberación o el cuidado, según corresponda, de los ejemplares de vida silvestre que hubiesen sido asegurados derivados de una sanción administrativa o 
                  infracción en que se imponga el decomiso <em> (Art. 128 de la LGVS).</em></p>
              </td>
              <td>
                <p>Participar en programas o proyectos de conservación, restauración, repoblación y reintroducción, así como de investigación y educación ambiental autorizados por la Secretaría y que tenga relación con ejemplares confinados de 
                  las especies probablemente extintas en el medio silvestre <em>(Art. 59 de la LGVS).</em></p>
                <p>Participar en el desarrollo de proyectos de conservación y recuperación, el establecimiento de medidas especiales de manejo y conservación de hábitat críticos y de áreas de refugio para proteger especies acuáticas,
                  la coordinación de programas de muestreo y seguimiento permanente de las especies y poblaciones en riesgo y de aquellas consideradas como prioritarias para la conservación <em>(Art. 60 y 62 de la LGVS).</em></p>
                <p>Implementar medidas de control de especies que se tornen perjudiciales, previa autorización de la DGVS <em>(Art. 72 de la LGVS, 78, 79, y 80 RLGVS).</em>
                Participar en el establecimiento y desarrollo de estrategias para el desarrollo natural de poblaciones de especies silvestres nativas, en conjunto con la autoridad correspondiente <em>(Art. 75 de la LGVS).</em></p>
                <p>Participar en los programas y proyectos de liberación de ejemplares a su hábitat natural en los siguientes supuestos; por rehabilitación, translocación, repoblación o de reintroducción en el marco
                  de la ley y su reglamento,<em> (Art. 79, 80 y 81 de la LGVS, 83 RLGVS). </em></p>
                <p>Recibir el apoyo, asesoría técnica y capacitación por parte de las autoridades competentes cuando realicen el aprovechamiento de ejemplares, partes y derivados de vida silvestre para su consumo directo, 
                  o para su venta en cantidades que sean proporcionales a la satisfacción 
                  de las necesidades básicas de éstas y de sus dependientes económicos <em>(Art. 92 de la LGVS).</em></p>
                <p>
                  Ser notificado de cualquier acto administrativo que se generen durante el procedimiento de inspección <em>(Art. 125 de la LGVS).<em></p>
              </td>
            </tr>
            <!-- Continuación de la tabla... -->
          </table>
          <p><em>Fuente: LGVS y RLGVS</em></p>
          <div class="page-number">Página 2 de 29</div>
        </div>

        <!-- Página 3: Información Biológica y Ecológica -->
        <div class="page" id="page3">
          <h4><strong>INFORMACIÓN BIOLÓGICA Y ECOLÓGICA</strong></h4>
          <p>Artículo 40. Para registrar los predios como unidades de manejo para la conservación de vida silvestre, la Secretaría integrará, de conformidad con lo establecido en el reglamento, un expediente con los datos generales, los títulos que acrediten la propiedad o legítima posesión del promovente sobre los predios; la ubicación geográfica, superficie y colindancias de los mismos; y un plan de manejo.</p>
          <p>El plan de manejo deberá contener:</p>
          <ol type="a">
            <li>Sus objetivos específicos; metas a corto, mediano y largo plazos; e indicadores de éxito.</li>
            <li>Información biológica de la o las especies sujetas a plan de manejo.</li>
            <li>La descripción física y biológica del área y su infraestructura.</li>
            <li>Los métodos de muestreo.</li>
            <li>El calendario de actividades.</li>
            <li>Las medidas de manejo del hábitat, poblaciones y ejemplares.</li>
            <li>Las medidas de contingencia.</li>
            <li>Los mecanismos de vigilancia.</li>
            <li>En su caso, los medios y formas de aprovechamiento y el sistema de marca para identificar los ejemplares, partes y derivados que sean aprovechados de manera sustentable.</li>
          </ol>
          <p>El plan de manejo deberá ser elaborado por el responsable técnico, quien será responsable solidario con el titular de la unidad registrada, del aprovechamiento sustentable de la vida silvestre, su conservación y la de su hábitat, en caso de otorgarse la autorización y efectuarse el registro.</p>
          <div class="page-number">Página 3 de 29</div>
        </div>

        <!-- Página 4: Objetivo General -->
        <div class="page" id="page4">
          <h4><strong>OBJETIVO GENERAL</strong></h4>
          <p><strong><em>Conservación de hábitat natural, poblaciones y ejemplares de especies de vida silvestre mediante el manejo del hábitat que compone al Rancho Xamantún.</em></strong></p>
          
          <h4><strong>OBJETIVOS ESPECÍFICOS, METAS E INDICADORES DE DESEMPEÑO</strong></h4>
          <h5>Objetivos ecológicos</h5>
          
          <table>
            <tr>
              <th>OBJETIVO ESPECÍFICO</th>
              <th class="text-center">META (CORTO PLAZO)</th>
              <th class="text-center">INDICADOR</th>
            </tr>
            <tr>
              <td rowspan="5"><strong>Obj. 1:</strong> Conservación del hábitat natural del sitio.</td>
              <td>Conservación y documentación del 15% del área del hábitat.</td>
              <td>Porcentaje alcanzado en la documentación de los estudios de caracterización ambiental.</td>
            </tr>
            <tr>
              <td class="text-center" bgcolor="#43ff32ff"><strong>META (MEDIANO PLAZO)</strong></td>
              <td class="text-center"bgcolor="#43ff32ff"><strong>INDICADOR</strong></td>
            </tr>
            <tr>
              <td>Conservación y documentación del 35% del área del hábitat.</td>
              <td>Porcentaje alcanzado en la documentación de los estudios de caracterización ambiental.</td>
            </tr>
            <tr>
              <td class="text-center" bgcolor="#43ff32ff"><strong>META (LARGO PLAZO)</strong></td>
              <td class="text-center" bgcolor="#43ff32ff"><strong>INDICADOR</strong></td>
            </tr>
            <tr>
              <td>Conservación y documentación del 50% del área del hábitat.</td>
              <td>Porcentaje alcanzado en la documentación de los estudios de caracterización ambiental.</td>
            </tr>
          </table>
          <div class="page-number">Página 4 de 29</div>
        </div>

        <!-- Página 5: Objetivos Específicos 2 -->
        <div class="page" id="page5">
          <h4><strong>OBJETIVOS ESPECÍFICOS (CONTINUACIÓN)</strong></h4>
          <table>
            <tbody>
            <tr>
              <th class="text-center">OBJETIVO ESPECÍFICO</th>
              <th class="text-center">META (CORTO PLAZO)</th>
              <th class="text-center">INDICADOR</th>
            </tr>
            <tr>
              <td rowspan="5"><strong>Obj. 2:</strong> Investigación in situ de las especies de vida silvestre listadas en la NOM-059-SEMARNAT-2010, Prioritarias, Endémicas y Nativas sujetas a conservación.</td>
              <td>Investigación, conservación y documentos comprobatorios de 5 especies.</td>
              <td>Número de especies investigadas y en actividades de conservación.</td>
            </tr>
            <tr>
              <td class="text-center"bgcolor="#43ff32ff"><strong>META (MEDIANO PLAZO)</strong></td>
              <td class="text-center" bgcolor="#43ff32ff"><strong>INDICADOR</strong></td>
            </tr>
            <tr>
              <td>Investigación, conservación y documentos comprobatorios de 10 especies.</td>
              <td>Número de especies investigadas y en actividades de conservación.</td>
            </tr>
            <tr>
              <td class="text-center" bgcolor="#43ff32ff"><strong>META (LARGO PLAZO)</strong></td>
              <td class="text-center" bgcolor="#43ff32ff"><strong>INDICADOR</strong></td>
            </tr>
            <tr>
              <td>Investigación, conservación y documentos comprobatorios de 15 especies.</td>
              <td>Número de especies investigadas y en actividades de conservación.</td>
            </tr>
            </tbody>
          </table>
          
        
          <div class="page-number">Página 5 of 29</div>
        </div>

        <!-- Página 6: Objetivos Sociales y Económicos -->
        <div class="page" id="page6">
          <h4><strong>Objetivos sociales</strong></h4>
          <table>
            <tr>
              <th class="text-center">OBJETIVO ESPECÍFICO</th>
              <th class="text-center">META (CORTO PLAZO)</th>
              <th class="text-center">INDICADOR</th>
            </tr>
            <tr>
              <td rowspan="6"><strong>Obj 3:</strong> Educación, difusión y divulgación científica a todos los niveles educativos, así como a los visitantes y capacitación del personal del Rancho Xamantún, así como personal de las autoridades locales, municipales, estatales o federales.</td>
              <td>El 100% del personal está capacitado en cada una de sus funciones.</td>
              <td>Porcentaje de personal capacitado por rubro.</td>
            </tr>
          <h4><strong>OBJETIVOS SOCIALES Y ECONÓMICOS</strong></h4>
            <tr>
              <td class="text-center" bgcolor="#43ff32ff"><strong>META (MEDIANO PLAZO)</strong></td>
              <td class="text-center" bgcolor="#43ff32ff"><strong>INDICADOR</strong></td>
            </tr>
            <tr>
              <td>Se cuenta con tres programas de educación ambiental dirigido a escuelas de educación básica (preescolar, primaria y secundaria).</td>
              <td>Número de programas implementados de educación ambiental para escuelas de educación básica.</td>
            </tr>
            <tr>
              <td class="text-center" bgcolor="#43ff32ff"><strong>META (LARGO PLAZO)</strong></td>
              <td class="text-center" bgcolor="#43ff32ff"><strong>INDICADOR</strong></td>
            </tr>
            <tr>
              <td>Se cuenta con un programa de educación ambiental dirigida a niveles de estudios medio y superior y otro para visitantes (público en general).</td>
              <td>Número de programas de educación ambiental dirigido a nivel medio y superior. Número de programas para visitantes (público en general).</td>
            </tr>
            <tr>
              <td>Se capacita al personal con apoyo de las autoridades federales de la PROFEPA en cada estado, en el conocimiento de las especies en la NOM059-SEMARNAT-2010 y para apoyar la toma de decisiones.</td>
              <td>Una plática, taller o curso al año.</td>
            </tr>
          </table>
          
          <h4><strong>Objetivos económicos</strong></h4>
          <table>
            <tr>
              <th class="text-center">OBJETIVO ESPECÍFICO</th>
              <th class="text-center">META (CORTO PLAZO)</th>
              <th class="text-center">INDICADOR</th>
            </tr>
            <tr>
              <td rowspan="5"><strong>Obj. 4:</strong> Mejorar los ingresos del Rancho Xamantún a través de los servicios ofrecidos.</td>
              <td>Incremento igual o mayor en 10% los ingresos anuales por servicios ofertados.</td>
              <td>Incremento en el porcentaje de ingresos anuales por servicios ofertados.</td>
            </tr>
            <tr>
              <td class="text-center" bgcolor="#43ff32ff"><strong>META (MEDIANO PLAZO)</strong></td>
              <td class="text-center" bgcolor="#43ff32ff"><strong>INDICADOR</strong></td>
            </tr>
            <tr>
              <td>Incremento del 15% en de los ingresos anuales por servicios ofertados.</td>
              <td>Incremento en el porcentaje de ingresos anuales por servicios ofertados.</td>
            </tr>
            <tr>
              <td class="text-center" bgcolor="#43ff32ff"><strong>META (LARGO PLAZO)</strong></td>
              <td class="text-center" bgcolor="#43ff32ff"><strong>INDICADOR</strong></td>
            </tr>
            <tr>
              <td>Incremento igual o mayor en 20% los ingresos anuales por servicios ofertados.</td>
              <td>Incremento en el porcentaje de ingresos anuales por servicios ofertados.</td>
            </tr>
          </table>
          
          <p>Los tiempos estipulados para las metas son:</p>
          <ul>
            <li><strong>Corto plazo:</strong> Menor de 2 años.</li>
            <li><strong>Mediano plazo:</strong> Igual o mayor a 2 años pero menor a 4.</li>
            <li><strong>Largo plazo:</strong> Igual o mayor a 4 años.</li>
          </ul>
          <p>Además de las obligaciones estipuladas en la Ley General de Vida Silvestre y su Reglamento, 
            los cuatro objetivos específicos que se deben de cumplir, acorde con el Convenio sobre la
            Diversidad Biológica (CDB), son:</p>
          <ol>
            <li><strong> Ecológico.</strong> Conservación de las especies de vida silvestre según su ámbito de influencia 
            y/o de interés (conservación; objetivo estratégico 2 de la EMCV).</li>
            <li><strong> Ecológico.</strong> Investigación sobre especies de vida silvestre listadas en la NOM-059- SEMARNAT-2010,
              Prioritarias, Endémicas y Nativas sujetas a conservación.</li>
            <li><strong> Social.</strong> Educación, difusión y divulgación científica a todos los niveles educativos,
              así como a los visitantes y capacitación del personal del Rancho Xamantún, así como a personal 
              de las autoridades locales, municipales, estatales o federales, educación y cultura ambiental. </li>
            <li><strong> Económico.</strong> Mejorar los ingresos del rancho Xamantún a través de los servicios ofrecidos 
              y búsqueda de fondos para actividades específicas.</li>
          </ol>
          <div class="page-number">Página 6 de 29</div>
        </div>

        <!-- Página 7: Descripción Física - Localización -->
        <div class="page" id="page7">
          <h4><strong>DESCRIPCIÓN FÍSICA DEL RANCHO XAMANTÚN Y SU INFRAESTRUCTURA</strong></h4>
          <h4><strong>LOCALIZACIÓN</strong></h4>
          <p>El rancho de Xamantún se ubica en el municipio de Campeche, sobre la carretera que conduce a la comunidad de Tixmucuy en la intersección hacia la comunidad de Uayamón. Pertenece al Instituto Tecnológico de Chiná, el cual a su vez funciona como Campus del Tecnológico Nacional de México (figura 1).</p>
          <div class="header-image">
            <center><img src="../img2/maps.png" width="380px" height="200px"></center>
            <div class="placeholder-img">Imagen 1: Ubicación del rancho Xamantún dentro del municipio de Campeche, en el estado de Campeche (imagen tomada del Google Earth Pro, 2024).</div>
          </div>         
          
          <p>Posee una extensión de 150 hectáreas, de las cuales 50 se encuentran bajo manejo productivo en el cual destacan áreas de agricultura, ganadería y plantaciones forestales. Las 100 restantes están compuestas de una vegetación de selva mediana.</p>
          
          <p>Es considerado un rancho de enseñanza y el cual sirve para las carreras que oferta el Instituto Tecnológico de Chiná principalmente a ingeniería en agronomía, ingeniería forestal y licenciatura en biología, de igual forma a la maestría en agroecosistemas sostenibles. El Instituto ofrece otras cuatro carreras más que hacen uso de forma ocasional del rancho: ingenierías en informática, en industrias alimentarios, en administración y en gestión empresarial.</p>
          
          <p>En la figura 2 se aprecian las áreas de producción, señaladas en colores, en las cuales se realizan actividades de:</p>
          <ul>
            <li>Agricultura: cultivo de temporal, papaya, etc.</li>
            <li>Plantaciones: de palmas, frutales y de caoba</li>
            <li>Ganadería: cultivo de ganado vacuno</li>
          </ul>
          <p>Las áreas sin colorear se refieren a los espacios con presencia de selva, en la cual se encuentra un apiario bajo desarrollo académico y un vivero que se trabaja en convenio con la SDA.
          <center><img src="../img2/color.png" width="380px" height=300px"></center>
          <div class="page-number">Página 7 de 29</div>
        </div>

        <!-- Página 8: Sistema Vegetacional Dominante -->
        <div class="page" id="page8">
          <h4><strong>SISTEMA VEGETACIONAL DOMINANTE</strong></h4>
          <p>El rancho Xamantún pertenece a un macizo de selva caducifolia, en el cual se pueden encontrar ejemplares de <em>Metopium brownei</em>, <em>Bursera simaruba</em>, <em>Jatropha gaumeri</em>, <em>Lonchocarpus xuul</em>, <em>Lysiloma latisiliquum</em>, <em>Piscidia piscipula</em>, <em>Ceiba pentadra</em>, <em>Manilkara zapota</em>, <em>Sabal mexicana</em> (figura 3).</p>
          <center><img src="../img2/color2.png" width="380px" height="200px"></center>
          <p>Figura 3.- Imagen tomada del SIG GAIA del INEGI (2024), en el cual se observa la vegetación y uso del suelo presente en el rancho Xamantún.</p>          
          <div class="page-number">Página 8 de 29</div>
        </div>

        <!-- Página 9: Fauna Presente en el Sitio -->
        <div class="page" id="page9">
          <h4><strong>FAUNA PRESENTE EN EL SITIO</strong></h4>
          <p>Como se mencionó, el sistema vegetacional corresponde a selva mediana, en estos sistemas las principales especies de mamíferos que se han reportado son: jaguar (<em>Panthera onca</em>), el tigrillo (<em>Leopardus wiedii</em>), el venado temazate (<em>Mazama pandora</em>), el tepezcuintle (<em>Cuniculus paca</em>), el oso hormiguero (<em>Tamandua mexicana</em>), el viejo de monte (<em>Eira barbara</em>), el pecarí de collar (<em>Pecari tajacu</em>), la martucha (<em>Potos flavus</em>), puercoespín arborícola (<em>Sphiggurus mexicanus</em>), puma (<em>Puma concolor</em>), tlacuache (<em>Didelphis marsupialis</em>), sereque (<em>Dasyprocta punctata</em>), el mapache (<em>Procyon lotor</em>), el chico solo (<em>Nasua narica</em>), venado cola blanca (<em>Odocoileus virginianus</em>), el <em>Urocyon cinereoargenteus</em>, el jaguarundi (<em>Puma yagouaroundi</em>), el armadillo (<em>Dasypus novencinctus</em>).</p>
          
          <p>En cuanto a reptiles destaca <em>Kinosternon scorpioides</em>, <em>Rhinoclemmys areolata</em>, <em>Ctenosaura similis</em> y <em>Crotalus tzabcan</em>. En aves <em>Crypturellus cinnamomeus</em>, <em>Dendrocygna autumnalis</em>, <em>Ortalis vetula</em>, <em>Meleagris ocellata</em>, <em>Zenaida asiatica</em>, <em>Dactylortis thoracicus</em>, <em>Colinus nigrogularis</em>, <em>Amazona albrifrons</em>.</p>
          
          <p>En el sitio se han observado la mayoría de las especies enlistadas, toda la información ha sido por comunicación personal con los trabajadores del rancho. En los monitoreos realizados se observaron ejemplares de tlacuache, chico solo y ratones de campo; igual individuos de iguana negra.</p>
          <div class="page-number">Página 9 de 29</div>
        </div>

        <!-- Página 10: Manejo y Conservación del Hábitat -->
        <div class="page" id="page10">
          <h4><strong>MANEJO Y CONSERVACIÓN DEL HÁBITAT</strong></h4>
          <p>Las acciones aquí señaladas deben ser realizadas de acuerdo con el cronograma de actividades que las delimita a corto, mediano o largo plazo. Los plazos en los que sean realizados deben permitir programar las tareas, priorizarlas y evaluar los resultados obtenidos (tabla X).</p>
          
          <table>
            <tr>
              <th>Acción</th>
              <th>Plazo</th>
              <th>Objetivo</th>
              <th>Resultado</th>
            </tr>
            <tr>
              <td>Control y erradicación de especies invasivas de flora y fauna</td>
              <td>Corto</td>
              <td>Eliminar las especies de flora y fauna que resulten un factor limitante para la fauna y la flora nativa</td>
              <td>Mejorar la tasa de sobrevivencia y natalidad de las especies</td>
            </tr>
            <tr>
              <td>Control y erradicación de especies ferales</td>
              <td>Corto</td>
              <td>Eliminar la depredación ocasionada por especies ferales</td>
              <td>Mejorar la tasa de sobrevivencia y natalidad de las especies</td>
            </tr>
            <tr>
              <td>Control y erradicación de especies exóticas</td>
              <td>Corto</td>
              <td>Reducir o eliminar la competencia generada por la presencia de especies exóticas.</td>
              <td>Mejorar la tasa de las especies y reducir la presión al hábitat</td>
            </tr>
            <tr>
              <td>Regulación de las actividades rurales productivas</td>
              <td>Corto</td>
              <td>Restringir los sitios de pastoreo de ganado doméstico dentro de la UMA. Optimizar el uso de suelo agrícola</td>
              <td>Evitar sobrepastoreo, erosión, pérdida de la calidad del suelo y competencia por recursos</td>
            </tr>
            <tr>
              <td>Control de la erosión</td>
              <td>Mediano</td>
              <td>Eliminar sitios afectados por actividades humanas (sobrepastoreo, desmonte, etc.) que se encuentren erosionados o propensos a la erosión.</td>
              <td>Detener los procesos de erosión en zonas afectadas que limitan la distribución de las especies de interés</td>
            </tr>
            <tr>
              <td>Restauración y revegetación de zonas afectadas</td>
              <td>Mediano</td>
              <td>Promover la sucesión ecológica en sitios afectados que limiten la distribución de las especies de interés.</td>
              <td>Aumentar la superficie de hábitat disponible para las especies</td>
            </tr>
            <tr>
              <td>Diversificación de la UMA</td>
              <td>Largo</td>
              <td>Diversificar aprovechamientos incluyendo manejo de otras especies de flora y fauna que favorezcan un manejo integral de la UMA</td>
              <td>Conservación de todas especies silvestres y beneficios adicionales a los propietarios de la UMA</td>
            </tr>
            <tr>
              <td>Construcción de Infraestructura</td>
              <td>Permanente</td>
              <td>Acondicionar caminos, cercos, bebederos y otras obras, compatibles con la biología de las aves canoras de interés</td>
              <td>Mejorar y facilitar el manejo de la UMA</td>
            </tr>
          </table>
          <div class="page-number">Página 10 de 29</div>
        </div>

        <!-- Continuar con las páginas restantes (11-29) -->
        <!-- Página 11: Monitoreo de Especies Indicadoras -->
        <div class="page" id="page11">
          <h4><strong>MONITOREO DE ESPECIES INDICADORAS</strong></h4>
          <p>El monitoreo de especies indicadoras es un aspecto fundamental para la evaluación de las condiciones de la UMA. La presencia de especies de depredadores indica de manera indirecta la calidad del hábitat que se mantiene en la UMA, debido a que indica que mantiene las condiciones para sostener poblaciones saludables que sean la base de la cadena trófica.</p>
          
          <p>La forma en la que se realizará el monitoreo de especies indicadores será a través de registros de observación de las diferentes especies de depredadores y otras especies indicadoras presentes en la zona durante los trabajos y actividades que se realicen en la UMA (ANEXO VII). La información que se obtenga será cualitativa pero permitirá reunir información precisa de la condición del hábitat y la abundancia relativa de las poblaciones.</p>
          <div class="page-number">Página 11 de 29</div>
        </div>

        <!-- Página 12: Monitoreo de Cambio en la Cobertura Vegetal -->
        <div class="page" id="page12">
          <h4><strong>MONITOREO DE CAMBIO EN LA COBERTURA VEGETAL A TRAVÉS DE ZONIFICACIÓN</strong></h4>
          <p>Para conocer la tendencia en las coberturas vegetales presentes en la UMA, las modificaciones de éstas y para identificar el efecto de las acciones de manejo, se realizará el monitoreo del hábitat a través de los cambios en las coberturas vegetales. Lo cual será presentado mediante la elaboración de planos de zonificación, donde se identificarán y delimitarán las áreas clave en la UMA, los tipos de vegetación y la modificación que éstas vayan presentando con el tiempo. Se indicarán los puntos cartográficos representativos, los sitios de importancia, zonas de manejo, los límites de la UMA y aquellas zonas de relevancia ecológica como cuevas, barrancas, cuerpos de agua temporales y permanentes, etcétera.</p>
          
          <p>Los reportes de los monitoreos de especies de depredadores y la actualización de la zonificación de la UMA se presentarán cada 3 años y se deberán de resaltar los cambios en la vegetación de la UMA, las modificaciones que se hayan realizado en la infraestructura para el manejo, caminos y cambios en el uso de suelo.</p>
          
          <p>Para evaluar las poblaciones de fauna y flora se empleará la metodología denominada BIOCOMUNI, la cual recoge información de dos elementos clave de los ecosistemas terrestres: fauna y vegetación.</p>
          
          <p>De acuerdo a esta metodología la fauna silvestre es un indicador de la salud del ecosistema. Regula su estructura, composición, funcionamiento y servicios ecosistémicos como la polinización, la dispersión de semillas, la depredación, la herbivoría y la redistribución de nutrientes.</p>
          
          <p>La vegetación alberga todos los componentes de la biodiversidad y mantiene las condiciones ambientales necesarias para que el ecosistema produzca bienes y servicios que la sociedad necesita y disfruta, como el agua, el clima, la protección del suelo, entre otros.</p>
          <div class="page-number">Página 12 de 29</div>
        </div>

        <!-- Página 13: Metodología BIOCOMUNI -->
        <div class="page" id="page13">
          <h4><strong>METODOLOGÍA BIOCOMUNI</strong></h4>
          <p>En BIOCOMUNI, los interesados pueden elegir la periodicidad del muestreo de acuerdo a sus necesidades e intereses, siempre y cuando mantengan el rigor metodológico expuesto en esta guía. El monitoreo de fauna se hará por lo menos dos veces al año, en temporada seca y de lluvia; el monitoreo de vegetación, una vez al año, de preferencia en temporada de lluvia.</p>
          
          <p>El tiempo mínimo que la cámra trampa debe permanecer en campo es de 40 días efectivos. El núcleo agrario puede aumentar este periodo, en tanto la brigada acuda a la Unidad de Muestreo cada cierto tiempo a cambiar la tarjeta de memoria y las baterías.</p>
          
          <p>Para realizar los muestreos, BIOCOMUNI recopila datos de cinco componentes:</p>
          <ol>
            <li>Aves</li>
            <li>Mamíferos terrestres</li>
            <li>Arbustos, repoblado (renovación de la vegetación) y vegetación menor</li>
            <li>Arbolado y vegetación mayor</li>
            <li>Impactos ambientales</li>
          </ol>
          
          <p>La información que los núcleos agrarios obtengan sobre estos componentes permitirá conocer la composición y la distribución de las especies animales y vegetales, e identificar sus cambios en el tiempo y el espacio.</p>
          
          <table>
            <tr>
              <th>Componente</th>
              <th>Relevancia</th>
            </tr>
            <tr>
              <td class="text-center">Aves</td>
              <td>Las aves son útiles para evaluar y monitorear la salud ambiental, porque son fáciles de observar, viven en todo tipo de ecosistemas, ocupan distintos niveles tróficos y son sensibles a los cambios en su hábitat. Una disminución en el número de especies o de los individuos de una especie puede ser reflejo del deterioro del hábitat, al alterar la disponibilidad de alimento o los lugares de refugio y nidificación de los cuales dependen. Monitorear las aves permite proponer medidas de mitigación de impacts de las actividades humanas o de protección, recuperación y restauración de su hábitat.</td>
            </tr>
            <tr>
              <td class="text-center">Mamíferos terrestres</td>
              <td>Los mamíferos pequeños, medianos y grandes son muy sensibles a las perturbaciones de su hábitat, por ello son buenos indicadores de la salud del ecosistema. Además, su movilidad les permite trasladarse de lugares no aptos para su desarrollo hacia zonas más favorables, aspecto que los vuelve útiles para evaluar condiciones a través del paisaje. En el caso de los grandes felinos, por ejemplo, su función como depredadores tope y la condición de sus poblaciones los sitúa como un importante indicador del estado general del ecosistema.</td>
            </tr>
            <tr>
              <td class="text-center">Arbustos, repoblado y vegetación menor</td>
              <td>Los arbustos, el repoblado y la vegetación menor detallan la dinámica de sucesión y regeneración del ecosistema. Son un buen indicador de la salud del mismo porque constituyen la base de las redes alimentarias, sirven de refugio de gran parte de la fauna, protegen el suelo de la erosión y favorecen la infiltración de agua de lluvia. La reducción en la composición y cobertura de las especies del sotobosque y la baja regeneración natural suelen relacionarse con procesos de sobrepastoreo, sobreexplotación de productos maderables y no maderables, incendios forestales, entre otros factores de degradación.</td>
            </tr>
            <tr>
              <td class="text-center">Arbolado y vegetación mayor</td>
              <td>El arbolado y la vegetación mayor representan un componente muy importante de los ecosistemas, en términos de la captación y almacenamiento de carbono y nutrientes, refugio para la fauna y mantenimiento de la estabilidad del sistema en general. La diversificación vertical con individuos en todas las fases de desarrollo indica que el ecosistema está sano y sigue su dinámica natural de sucesión.
              En este componente se registran las epífitas, plantas que viven en otros vegetales usándolos como soporte. Su distribución en los árboles provee una variedad de nichos y recursos que son aprovechados por distintos animales (refugio, alimentos, agua, nutrientes). Su ausencia puede desencadenar la desaparición de algunas especies importantes para la salud de los ecosistemas.
              </td>
            </tr>
            <tr>
              <td class="text-center">Impactos ambientales</td>
              <td>Los impactos ambientales (incendios, plagas, huracanes, etc.) son elementos esenciales en el funcionamiento de numerosos ecosistemas forestales. Aunque representan perturbaciones naturales frecuentes, si ocurren con gran intensidad y durante un tiempo prolongado, pueden alterar el equilibrio del ecosistema de manera irreversible.</td>
            </tr>
          </table>
          <div class="page-number">Página 13 de 29</div>
        </div>

        <!-- Página 14: Medidas de Contingencia -->
        <div class="page" id="page14">
          <h4><strong>MEDIDAS DE CONTINGENCIA (Y DE SEGURIDAD)</strong></h4>
          <p>Se realizará un análisis de las condiciones generales de los ejemplares capturados una vez que se inicie el aprovechamiento, lo cual estará definido de acuerdo con las recomendaciones realizadas por la Dirección General de Vida Silvestre.</p>
          
          <p>Paralelamente se tienen programadas actividades para la prevención y control de incendios forestales, que pudieran afectar superficies importantes de vegetación dentro de la UMA, en el programa de manejo y conservación.</p>
          
          <h4><strong>VIGILANCIA PARTICIPATIVA</strong></h4>
          <p>Se llevará a cabo mediante comités de vigilancia participativa por medio de acuerdos y convenios con las autoridades locales y la PROFEPA.</p>
          
          <p>La estrategia contempla la instalación de letreros alusivos a la unidad de manejo, mostrando el nombre, clave de registro, propietario, zonas de exclusión, área de bebederos, área de prohibición de cacería, etcétera (tabla X).</p>
          <p>Tabla Señalización, protección y vigilancia participativa.</p>
          <table>
            <tr>
              <th class="text-center">Acción</th>
              <th class="text-center">Plazo</th>
              <th class="text-center">Objetivo</th>
              <th class="text-center">Resultado</th>
            </tr>
            <tr>
              <td>Construcción de infraestructura para el manejo adecuado de la UMA</td>
              <td class="text-center">Corto</td>
              <td>Desarrollar la infraestructura para realizar el manejo óptimo de las poblaciones de interés.</td>
              <td>Mejorar las condiciones de manejo para evitar estrés, manejo excesivo y enfermedades.</td>
            </tr>
            <tr>
              <td>Mejorar las condiciones de manejo para evitar estrés, manejo excesivo y enfermedades.</td>
              <td class="text-center">Corto</td>
              <td>Instalar señalización donde se indique en diferentes puntos de la UMA las actividades permitidas, información relevante y sitios de importancia</td>
              <td>Mejorar el control de los visitantes y proporcionar la información necesaria</td>
            </tr>
            <tr>
              <td>Prevención de incendios forestales (brechas corta fuego y retiro de material combustible).</td>
              <td class="text-center">Corto</td>
              <td>Prevenir incendios forestales y facilitar su control.</td>
              <td>Disminuir la presencia de incendios forestales y los daños que puedan ocasionar.</td>
            </tr>
            <tr>
              <td>Zonificación de la UMA</td>
              <td class="text-center">Corto y periódicamente</td>
              <td>Restringir y controlar las actividades dentro de la UMA, con el fin de evitar manejos inadecuados, accidentes y otras eventualidades</td>
              <td>Tener un mejor control de las actividades de la UMA y evitar riesgos innecesarios</td>
            </tr>
            <tr>
              <td>Plan de seguridad contra contingencias ambientales</td>
              <td class="text-center">Permanente</td>
              <td>Establecer medidas de seguridad pertinentes en caso de presentarse contingencias ambientales como incendios forestales, inundaciones y otras situaciones poco predecibles</td>
              <td>Minimizar los riesgos y reducir daños en caso de eventos que afecten la UMA</td>
            </tr>
            <tr>
              <td>Vigilancia participativa</td>
              <td class="text-center">Permanente</td>
              <td>Involucrar a los beneficiados por la operación de la UMA, en un programa colectivo de vigilancia para llevar un mejor control y garantizar la seguridad dentro de la UMA</td>
              <td>Reducir el número cazadores furtivos, evitar malos manejos por parte de usuarios e identificación temprana de riesgos.</td>
            </tr>
            <tr>
              <td>Monitoreos periódicos y control de plagas</td>
              <td class="text-center">Permanente</td>
              <td>Realizar monitoreos periódicos de plagas y enfermedades que puedan afectar la calidad del hábitat, e implementar las medidas de control pertinentes</td>
              <td>Identificar a tiempo posibles plagas y evitar daños severos al hábitat</td>
            </tr>
          </table>
          <div class="page-number">Página 14 de 29</div>
        </div>

        <!-- Página 15: Monitoreo Sanitario -->
        <div class="page" id="page15">
          <h4><strong>MONITOREO SANITARIO</strong></h4>
          <p>La presente actividad es de prioridad alta; la detección temprana e inmediata erradicación de cualquier evento sanitario representa la permanencia de la especie de interés y de la propia UMA. El monitoreo sanitario debe de ser periódico y permanente. Para ello se deben efectuar recorridos y observar a detalle las condiciones del hábitat, esta información se anexará al informe anual de actividades.</p>
          
          <p>Algunos de los principales problemas puede ser la presencia plagas y enfermedades tanto en la flora como en la fauna, por ello se deben llevar a cabo labores de forma preventiva para evitar que prolifere alguna, a la vez que es necesario realizar investigación y control para remediar cualquier situación que pueda presentarse.</p>
          
          <p>En particular se debe dar manejo preventivo de sanidad a los especímenes que sea identificado con algún problema. En caso de fenómenos fitosanitarios que superen la capacidad de control se notificará a la Comisión Nacional Forestal (CONAFOR) y a la Dirección de Sanidad Vegetal del Estado correspondiente (SAGARPA), para su diagnóstico y seguimiento de condicionantes para erradicación (en este caso se seguirán las recomendaciones directas de la Autoridad).</p>
          
          <p>En el caso de alguna urgencia zoosanitaria, ya sea la presencia o sospecha de detección de alguna enfermedad deberá reportarse de inmediato a la Dirección General de Vida Silvestre al teléfono 56-24-36-50 o a la siguiente dirección de correo electrónico: antonio.gonzalez@semarnat.gob.mx.</p>
          <div class="page-number">Página 15 de 29</div>
        </div>

        <!-- Página 16: Mecanismos de Vigilancia -->
        <div class="page" id="page16">
          <h4><strong>MECANISMOS DE VIGILANCIA</strong></h4>
          <p>Con el propósito de contribuir al mejor funcionamiento de las actividades de la UMA, se propone la creación de un programa de vigilancia, el cual debe ser operado por los integrantes de la UMA.</p>
          
          <p>El personal deberá de estar capacitado para llevar a cabo las actividades de vigilancia e informar a la Procuraduría Federal de Protección al Ambiente (PROFEPA), Comisión Nacional Forestal (CONAFOR), a la Comisión Nacional de Áreas Naturales Protegidas (CONANP), a la Secretaría de Agricultura, Ganadería, Desarrollo Rural, Pesca y Alimentación (SAGARPA), Protección Civil, entre otras instancias para solicitar el apoyo necesario.</p>
          
          <p>Un programa de vigilancia debe contemplar las siguientes acciones:</p>
          <ul>
            <li>Buscar la colaboración de las instancias de seguridad municipal, estatal o federal para efectuar acciones de vigilancia en la UMA.</li>
            <li>Establecer rutas para efectuar recorridos de vigilancia, que serán periódicos y permanentes.</li>
            <li>Desarrollar y ejecutar un plan de operaciones para control y vigilancia.</li>
            <li>Desarrollar y ejecutar un programa de vigilancia fitosanitaria.</li>
            <li>Contar con una bitácora de datos para el registro de todas las acciones de vigilancia.</li>
            <li>Difundir las regulaciones existentes en la UMA, así como las instituciones que apoyan en la vigilancia.</li>
            <li>Elaboración y colocación de letreros en puntos estratégicos con la información de la unidad de manejo.</li>
            <li>Detección de fauna doméstica y exótica.</li>
            <li>Prevención y combate a la tala ilegal.</li>
            <li>Efectuar pláticas informativas enfocadas a la prevención de ilícitos ambientales dirigidas a las comunidades aledañas a la UMA.</li>
            <li>Contar con un directorio de instituciones donde se puede denunciar el delito ambiental (PROFEPA, SEMARNAT, SAGARPA, CONAFOR y las instancias de seguridad Municipal, Estatal Y Federal).</li>
            <li>Contar con un directorio de otras instituciones como Protección Civil y Bomberos.</li>
          </ul>
          
          <p>La aplicación de este programa ayudará a que la UMA cumpla con el objetivo general de conservar del hábitat natural, poblaciones y ejemplares que alberga en particular y de la fauna y flora silvestres en general.</p>
          <div class="page-number">Página 16 de 29</div>
        </div>

        <!-- Página 17: Tipo de Aprovechamiento -->
        <div class="page" id="page17">
          <h4><strong>MEDIOS, FORMAS DE APROVECHAMIENTO Y SISTEMA DE MARCAJE PARA IDENTIFICAR LOS EJEMPLARES, PARTES Y DERIVADOS QUE SEAN APROVECHADOS DE MANERA SUSTENTABLE</strong></h4>
          <h4><strong>TIPO DE APROVECHAMIENTO</strong></h4>
          <p>Los tipos de aprovechamiento que se realizarán en la UMA es de tipo no extractivo siendo los principales objetivos la investigación, educación ambiental, exhibición, comercialización y restauración.</p>
          
          <p>Las acciones de aprovechamiento no extractivo se refieren a actividades de turismo de sendero para lo cual se realizarán actividades de manejo del hábitat, que serán las de restauración de la zona de selva.</p>
          
          <p>Se pretende el establecimiento de dos senderos que estarán ubicados uno en la zona con actividades agropecuarias y el segundo en la zona de selva.</p>
          
          <p>El primer sendero pretende ofertar un turismo rural, el cual se basará en la exposición de las actividades agrícolas ganaderas junto con las plantaciones forestales, que se desarrollan en el rancho. Tendrá una distancia de 1.36 kilómetros (figura X).</p>
          <center><img src="../img2/maps2.png" width="380px" height="200px"></center>
          <p>Figura X: Se muestra en línea roja la ubicación del sendero de turismo rural, el cual abarca las principales áreas de producción agropecuarias del rancho, con una distancia de 1.36 km.</p>
         
          
          <p>El segundo sendero se pretende ubicar a lo largo del área de selva, aprovechando la presencia de un camino antiguo en la parte central del rancho, lo cual permitirá apreciar el estado de conservación que guarda la selva (figura X).</p>
          
          <p>A lo largo de ese sendero, el cual tendrá una distancia de 2.22 km, pasará por la zona con mayor altura del predio con 73 m de acuerdo a lo establecido por el Google Earth Pro (2024), con una diferencia de 20 m entre el punto más alto y más bajo del sendero (figura X).</p>
          <center><img src="../img2/maps3.png" width="380px" height="300px"></center>
          <p>Figura X: Sendero dentro de la selva con una distancia de 2.22 km, el cual pasará por la parte más alta del rancho.</p>
          <center><img src="../img2/maps4.png" width="380px" height="200px"></center>
          <p>Figura X: Esquema de las alturas del predio, la flecha indica la posición dentro del sendero.</p>
          <div class="page-number">Página 17 de 29</div>
        </div>

        <!-- Página 18: Calendario de Actividades -->
        <div class="page" id="page18">
          <h4><strong>CALENDARIO DE ACTIVIDADES (se refiere a conservación, investigación y educación)</strong></h4>
          <p>A continuación se presenta el calendario de actividades de acuerdo al programa de actividades anuales:</p>
          
          <table>
            <tr>
              <th>Actividad</th>
              <th>E</th>
              <th>F</th>
              <th>M</th>
              <th>A</th>
              <th>M</th>
              <th>J</th>
              <th>J</th>
              <th>A</th>
              <th>S</th>
              <th>O</th>
              <th>N</th>
              <th>D</th>
            </tr>
            <tr>
              <td>Entrega de documentos y registro</td>
              <td></td>
              <td></td>
              <td></td>
              <td>X</td>
              <td></td>
              <td></td>
              <td></td>
              <td></td>
              <td></td>
              <td></td>
              <td></td>
              <td></td>
            </tr>
            <tr>
              <td>Material y equipo</td>
              <td>X</td>
              <td>X</td>
              <td>X</td>
              <td></td>
              <td></td>
              <td></td>
              <td></td>
              <td></td>
              <td></td>
              <td></td>
              <td></td>
              <td></td>
            </tr>
            <tr>
              <td>Señalización</td>
              <td>X</td>
              <td>X</td>
              <td>X</td>
              <td></td>
              <td></td>
              <td></td>
              <td></td>
              <td></td>
              <td></td>
              <td></td>
              <td></td>
              <td></td>
            </tr>
            <tr>
              <td>Construcción y reparación de infraestructura</td>
              <td>X</td>
              <td>X</td>
              <td>X</td>
              <td>X</td>
              <td>X</td>
              <td>X</td>
              <td>X</td>
              <td>X</td>
              <td>X</td>
              <td></td>
              <td></td>
              <td></td>
            </tr>
            <tr>
              <td>Muestreo de la vegetación del predio</td>
              <td>X</td>
              <td></td>
              <td></td>
              <td>X</td>
              <td></td>
              <td></td>
              <td>X</td>
              <td></td>
              <td></td>
              <td>X</td>
              <td></td>
              <td></td>
            </tr>
            <tr>
              <td>Visitas guiadas</td>
              <td></td>
              <td></td>
              <td></td>
              <td></td>
              <td></td>
              <td></td>
              <td></td>
              <td></td>
              <td></td>
              <td></td>
              <td></td>
              <td>X</td>
            </tr>
            <tr>
              <td>Talleres</td>
              <td></td>
              <td></td>
              <td></td>
              <td></td>
              <td></td>
              <td></td>
              <td></td>
              <td>X</td>
              <td></td>
              <td></td>
              <td></td>
              <td>X</td>
            </tr>
            <tr>
              <td>Servicios Sociales, residencias profesionales, y voluntariados</td>
              <td>X</td>
              <td>X</td>
              <td>X</td>
              <td>X</td>
              <td>X</td>
              <td>X</td>
              <td>X</td>
              <td>X</td>
              <td>X</td>
              <td>X</td>
              <td>X</td>
              <td>X</td>
            </tr>
            <tr>
              <td>Tesis</td>
              <td>s/d</td>
              <td>s/d</td>
              <td>s/d</td>
              <td>s/d</td>
              <td>s/d</td>
              <td>s/d</td>
              <td>s/d</td>
              <td>s/d</td>
              <td>s/d</td>
              <td>s/d</td>
              <td>s/d</td>
              <td>s/d</td>
            </tr>
            <tr>
              <td>Propuestas de investigación internas (depende de la demanda)</td>
              <td>s/d</td>
              <td>s/d</td>
              <td>s/d</td>
              <td>s/d</td>
              <td>s/d</td>
              <td>s/d</td>
              <td>s/d</td>
              <td>s/d</td>
              <td>s/d</td>
              <td>s/d</td>
              <td>s/d</td>
              <td>s/d</td>
            </tr>
            <tr>
              <td>Propuestas de investigación externas (depende de la demanda)</td>
              <td>s/d</td>
              <td>s/d</td>
              <td>s/d</td>
              <td>s/d</td>
              <td>s/d</td>
              <td>s/d</td>
              <td>s/d</td>
              <td>s/d</td>
              <td>s/d</td>
              <td>s/d</td>
              <td>s/d</td>
              <td>s/d</td>
            </tr>
            <tr>
              <td>Ambientación</td>
              <td></td>
              <td>X</td>
              <td></td>
              <td>X</td>
              <td></td>
              <td>X</td>
              <td></td>
              <td>X</td>
              <td></td>
              <td>X</td>
              <td></td>
              <td>X</td>
            </tr>
            <tr>
              <td>Vigilancia ante contingencias</td>
              <td class="text.center" colspan="12">Periódico y permanente</td>
            </tr>
            <tr>
              <td>Monitoreo fitosanitario</td>
              <td class="text.center" colspan="12">Periódico y permanente</td>
            </tr>
            <tr>
              <td>Entrega del Informe sobre contingencias</td>
              <td></td>
              <td></td>
              <td></td>
              <td>X</td>
              <td></td>
              <td></td>
              <td></td>
              <td></td>
              <td></td>
              <td></td>
              <td></td>
              <td></td>
            </tr>
            <tr>
              <td>Entrega del Informe anual de actividades</td>
              <td></td>
              <td></td>
              <td></td>
              <td>X</td>
              <td></td>
              <td></td>
              <td></td>
              <td></td>
              <td></td>
              <td></td>
              <td></td>
              <td></td>
            </tr>
          </table>
          <p><strong>*s/d:</strong> Según demanda</p>
          <div class="page-number">Página 18 de 29</div>
        </div>

        <!-- Página 19: Bibliografía Consultada -->
        <div class="page" id="page19">
          <h4><strong>BIBLIOGRAFÍA CONSULTADA</strong></h4>
          <ul>
            <li>Bowles, J. M. 2004. Guide to plant collection and identification. Herbarium workshop in plant collection and identification. University of Western Ontario. En: <a href="http://www.uwo.ca/biology/facilities/herbarium/collectingguide.pdf">http://www.uwo.ca/biology/facilities/herbarium/collectingguide.pdf</a></li>
            <li>Comisión Nacional para el Conocimiento y Uso de la Biodiversidad. México p. 159-169.</li>
            <li>Cámara de Diputados del Honorable Congreso de la Unión, 1992. Ley Federal sobre Metrología y Normalización (1 de julio, 1992; última modificación DOF 09-04-2012).</li>
            <li>Cámara de Diputados del Honorable Congreso de la Unión. 1988. Ley General del Equilibrio Ecológico y la Protección al Ambiente. Última reforma publicada en el DOF del 28 de enero de 2011. SEMARNAT, México. <a href="http://biblioteca.semarnat.gob.mx/janium/Documentos/Ciga/agenda/DOFsr/CD820_ACT.pdf">http://biblioteca.semarnat.gob.mx/janium/Documentos/Ciga/agenda/DOFsr/CD820_ACT.pdf</a></li>
            <li>Cámara de Diputados del Honorable Congreso de la Unión. 2000. Ley General de Vida Silvestre. Última reforma publicada en el DOF del 06 de junio de 2012. SEMARNAT, México. <a href="http://biblioteca.semarnat.gob.mx/janium/Documentos/Ciga/libros2009/ACD000824_2.pdf">http://biblioteca.semarnat.gob.mx/janium/Documentos/Ciga/libros2009/ACD000824_2.pdf</a></li>
            <li>CDB, 2012. <em>Estrategia Mundial para la Conservación de las Especies Vegetales: 2011-2020.</em> Botanic Gardens Conservation International. Richmond, UK.</li>
            <li>CONABIO y SEMARNAT. 2009. Cuarto Informe Nacional de México al Convenio sobre Diversidad Biológica (CDB). Comisión Nacional para el Conocimiento y Uso de la Biodiversidad y Secretaría de Medio Ambiente y Recursos Naturales. México.</li>
            <li>CONABIO. 2012. Estrategia Mexicana para la Conservación Vegetal, 2012-2030. Comisión Nacional para el Conocimiento y Uso de la Biodiversidad. México.</li>
            <li>CONABIO-CONANP-SEMARNAT. 2008. Estrategia Mexicana para la Conservación Vegetal: Objetivos y Metas. México.</li>
            <li>Dirzo, R. y P. H. Raven. 1994. Un inventario biológico para México. Boletín de la Sociedad Botánica de México 55: 29-34.</li>
            <li>Miranda, F. y E. Hernández X. 1963. Los tipos de vegetación de México y su clasificación. Bol. Soc. Mex. 28:29-179.</li>
            <li>SEMARNAT. 2006. Estrategia de Educación Ambiental para la Sustentabilidad en México. SEMARNAT- CECADESU. México.</li>
            <li>UICN y UNESCO, 1977Conferencia Intergubernamental sobre Educación Ambiental Reporte Final. Unesco, París Francia.</li>
          </ul>
          <div class="page-number">Página 19 de 29</div>
        </div>

        <!-- Página 20: Anexos - Descripción Física y Biológica -->
        <div class="page" id="page20">
          <h4><strong>ANEXOS</strong></h4>
          <h4><strong>I.- Descripción física y biológica del área y su infraestructura" (Art. 40 B, Ley General de Vida Silvestre):</strong></h4>
          
          <p><strong>Suelo Agua</strong></p>
          <p>El tipo de suelo, de acuerdo a lo indicado por el INEGI, es de tipo Leptosol.</p>
          <center><img src="../img2/maps5.png" width="200px" height="200px"></center>

          <p><strong>Vegetación:</strong></p>
          <p>De acuerdo al inventario del 2016, la vegetación dominante es selva caducifolia.</p>
          <center><img src="../img2/maps6.png" width="200px" height="200px"></center>

          <p><strong>Topografía</strong></p>
          <p>De acuerdo al INEGI, la topografía del sitio pertenece al sistema de topoformas de Llanura de Campeche, con elevaciones no mayores de 100 m de altura.</p>
          <center><img src="../img2/maps8.png" width="200px" height="200px"></center>
          
          <p><strong>Clima</strong></p>
          <p>De acuerdo al INEGI, el tipo de clima dominante es de Cálido Subhúmedo.</p>
          <center><img src="../img2/maps7.png" width="200px" height="200px"></center>

          <div class="page-number">Página 20 de 29</div>
        </div>

        <!-- Página 21: Anexos - Características de la UMA -->
        <div class="page" id="page21">
          <h4><strong>III.- Características de la UMA:</strong></h4>
          <p>A. Localización</p>
          <p>Se ubica sobre la carretera 180 en la vialidad conocida como China-Tixmucuy. Siendo una vía de administración estatal perteneciente al estado de Campeche.</p>
          
          <p>B. Superficie total de la Unidad (ha): 150 ha</p>
          <p>Superficie destinada para el manejo intensivo (son las áreas que corresponden a los viveros, propagación, colecciones de respaldo, área de plantas madre, etc.) en metros cuadrados o hectáreas: No aplica</p>
          
          <p>Régimen de propiedad:</p>
          <p>Federal □ Estatal □ Municipal □ Ejidal □ Privado ☑ Comunal □ Otro: ___________</p>
          
          <p>Tipo de tenencia:</p>
          <p>Particular □ Ejidal □ Comunal □ Concesión □ Comodato □ Arrendamiento □ Otro: ___________</p>
          
          <p>C. Clima</p>
          <p>D. Tipo de colecciones (local, regional, de conservación, educación, exhibición, temática, etc.): No Aplica</p>
          <div class="page-number">Página 21 de 29</div>
        </div>

        <!-- Página 22: Anexos - Ubicación -->
        <div class="page" id="page22">
          <h4><strong>IV.- Ubicación</strong></h4>
          <p>Domicilio: Ubicado en la intersección entre la carretera China-Tixmucuy y la Xkeulil-Uayamón. En la coordenada 90° 25' 8.76" W, 19° 43' 21.07" N.</p>
          <p>Estado: Campeche Municipio: Campeche</p>
          
          <p>Anexar mapa de localización y coordenadas UTM.</p>
          <center><img src="../img2/maps9.png" width="250px" height="250px"></center>
          <p>Figura X.- Ubicación de los vértices del predio rancho Xamantún.</p>
          
          <p>Tabla con las coordenadas del predio tanto en geográficas como en UTM.</p>
          <table>
            <tr>
              <th class="text-center" rowspan="3">VÉRTICE</th>
              <th class="text-center" colspan="4">Coordenadas del predio Rancho Xamantún</th>
            </tr>
            <tr>
              <th class="text-center" colspan="2">Geográficas</th>
              <th class="text-center" colspan="2">UTM (15Q)</th>
            </tr>
            <tr>
              <th>Lat N</th>
              <th>Long W</th>
              <th>E</th>
              <th>N</th>
            </tr>
            <tr>
              <td class="text-center">A</td>
              <td>19°43'20.81"</td>
              <td>90°25'17.46"</td>
              <td>770263.361</td>
              <td>2182820.999</td>
            </tr>
            <tr>
              <td class="text-center">B</td>
              <td>19°43'20.87"</td>
              <td>90°25'12.48"</td>
              <td>770408.403</td>
              <td>2182825.050</td>
            </tr>
            <tr>
              <td class="text-center">C</td>
              <td>19°43'22.89"</td>
              <td>90°24'29.49"</td>
              <td>771659.78</td>
              <td>2182906.277</td>
            </tr>
            <tr>
              <td class="text-center">D</td>
              <td>19°42'56.29"</td>
              <td>90°24'37.16"</td>
              <td>771448.836</td>
              <td>2182084.587</td>
            </tr>
            <tr>
              <td class="text-center">E</td>
              <td>19°42'24.77"</td>
              <td>90°24'46.79"</td>
              <td>771183.072</td>
              <td>2181110.681</td>
            </tr>
            <tr>
              <td class="text-center">F</td>
              <td>19°42'28.18"</td>
              <td>90°24'56.00"</td>
              <td>770576.691</td>
              <td>2181894.685</td>
            </tr>
            <tr>
              <td class="text-center">G</td>
              <td>19°42'39.75"</td>
              <td>90°24'59.46"</td>
              <td>770641.536</td>
              <td>2181788.594</td>
            </tr>
            <tr>
              <td class="text-center">H</td>
              <td>19°42'44.38"</td>
              <td>90°25'1.66"</td>
              <td>770740.679</td>
              <td>2181707.333</td>
            </tr>
            <tr>
              <td class="text-center">I</td>
              <td>19°42'47.07"</td>
              <td>90°25'5.02"</td>
              <td>770806.938</td>
              <td>2181565.879</td>
            </tr>
            <tr>
              <td class="text-center">J</td>
              <td>19°42'50.55"</td>
              <td>90°25'7.19"</td>
              <td>770913.156</td>
              <td>2181211.494</td>
            </tr>
            <tr>
              <td class="text-center">K</td>
              <td>19°42'52.07"</td>
              <td>90°25'7.54"</td>
              <td>770565.783</td>
              <td>2181941.288</td>
            </tr>
            <tr>
              <td class="text-center">L</td>
              <td>19°43'1.05"</td>
              <td>90°25'13.10"</td>
              <td>770399.611</td>
              <td>2182215.070</td>
            </tr>
            <tr>
              <td class="text-center">M</td>
              <td>19°43'4.75"</td>
              <td>90°25'12.42"</td>
              <td>770417.69</td>
              <td>2182329.191</td>
            </tr>
            <tr>
              <td class="text-center">N</td>
              <td>19°43'7.38"</td>
              <td>90°25'13.44"</td>
              <td>770386.746</td>
              <td>2182409.644</td>
            </tr>
            <tr>
              <td class="text-center">O</td>
              <td>19°43'13.37"</td>
              <td>90°25'15.01"</td>
              <td>770338.209</td>
              <td>2182593.214</td>
            </tr>
            <tr>
              <td class="text-center">P</td>
              <td>19°43'14.95"</td>
              <td>90°25'16.91"</td>
              <td>770282.122</td>
              <td>2182640.977</td>
            </tr>
            <tr>
              <td class="text-center">Q</td>
              <td>19°43'16.03"</td>
              <td> 90°25'17.49"</td>
              <td>770264.721</td>
              <td>2182673.943</td>
            </tr>
            <!-- Continuar con las demás coordenadas -->
          </table>
          <div class="page-number">Página 22 de 29</div>
        </div>

        <!-- Página 23: Anexos - Vías de Acceso -->
        <div class="page" id="page23">
          <p>Vías de acceso a la Unidad:</p>
          <p>Se accede por la carretera 180 Chiná-Tixmucuy, con intersección con la carretera Xkeulil-Uayamón.</p>
          <center><img src="../img2/maps10.png" width="380px" border="1" height="250px"></center>

          <p>Figura X.- Ubicación del predio en la intersección de la carretera 180 Chiná-Tixmucuy con la carretera Xkeuelil-Uayamon.</p>
          
          <h4><strong>V.- Disponibilidad de agua</strong></h4>
          <p>El rancho cuenta con pozos propios que suministran el agua potable.</p>
          
          <h4><strong>VI.- Instalaciones</strong></h4>
          <p>Se pretende la adecuación y construcción de la siguiente infraestructura.</p>
          
          <table>
            <tr>
              <th>TIPO DE INSTALACIONES</th>
              <th class="text-center">EXISTE</th>
              <th class="text-center">CONSTRUIR</th>
            </tr>
            <tr>
              <td>Áreas de exhibición</td>
              <td class="text-center">1</td>
              <td class="text-center"></td>
            </tr>
            <tr>
              <td>Bodega</td>
              <td class="text-center">1</td>
              <td class="text-center"></td>
            </tr>
            <tr>
              <td>Casa-habitación</td>
              <td class="text-center"></td>
              <td class="text-center">1</td>
            </tr>
            <tr>
              <td>Sistema de riego</td>
              <td class="text-center">1</td>
              <td class="text-center"></td>
            </tr>
            <tr>
              <td>Vivero</td>
              <td class="text-center">1</td>
              <td class="text-center"></td>
            </tr>
            <tr>
              <td>Recepción</td>
              <td class="text-center"></td>
              <td class="text-center">1</td>
            </tr>
            <tr>
              <td>Oficinas</td>
              <td class="text-center"></td>
              <td class="text-center">1</td>
            </tr>
            <tr>
              <td>Área de Educación Ambiental</td>
              <td class="text-center"></td>
              <td class="text-center">1</td>
            </tr>
            <tr>
              <td>Comedor</td>
              <td class="text-center">1</td>
              <td class="text-center"></td>
            </tr>
            <tr>
              <td>Baños</td>
              <td class="text-center">1</td>
              <td class="text-center"></td>
            </tr>
          </table>
          <div class="page-number">Página 23 de 29</div>
        </div>

        <!-- Página 24: Anexos - Recursos Humanos y Especies -->
        <div class="page" id="page24">
          <h4><strong>VII.- Recursos humanos</strong></h4>
          <p>Actualmente el rancho cuenta con cuatro trabajadores y los cuales están dedicados al mantenimiento de las áreas de producción.</p>
          <p>Al momento de iniciarse las actividades de turismo, se pretende que se incorporen estudiantes que serán los que apoyen en las actividades de cuidado y mantenimiento, de muestreo y de vigilancia de la zona de selva.</p>
          
          <h4><strong>VIII.- Especies silvestres sujetas a manejo y aprovechamiento</strong></h4>
          <p>No se pretende tener especies sujetas a manejo o aprovechamiento extractivo, sino el manejo del hábitat para el mejoramiento de las poblaciones existentes.</p>
          <p>Por lo cual, no se puede establecer un listado de especies a manejo.</p>
          <div class="page-number">Página 24 de 29</div>
        </div>

        <!-- Página 25: Anexos - Protocolo de Incorporación -->
        <div class="page" id="page25">
          <h4><strong>ANEXO 4.- Protocolo de incorporación de ejemplares a la colección y manejo</strong></h4>
          <p>Se pretende solicitar el permiso ante la SEMARNAT para poder recibir ejemplares decomisados para su resguardo con la finalidad de su protección y, de ser posible, liberación dentro de la superficie de la UMA. Todo ejemplar ingresado que se encuentre listado en la NOM-059, deberá contar con la documentación correspondiente que avale su legal procedencia según el Art. 51 de la Ley General de Vida Silvestre y el Art 53 del Reglamento de la Ley General de Vida Silvestre.</p>
          
          <p><em>... "Artículo 51. La legal procedencia de ejemplares de la vida silvestre que se encuentran fuera de su hábitat natural, así como de sus partes y derivados, se demostrará, de conformidad con lo establecido en el reglamento, con la marca que muestre que han sido objeto de un aprovechamiento sustentable y la tasa de aprovechamiento autorizada, o la nota de remisión o factura correspondiente.</em></p>
          
          <p>Independientemente de lo anterior:</p>
          <p>a) Para ingreso de nuevos ejemplares: se realizará un registro en libro o bitácora. Se deben enlistar el número de ejemplares a ingresar en una colección indicando el motivo. Posteriormente se deberá incluir la información en la base general con el mismo formato de tabla o cuadro, en un programa de fácil acceso y uso generalizado como Excel, el cual permita exportar la información a programas de bases de datos. Antes del ingreso de los nuevos ejemplares deberán someterse a los procesos de control sanitario y bioseguridad descritos en el presente documento.</p>
          
          <p>Se realizará informe de actividades, que a la vez se remitirá a la Autoridad de acuerdo a la normativa vigente.</p>
          
          <p>Donación de ejemplares: Solo se podrán recibir en donación ejemplares de especies cuando éstos vengan avalados por una nota o factura según lo estipulado en el Art. 51 antes citado. Quienes brinden los ejemplares en donación deben tener respaldada la legal procedencia de los individuos de la Autoridad que corresponda.</p>
          
          <p>Monitoreo y seguimiento de inventario: Se deben realizar inventarios periódicos y vigilancia de los ejemplares de la UMA para medir el crecimiento de las poblaciones naturales que la habiten.</p>
          <div class="page-number">Página 25 de 29</div>
        </div>

        <!-- Página 26: Anexos - Programa de Educación Ambiental (Parte 1) -->
        <div class="page" id="page26">
          <h4><strong>ANEXO 10. Programa de educación ambiental</strong></h4>
          <p>Se define a la Educación ambiental como "El proceso de reconocer valores y aclarar conceptos para crear habilidades y actitudes necesarias, tendientes a comprender y apreciar la relación mutua entre el hombre, su cultura y el medio biofísico circundante. También incluye la toma de decisiones y formular un código de comportamiento respecto a cuestiones que conciernen a la calidad ambiental" (UICN y UNESCO, 1977). O de acuerdo a la interpretación de María Novo, (1986) que menciona que es "Un proceso que consiste en acercar a las personas a una concepción global de medioambiente, para adquirir conocimientos, elucidar valores y desarrollar actitudes y aptitudes que le permitan adoptar una posición crítica y participativa respecto de las cuestiones relacionadas con la conservación y correcta utilización de los recursos y la calidad de vida".</p>
          
          <p>La educación ambiental se puede realizar de manera <em>formal</em>, a través de las instituciones y planes de estudio que configuran la acción educativa como educación preescolar, primaria, secundaria hasta la universitaria; <em>no formal,</em> siendo intencional como la educación formal.</p>
          
          <p>De acuerdo a Molina (2013), el valor de la Educación Ambiental incrementa la percepción, comprensión y preocupación por el Ambiente y su problemática; sirve para adquirir los conocimientos básicos del ambiente; desarrolla y fomenta una comprensión de los conceptos ambientales específicos y fundamentales; favorece actitudes de participación para proteger y mejorar las relaciones entre el hombre y el medio que lo rodea; el visitante viene a disfrutar y comprender sobre la naturaleza, por consiguiente su aprendizaje será significativo y mejor asimilado; Pretende favorecer un cambio de actitud en las relaciones de los visitantes con el ambiente; El poder de asombro al vivir una experiencia directa beneficia el aprendizaje.</p>
          <div class="page-number">Página 26 de 29</div>
        </div>

        <!-- Página 27: Anexos - Programa de Educación Ambiental (Parte 2) -->
        <div class="page" id="page27">
          <p>Asimismo, de acuerdo con Martínez, <em>et al.</em> (2012) la educación ambiental que se ofrece debe aspirar, entre otros aspectos, a: a) generar procesos de reflexión que conduzcan a aprendizajes significativos de personas, grupos y comunidades; b) evidenciar la íntima relación que existe entre la diversidad biológica y la cultural, c) enriquecer el currículo escolar, reforzando o complementando los contenidos de la educación formal; d) construir conocimientos, inducir actitudes y hábitos, impulsar prácticas para la conservación de plantas vivas y afrontar problemas ambientales; e) generar procesos de comunicación horizontal para la atención a los problemas de la vida cotidiana relacionados con la conservación y la sustentabilidad; f) impulsar enfoques interdisciplinarios y sistémicos que aporten a la sustentabilidad; g) apoyar la formación, consolidación y articulación de actores sociales interesados en la conservación de plantas y en dar respuestas a problemas ambientales y generar políticas públicas ligadas a la sustentabilidad.</p>
          
          <p>Entre los principales aspectos que el "Modelo" señala que deben ser considerados en un programa educativo:</p>
          <ol>
            <li>El Programa educativo debe sustentarse en un diagnóstico ambiental de su área de influencia (incluyendo aspectos ecológicos y sociales en el ámbito local, regional, nacional e internacional y del contexto de las comunidades que pueden asistir a la UMA).</li>
            <li>El programa educativo debe señalar: a) la visión, misión, principios y valores del área educativa, así como, b) el público meta que atiende primordialmente.</li>
            <li>La UMA debe contar con un organigrama del área educativa; a) un manual de funciones; b) perfiles del área educativa; c) metas a corto, mediano y largo plazo; d) líneas estratégicas de trabajo y e) ejes de contenido.</li>
            <li>El programa educativo debe contar con: a) un marco teórico (conceptos fundamentales como educación, pedagogía, ambiente, educación ambiental, entre otros),</li>
          </ol>
          <div class="page-number">Página 27 de 29</div>
        </div>

        <!-- Página 28: Anexos - Programa de Educación Ambiental (Parte 3) -->
        <div class="page" id="page28">
          <p>b) una descripción de programa y proyectos prioritarios; enfoque y principios pedagógicos en que se sustenta; c) criterios para la sistematización (documentos donde se registran y sistematizan las experiencias) y d) estrategias de evaluación de su trabajo educativo.</p>
          
          <p>5. El programa educativo debe dar muestras del trabajo colegiado, y de la participación de los educadores ambientales en el diseño del mismo.</p>
          
          <p>6. El programa educativo debe señalar las orientaciones para planear y diseñar actividades, así como para definir los espacios y recursos adecuados para cada proyecto específico.</p>
          
          <p>7. El programa educativo responde a las prioridades ambientales definidas en el diagnóstico educativo ambiental, y aquel dirigido a grupos escolares debe dar muestra de su vinculación con los contenidos del currículo de los diferentes niveles educativos del Sistema Educativo Nacional.</p>
          
          <p>8. El programa educativo debe ofertar diferentes tipos de programas (talleres, jornadas, capacitación, visitas guiadas, campamentos) que dan respuesta a las necesidades del contexto.</p>
          
          <p>9. Los programas proponen diversas estrategias didácticas acordes con los diferentes grupos de población que se atienden y a los contenidos abordados en los programas específicos; y señalan: a) los medios y materiales didácticos necesarios para su realización; b) los instrumentos y procesos de evaluación diagnóstica, continua y final.</p>
          
          <p>10. El programa educativo señala los conocimientos básicos que debe poseer el educador ambiental que los imparte (conceptos básicos de medio ambiente, problemática ambiental, desarrollo sustentable y educación ambiental); así como formación básica sobre: a) características de desarrollo y aprendizaje de sus públicos meta; b) manejo y técnicas de grupo; c) necesidades de aprendizaje de los diferentes grupos de edad a los que atienden; d) conocimiento y aplicación de diferentes técnicas didácticas; e) competencias para el diseño de programas; f) competencias para el uso de estrategias didácticas que faciliten la construcción del conocimiento, g) competencias para la evaluación de programas.</p>
          
          <p>11. La UMA debe contar con materiales didácticos adecuados, para promover en los destinatarios la reflexión sobre la realidad cotidiana y facilitar cambios en los hábitos y actitudes.</p>
          <div class="page-number">Página 28 de 29</div>
        </div>

        <!-- Página 29: Fin del Documento -->
        <div class="page" id="page29">
          <h4><strong>FIN DEL DOCUMENTO</strong></h4>
          <p>Este es el plan de manejo completo para la UMA Rancho Xamantún, que incluye todos los aspectos requeridos por la legislación ambiental mexicana para el manejo y conservación de la vida silvestre.</p>
          <p>Documento elaborado de conformidad con la Ley General de Vida Silvestre y su Reglamento.</p>
          <div class="page-number">Página 29 de 29</div>
        </div>

      </div>
      
      <div class="navigation-buttons">
        <button class="btn btn-primary" onclick="prevPage()">
          <i class="bi bi-arrow-left"></i> Anterior
        </button>
        <center><img src="../img2/word.png" height="70px" width="70px"></center>
        <button class="btn btn-primary" onclick="nextPage()">
          Siguiente <i class="bi bi-arrow-right"></i>
        </button>
      </div>
      
      <div class="mt-3 text-center">
        <a href="../documentos/plan de manejo Xamantun.docx" class="btn btn-primary" download>
          <i class="bi bi-download"></i> Descargar Documento Completo
        </a>
      </div>
    </div>
  </div>

  <script>
    let currentPage = 1;
    const totalPages = 29;
    
    function showPage(pageNum) {
      // Ocultar todas las páginas
      document.querySelectorAll('.page').forEach(page => {
        page.classList.remove('active');
      });
      
      // Mostrar la página solicitada
      document.getElementById('page' + pageNum).classList.add('active');
      currentPage = pageNum;
      
      // Desplazar al inicio de la página
      document.querySelector('.document-viewer').scrollTop = 0;
    }
    
    function nextPage() {
      if (currentPage < totalPages) {
        showPage(currentPage + 1);
      }
    }
    
    function prevPage() {
      if (currentPage > 1) {
        showPage(currentPage - 1);
      }
    }
    
    // Inicializar mostrando la primera página
    showPage(1);
  </script>
  <br>
</body>
</html>