��    �        �   
      �      �     �  &   �     �          2     I     _  /   r     �  "   �  1   �  �     "   �  j   �  o   A     �  D   �  !     3   6  ?   j  H   �  D   �  C   8  E   |  ?   �  ?     >   B  9   �  L   �  B     E   K  �   �  0     F   G  >   �  B   �  I     %   Z  <   �  O   �  7        E     L     U     g  M   {     �  -   �  !     >   )  E   h  C   �  y   �  9   l  D   �  C   �  D   /  E   t  >   �  A   �  '   ;  (   c  ,   �  7   �  2   �  6   $  >   [  *   �  /   �  7   �  4   -  %   b  %   �  1   �  0   �  #        5  4   S  7   �  2   �  6   �  1   *  0   \  ,   �  .   �  3   �  7         U   +   u   1   �   6   �   6   
!  1   A!  *   s!  "   �!  7   �!  "   �!  $   "  J   A"     �"     �"  3   �"  0   �"     $#  #   7#  !   [#     }#  !   �#  $   �#      �#  -   $     2$  4   R$  %   �$  $   �$  "   �$  !   �$  u   %  F   �%     �%  7   �%  )    &  k   J&  `   �&  %   '  &   ='     d'  d   l'     �'  /   �'  &    (  0   G(  .   x(  -   �(     �(     �(  &   �(      %)  ,   F)  (   s)     �)  %   �)     �)     �)     �)     *     *     1*     G*  #   X*     |*     �*     �*     �*     �*      �*  "   +     (+    G+  C   L-      �-  G   �-  >   �-  B   8.  @   {.  "   �.     �.  N   �.  !   I/  6   k/  G   �/  �   �/  6   �0  �   1  �   �1  1   I2  j   {2  )   �2  N   3  `   _3  g   �3  y   (4  Y   �4  v   �4  E   s5  O   �5  \   	6  Q   f6  �   �6  {   �7  U   8  �   l8  C   >9  |   �9  p   �9  d   p:  �   �:  G   [;  f   �;  �   
<  h   �<     7=     F=  )   U=  +   =  �   �=     C>  K   \>  0   �>  {   �>  �   U?  �   �?    }@  k   �A      B  �   �B  �   C  �   �C  �   D  {   �D  M   &E  H   tE  a   �E  r   F  c   �F  h   �F  o   _G  L   �G  L   H  e   iH  k   �H  =   ;I  L   yI  `   �I  T   'J  C   |J  9   �J  f   �J  v   aK  g   �K  l   @L  ]   �L  `   M  J   lM  Z   �M  n   N  _   �N  <   �N  S   O  X   rO  V   �O  t   "P  e   �P  R   �P  2   PQ  f   �Q  9   �Q  G   $R  �   lR  '   �R  '   S  ^   DS  c   �S  "   T  g   *T  P   �T  5   �T  -   U  >   GU  <   �U  T   �U     V  h   8V  >   �V  >   �V  <   W  <   \W  �   �W  t   VX  "   �X  X   �X  I   GY  �   �Y  �   CZ  M   [  [   _[     �[  �   �[  2   �\  p   �\  V   :]  u   �]  J   ^  K   R^  )   �^  "   �^  =   �^  C   )_  Z   m_  J   �_  K   `  W   _`  5   �`     �`     a     *a  "   Ga  ,   ja  /   �a  c   �a     +b  $   Hb  "   mb  B   �b  a   �b  5   5c  D   kc  1   �c     a   2   *   B       �   ,   U   �   )   7       M   �       ;   L   <       R      W          &   b   G   8   4          }   �           -      6      �       O       �      ~   T               1   K   h   �   �              �   P      {       X          0       �       �          �   !   :   w   |   �   z       #   �      p   +       t       V      e           9   N               d       �   @   D   �      �   i   c   %      "   �              r       _   f   s   �   .       �       E   �       /      ]   `      Q   >               Z   �   
           (       v   �           \   m   $   k   �   l       g          A   �              =           x       J       n       �       �   '   ^   [          �       H   q   �   o   �   C          y       Y       S      �   ?                	   j   �                     u   F   I          �   3          5    
Allowed signal names for kill:
 
Common options:
 
Options for register and unregister:
 
Options for start or restart:
 
Options for stop or restart:
 
Report bugs to <%s>.
 
Shutdown modes are:
 
Start types are:
   %s init[db]   [-D DATADIR] [-s] [-o OPTIONS]
   %s kill       SIGNALNAME PID
   %s logrotate  [-D DATADIR] [-s]
   %s promote    [-D DATADIR] [-W] [-t SECS] [-s]
   %s register   [-D DATADIR] [-N SERVICENAME] [-U USERNAME] [-P PASSWORD]
                    [-S START-TYPE] [-e SOURCE] [-W] [-t SECS] [-s] [-o OPTIONS]
   %s reload     [-D DATADIR] [-s]
   %s restart    [-D DATADIR] [-m SHUTDOWN-MODE] [-W] [-t SECS] [-s]
                    [-o OPTIONS] [-c]
   %s start      [-D DATADIR] [-l FILENAME] [-W] [-t SECS] [-s]
                    [-o OPTIONS] [-p PATH] [-c]
   %s status     [-D DATADIR]
   %s stop       [-D DATADIR] [-m SHUTDOWN-MODE] [-W] [-t SECS] [-s]
   %s unregister [-N SERVICENAME]
   -?, --help             show this help, then exit
   -D, --pgdata=DATADIR   location of the database storage area
   -N SERVICENAME  service name with which to register PostgreSQL server
   -P PASSWORD     password of account to register PostgreSQL server
   -S START-TYPE   service start type to register PostgreSQL server
   -U USERNAME     user name of account to register PostgreSQL server
   -V, --version          output version information, then exit
   -W, --no-wait          do not wait until operation completes
   -c, --core-files       allow postgres to produce core files
   -c, --core-files       not applicable on this platform
   -e SOURCE              event source for logging when running as a service
   -l, --log=FILENAME     write (or append) server log to FILENAME
   -m, --mode=MODE        MODE can be "smart", "fast", or "immediate"
   -o, --options=OPTIONS  command line options to pass to postgres
                         (PostgreSQL server executable) or initdb
   -p PATH-TO-POSTGRES    normally not necessary
   -s, --silent           only print errors, no informational messages
   -t, --timeout=SECS     seconds to wait when using -w option
   -w, --wait             wait until operation completes (default)
   auto       start service automatically during system startup (default)
   demand     start service on demand
   fast        quit directly, with proper shutdown (default)
   immediate   quit without complete shutdown; will lead to recovery on restart
   smart       quit after all clients have disconnected
  done
  failed
  stopped waiting
 %s home page: <%s>
 %s is a utility to initialize, start, stop, or control a PostgreSQL server.

 %s() failed: %m %s: -S option not supported on this platform
 %s: PID file "%s" does not exist
 %s: WARNING: cannot create restricted tokens on this platform
 %s: WARNING: could not locate all job object functions in system API
 %s: another server might be running; trying to start server anyway
 %s: cannot be run as root
Please log in (using, e.g., "su") as the (unprivileged) user that will
own the server process.
 %s: cannot promote server; server is not in standby mode
 %s: cannot promote server; single-user server is running (PID: %ld)
 %s: cannot reload server; single-user server is running (PID: %ld)
 %s: cannot restart server; single-user server is running (PID: %ld)
 %s: cannot rotate log file; single-user server is running (PID: %ld)
 %s: cannot set core file size limit; disallowed by hard limit
 %s: cannot stop server; single-user server is running (PID: %ld)
 %s: control file appears to be corrupt
 %s: could not access directory "%s": %s
 %s: could not allocate SIDs: error code %lu
 %s: could not create log rotation signal file "%s": %s
 %s: could not create promote signal file "%s": %s
 %s: could not create restricted token: error code %lu
 %s: could not determine the data directory using command "%s"
 %s: could not find own program executable
 %s: could not find postgres program executable
 %s: could not get LUIDs for privileges: error code %lu
 %s: could not get token information: error code %lu
 %s: could not open PID file "%s": %s
 %s: could not open log file "%s": %s
 %s: could not open process token: error code %lu
 %s: could not open service "%s": error code %lu
 %s: could not open service manager
 %s: could not read file "%s"
 %s: could not register service "%s": error code %lu
 %s: could not remove log rotation signal file "%s": %s
 %s: could not remove promote signal file "%s": %s
 %s: could not send log rotation signal (PID: %ld): %s
 %s: could not send promote signal (PID: %ld): %s
 %s: could not send reload signal (PID: %ld): %s
 %s: could not send signal %d (PID: %ld): %s
 %s: could not send stop signal (PID: %ld): %s
 %s: could not start server
Examine the log output.
 %s: could not start server due to setsid() failure: %s
 %s: could not start server: %s
 %s: could not start server: error code %lu
 %s: could not start service "%s": error code %lu
 %s: could not unregister service "%s": error code %lu
 %s: could not write log rotation signal file "%s": %s
 %s: could not write promote signal file "%s": %s
 %s: database system initialization failed
 %s: directory "%s" does not exist
 %s: directory "%s" is not a database cluster directory
 %s: invalid data in PID file "%s"
 %s: missing arguments for kill mode
 %s: no database directory specified and environment variable PGDATA unset
 %s: no operation specified
 %s: no server running
 %s: old server process (PID: %ld) seems to be gone
 %s: option file "%s" must have exactly one line
 %s: out of memory
 %s: server did not promote in time
 %s: server did not start in time
 %s: server does not shut down
 %s: server is running (PID: %ld)
 %s: service "%s" already registered
 %s: service "%s" not registered
 %s: single-user server is running (PID: %ld)
 %s: the PID file "%s" is empty
 %s: too many command-line arguments (first is "%s")
 %s: unrecognized operation mode "%s"
 %s: unrecognized shutdown mode "%s"
 %s: unrecognized signal name "%s"
 %s: unrecognized start type "%s"
 HINT: The "-m fast" option immediately disconnects sessions rather than
waiting for session-initiated disconnection.
 If the -D option is omitted, the environment variable PGDATA is used.
 Is server running?
 Please terminate the single-user server and try again.
 Server started and accepting connections
 The program "%s" is needed by %s but was not found in the
same directory as "%s".
Check your installation.
 The program "%s" was found by "%s"
but was not the same version as %s.
Check your installation.
 Timed out waiting for server startup
 Try "%s --help" for more information.
 Usage:
 WARNING: online backup mode is active
Shutdown will not complete until pg_stop_backup() is called.

 Waiting for server startup...
 cannot duplicate null pointer (internal error)
 child process exited with exit code %d child process exited with unrecognized status %d child process was terminated by exception 0x%X child process was terminated by signal %d: %s command not executable command not found could not change directory to "%s": %m could not find a "%s" to execute could not get current working directory: %s
 could not identify current directory: %m could not read binary "%s" could not read symbolic link "%s": %m invalid binary "%s" out of memory out of memory
 server promoted
 server promoting
 server shutting down
 server signaled
 server signaled to rotate log file
 server started
 server starting
 server stopped
 starting server anyway
 trying to start server anyway
 waiting for server to promote... waiting for server to shut down... waiting for server to start... Project-Id-Version: pg_ctl (PostgreSQL current)
Report-Msgid-Bugs-To: pgsql-bugs@lists.postgresql.org
POT-Creation-Date: 2025-02-08 07:45+0200
PO-Revision-Date: 2024-09-07 06:47+0300
Last-Translator: Alexander Lakhin <exclusion@gmail.com>
Language-Team: Russian <pgsql-ru-general@postgresql.org>
Language: ru
MIME-Version: 1.0
Content-Type: text/plain; charset=UTF-8
Content-Transfer-Encoding: 8bit
Plural-Forms: nplurals=3; plural=(n%10==1 && n%100!=11 ? 0 : n%10>=2 && n%10<=4 && (n%100<10 || n%100>=20) ? 1 : 2);
 
Разрешённые сигналы для команды kill:
 
Общие параметры:
 
Параметры для регистрации и удаления:
 
Параметры запуска и перезапуска:
 
Параметры остановки и перезапуска:
 
Об ошибках сообщайте по адресу <%s>.
 
Режимы остановки:
 
Типы запуска:
   %s init[db]   [-D КАТАЛОГ-ДАННЫХ] [-s] [-o ПАРАМЕТРЫ]
   %s kill       СИГНАЛ PID
   %s logrotate  [-D КАТАЛОГ-ДАННЫХ] [-s]
   %s promote    [-D КАТАЛОГ-ДАННЫХ] [-W] [-t СЕК] [-s]
   %s register   [-D КАТАЛОГ-ДАННЫХ] [-N ИМЯ-СЛУЖБЫ] [-U ПОЛЬЗОВАТЕЛЬ] [-P ПАРОЛЬ]
                    [-S ТИП-ЗАПУСКА] [-e ИСТОЧНИК] [-W] [-t СЕК] [-s] [-o ПАРАМЕТРЫ]
   %s reload     [-D КАТАЛОГ-ДАННЫХ] [-s]
   %s restart    [-D КАТАЛОГ-ДАННЫХ] [-m РЕЖИМ-ОСТАНОВКИ] [-W] [-t СЕК] [-s]
                    [-o ПАРАМЕТРЫ] [-c]
   %s start      [-D КАТАЛОГ-ДАННЫХ] [-l ИМЯ-ФАЙЛА] [-W] [-t СЕК] [-s]
                    [-o ПАРАМЕТРЫ] [-p ПУТЬ] [-c]
   %s status     [-D КАТАЛОГ-ДАННЫХ]
   %s stop       [-D КАТАЛОГ-ДАННЫХ] [-m РЕЖИМ-ОСТАНОВКИ] [-W] [-t СЕК] [-s]
   %s unregister [-N ИМЯ-СЛУЖБЫ]
   -?, --help             показать эту справку и выйти
   -D, --pgdata=КАТАЛОГ   расположение хранилища баз данных
   -N ИМЯ-СЛУЖБЫ   имя службы для регистрации сервера PostgreSQL
   -P ПАРОЛЬ       пароль учётной записи для регистрации сервера PostgreSQL
   -S ТИП-ЗАПУСКА  тип запуска службы сервера PostgreSQL
   -U ПОЛЬЗОВАТЕЛЬ имя пользователя для регистрации сервера PostgreSQL
   -V, --version          показать версию и выйти
   -W, --no-wait          не ждать завершения операции
   -c, --core-files       указать postgres создавать дампы памяти
   -c, --core-files       неприменимо на этой платформе
   -e ИСТОЧНИК            источник событий, устанавливаемый при записи в журнал,
                         когда сервер работает в виде службы
   -l, --log=ФАЙЛ         записывать (или добавлять) протокол сервера в ФАЙЛ.
   -m, --mode=РЕЖИМ       может быть "smart", "fast" или "immediate"
   -o, --options=ПАРАМЕТРЫ  передаваемые postgres (исполняемому файлу PostgreSQL)
                         или initdb параметры командной строки
   -p ПУТЬ-К-POSTGRES     обычно не требуется
   -s, --silent           выводить только ошибки, без информационных сообщений
   -t, --timeout=СЕК      время ожидания при использовании параметра -w
   -w, --wait             ждать завершения операции (по умолчанию)
   auto       запускать службу автоматически при старте системы (по умолчанию)
   demand     запускать службу по требованию
   fast        закончить сразу, в штатном режиме (по умолчанию)
   immediate   закончить немедленно, в экстренном режиме; влечёт за собой
              восстановление при перезапуске
   smart       закончить работу после отключения всех клиентов
  готово
  ошибка
  прекращение ожидания
 Домашняя страница %s: <%s>
 %s - это утилита для инициализации, запуска, остановки и управления сервером PostgreSQL.

 ошибка в %s(): %m %s: параметр -S не поддерживается в этой ОС
 %s: файл PID "%s" не существует
 %s: ПРЕДУПРЕЖДЕНИЕ: в этой ОС нельзя создавать ограниченные маркеры
 %s: ПРЕДУПРЕЖДЕНИЕ: не удалось найти все функции для работы с задачами в системном API
 %s: возможно, уже работает другой сервер; всё же пробуем запустить этот сервер
 Запускать %s от имени root нельзя.
Пожалуйста, переключитесь на обычного пользователя (например,
используя "su"), который будет запускать серверный процесс.
 %s: повысить сервер нельзя - он работает не в режиме резерва
 %s: повысить сервер с PID %ld нельзя - он выполняется в монопольном режиме
 %s: перезагрузить сервер с PID %ld нельзя - он запущен в монопольном режиме
 %s: перезапустить сервер с PID %ld нельзя - он запущен в монопольном режиме
 %s: не удалось прокрутить файл журнала; сервер работает в монопольном режиме (PID: %ld)
 %s: не удалось ограничить размер дампа памяти; запрещено жёстким ограничением
 %s: остановить сервер с PID %ld нельзя - он запущен в монопольном режиме
 %s: управляющий файл, по-видимому, испорчен
 %s: ошибка при обращении к каталогу "%s": %s
 %s: не удалось подготовить структуры SID (код ошибки: %lu)
 %s: не удалось создать файл "%s" с сигналом к прокрутке журнала: %s
 %s: не удалось создать файл "%s" с сигналом к повышению: %s
 %s: не удалось создать ограниченный маркер (код ошибки: %lu)
 %s: не удалось определить каталог данных с помощью команды "%s"
 %s: не удалось найти свой исполняемый файл
 %s: не удалось найти исполняемый файл postgres
 %s: не удалось получить LUID для привилегий (код ошибки: %lu)
 %s: не удалось получить информацию о маркере (код ошибки: %lu)
 %s: не удалось открыть файл PID "%s": %s
 %s: не удалось открыть файл протокола "%s": %s
 %s: не удалось открыть маркер процесса (код ошибки: %lu)
 %s: не удалось открыть службу "%s" (код ошибки: %lu)
 %s: не удалось открыть менеджер служб
 %s: не удалось прочитать файл "%s"
 %s: не удалось зарегистрировать службу "%s" (код ошибки: %lu)
 %s: ошибка при удалении файла "%s" с сигналом к прокрутке журнала: %s
 %s: ошибка при удалении файла "%s" с сигналом к повышению: %s
 %s: не удалось отправить сигнал к прокрутке журнала (PID: %ld): %s
 %s: не удалось отправить сигнал к повышению (PID: %ld): %s
 %s: не удалось отправить сигнал перезагрузки (PID: %ld): %s
 %s: не удалось отправить сигнал %d (PID: %ld): %s
 %s: не удалось отправить сигнал остановки (PID: %ld): %s
 %s: не удалось запустить сервер
Изучите протокол выполнения.
 %s: не удалось запустить сервер из-за ошибки в setsid(): %s
 %s: не удалось запустить сервер: %s
 %s: не удалось запустить сервер (код ошибки: %lu)
 %s: не удалось запустить службу "%s" (код ошибки: %lu)
 %s: ошибка при удалении службы "%s" (код ошибки: %lu)
 %s: не удалось записать файл "%s" с сигналом к прокрутке журнала: %s
 %s: не удалось записать файл "%s" с сигналом к повышению: %s
 %s: сбой при инициализации системы баз данных
 %s: каталог "%s" не существует
 %s: каталог "%s" не содержит структуры кластера баз данных
 %s: неверные данные в файле PID "%s"
 %s: отсутствуют аргументы для режима kill
 %s: каталог баз данных не указан и переменная окружения PGDATA не установлена
 %s: команда не указана
 %s: сервер не работает
 %s: похоже, что старый серверный процесс (PID: %ld) исчез
 %s: в файле параметров "%s" должна быть ровно одна строка
 %s: нехватка памяти
 %s: повышение сервера не завершилось за отведённое время
 %s: сервер не запустился за отведённое время
 %s: сервер не останавливается
 %s: сервер работает (PID: %ld)
 %s: служба "%s" уже зарегистрирована
 %s: служба "%s" не зарегистрирована
 %s: сервер работает в монопольном режиме (PID: %ld)
 %s: файл PID "%s" пуст
 %s: слишком много аргументов командной строки (первый: "%s")
 %s: нераспознанный режим работы "%s"
 %s: неизвестный режим остановки "%s"
 %s: нераспознанное имя сигнала "%s"
 %s: нераспознанный тип запуска "%s"
 ПОДСКАЗКА: Параметр "-m fast" может сбросить сеансы принудительно,
не дожидаясь, пока они завершатся сами.
 Если параметр -D опущен, используется переменная окружения PGDATA.
 Запущен ли сервер?
 Пожалуйста, остановите его и повторите попытку.
 Сервер запущен и принимает подключения
 Программа "%s" нужна для %s, но она не найдена
в каталоге "%s".
Проверьте правильность установки СУБД.
 Программа "%s" найдена программой "%s",
но её версия отличается от версии %s.
Проверьте правильность установки СУБД.
 Превышено время ожидания запуска сервера
 Для дополнительной информации попробуйте "%s --help".
 Использование:
 ПРЕДУПРЕЖДЕНИЕ: активен режим копирования "на ходу"
Выключение произойдёт только при вызове pg_stop_backup().

 Ожидание запуска сервера...
 попытка дублирования нулевого указателя (внутренняя ошибка)
 дочерний процесс завершился с кодом возврата %d дочерний процесс завершился с нераспознанным кодом состояния %d дочерний процесс прерван исключением 0x%X дочерний процесс завершён по сигналу %d: %s неисполняемая команда команда не найдена не удалось перейти в каталог "%s": %m не удалось найти запускаемый файл "%s" не удалось определить текущий рабочий каталог: %s
 не удалось определить текущий каталог: %m не удалось прочитать исполняемый файл "%s" не удалось прочитать символическую ссылку "%s": %m неверный исполняемый файл "%s" нехватка памяти нехватка памяти
 сервер повышен
 сервер повышается
 сервер останавливается
 сигнал отправлен серверу
 сигнал для прокрутки файла журнала отправлен серверу
 сервер запущен
 сервер запускается
 сервер остановлен
 сервер запускается, несмотря на это
 производится попытка запуска сервера в любом случае
 ожидание повышения сервера... ожидание завершения работы сервера... ожидание запуска сервера... 