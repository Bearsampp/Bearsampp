@ECHO OFF

%~dp0bin\initdb.exe -U postgres -A trust -E utf8 -D "%~dp0data" > "~BEARSAMPP_WIN_PATH~\logs\postgresql-install.log" 2>&1
copy /y "%~dp0postgresql.conf.ber" "%~dp0data\postgresql.conf"
copy /y "%~dp0pg_hba.conf.ber" "%~dp0data\pg_hba.conf"
