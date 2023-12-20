<?php
require_once '../php/modelos/categoria.php';
/**
 * Clase que representa la gestión de categorías.
 */
class Categoria
{
    /**
     * @var string|null $vista Nombre de la vista actual.
     */
    public $vista;
    private $modelo;
    
    /**
     * Constructor de la clase.
     */
    public function __construct()
    {
        $this->vista = null;
        $this->modelo = new CategoriaModelo();
    }
    /**
     * Establece la vista para la página de inicio.
     * JUSTIFICACIÓN: Al no tener ninguna función en el controlador que se encargue de la página de inicio, se hace así para que el index.php cargue su vista.
     */
    public function inicio()
    {
        $this->vista = 'admin';
    }
    /**
     * Establece la vista para seleccionar una categoría.
     * 
     * JUSTIFICACION: Esta función se encarga únicamente de llamar a la vista adecuada para seleccionar una categoría dentro de la gestión de objetos y preguntas.
     * Llamamos al controlador por defecto para reutilizar la vista.
     * Se hace así ya que el index.php recoge el nombre de la vista a través de una variable del controlador.
     */
    public function selectCategoria()
    {
        $this->vista = 'selectCategoria';
    }
        /**
     * Método que devuelve la tabla de categorías.
     *
     * @return array Un array bidimensional con las filas de la tabla categoría.
     */
    public function tablaCategoria()
    {
        $this->vista = 'categoria';
        return $this->modelo->tablaCategoria();
    }

    /**
     * Obtiene el nombre de una categoría por su ID.
     *
     * @param int $id ID de la categoría.
     * @return string Nombre de la categoría.
     */
    public function nombreCategoria($id)
    {
        $fila = $this->modelo->verCategoria($id);
        return $fila['nombre'];
    }

    /**
     * Obtiene el nombre de un tablero por el ID de la categoría.
     *
     * @param int $idCategoria ID de la categoría.
     * @return string Nombre del tablero.
     */
    public function nombreTablero($idCategoria)
    {
        $fila = $this->modelo->verTablero($idCategoria);
        return $fila['nombre'];
    }

    /**
     * Obtiene el fondo de un tablero por el ID de la categoría.
     *
     * @param int $idCategoria ID de la categoría.
     * @return string Ruta del fondo del tablero.
     */
    public function fondoTablero($idCategoria)
    {
        $fila = $this->modelo->verTablero($idCategoria);
        return $fila['imagenFondo'];
    }

    /**
     * Obtiene la información de un tablero por el ID de la categoría.
     *
     * @param int $idCategoria ID de la categoría.
     * @return array Información del tablero.
     */
    public function verTablero($idCategoria)
    {
        return $this->modelo->verTablero($idCategoria);
    }

    /**
     * Obtiene información de un tablero aleatorio.
     *
     * @return array Información del tablero aleatorio.
     */
    public function randomTablero()
    {
        return $this->modelo->randomTablero();
    }

    /**
     * Inserta una nueva categoría en la base de datos.
     * JUSTIFICACIÓN: No usamos return y llamamos por header ya que tenemos que llamar a una vista cuyas funciones están definidas en el controlador principal.
     * Debido a eso, tenemos que cambiar de controlador.
     */
    public function insertarCategoria()
    {
        /* Cambio respecto a la versión anterior. Se llama a la vista junto con la función de dicha vista */

        $this->vista = 'anadir_categoria';

        if ($_SERVER["REQUEST_METHOD"] === "POST") {
    
        /**
         * Sanitiza las entradas.
         *
         * @var string $categoria Nombre de la categoría.
         * @var string $tablero   Nombre del tablero.
         */
        $categoria = $this->sanitizarEntrada($_POST["categoria"]);
        $tablero = $this->sanitizarEntrada($_POST["tablero"]);
    
        if (empty($categoria) || empty($tablero)) {
            // Mensaje de error si la entrada se vuelve vacía después de la sanitización
            $_GET['msg'] = "Error: La entrada no puede estar vacía o contener solo caracteres especiales.";
            /* CAMBIO: Llamada a la vista */
            $this->inicio();
            /* CAMBIO: Quitado el header, reemplazado por return */
            return $_GET['msg'];
        }
    
        /**
         * Ruta temporal del archivo de la imagen.
         * @var string $fondo
         */
        $fondo = $_FILES['img']['tmp_name'];
        $tipo = $_FILES['img']['type'];
    
        // Validar tipo de extensión de imagen
        $extensionesValidas = array('image/png', 'image/jpg', 'image/jpeg');
        if (!in_array($tipo, $extensionesValidas)) {
            // Mensaje de error si la extensión no es válida
            $_GET['msg'] = "Error: Solo se permiten imágenes en formato PNG, JPG o JPEG.";
            /* CAMBIO: Llamada a la vista */
            $this->inicio();
            /* CAMBIO: Quitado el header, reemplazado por return */
            return $_GET['msg'];
        }
    
        $contenido = file_get_contents($fondo);
        $base64 = base64_encode($contenido);
    
        $this->modelo->insertarCategoria($categoria, $tablero, $base64);
    
        // Mensaje de éxito
        $_GET['msg'] = "Categoría añadida correctamente";
        /* CAMBIO: Llamada a la vista */
        $this->inicio();
        /* CAMBIO: Quitado el header, reemplazado por return */
        return $_GET['msg'];
        }
    }
    
