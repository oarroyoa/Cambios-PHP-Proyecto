<?php
require_once 'conexion.php';
/**
 * Clase Modelo para la interacción con la base de datos.
 */
class Modelo extends Conexion
{
    /**
     * Obtiene la configuración actual del juego.
     *
     * @return array Un array asociativo con la configuración del juego.
     */
    function configuracion()
    {
        $sql = "SELECT * FROM config";
        $stmt = $this->conexion->prepare($sql);
        $stmt->execute();
        $resultado = $stmt->get_result();
        $configuracion = $resultado->fetch_assoc();
        $stmt->close();
        return $configuracion;
    }

    /**
     * Actualiza la configuración del juego.
     *
     * @param int $tiempoCrono Tiempo del cronómetro.
     * @param int $nPregunta Número de preguntas.
     * @param int $nObjetosBuenos Número de objetos buenos.
     */
    function actualizarConfiguracion($tiempoCrono, $nPregunta, $nObjetosBuenos)
    {
        // Realizar la actualización en la tabla config
        $sql = "UPDATE config SET tiempoCrono = ?, nPregunta = ?, nObjetosBuenos = ?";
        $stmt = $this->conexion->prepare($sql);
        $stmt->bind_param("iii", $tiempoCrono, $nPregunta, $nObjetosBuenos);
        $stmt->execute();
        $stmt->close();
    }
}
