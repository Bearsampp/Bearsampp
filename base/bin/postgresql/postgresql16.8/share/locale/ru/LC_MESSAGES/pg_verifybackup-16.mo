��    i      d  �   �       	     	  8   	  8   Q	  D   �	  8   �	  4   
  >   =
  <   |
  I   �
  9     ?   =  7   }     �  /   �  /     1   5     g     �  3   �  ,   �  !   �  $     $   =     b  $   �  .   �  &   �  '   �      #  	   D  $   N     s  %   �  I   �  d   �  8   _  3   �  #   �  "   �  #        7  $   U  /   z     �     �     �  "        &     D  (   _  '   �  *   �  )   �  !        '  #   D     h     �     �  )   �     �  )   �  &   (  %   O     u  ,   ~     �     �     �  4   �  6        T     p  $   w     �      �     �     �          -     =     O     \     n          �     �     �  L   �  A        X  /   s     �     �     �     �                2     J     b  %   t     �  	   �  *  �  @   �  S   -  U   �  J   �  T   "  �   w  h   �  V   e  o   �  d   ,  i   �  v   �  @   r  i   �  i     e   �  %   �  +      f   ?   P   �   E   �   B   =!  B   �!  7   �!  I   �!  Z   E"  `   �"  K   #  @   M#     �#  >   �#  '   �#  Z   
