��    w      �  �   �      
     
  9   3
     m
  F   �
  =   �
  D   	  I   N  �   �  A   W  ;   �  M   �  K   #  K   o  0   �  =   �  ;   *  2   f     �  +   �     �  )   �  )     )   =     g  )   �  )   �  +   �  )     R   .  )   �  )   �     �  U   �  A   H  )   �  )   �  )   �  ,     )   5  )   _  )   �  )   �  )   �  )     )   1  )   [  )   �  )   �  )   �  )     )   -  )   W  )   �  )   �  )   �  )   �     )  )   @  )   j  )   �  )   �  	   �  )   �  �     %   �  !   �  )        /  ,   F  *   s  A   �     �     �     �  @     '   T  &   |  "   �  1   �     �  7     +   O  !   {  (   �     �  ,   �  :     !   K     m  0   �  8   �     �  "        5     >     F     V     ]     |  &   �  +   �  )   �          +  -   /  >   ]  )   �     �  ;   �  =     �   C  )   �  /   
  B   :  7   }  (   �     �  	   �  
    A   !  �   P!  @   �!  g   *"  X   �"  }   �"  O   i#  �   �#  m   �$  f    %  �   �%  �   4&  �   �&  G   �'  I   �'  [   (  H   y(  +   �(  G   �(     6)  E   Q)  6   �)  <   �)  -   *  E   9*  6   *  H   �*  B   �*  m   B+  H   �+  :   �+  ;   4,  �   p,  �   -  8   �-  8   �-  ;   .  >   L.  G   �.  9   �.  4   /  4   B/  4   w/  7   �/  ;   �/  >    0  ;   _0  B   �0  E   �0  I   $1  A   n1  )   �1  )   �1  )   2  .   .2  )   ]2  0   �2  ,   �2  )   �2  ,   3  )   <3     f3  D   {3  .  �3  Z   �4  T   J5  4   �5  1   �5  _   6  C   f6  Z   �6     7     7  <   -7  s   j7  \   �7  =   ;8  :   y8  c   �8  1   9  o   J9  [   �9  :   :  H   Q:  4   �:  [   �:  z   +;  >   �;  8   �;  N   <  k   m<  6   �<  D   =     U=     n=  3   }=     �=  ?   �=  7   >  P   >>  _   �>  )   �>  -   ?  	   G?  c   Q?  g   �?  )   @     G@  �   O@  �   �@  �  tA  4   0C  c   eC  j   �C  T   4D  P   �D  +   �D     E         >   9   E   #   o      T                  1          6           !               \            d       C   `       S   
   p   n   ^   4       F   c   h       U   N   r       (       '       V      t   ,       *      W   B   3   Z       a   u   Y      q   P   v           %          	   e   J      @       A       H          _   G   l           0   m   ?   =   .   $             Q   s                                      [   O       2      5   +       j   M   f       w   8                     X   k       ;   /      7           K       R   ]   D   I   -   &       )   <   "          :       L   i   g       b        

Values to be changed:

 
If these values seem acceptable, use -f to force reset.
 
Report bugs to <%s>.
       --wal-segsize=SIZE           size of WAL segments, in megabytes
   -?, --help                       show this help, then exit
   -O, --multixact-offset=OFFSET    set next multitransaction offset
   -V, --version                    output version information, then exit
   -c, --commit-timestamp-ids=XID,XID
                                   set oldest and newest transactions bearing
                                   commit timestamp (zero means no change)
   -e, --epoch=XIDEPOCH             set next transaction ID epoch
   -f, --force                      force update to be done
   -l, --next-wal-file=WALFILE      set minimum starting location for new WAL
   -m, --multixact-ids=MXID,MXID    set next and oldest multitransaction ID
   -n, --dry-run                    no update, just show what would be done
   -o, --next-oid=OID               set next OID
   -u, --oldest-transaction-id=XID  set oldest transaction ID
   -x, --next-transaction-id=XID    set next transaction ID
  [-D, --pgdata=]DATADIR            data directory
 %s home page: <%s>
 %s resets the PostgreSQL write-ahead log.

 64-bit integers Blocks per segment of large relation: %u
 Bytes per WAL segment:                %u
 Catalog version number:               %u
 Current pg_control values:

 Data page checksum version:           %u
 Database block size:                  %u
 Database system identifier:           %llu
 Date/time type storage:               %s
 File "%s" contains "%s", which is not compatible with this program's version "%s". First log segment after reset:        %s
 Float8 argument passing:              %s
 Guessed pg_control values:

 If you are sure the data directory path is correct, execute
  touch %s
and try again. Is a server running?  If not, delete the lock file and try again. Latest checkpoint's NextMultiOffset:  %u
 Latest checkpoint's NextMultiXactId:  %u
 Latest checkpoint's NextOID:          %u
 Latest checkpoint's NextXID:          %u:%u
 Latest checkpoint's TimeLineID:       %u
 Latest checkpoint's full_page_writes: %s
 Latest checkpoint's newestCommitTsXid:%u
 Latest checkpoint's oldestActiveXID:  %u
 Latest checkpoint's oldestCommitTsXid:%u
 Latest checkpoint's oldestMulti's DB: %u
 Latest checkpoint's oldestMultiXid:   %u
 Latest checkpoint's oldestXID's DB:   %u
 Latest checkpoint's oldestXID:        %u
 Maximum columns in an index:          %u
 Maximum data alignment:               %u
 Maximum length of identifiers:        %u
 Maximum size of a TOAST chunk:        %u
 NextMultiOffset:                      %u
 NextMultiXactId:                      %u
 NextOID:                              %u
 NextXID epoch:                        %u
 NextXID:                              %u
 OID (-o) must not be 0 OldestMulti's DB:                     %u
 OldestMultiXid:                       %u
 OldestXID's DB:                       %u
 OldestXID:                            %u
 Options:
 Size of a large-object chunk:         %u
 The database server was not shut down cleanly.
Resetting the write-ahead log might cause data to be lost.
If you want to proceed anyway, use -f to force reset.
 Try "%s --help" for more information. Usage:
  %s [OPTION]... DATADIR

 WAL block size:                       %u
 Write-ahead log reset
 You must run %s as the PostgreSQL superuser. argument of --wal-segsize must be a number argument of --wal-segsize must be a power of 2 between 1 and 1024 by reference by value cannot be executed by "root" cannot create restricted tokens on this platform: error code %lu could not allocate SIDs: error code %lu could not change directory to "%s": %m could not close directory "%s": %m could not create restricted token: error code %lu could not delete file "%s": %m could not get exit code from subprocess: error code %lu could not load library "%s": error code %lu could not open directory "%s": %m could not open file "%s" for reading: %m could not open file "%s": %m could not open process token: error code %lu could not re-execute with restricted token: error code %lu could not read directory "%s": %m could not read file "%s": %m could not read permissions of directory "%s": %m could not start process for command "%s": error code %lu could not write file "%s": %m data directory is of wrong version detail:  error:  fsync error: %m hint:  invalid argument for option %s lock file "%s" exists multitransaction ID (-m) must not be 0 multitransaction offset (-O) must not be -1 newestCommitTsXid:                    %u
 no data directory specified off oldest multitransaction ID (-m) must not be 0 oldest transaction ID (-u) must be greater than or equal to %u oldestCommitTsXid:                    %u
 on pg_control exists but has invalid CRC; proceed with caution pg_control exists but is broken or wrong version; ignoring it pg_control specifies invalid WAL segment size (%d byte); proceed with caution pg_control specifies invalid WAL segment size (%d bytes); proceed with caution pg_control version number:            %u
 too many command-line arguments (first is "%s") transaction ID (-c) must be either 0 or greater than or equal to 2 transaction ID (-x) must be greater than or equal to %u transaction ID epoch (-e) must not be -1 unexpected empty file "%s" warning:  Project-Id-Version: pg_resetxlog (PostgreSQL current)
Report-Msgid-Bugs-To: pgsql-bugs@lists.postgresql.org
POT-Creation-Date: 2022-08-27 14:52+0300
PO-Revision-Date: 2024-09-05 12:19+0300
Last-Translator: Alexander Lakhin <exclusion@gmail.com>
Language-Team: Russian <pgsql-ru-general@postgresql.org>
Language: ru
MIME-Version: 1.0
Content-Type: text/plain; charset=UTF-8
Content-Transfer-Encoding: 8bit
Plural-Forms: nplurals=3; plural=(n%10==1 && n%100!=11 ? 0 : n%10>=2 && n%10<=4 && (n%100<10 || n%100>=20) ? 1 : 2);
 

Значения, которые будут изменены:

 
Если эти значения всё же приемлемы, выполните сброс принудительно, добавив ключ -f.
 
Об ошибках сообщайте по адресу <%s>.
       --wal-segsize=РАЗМЕР         размер сегментов WAL (в мегабайтах)
   -?, --help                       показать эту справку и выйти
   -O, --multixact-offset=СМЕЩЕНИЕ  задать смещение следующей мультитранзакции
   -V, --version                    показать версию и выйти
   -c, --commit-timestamp-ids=XID,XID
                                   задать старейшую и новейшую транзакции,
                                   несущие метки времени (0 — не менять)
   -e, --epoch=XIDEPOCH             задать эпоху для ID следующей транзакции
   -f, --force                      принудительное выполнение операции
   -l, --next-wal-file=ФАЙЛ_WAL     задать минимальное начальное положение
                                   для нового WAL
   -m, --multixact-ids=MXID,MXID    задать ID следующей и старейшей
                                   мультитранзакции
   -n, --dry-run                    показать, какие действия будут выполнены,
                                   но не выполнять их
   -o, --next-oid=OID               задать следующий OID
   -u, --oldest-transaction-id=XID  задать ID старейшей ID
   -x, --next-transaction-id=XID    задать ID следующей транзакции
  [-D, --pgdata=]КАТ_ДАННЫХ         каталог данных
 Домашняя страница %s: <%s>
 %s сбрасывает журнал предзаписи PostgreSQL.

 64-битные целые Блоков в макс. сегменте отношений:    %u
 Байт в сегменте WAL:                  %u
 Номер версии каталога:                %u
 Текущие значения pg_control:

 Версия контрольных сумм страниц:      %u
 Размер блока БД:                      %u
 Идентификатор системы баз данных:     %llu
 Формат хранения даты/времени:         %s
 Файл "%s" содержит строку "%s", а ожидается версия программы "%s". Первый сегмент журнала после сброса:  %s
 Передача аргумента float8:            %s
 Предполагаемые значения pg_control:

 Если вы уверены, что путь к каталогу данных правильный, выполните
  touch %s
и повторите попытку. Возможно, сервер запущен? Если нет, удалите этот файл и попробуйте снова. NextMultiOffset послед. конт. точки:  %u
 NextMultiXactId послед. конт. точки:  %u
 NextOID последней конт. точки:        %u
 NextXID последней конт. точки:        %u:%u
 Линия времени последней конт. точки:  %u
 Режим full_page_writes последней к.т: %s
 newestCommitTsXid последней к. т.:    %u
 oldestActiveXID последней к. т.:      %u
 oldestCommitTsXid последней к. т.:    %u
 БД с oldestMulti последней к. т.:     %u
 oldestMultiXid последней конт. точки: %u
 БД с oldestXID последней конт. точки: %u
 oldestXID последней конт. точки:      %u
 Макс. число столбцов в индексе:       %u
 Макс. предел выравнивания данных:     %u
 Максимальная длина идентификаторов:   %u
 Максимальный размер порции TOAST:     %u
 NextMultiOffset:                      %u
 NextMultiXactId:                      %u
 NextOID:                              %u
 Эпоха NextXID:                        %u
 NextXID:                              %u
 OID (-o) не должен быть равен 0 БД с oldestMultiXid:                  %u
 OldestMultiXid:                       %u
 БД с oldestXID:                       %u
 OldestXID:                            %u
 Параметры:
 Размер порции большого объекта:       %u
 Сервер баз данных был остановлен некорректно.
Сброс журнала предзаписи может привести к потере данных.
Если вы хотите сбросить его, несмотря на это, добавьте ключ -f.
 Для дополнительной информации попробуйте "%s --help". Использование:
  %s [ПАРАМЕТР]... КАТАЛОГ-ДАННЫХ

 Размер блока WAL:                     %u
 Журнал предзаписи сброшен
 Запускать %s нужно от имени суперпользователя PostgreSQL. аргументом --wal-segsize должно быть число аргументом --wal-segsize должна быть степень 2 от 1 до 1024 по ссылке по значению программу не должен запускать root в этой ОС нельзя создавать ограниченные маркеры (код ошибки: %lu) не удалось подготовить структуры SID (код ошибки: %lu) не удалось перейти в каталог "%s": %m не удалось закрыть каталог "%s": %m не удалось создать ограниченный маркер (код ошибки: %lu) ошибка удаления файла "%s": %m не удалось получить код выхода от подпроцесса (код ошибки: %lu) не удалось загрузить библиотеку "%s" (код ошибки: %lu) не удалось открыть каталог "%s": %m не удалось открыть файл "%s" для чтения: %m не удалось открыть файл "%s": %m не удалось открыть маркер процесса (код ошибки: %lu) не удалось перезапуститься с ограниченным маркером (код ошибки: %lu) не удалось прочитать каталог "%s": %m не удалось прочитать файл "%s": %m не удалось прочитать права на каталог "%s": %m не удалось запустить процесс для команды "%s" (код ошибки: %lu) не удалось записать файл "%s": %m каталог данных имеет неверную версию подробности:  ошибка:  ошибка синхронизации с ФС: %m подсказка:  недопустимый аргумент параметра %s файл блокировки "%s" существует ID мультитранзакции (-m) не должен быть равен 0 смещение мультитранзакции (-O) не должно быть равно -1 newestCommitTsXid:                    %u
 каталог данных не указан выкл. ID старейшей мультитранзакции (-m) не должен быть равен 0 ID старейшей транзакции (-u) должен быть больше или равен %u oldestCommitTsXid:                    %u
 вкл. pg_control существует, но его контрольная сумма неверна; продолжайте с осторожностью pg_control испорчен или имеет неизвестную либо недопустимую версию; игнорируется... в pg_control указан некорректный размер сегмента WAL (%d Б); продолжайте с осторожностью в pg_control указан некорректный размер сегмента WAL (%d Б); продолжайте с осторожностью в pg_control указан некорректный размер сегмента WAL (%d Б); продолжайте с осторожностью Номер версии pg_control:              %u
 слишком много аргументов командной строки (первый: "%s") ID транзакции (-c) должен быть равен 0, либо больше или равен 2 ID транзакции (-x) должен быть больше или равен %u эпоха ID транзакции (-e) не должна быть равна -1 файл "%s" оказался пустым предупреждение:  