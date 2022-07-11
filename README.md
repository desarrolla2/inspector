# Inspector

Este proyecto analiza un repositorio git e informa sobre las modificaciones que han realizado sobre el cada uno de los
usuarios.

## Disclaimer

Obviamente, no se puede evaluar a una persona simplemente por las líneas que introduce en un proyecto, sino que esto es
una métrica más que ayuda a evaluar el rendimiento de una persona.

El proyecto muestra un resumen de líneas por día, por semana y por mes lo que puede indicar a detectar que está
ocurriendo algún problema en el equipo.

## Ejecución

Ejecuta y edita su contenido, para inidicar la ruta al repositorio que quieres analizar.

```shell
cp .env.local.dist .env.local
```

Luego ejecuta

```shell
php bin/console app:run
```

La salida inidica las lineas añadidas por usuario.

```
Daily
+-------------------------------+--------+--------+--------+--------+--------+--------+--------+--------+--------+--------+--------+
| user                          | Mon 27 | Tue 28 | Wed 29 | Thu 30 | Fri 01 | Mon 04 | Tue 05 | Wed 06 | Thu 07 | Fri 08 | Mon 11 |
+-------------------------------+--------+--------+--------+--------+--------+--------+--------+--------+--------+--------+--------+
| aaaaaaaaaaaaaa@domain.com     | 221    | 170    | 524    | 22     | 109    | 154    | 996    | 342    | 293    | 105    | 5      |
| bbbbbbbbbbbbbbbbbb@domain.com | 1,059  | 164    | 80     | 98     | 0      | 4      | 628    | 0      | 0      | 0      | 0      |
| ccccccccccccccc@domain.com    | 989    | 767    | 12     | 1,497  | 45     | 174    | 909    | 3,381  | 878    | 108    | 0      |
+-------------------------------+--------+--------+--------+--------+--------+--------+--------+--------+--------+--------+--------+

Weekly
+-------------------------------+-------+-------+-------+-------+-------+-------+-------+-------+-------+-------+-------+
| user                          | 02/05 | 09/05 | 16/05 | 23/05 | 30/05 | 06/06 | 13/06 | 20/06 | 27/06 | 04/07 | 11/07 |
+-------------------------------+-------+-------+-------+-------+-------+-------+-------+-------+-------+-------+-------+
| aaaaaaaaaaaaaa@domain.com     | 1,464 | 1,418 | 1,349 | 288   | 2,272 | 1,234 | 788   | 478   | 1,046 | 1,789 | 0     |
| bbbbbbbbbbbbbbbbbb@domain.com | 189   | 1,192 | 1,871 | 1,547 | 592   | 873   | 551   | 753   | 700   | 632   | 0     |
| ccccccccccccccc@domain.com    | 819   | 3,980 | 4,800 | 3,537 | 405   | 1,210 | 3,871 | 2,116 | 3,233 | 5,432 | 0     |
+-------------------------------+-------+-------+-------+-------+-------+-------+-------+-------+-------+-------+-------+

Montly
+-------------------------------+--------+--------+--------+--------+--------+--------+-------+
| user                          | Jan    | Feb    | Mar    | Apr    | May    | Jun    | Jul   |
+-------------------------------+--------+--------+--------+--------+--------+--------+-------+
| aaaaaaaaaaaaaa@domain.com     | 7,435  | 5,473  | 8,916  | 10,550 | 6,255  | 4,851  | 2,004 |
| bbbbbbbbbbbbbbbbbb@domain.com | 0      | 0      | 19,162 | 4,649  | 5,272  | 4,128  | 632   |
| ccccccccccccccc@domain.com    | 26,919 | 19,466 | 10,629 | 16,050 | 19,729 | 10,989 | 5,496 |
+-------------------------------+--------+--------+--------+--------+--------+--------+-------+
```
