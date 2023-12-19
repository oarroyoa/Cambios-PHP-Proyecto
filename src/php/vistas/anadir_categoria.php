<!-- Etiqueta del título de la página -->
<title>Crear Categoría y Tablero</title>
<!-- Etiqueta de cierre de la sección de encabezado -->
</head>

<!-- Comienzo del cuerpo del documento -->
<body>
    <!-- Encabezado de la página -->
    <header>
        Crear Categoría y Tablero

        <!-- Incluir la barra de navegación desde un archivo externo -->
        <?php
            include 'template/navegacion.html';
        ?>
    </header>

    <!-- Sección principal del contenido -->
    <main class="aumentarMargin100">
        
        <!-- Formulario para insertar categoría y tablero -->
        <form action="index.php?accion=insertarCategoria&controlador=categoria" method="post" enctype="multipart/form-data">
            <div id="contenido">
                <!-- Campo para ingresar el nombre de la categoría -->
                <label for='categoria'>Nombre de la categoría:</label>
                <input type='text' id='categoria' name='categoria' required><br>


                <!-- Campo para ingresar el nombre del tablero -->
                <label for='tablero'>Nombre del tablero:</label>
                <input type='text' id='tablero' name='tablero' required><br>


                <!-- Campo para seleccionar una imagen de fondo -->
                <label for='img'>Fondo:</label>
                <input type='file' id='img' name='img' required><br>


                <!-- Campo oculto para definir el tipo de acción -->
                <input type="hidden" name="tipo" value="add">

                <!-- Sección de botones para enviar el formulario o volver -->
                <div id="botones">
                    <input type='submit' value='Añadir'>
                    <a href="index.php" class="submit">Volver</a>
                </div>
            </div>
        </form>
    </main>
</body>
