<?php
require_once '../php/modelos/modelo.php';

/**
 * Controlador para interactuar con la lógica de negocio y la presentación.
 */
class Controlador
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
        $this->modelo = new Modelo();

    }

    /**
     * Llama a la vista para remover, ya sea una categoría, un objeto o una pregunta.
     * JUSTIFICACION: No hay ninguna otro funcion dentro de este controlador que realice una funcion para eliminar, por lo que se crea esta para que llame a su correspondiente vista.
     */
    public function remove(){
        $this->vista = 'remove';
    }


    /**
     * Método que devuelve la configuración del juego.
     * 
     * @return array Un array con la configuración.
     */
    public function configuracion()
    {
        $this->vista = 'modConfig';
        return $this->modelo->configuracion();
    }

    
    /**
     * Actualiza la configuración del juego en la base de datos.
     */
    public function actualizarConfiguracion()
    {        
        $_GET['msg'] = ''; // Variable para almacenar el mensaje
    
        if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['accion']) && $_POST['accion'] == 'actualizarConfiguracion') {
    
            // Recuperar los valores del formulario y sanitizar cada entrada permitiendo solo números
            $parametro1 = isset($_POST['parametro1']) ? $this->sanitizarEntrada($_POST['parametro1'], true) : '';
            $parametro2 = isset($_POST['parametro2']) ? $this->sanitizarEntrada($_POST['parametro2'], true) : '';
            $parametro3 = isset($_POST['parametro3']) ? $this->sanitizarEntrada($_POST['parametro3'], true) : '';
            // Agregar más variables según los parámetros que existan en la tabla config
    
            // Verificar si los campos son numéricos después de la sanitización
            if (!is_numeric($parametro1) || !is_numeric($parametro2) || !is_numeric($parametro3)) {
                $_GET['msg'] = 'Error: Los campos deben contener solo números.';
            } else {
                // Los datos son válidos, proceder a actualizar la configuración en la base de datos
                $this->modelo->actualizarConfiguracion($parametro1, $parametro2, $parametro3);
                $_GET['msg'] = 'Configuración actualizada correctamente.';
            }
        }
        
        $this->configuracion();
        // Redirigir a la vista de configuración con el mensaje
        return $_GET['msg'];
    }

    /**
     * Sanitiza una entrada eliminando etiquetas HTML, emojis y otros caracteres especiales.
     *
     * @param string $input Entrada a sanitizar.
     * @param bool $permitirSoloNumeros Indica si se deben permitir solo números en la entrada.
     * @return string Entrada sanitizada.
     */
    private function sanitizarEntrada($input, $permitirSoloNumeros = false)
    {
        // Si se permite solo números, eliminar todo excepto los dígitos
        if ($permitirSoloNumeros) {
            $sanitizedInput = preg_replace('/[^\p{N}]/u', '', $input);
        } else {
            // Eliminar etiquetas HTML, emojis y otros caracteres especiales excepto los permitidos sin adyacencia a otros caracteres
            $sanitizedInput = preg_replace('/(<[^>]+[\'"]>|\w(?=\w))|[^ \w"<>]+/', '', strip_tags($input));
        }
        return $sanitizedInput;
    }
}