$  k   e$  �   �$  �   _%  v   �%  R   ]&  N   �&  =   �&  -   ='  Q   k'  p   �'  =   .(  E   l(  Q   �(  :   )  4   ?)  >   t)  e   �)  h   *  m   �*  p   �*  :   a+  4   �+  F   �+  ;   ,  =   T,  8   �,  `   �,  P   ,-  h   }-  g   �-  j   N.     �.  J   �.     /  7   ,/  0   d/  �   �/  n   0  H   �0     �0  O   �0  N   61  f   �1  A   �1  4   .2  >   c2  +   �2  0   �2  6   �2  -   63  0   d3  +   �3     �3     �3  "   �3  f   !4  �   �4  C   5  c   O5  J   �5  0   �5  2   /6  1   b6  6   �6  0   �6  .   �6  2   +7  :   ^7  b   �7  F   �7     C8     0             U   &       1       :   (   V          T       S           5   6   +      W          *      7                            X       '   =      R                        /              Z   @   ,   ?              ]   `   Y   4           -       C          O      P       I       A   [               >   M       E   F   d   "       b   G          9      g      L   e   
   !          J   c   D   \          B   .   2       Q   #          ;   f   H   )           _   a       	           $   <   %   ^             N   i      K   h             8           3    
Report bugs to <%s>.
   -?, --help                  show this help, then exit
   -P, --progress              show progress information
   -V, --version               output version information, then exit
   -e, --exit-on-error         exit immediately on error
   -i, --ignore=RELATIVE_PATH  ignore indicated path
   -m, --manifest-path=PATH    use specified path for manifest
   -n, --no-parse-wal          do not try to parse WAL files
   -q, --quiet                 do not print any output, except for errors
   -s, --skip-checksums        skip checksum verification
   -w, --wal-directory=PATH    use specified path for WAL files
 "%s" has size %lld on disk but size %zu in the manifest "%s" is not a file or directory "%s" is present in the manifest but not on disk "%s" is present on disk but not in the manifest "\u" must be followed by four hexadecimal digits. %*s/%s kB (%d%%) verified %s home page: <%s>
 %s verifies a backup against the backup manifest.

 Character with value 0x%02x must be escaped. Escape sequence "\%s" is invalid. Expected "," or "]", but found "%s". Expected "," or "}", but found "%s". Expected ":", but found "%s". Expected JSON value, but found "%s". Expected array element or "]", but found "%s". Expected end of input, but found "%s". Expected string or "}", but found "%s". Expected string, but found "%s". Options:
 The input string ended unexpectedly. Token "%s" is invalid. Try "%s --help" for more information. Unicode escape value could not be translated to the server's encoding %s. Unicode escape values cannot be used for code point values above 007F when the encoding is not UTF8. Unicode high surrogate must not follow a high surrogate. Unicode low surrogate must follow a high surrogate. Usage:
  %s [OPTION]... BACKUPDIR

 WAL parsing failed for timeline %u \u0000 cannot be converted to text. backup successfully verified
 both path name and encoded path name cannot duplicate null pointer (internal error)
 cannot specify both %s and %s checksum mismatch for file "%s" checksum without algorithm could not close directory "%s": %m could not close file "%s": %m could not decode file name could not finalize checksum of file "%s" could not finalize checksum of manifest could not initialize checksum of file "%s" could not initialize checksum of manifest could not open directory "%s": %m could not open file "%s": %m could not parse backup manifest: %s could not parse end LSN could not parse start LSN could not read file "%s": %m could not read file "%s": read %d of %lld could not stat file "%s": %m could not stat file or directory "%s": %m could not update checksum of file "%s" could not update checksum of manifest detail:  duplicate path name in backup manifest: "%s" error:  expected at least 2 lines expected version indicator file "%s" has checksum of length %d, but expected %d file "%s" should contain %zu bytes, but read %zu bytes file size is not an integer hint:  invalid checksum for file "%s": "%s" invalid manifest checksum: "%s" last line not newline-terminated manifest checksum mismatch manifest ended unexpectedly manifest has no checksum missing end LSN missing path name missing size missing start LSN missing timeline no backup directory specified out of memory out of memory
 parsing failed program "%s" is needed by %s but was not found in the same directory as "%s" program "%s" was found by "%s" but was not the same version as %s timeline is not an integer too many command-line arguments (first is "%s") unexpected WAL range field unexpected array end unexpected array start unexpected file field unexpected manifest version unexpected object end unexpected object field unexpected object start unexpected scalar unrecognized checksum algorithm: "%s" unrecognized top-level field warning:  Project-Id-Version: pg_verifybackup (PostgreSQL) 13
Report-Msgid-Bugs-To: pgsql-bugs@lists.postgresql.org
POT-Creation-Date: 2023-08-28 07:59+0300
PO-Revision-Date: 2024-09-07 09:48+0300
Last-Translator: Alexander Lakhin <a.lakhin@postgrespro.ru>
Language-Team: Russian <pgsql-ru-general@postgresql.org>
Language: ru
MIME-Version: 1.0
Content-Type: text/plain; charset=UTF-8
Content-Transfer-Encoding: 8bit
Plural-Forms: nplurals=3; plural=(n%10==1 && n%100!=11 ? 0 : n%10>=2 && n%10<=4 && (n%100<10 || n%100>=20) ? 1 : 2);
X-Generator: Lokalize 19.12.3
 
Об ошибках сообщайте по адресу <%s>.
   -?, --help                  показать эту справку и выйти
   -P, --progress              показывать прогресс операции
   -V, --version               показать версию и выйти
   -e, --exit-on-error         немедленный выход при ошибке
   -i, --ignore=ОТНОСИТЕЛЬНЫЙ_ПУТЬ
                              игнорировать заданный путь
   -m, --manifest-path=ПУТЬ    использовать заданный файл манифеста
   -n, --no-parse-wal          не пытаться разбирать файлы WAL
   -q, --quiet                 не выводить никаких сообщений, кроме ошибок
   -s, --skip-checksums        пропустить проверку контрольных сумм
   -w, --wal-directory=ПУТЬ    использовать заданный путь к файлам WAL
 файл "%s" имеет размер на диске: %lld, тогда как размер в манифесте: %zu "%s" не указывает на файл или каталог файл "%s" присутствует в манифесте, но отсутствует на диске файл "%s" присутствует на диске, но отсутствует в манифесте За "\u" должны следовать четыре шестнадцатеричные цифры. %*s/%s КБ (%d%%) проверено Домашняя страница %s: <%s>
 %s проверяет резервную копию, используя манифест копии.

 Символ с кодом 0x%02x необходимо экранировать. Неверная спецпоследовательность: "\%s". Ожидалась "," или "]", но обнаружено "%s". Ожидалась "," или "}", но обнаружено "%s". Ожидалось ":", но обнаружено "%s". Ожидалось значение JSON, но обнаружено "%s". Ожидался элемент массива или "]", но обнаружено "%s". Ожидался конец текста, но обнаружено продолжение "%s". Ожидалась строка или "}", но обнаружено "%s". Ожидалась строка, но обнаружено "%s". Параметры:
 Неожиданный конец входной строки. Ошибочный элемент "%s". Для дополнительной информации попробуйте "%s --help". Спецкод Unicode нельзя преобразовать в серверную кодировку %s. Спецкоды Unicode для значений выше 007F можно использовать только с кодировкой UTF8. Старшее слово суррогата Unicode не может следовать за другим старшим словом. Младшее слово суррогата Unicode должно следовать за старшим словом. Использование:
  %s [ПАРАМЕТР]... КАТАЛОГ_КОПИИ

 не удалось разобрать WAL для линии времени %u \u0000 нельзя преобразовать в текст. копия проверена успешно
 путь задан в обычном виде и в закодированном попытка дублирования нулевого указателя (внутренняя ошибка)
 указать %s и %s одновременно нельзя ошибка контрольной суммы для файла "%s" не задан алгоритм расчёта контрольной суммы не удалось закрыть каталог "%s": %m не удалось закрыть файл "%s": %m не удалось декодировать имя файла не удалось завершить расчёт контрольной суммы файла "%s" не удалось завершить расчёт контрольной суммы манифеста не удалось подготовить контекст контрольной суммы файла "%s" не удалось подготовить контекст контрольной суммы манифеста не удалось открыть каталог "%s": %m не удалось открыть файл "%s": %m не удалось разобрать манифест копии: %s не удалось разобрать конечный LSN не удалось разобрать начальный LSN не удалось прочитать файл "%s": %m не удалось прочитать файл "%s" (прочитано байт: %d из %lld) не удалось получить информацию о файле "%s": %m не удалось получить информацию о файле или каталоге "%s": %m не удалось изменить контекст контрольной суммы файла "%s" не удалось изменить контекст контрольной суммы манифеста подробности:  дублирующийся путь в манифесте копии: "%s" ошибка:  ожидалось как минимум 2 строки ожидалось указание версии для файла "%s" задана контрольная сумма размером %d, но ожидаемый размер: %d файл "%s" должен содержать байт: %zu, но фактически прочитано: %zu размер файла не является целочисленным подсказка:  неверная контрольная сумма для файла "%s": "%s" неверная контрольная сумма в манифесте: "%s" последняя строка не оканчивается символом новой строки ошибка контрольной суммы манифеста неожиданный конец манифеста в манифесте нет контрольной суммы отсутствует конечный LSN отсутствует указание пути отсутствует указание размера отсутствует начальный LSN отсутствует линия времени каталог копии не указан нехватка памяти нехватка памяти
 ошибка при разборе программа "%s" нужна для %s, но она не найдена в каталоге "%s" программа "%s" найдена программой "%s", но её версия отличается от версии %s линия времени задана не целым числом слишком много аргументов командной строки (первый: "%s") неизвестное поле в указании диапазона WAL неожиданный конец массива неожиданное начало массива неизвестное поле для файла неожиданная версия манифеста неожиданный конец объекта неожиданное поле объекта неожиданное начало объекта неожиданное скалярное значение нераспознанный алгоритм расчёта контрольных сумм: "%s" нераспознанное поле на верхнем уровне предупреждение:  