# Inspector

This project analyzes a git repository and reports on the modifications that have been made to it by each of the users.

## Disclaimer

Obviously a person cannot be evaluated simply by the lines that they introduce in a project, but this is one more metric
that helps evaluate a person's performance.

The project shows a summary of lines by day, by week and by month which can indicate to detect that it is something
wrong with the equipment.

## Ejecución

Execute and edit its content, to indicate the emails used by each of your users.

```shell
cp config/users.yaml.dist config/users.yaml
```

Then run the following to perform the analysis.

```shell
php bin/console app:run path/to/your/git/repository
```

The output indicates the lines added per user.

```
Daily
+---------+--------+--------+--------+--------+--------+--------+--------+--------+--------+--------+--------+
| user    | Mon 27 | Tue 28 | Wed 29 | Thu 30 | Fri 01 | Mon 04 | Tue 05 | Wed 06 | Thu 07 | Fri 08 | Mon 11 |
+---------+--------+--------+--------+--------+--------+--------+--------+--------+--------+--------+--------+
| Alice   | 221    | 170    | 524    | 22     | 109    | 154    | 996    | 342    | 293    | 105    | 5      |
| Bob     | 1,059  | 164    | 80     | 98     | 0      | 4      | 628    | 0      | 0      | 0      | 0      |
| Charlie | 989    | 767    | 12     | 1,497  | 45     | 174    | 909    | 3,381  | 878    | 108    | 0      |
+---------+--------+--------+--------+--------+--------+--------+--------+--------+--------+--------+--------+

Weekly
+---------+-------+-------+-------+-------+-------+-------+-------+-------+-------+-------+-------+
| user    | 02/05 | 09/05 | 16/05 | 23/05 | 30/05 | 06/06 | 13/06 | 20/06 | 27/06 | 04/07 | 11/07 |
+---------+-------+-------+-------+-------+-------+-------+-------+-------+-------+-------+-------+
| Alice   | 1,464 | 1,418 | 1,349 | 288   | 2,272 | 1,234 | 788   | 478   | 1,046 | 1,789 | 0     |
| Bob     | 189   | 1,192 | 1,871 | 1,547 | 592   | 873   | 551   | 753   | 700   | 632   | 0     |
| Charlie | 819   | 3,980 | 4,800 | 3,537 | 405   | 1,210 | 3,871 | 2,116 | 3,233 | 5,432 | 0     |
+---------+-------+-------+-------+-------+-------+-------+-------+-------+-------+-------+-------+

Montly
+---------+--------+--------+--------+--------+--------+--------+--------+--------+--------+--------+--------+
| user    | Mon 27 | Tue 28 | Wed 29 | Thu 30 | Fri 01 | Mon 04 | Tue 05 | Wed 06 | Thu 07 | Fri 08 | Mon 11 |
+---------+--------+--------+--------+--------+--------+--------+--------+--------+--------+--------+--------+
| Alice   | 221    | 170    | 524    | 22     | 109    | 154    | 996    | 342    | 293    | 105    | 5      |
| Bob     | 1,059  | 164    | 80     | 98     | 0      | 4      | 628    | 0      | 0      | 0      | 0      |
| Charlie | 989    | 767    | 12     | 1,497  | 45     | 174    | 909    | 3,381  | 878    | 108    | 0      |
+---------+--------+--------+--------+--------+--------+--------+--------+--------+--------+--------+--------+
```