    /**
     * Actualiza la información de un tablero en la base de datos.
     */
    public function actualizarTablero()
    {
        $this->vista = 'modificar_tablero';

        if ($_SERVER["REQUEST_METHOD"] == "POST") {
            // Sanitiza las entradas
            $id = $this->sanitizarEntrada($_POST['idTablero']);
            $nombre = $this->sanitizarEntrada($_POST['tablero']);
            $idCategoria = $this->sanitizarEntrada($_POST['idCategoria']);

            if (empty($id) || empty($nombre) || empty($idCategoria)) {
                // Mensaje de error si la entrada se vuelve vacía después de la sanitización
                $_GET['msg'] = "Error: La entrada no puede estar vacía o contener solo caracteres especiales";
                /* CAMBIO: Llamada a la vista */
                $this->tablaCategoria();
                /* CAMBIO: Quitado el header, reemplazado por return */
                return $_GET['msg'];
            }

            // Obtiene la imagen del formulario
            $base64 = '';
            if (!empty($_FILES['img']['tmp_name'])) {
                $img = $_FILES['img'];
                if (in_array($img['type'], array('image/png', 'image/jpg', 'image/jpeg'))) {
                    $imagenTmp = $img['tmp_name'];
                    $contenido = file_get_contents($imagenTmp);
                    $base64 = base64_encode($contenido);
                }
            } else {
                // Si no se seleccionó un nuevo archivo, utiliza la imagen actual
                if (isset($_POST['imgActual']) && strpos($_POST['imgActual'], 'base64:') === 0) {
                    $base64 = substr($_POST['imgActual'], 7);
                }
            }

            if (!empty($base64)) {
                // Actualiza el tablero con la nueva información
                $this->modelo->actualizarTablero($id, $nombre, $base64);
            }

            // Mensaje de éxito
            $_GET['msg'] = "Tablero actualizado correctamente";
            $_GET['id'] = $idCategoria;
            /* CAMBIO: Llamada a la vista */
            $this->tablaCategoria();
            /* CAMBIO: Quitado el header, reemplazado por return */
            return $_GET['msg'];
        }
    }

    /**
     * Elimina una categoría de la base de datos.
     */
    public function borrarCategoria()
    {
        $this->modelo->borrarCategoria($_POST["id"]);
        $_GET['msg'] = "Categoría borrada correctamente";
        /* CAMBIO: Llamada a la vista */
        $this->inicio();
        /* CAMBIO: Quitado el header, reemplazado por return */
        return $_GET['msg'];
    }

    /**
     * Sanitiza una entrada eliminando etiquetas HTML, emojis y otros caracteres especiales.
     *
     * @param string $input Entrada a sanitizar.
     * @return string Entrada sanitizada.
     */
    private function sanitizarEntrada($input)
    {
        // Eliminar etiquetas HTML, emojis y otros caracteres especiales
        $sanitizedInput = preg_replace('/[^\p{L}\p{N}\s\p{P}]/u', '', strip_tags($input));
        return $sanitizedInput;
    }
}
?>
