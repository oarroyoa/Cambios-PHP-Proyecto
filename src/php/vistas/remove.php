<title>Eliminar</title>
</head>
<body>
    <header>
        <!-- Encabezado: Eliminar -->
        Eliminar
        <?php
        // Inclusión de la navegación desde un archivo externo
        include 'template/navegacion.html';
        ?>
    </header>
    <main class="aumentarMargin100">
        <?php
        // Verificación de parámetros recibidos por GET
        if (isset($_GET['funcion']) && isset($_GET['id'])) {
            // Almacenamiento de parámetros en variables
            $funcion = $_GET['funcion'];
            $id = $_GET['id'];

            // Creación del formulario de eliminación basado en la función recibida
            echo "<form action='index.php?accion=borrar{$funcion}&controlador={$funcion}' method='post'>
                <div>
                    <p class='tamFuenteGrande'>¿Desea eliminar el/la $funcion";
                    
            // Si la función es 'Categoria', muestra el nombre de la categoría
            if ($funcion == 'Categoria') {
                require_once 'controladores/categoria.php';
                /* Cambio: Creado objeto para llamar al controlador de categoría */
                $objCategoria = new Categoria();
                echo " " . $objCategoria->nombreCategoria($id);       
            }

            // Comprobación de la existencia del parámetro 'idCategoria'
            $idCategoria = isset($_GET['idCategoria']) ? $_GET['idCategoria'] : '';

            // Cierre del mensaje y creación de campos ocultos con los IDs
            echo "?</p>
            <input type='hidden' name='id' value='$id'>
            <input type='hidden' name='idCategoria' value='$idCategoria'>
            <div id='botones'>
                <input type='submit' value='Eliminar'>
                <a href='index.php' class=submit>Volver</a>
            </div>
        </div>
            </form>";
        }
        ?>
    </main>