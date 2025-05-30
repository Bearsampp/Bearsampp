��    8      �  O   �      �  X   �  
   2     =  5   Y  P   �  5   �  A     :   X  2   �  1   �  G   �  3   @  *   t     �  T   �          "     6     J     h     �     �     �     �  k   �  &   V	     }	  a   �	     �	     
  ;   &
     b
  !   |
     �
  (   �
  3   �
       )   5  5   _  .   �  -   �  )   �  "        ?     G  3   O  +   �     �  2   �  !   �  )         J  /   a     �  	   �  �  �  �   I     �  ,     P   5  r   �  R   �  G   L  w   �  P     N   ]  �   �  R   3  @   �  '   �  �   �  +   �  7   �  .   �  F     I   [  G   �  >   �  .   ,  ?   [  �   �  [   i     �  �   �  E   �  G   �  M     5   i  :   �  4   �  I     o   Y  P   �  E     k   `  O   �  M     K   j  =   �     �       `     M   q  -   �  Z   �  D   H  L   �  8   �  c     :   w     �     8          .   )                   !   #               /          4   (   *             ,       0       3      &                               7   6          1            2   '       $                          +              "              
          %             5      -   	        
If no data directory (DATADIR) is specified, the environment variable PGDATA
is used.

 
Options:
   %s [OPTION]... [DATADIR]
   -?, --help               show this help, then exit
   -N, --no-sync            do not wait for changes to be written safely to disk
   -P, --progress           show progress information
   -V, --version            output version information, then exit
   -c, --check              check data checksums (default)
   -d, --disable            disable data checksums
   -e, --enable             enable data checksums
   -f, --filenode=FILENODE  check only relation with specified filenode
   -v, --verbose            output verbose messages
  [-D, --pgdata=]DATADIR    data directory
 %*s/%s MB (%d%%) computed %s enables, disables, or verifies data checksums in a PostgreSQL database cluster.

 %s home page: <%s>
 Bad checksums:  %s
 Blocks scanned: %s
 Checksum operation completed
 Checksums disabled in cluster
 Checksums enabled in cluster
 Data checksum version: %u
 Files scanned:  %s
 Report bugs to <%s>.
 The database cluster was initialized with block size %u, but pg_checksums was compiled with block size %u.
 Try "%s --help" for more information.
 Usage:
 checksum verification failed in file "%s", block %u: calculated checksum %X but block contains %X checksums enabled in file "%s" checksums verified in file "%s" cluster is not compatible with this version of pg_checksums cluster must be shut down could not open directory "%s": %m could not open file "%s": %m could not read block %u in file "%s": %m could not read block %u in file "%s": read %d of %d could not stat file "%s": %m could not write block %u in file "%s": %m could not write block %u in file "%s": wrote %d of %d data checksums are already disabled in cluster data checksums are already enabled in cluster data checksums are not enabled in cluster database cluster is not compatible error:  fatal:  invalid filenode specification, must be numeric: %s invalid segment number %d in file name "%s" no data directory specified option -f/--filenode can only be used with --check pg_control CRC value is incorrect seek failed for block %u in file "%s": %m syncing data directory too many command-line arguments (first is "%s") updating control file warning:  Project-Id-Version: pg_verify_checksums (PostgreSQL) 11
Report-Msgid-Bugs-To: pgsql-bugs@lists.postgresql.org
POT-Creation-Date: 2024-11-09 07:48+0300
PO-Revision-Date: 2021-02-08 07:59+0300
Last-Translator: Alexander Lakhin <exclusion@gmail.com>
Language-Team: Russian <pgsql-ru-general@postgresql.org>
Language: ru
MIME-Version: 1.0
Content-Type: text/plain; charset=UTF-8
Content-Transfer-Encoding: 8bit
 
Если каталог данных (КАТ_ДАННЫХ) не задан, используется значение
переменной окружения PGDATA.

 
Параметры:
   %s [ПАРАМЕТР]... [КАТАЛОГ]
   -?, --help               показать эту справку и выйти
   -N, --no-sync            не ждать завершения сохранения данных на диске
   -P, --progress           показывать прогресс операции
   -V, --version            показать версию и выйти
   -c, --check              проверить контрольные суммы данных (по умолчанию)
   -d, --disable            отключить контрольные суммы
   -e, --enable             включить контрольные суммы
   -f, --filenode=ФАЙЛ_УЗЕЛ проверить только отношение с заданным файловым узлом
   -v, --verbose            выводить подробные сообщения
  [-D, --pgdata=]КАТ_ДАННЫХ каталог данных
 %*s/%s МБ (%d%%) обработано %s включает, отключает, проверяет контрольные суммы данных в кластере БД PostgreSQL.

 Домашняя страница %s: <%s>
 Неверные контрольные суммы: %s
 Просканировано блоков: %s
 Обработка контрольных сумм завершена
 Контрольные суммы в кластере отключены
 Контрольные суммы в кластере включены
 Версия контрольных сумм данных: %u
 Просканировано файлов: %s
 Об ошибках сообщайте по адресу <%s>.
 Кластер баз данных был инициализирован с размером блока %u, а утилита pg_checksums скомпилирована для размера блока %u.
 Для дополнительной информации попробуйте "%s --help".
 Использование:
 ошибка контрольных сумм в файле "%s", блоке %u: вычислена контрольная сумма %X, но блок содержит %X контрольные суммы в файле "%s" включены контрольные суммы в файле "%s" проверены кластер несовместим с этой версией pg_checksums кластер должен быть отключён не удалось открыть каталог "%s": %m не удалось открыть файл "%s": %m не удалось прочитать блок %u в файле "%s": %m не удалось прочитать блок %u в файле "%s" (прочитано байт: %d из %d) не удалось получить информацию о файле "%s": %m не удалось записать блок %u в файл "%s": %m не удалось записать блок %u в файле "%s" (записано байт: %d из %d) контрольные суммы в кластере уже отключены контрольные суммы в кластере уже включены контрольные суммы в кластере не включены несовместимый кластер баз данных ошибка:  важно:  неверное указание файлового узла, требуется число: %s неверный номер сегмента %d в имени файла "%s" каталог данных не указан параметр -f/--filenode можно использовать только с --check ошибка контрольного значения в pg_control ошибка при переходе к блоку %u в файле "%s": %m синхронизация каталога данных слишком много аргументов командной строки (первый: "%s") модификация управляющего файла предупреждение:  