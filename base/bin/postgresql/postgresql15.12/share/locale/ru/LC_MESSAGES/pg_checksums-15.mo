��    <      �  S   �      (  X   )  
   �     �  5   �  P   �  5   0  A   f  :   �  2   �  1     G   H  3   �  *   �     �  T        a     u     �     �     �     �     �     	     .	     I	     `	     v	  j   �	  %   �	     
  a   %
     �
     �
  ;   �
       !        >  (   [  3   �     �  )   �  5   �  .   5  -   d  )   �  "   �     �     �     �  +   �      #     D  2   `  !   �  )   �     �  /   �     &  	   <  �  F  �   �     �  ,   �  P   �  r     R   �  G   �  w   )  P   �  N   �  �   A  R   �  @     *   \  �   �  +     F   E  9   �  0   �  $   �  F     I   c  G   �  >   �  0   4  $   e  ?   �  �   �  Z   �     �  �     E   �  G      M   H  5   �  :   �  4     I   <  o   �  P   �  E   G  k   �  O   �  M   I  K   �  =   �     !     :     I  M   ^  C   �  -   �  Z     D   y  L   �  8      c   D   :   �      �      &          %   ,                   *   :            )   7                            8           0   .   1      5                                       3      /          <   #      ;      4         "      '          $   2       	   !          6   9       -          
   +                     (        
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
 %lld/%lld MB (%d%%) computed %s enables, disables, or verifies data checksums in a PostgreSQL database cluster.

 %s home page: <%s>
 %s must be in range %d..%d Bad checksums:  %lld
 Blocks scanned:  %lld
 Blocks written: %lld
 Checksum operation completed
 Checksums disabled in cluster
 Checksums enabled in cluster
 Data checksum version: %u
 Files scanned:   %lld
 Files written:  %lld
 Report bugs to <%s>.
 The database cluster was initialized with block size %u, but pg_checksums was compiled with block size %u. Try "%s --help" for more information. Usage:
 checksum verification failed in file "%s", block %u: calculated checksum %X but block contains %X checksums enabled in file "%s" checksums verified in file "%s" cluster is not compatible with this version of pg_checksums cluster must be shut down could not open directory "%s": %m could not open file "%s": %m could not read block %u in file "%s": %m could not read block %u in file "%s": read %d of %d could not stat file "%s": %m could not write block %u in file "%s": %m could not write block %u in file "%s": wrote %d of %d data checksums are already disabled in cluster data checksums are already enabled in cluster data checksums are not enabled in cluster database cluster is not compatible detail:  error:  hint:  invalid segment number %d in file name "%s" invalid value "%s" for option %s no data directory specified option -f/--filenode can only be used with --check pg_control CRC value is incorrect seek failed for block %u in file "%s": %m syncing data directory too many command-line arguments (first is "%s") updating control file warning:  Project-Id-Version: pg_verify_checksums (PostgreSQL) 11
Report-Msgid-Bugs-To: pgsql-bugs@lists.postgresql.org
POT-Creation-Date: 2024-11-09 07:47+0300
PO-Revision-Date: 2022-09-05 13:34+0300
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
 %lld/%lld МБ (%d%%) обработано %s включает, отключает, проверяет контрольные суммы данных в кластере БД PostgreSQL.

 Домашняя страница %s: <%s>
 значение %s должно быть в диапазоне %d..%d Неверные контрольные суммы: %lld
 Просканировано блоков: %lld
 Записано блоков: %lld
 Обработка контрольных сумм завершена
 Контрольные суммы в кластере отключены
 Контрольные суммы в кластере включены
 Версия контрольных сумм данных: %u
 Просканировано файлов: %lld
 Записано файлов: %lld
 Об ошибках сообщайте по адресу <%s>.
 Кластер баз данных был инициализирован с размером блока %u, а утилита pg_checksums скомпилирована для размера блока %u. Для дополнительной информации попробуйте "%s --help". Использование:
 ошибка контрольных сумм в файле "%s", блоке %u: вычислена контрольная сумма %X, но блок содержит %X контрольные суммы в файле "%s" включены контрольные суммы в файле "%s" проверены кластер несовместим с этой версией pg_checksums кластер должен быть отключён не удалось открыть каталог "%s": %m не удалось открыть файл "%s": %m не удалось прочитать блок %u в файле "%s": %m не удалось прочитать блок %u в файле "%s" (прочитано байт: %d из %d) не удалось получить информацию о файле "%s": %m не удалось записать блок %u в файл "%s": %m не удалось записать блок %u в файле "%s" (записано байт: %d из %d) контрольные суммы в кластере уже отключены контрольные суммы в кластере уже включены контрольные суммы в кластере не включены несовместимый кластер баз данных подробности:  ошибка:  подсказка:  неверный номер сегмента %d в имени файла "%s" неверное значение "%s" для параметра %s каталог данных не указан параметр -f/--filenode можно использовать только с --check ошибка контрольного значения в pg_control ошибка при переходе к блоку %u в файле "%s": %m синхронизация каталога данных слишком много аргументов командной строки (первый: "%s") модификация управляющего файла предупреждение:  