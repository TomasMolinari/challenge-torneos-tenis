# Challenge torneos tenis

En [esta sheet](https://docs.google.com/spreadsheets/d/1GZu4w8_NiJS8I1--C-N5O2dPoj_Bv-ojekMRDS2ToMQ/edit#gid=1490274874) vas a encontrar todos los torneos de tenis que se jugaron desde 1877.

Usando la [api de google sheets](https://developers.google.com/sheets/api/) te pedimos que nos digas de cada [Grand Slam](https://es.wikipedia.org/wiki/Grand_Slam_(tenis)), quién es el jugador que mas veces lo ganó (no vale hardcodear a Rafa en Roland Garros) y cuando haces un click sobre el jugador te diga cuando fue la ultima vez que lo ganó.
El diseño te lo dejamos para vos :)


### Consignas y Tips

* Resolvelo en PHP y que el backend funcione como una API REST.
* La resolución debe ser un fork de este repo (Se evalúa el uso de GIT).
* No uses frameworks para php, perdoná :)
* El resultado nos gustaría verlo en HTML, CSS y JS haciendo llamadas asincronas al backend. Podes usar el framework de js (menos Jquery, que no es un framework) que quieras :)
* No uses el nombre del Grand Slam como indentificador. Si le prestas atención al excel hay otra forma :).
* Implementá un sistema de Logs que registre tres tipos de mensajes (Error, Warning y Success) para la respuesta de nuestra API y de las peticiones que le realicemos al sheet que utilizamos.


### BONUS

* Desarrollá un script que permita ejecutar la aplicación (Back y Front) como si fuera un deploy y dar instrucciones de su ejecución. Preferentemente en Shell Script.

### El extra mile

Si se te ocurre alguna cosa piola para agregarle al challenge y queres hacerlo y mostrarnos nos ayudarías a mejorar este ejercicio.
