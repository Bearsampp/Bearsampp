��    b      ,  �   <      H      I     j     �     �     �     �  S   �  H   (	  V   q	  =   �	  A   
  U   H
  Z   �
  K   �
  M   E  I   �  I   �  T   '  T   |     �  <   �  D   )  B   n  <   �  D   �  B   3  A   v  :   �  H   �  8   <  6   u  =   �  M   �  K   8  ;   �  U   �  7     =   N  ;   �  :   �  8     <   <  ,   y  0   �  7   �       <        O     c  +   ~     �     �     �     �  %   �     #     +  V   D  )   �  9   �     �       /   >     n     �     �     �  *   �     �  :   �  ,   .  !   [     }     �  3   �  2   �  ;        ?  :   W  :   �     �     �     �        '   3  /   [     �  %   �     �  .   �  #        0     A  0   P     �  /   �  	   �  �  �  J   d  ,   �  "   �  @   �  3   @  3   t     �  _   (  �   �  c   )  Y   �  �   �  �   i  �   �  �   }   t   "!  �   �!  x   "  �   �"  )   #  W   B#  w   �#  }   $  Y   �$  �   �$  y   l%  }   �%  u   d&  N   �&  B   )'  E   l'  r   �'  o   %(  �   �(  x   )  �   �)  R   *  |   o*  t   �*  x   a+  a   �+  K   <,  @   �,  D   �,  K   -     Z-  v   ]-  +   �-  F    .  5   G.  -   }.  )   �.  B   �.  %   /  Z   >/     �/     �/  �   �/  C   c0  n   �0  0   1  9   G1  [   �1     �1     �1  F   2  G   \2  >   �2     �2  _   �2  L   R3  7   �3  *   �3     4  [   4  a   s4  T   �4  "   *5  ~   M5  �   �5  ?   U6  *   �6  ,   �6  C   �6  s   17  �   �7  4   /8  |   d8  >   �8  �    9  q   �9  ;   :     P:  `   b:  H   �:  c   ;     p;                           8   ^            $   @   1   b      Y       3           W           )          C      [   R               !   X   O   Q              0   D      "   7   .   ;   =   A      /   
   ?   P                 6   N          &   	       2   H             #      -       %   >          '      J       M   ]          T   +   (       G      S   9       `       B           4       U       ,       V   *      :   F   5   I         L      \   _      <          a      K   Z       E    
B-tree index checking options:
 
Connection options:
 
Other options:
 
Report bugs to <%s>.
 
Table checking options:
 
Target options:
       --endblock=BLOCK            check table(s) only up to the given block number
       --exclude-toast-pointers    do NOT follow relation TOAST pointers
       --heapallindexed            check that all heap tuples are found within indexes
       --install-missing           install missing extensions
       --maintenance-db=DBNAME     alternate maintenance database
       --no-dependent-indexes      do NOT expand list of relations to include indexes
       --no-dependent-toast        do NOT expand list of relations to include TOAST tables
       --no-strict-names           do NOT require patterns to match objects
       --on-error-stop             stop checking at end of first corrupt page
       --parent-check              check index parent/child relationships
       --rootdescend               search from root page to refind tuples
       --skip=OPTION               do NOT check "all-frozen" or "all-visible" blocks
       --startblock=BLOCK          begin checking table(s) at the given block number
   %s [OPTION]... [DBNAME]
   -?, --help                      show this help, then exit
   -D, --exclude-database=PATTERN  do NOT check matching database(s)
   -I, --exclude-index=PATTERN     do NOT check matching index(es)
   -P, --progress                  show progress information
   -R, --exclude-relation=PATTERN  do NOT check matching relation(s)
   -S, --exclude-schema=PATTERN    do NOT check matching schema(s)
   -T, --exclude-table=PATTERN     do NOT check matching table(s)
   -U, --username=USERNAME         user name to connect as
   -V, --version                   output version information, then exit
   -W, --password                  force password prompt
   -a, --all                       check all databases
   -d, --database=PATTERN          check matching database(s)
   -e, --echo                      show the commands being sent to the server
   -h, --host=HOSTNAME             database server host or socket directory
   -i, --index=PATTERN             check matching index(es)
   -j, --jobs=NUM                  use this many concurrent connections to the server
   -p, --port=PORT                 database server port
   -r, --relation=PATTERN          check matching relation(s)
   -s, --schema=PATTERN            check matching schema(s)
   -t, --table=PATTERN             check matching table(s)
   -v, --verbose                   write a lot of output
   -w, --no-password               never prompt for password
 %*s/%s relations (%d%%), %*s/%s pages (%d%%) %*s/%s relations (%d%%), %*s/%s pages (%d%%) %*s %*s/%s relations (%d%%), %*s/%s pages (%d%%) (%s%-*.*s) %s %s checks objects in a PostgreSQL database for corruption.

 %s home page: <%s>
 %s must be in range %d..%d Are %s's and amcheck's versions compatible? Cancel request sent
 Command was: %s Could not send cancel request:  Query was: %s Try "%s --help" for more information. Usage:
 btree index "%s.%s.%s":
 btree index "%s.%s.%s": btree checking function returned unexpected number of rows: %d cannot specify a database name with --all cannot specify both a database name and database patterns checking btree index "%s.%s.%s" checking heap table "%s.%s.%s" could not connect to database %s: out of memory database "%s": %s detail:  end block out of bounds end block precedes start block error sending command to database "%s": %s error:  heap table "%s.%s.%s", block %s, offset %s, attribute %s:
 heap table "%s.%s.%s", block %s, offset %s:
 heap table "%s.%s.%s", block %s:
 heap table "%s.%s.%s":
 hint:  improper qualified name (too many dotted names): %s improper relation name (too many dotted names): %s in database "%s": using amcheck version "%s" in schema "%s" including database "%s" internal error: received unexpected database pattern_id %d internal error: received unexpected relation pattern_id %d invalid argument for option %s invalid end block invalid start block invalid value "%s" for option %s no btree indexes to check matching "%s" no connectable databases to check matching "%s" no databases to check no heap tables to check matching "%s" no relations to check no relations to check in schemas matching "%s" no relations to check matching "%s" query failed: %s query was: %s
 skipping database "%s": amcheck is not installed start block out of bounds too many command-line arguments (first is "%s") warning:  Project-Id-Version: pg_amcheck (PostgreSQL) 14
Report-Msgid-Bugs-To: pgsql-bugs@lists.postgresql.org
POT-Creation-Date: 2022-08-27 14:52+0300
PO-Revision-Date: 2024-09-05 08:23+0300
Last-Translator: Alexander Lakhin <exclusion@gmail.com>
Language-Team: Russian <pgsql-ru-general@postgresql.org>
Language: ru
MIME-Version: 1.0
Content-Type: text/plain; charset=UTF-8
Content-Transfer-Encoding: 8bit
 
Параметры проверки индексов-B-деревьев:
 
Параметры подключения:
 
Другие параметры:
 
Об ошибках сообщайте по адресу <%s>.
 
Параметры проверки таблиц:
 
Параметры выбора объектов:
       --endblock=БЛОК             проверить таблицы(у) до блока с заданным номером
       --exclude-toast-pointers    не переходить по указателям в TOAST
       --heapallindexed            проверить, что всем кортежам кучи находится соответствие в индексах
       --install-missing           установить недостающие расширения
       --maintenance-db=ИМЯ_БД     другая опорная база данных
       --no-dependent-indexes      не включать в список проверяемых отношений индексы
       --no-dependent-toast        не включать в список проверяемых отношений TOAST-таблицы
       --no-strict-names           не требовать наличия объектов, соответствующих шаблонам
       --on-error-stop             прекратить проверку по достижении конца первой повреждённой страницы
       --parent-check              проверить связи родитель/потомок в индексах
       --rootdescend               перепроверять поиск кортежей от корневой страницы
       --skip=ТИП_БЛОКА            не проверять блоки типа "all-frozen" или "all-visible"
       --startblock=БЛОК           начать проверку таблиц(ы) с блока с заданным номером
   %s [ПАРАМЕТР]... [ИМЯ_БД]
   -?, --help                      показать эту справку и выйти
   -D, --exclude-database=ШАБЛОН   не проверять соответствующие шаблону базы
   -I, --exclude-index=ШАБЛОН      не проверять соответствующие шаблону индексы
   -P, --progress                  показывать прогресс операции
   -R, --exclude-relation=ШАБЛОН   не проверять соответствующие шаблону отношения
   -S, --exclude-schema=ШАБЛОН     не проверять соответствующие шаблону схемы
   -T, --exclude-table=ШАБЛОН      не проверять соответствующие шаблону таблицы
   -U, --username=ИМЯ              имя пользователя для подключения к серверу
   -V, --version                   показать версию и выйти
   -W, --password                  запросить пароль
   -a, --all                       проверить все базы
   -d, --database=ШАБЛОН           проверить соответствующие шаблону базы
   -e, --echo                      отображать команды, отправляемые серверу
   -h, --host=ИМЯ                  компьютер с сервером баз данных или каталог сокетов
   -i, --index=ШАБЛОН              проверить соответствующие шаблону индексы
   -j, --jobs=ЧИСЛО                устанавливать заданное число подключений к серверу
   -p, --port=ПОРТ                 порт сервера баз данных
   -r, --relation=ШАБЛОН           проверить соответствующие шаблону отношения
   -s, --schema=ШАБЛОН             проверить соответствующие шаблону схемы
   -t, --table=ШАБЛОН              проверить соответствующие шаблону таблицы
   -v, --verbose                   выводить исчерпывающие сообщения
   -w, --no-password               не запрашивать пароль
 отношений: %*s/%s (%d%%), страниц: %*s/%s (%d%%) отношений: %*s/%s (%d%%), страниц: %*s/%s (%d%%) %*s отношений: %*s/%s (%d%%), страниц: %*s/%s (%d%%) (%s%-*.*s) %s %s проверяет объекты в базе данных PostgreSQL на предмет повреждений.

 Домашняя страница %s: <%s>
 значение %s должно быть в диапазоне %d..%d Совместимы ли версии %s и amcheck? Сигнал отмены отправлен
 Выполнялась команда: %s Отправить сигнал отмены не удалось:  Выполнялся запрос: %s Для дополнительной информации попробуйте "%s --help". Использование:
 индекс btree "%s.%s.%s":
 индекс btree "%s.%s.%s": функция проверки btree выдала неожиданное количество строк: %d имя базы данных нельзя задавать с --all нельзя задавать одновременно имя базы данных и шаблоны имён проверка индекса btree "%s.%s.%s" проверка базовой таблицы "%s.%s.%s" не удалось подключиться к базе %s (нехватка памяти) база данных "%s": %s подробности:  конечный блок вне допустимых пределов конечный блок предшествует начальному ошибка передачи команды базе "%s": %s ошибка:  базовая таблица "%s.%s.%s", блок %s, смещение %s, атрибут %s:
 базовая таблица "%s.%s.%s", блок %s, смещение %s:
 базовая таблица "%s.%s.%s", блок %s:
 базовая таблица "%s.%s.%s":
 подсказка:  неверное полное имя (слишком много компонентов): %s неверное имя отношения (слишком много компонентов): %s база "%s": используется amcheck версии "%s" в схеме "%s" выбирается база "%s" внутренняя ошибка: получен неожиданный идентификатор шаблона базы %d внутренняя ошибка: получен неожиданный идентификатор шаблона отношения %d недопустимый аргумент параметра %s неверный конечный блок неверный начальный блок неверное значение "%s" для параметра %s не найдены подлежащие проверке индексы btree, соответствующие "%s" не найдены подлежащие проверке доступные базы, соответствующие шаблону "%s" не указаны базы для проверки не найдены подлежащие проверке базовые таблицы, соответствующие "%s" не найдены отношения для проверки не найдены подлежащие проверке отношения в схемах, соответствующих "%s" не найдены подлежащие проверке отношения, соответствующие "%s" ошибка при выполнении запроса: %s запрос: %s
 база "%s" пропускается: расширение amcheck не установлено начальный блок вне допустимых пределов слишком много аргументов командной строки (первый: "%s") предупреждение:  