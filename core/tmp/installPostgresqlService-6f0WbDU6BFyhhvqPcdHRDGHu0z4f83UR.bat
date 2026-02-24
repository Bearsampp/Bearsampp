@ECHO OFF

"E:\Bearsampp-development\sandbox\bin\postgresql\current\bin\pg_ctl.exe" register -N "bearsampppostgresql" -U "LocalSystem" -D "E:\Bearsampp-development\sandbox\bin\postgresql\current\data" -l "E:\Bearsampp-development\sandbox\logs\postgresql.log" -w

ECHO FINISHED! > "E:\Bearsampp-development\sandbox\core\tmp\installPostgresqlService-Vc83WAtc6wNwsjCfRlwA76A3tIy7pgyG.tmp"