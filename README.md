

Cambios en el MVC:

- Eliminados todos los redireccionamientos por header en todas las funciones CRUD de los controladores. Sustituido por llamadas a vistas y return.
    
- Eliminadas funciones repetidas en varios controladores y eliminadas funciones sin utilidad.
    
- Cambiadas funciones que solo llaman vista menos donde sea necesario
    
- Variable $mensaje reemplazada por $_GET[‘msg’]
    
- Instanciados objetos para llamar a controladores necesarios en las vistas.
    
- Instanciados los modelos en los constructores de cada controlador
    
