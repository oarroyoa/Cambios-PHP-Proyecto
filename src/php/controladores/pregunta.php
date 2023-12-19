<?php
require_once '../php/modelos/pregunta.php';
/**
 * Clase Pregunta que gestiona las operaciones relacionadas con las preguntas.
 */
class Pregunta
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
        $this->modelo = new PreguntaModelo();
    }

    /**
     * Devuelve las filas de las preguntas de una categoría.
     *
     * @param int $idCategoria El ID de la categoría.
     * @return array Un array bidimensional con las filas de la tabla pregunta.
     */
    public function tablaPregunta($idCategoria)
    {
        return $this->modelo->verPreguntas($idCategoria);;
    }

    /**
     * Borra una pregunta de la base de datos.
     */
    public function borrarPregunta()
    {
        $this->modelo->borrarPregunta($_POST["id"]);
        $this->anadir_pregunta(); 
        $_GET['msg'] = "Pregunta borrada correctamente";
        return $_GET['msg'];
    }

    /**
     * Obtiene la información de una pregunta por su ID.
     *
     * @param int $id El ID de la pregunta.
     * @return array Información de la pregunta.
     */
    public function pregunta($id)
    {
        return $this->modelo->verPregunta($id);
    }

    public function verTodasPreguntas(){
        return $this->modelo->verTodasPreguntas();
    }
    /**
     * Agrega o actualiza preguntas en la base de datos.
     */
    public function anadir_pregunta()
    {
        $this->vista = 'anadir_pregunta';

        $_GET['msg'] = '';

        if ($_SERVER["REQUEST_METHOD"] == "POST") {
            $idCategoria = isset($_POST['idCategoria']) ? $_POST['idCategoria'] : '';

            // Acceder a los valores del formulario
            $preguntas = isset($_POST['pregunta']) ? $_POST['pregunta'] : array();
            $respuestas = isset($_POST['opcion']) ? $_POST['opcion'] : array();
            $reflexionesAcierto = isset($_POST['ref1']) ? $_POST['ref1'] : array();
            $reflexionesFallo = isset($_POST['ref2']) ? $_POST['ref2'] : array();

            // Iterar sobre cada pregunta
            foreach ($preguntas as $index => $preguntaData) {
                // Obtener la pregunta actual
                $pregunta = isset($preguntaData['texto']) ? $this->sanitizarEntrada($preguntaData['texto']) : '';

                // Verificar si la pregunta está vacía después de la sanitización
                if (empty($pregunta)) {
                    $_GET['msg'] = 'Error: La pregunta no puede estar vacía. No se pueden introducir caracteres especiales.';
                    return $_GET['msg'];
                }

                // Verificar si la pregunta ya existe al añadir
                if (empty($preguntaData['idPregunta']) && $this->modelo->preguntaExiste($pregunta, $idCategoria)) {
                    $_GET['msg'] = 'Error: La pregunta ya existe. Por favor, elige otro nombre.';
                    return $_GET['msg'];
                }

                // Obtener el ID de la pregunta actual
                $idPregunta = $preguntaData['idPregunta'];

                // Obtener la respuesta correcta para la pregunta actual
                $respuesta = isset($respuestas[$index]) ? $respuestas[$index] : '';

                // Si la respuesta es un array, tomar el primer elemento (puede ser '1' o '0')
                if (is_array($respuesta)) {
                    $respuesta = isset($respuesta[0]) ? $respuesta[0] : '';
                }

                $refAcierto = isset($reflexionesAcierto[$index][0]) ? $this->sanitizarEntrada($reflexionesAcierto[$index][0]) : '';
                $refFallo = isset($reflexionesFallo[$index][0]) ? $this->sanitizarEntrada($reflexionesFallo[$index][0]) : '';

                // Intentar actualizar la pregunta directamente
                $this->modelo->modificarPregunta($idPregunta, $pregunta, $refAcierto, $refFallo, $respuesta, $idCategoria);

                // Verificar si la pregunta fue actualizada correctamente
                $preguntaActualizada = $this->modelo->verPregunta($idPregunta);

                if (!$preguntaActualizada) {
                    // La pregunta no existía, agregar
                    $this->modelo->agregarPregunta($pregunta, $refAcierto, $refFallo, $respuesta, $idCategoria);
                    $_GET['msg'] = 'Preguntas agregadas o actualizadas correctamente';
                    return $_GET['msg'];
                } else {
                    $_GET['msg'] = 'Preguntas actualizadas correctamente';
                }
            }

        }
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
