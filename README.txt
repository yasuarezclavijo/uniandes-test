Prueba uniandes
---------------------

A continuación se describe como se entendio y desarrollo cada punto de la prueba descrita.

1. Módulo de manejo de empleados (debe tener setup para instalar la estructura requerida).
------------

Se crea modulo, no me queda claro el termino "setup" pero se asumio como la creación de toda la configuración,
para resolver este punto me apoye en los archivos yml dentro de config/install y de el archivo .install
implementando hook como install y uninstall, para en el momento que se desinstale el modulo no deje basura, igualmente
en los yml se dejo en el enforced.


2. Identificar dependencias a otros módulos.
------------

Las podra visualizar en el archivo .info.yml


3. Creación de taxonomía "Tipo salario" con los siguientes términos:
------------

Nada que reportar. Creado por config/install y hook_install

4. Creación de tipo de contenido "Empleados" con los siguientes fields:
• Nombre del empleado
• Id remoto (el id del empleado en el API)
• Edad
• Tipo de salario (field tipo term_reference vinculado con la taxonomía "Tipo salario")
------------

Nada que reportar. Creado por config/install

5. Formulario de configuración que permite parametrizar datos del api y realizar proceso de
importación
• Realizar importación de eventos mediante batch_process
• Cada registro en la respuesta del API debe corresponder con un empleado
• Debe verificar si un nodo existe y crearlo o actualizarlo según corresponda
• Los empleados deben quedar asignados en término del a taxonomía así:
§ Salario entre 0 y 1000 (Salario bajo)
§ Salario entre 1001 y 4000 (Salario medio)
§ Salario superior a 4000 (Salario alto)

------------

Se crea formulario de configuración, se considera importante dejar configurable
(Inicio >> Administración >> Configuración >> Sistema >> Settings employees).
  - Endpoint
  - Limite inferior y superior para salario bajo.
  - Limite inferior y superior para salario medio.
  - Se considero salario alto todo por encima del limite superior de salario medio

Se genera las funciones batch, pero el boton solo se habilitara cuando la configuración este completada.

6. Al ingresar a la vista en el front de un término (salario bajo, medio o alto) debe mostrar
los empleados correspondientes al término.

------------

Esta vista es generada de manera automatica en las views para taxonomy, ya responden segun se solicita, se me ocurrio
un plus y cree un bloque con los enlaces para posicionar en el home al lado izquierdo siempre que el tema sea bartik.

7. En la primera vista debe mostrar los empleados cuyo nombre inicia por A o la primera letra disponible.

------------

Decidi hacerlo a traves de views, la misma se creara y se posicionara en el home, en el install se desahabilita frontpage
para evitar conflicto, en esta view se ordena la consulta de manera alfabetica.

8. Debe contar con filtro alfabético para poder visualizar los empleados con nombre que
inicia por otras letras.

------------

9. El filtro alfabético debe funcionar vía ajax (no debe refrescar la página)

------------

Se creo filtro expuesto por ajax como se indico, se agrupo y solo se puede seleccionar la letra por la que desea iniciar
todo esto a traves de la view, la misma se auto-crea gracias a config/install

10. Los empleados no cuentan con vista de detalle (solo la vista de listado).
------------

Se gestiona presentación y no se ve mas que el title del empleado, siendo honesto no encontre como deshabilitar
esa vista del detalle, asi que decidi a traves de gestion de la presentación.
