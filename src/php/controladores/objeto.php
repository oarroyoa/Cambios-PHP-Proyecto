<?php
require_once '../php/modelos/objeto.php';
/**
 * Clase Objeto que gestiona las operaciones relacionadas con los objetos.
 */
class Objeto
{
    /**
     * @var string|null $vista Nombre de la vista actual.
     */
    public $vista;
    public $modelo;
    /**
     * Constructor de la clase.
     */
    public function __construct()
    {
        $this->vista = null;
        $this->modelo = new ObjetoModelo();
    }


    /**
     * Agrega o actualiza objetos en la base de datos.
     */
    public function anadir_objeto()
    {
        $this->vista = 'anadir_objeto';

        $_GET['msg'] = '';
        if ($_SERVER["REQUEST_METHOD"] == "POST") {
            $idCategoria = isset($_POST['idCategoria']) ? $_POST['idCategoria'] : '';

            // Obtener objetos existentes desde el controlador
            $objetosExistentes = $this->tablaObjeto($idCategoria);

            // Acceder a los valores del formulario y sanitizar cada entrada
            $nombres = isset($_POST['nombre']) ? $_POST['nombre'] : array();
            $descripciones = isset($_POST['descripcion']) ? $_POST['descripcion'] : array();
            $imgs = isset($_FILES['img']) ? $_FILES['img'] : array();
            $puntuaciones = isset($_POST['punt']) ? $_POST['punt'] : array();
            $buenos = isset($_POST['bueno']) ? $_POST['bueno'] : array();

            // Iterar sobre cada objeto y realizar la sanitización
            foreach ($nombres as $index => $nombre) {
                $nombreSanitizado = $this->sanitizarEntrada($nombre);
                $descripcionSanitizada = $this->sanitizarEntrada(isset($descripciones[$index]) ? $descripciones[$index] : '');
                $puntuacionSanitizada = $this->sanitizarEntrada(isset($puntuaciones[$index]) ? $puntuaciones[$index] : '');
                $buenoSanitizado = isset($buenos[$index]) ? $buenos[$index] : 0; // Valor predeterminado a 0 si no está presente

                // Verificar si los campos sanitizados están completos
                if (!empty($nombreSanitizado) && !empty($descripcionSanitizada) && !empty($puntuacionSanitizada)) {
                    if (isset($objetosExistentes[$index])) {
                        // Verificar si se subió una nueva imagen
                        if (!empty($imgs['tmp_name'][$index]) && in_array($imgs['type'][$index], array('image/png', 'image/jpg', 'image/jpeg'))) {
                            $imagenTmp = $imgs['tmp_name'][$index];

                            // Leer el contenido de la nueva imagen
                            $contenido = file_get_contents($imagenTmp);
                            $base64 = base64_encode($contenido);

                            // Actualizar objeto con la nueva imagen
                            $this->modelo->actualizarObjeto(
                                $objetosExistentes[$index]['idObjeto'],
                                $nombreSanitizado,
                                $descripcionSanitizada,
                                $base64,
                                $puntuacionSanitizada,
                                $buenoSanitizado,
                                $idCategoria
                            );
                        } else {
                            // Actualizar objeto sin cambiar la imagen
                            $this->modelo->actualizarObjeto(
                                $objetosExistentes[$index]['idObjeto'],
                                $nombreSanitizado,
                                $descripcionSanitizada,
                                $objetosExistentes[$index]['imagen'],
                                $puntuacionSanitizada,
                                $buenoSanitizado,
                                $idCategoria
                            );
                        }
                    } else {
                        // Objeto no existente, agregar
                        if (!empty($imgs['tmp_name'][$index]) && in_array($imgs['type'][$index], array('image/png', 'image/jpg', 'image/jpeg'))) {
                            $imagenTmp = $imgs['tmp_name'][$index];

                            // Leer el contenido de la imagen
                            $contenido = file_get_contents($imagenTmp);
                            $base64 = base64_encode($contenido);

                            $this->modelo->agregarObjeto($nombreSanitizado, $descripcionSanitizada, $base64, $puntuacionSanitizada, ($buenoSanitizado ? 1 : 0), $idCategoria);
                        }
                    }
                    $_GET['msg'] = 'Objetos agregados o actualizados correctamente';
                } else {
                    // Mostrar mensaje de error si no se pueden agregar campos sanitizados
                    $_GET['msg'] = 'Error al agregar objetos. Verifica que todos los campos estén completos y válidos.';
                    break;
                }
            }            
            // Cerrar la conexión después de procesar todos los objetos
            return $_GET['msg'];

        }
    }


    /**
     * Borra un objeto de la base de datos.
     */
    public function borrarObjeto()
    {
        $this->modelo->borrarObjeto($_POST["id"]);
        $this->anadir_objeto(); 

        $_GET['msg'] = "Objeto borrado correctamente";
        return $_GET['msg'];
    }

    /**
     * Obtiene la información de un objeto por su ID.
     *
     * @param int $idObjeto El ID del objeto.
     * @return array Información del objeto.
     */
    public function ver_objeto($idObjeto)
    {
        return $this->modelo->verObjeto($idObjeto);
    }

    /**
     * Devuelve las filas de los objetos de una categoría.
     *
     * @param int $idCategoria El ID de la categoría.
     * @return array Un array bidimensional con las filas de la tabla objeto.
     */
    public function tablaObjeto($idCategoria)
    {
        return $this->modelo->verObjetos($idCategoria);
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
