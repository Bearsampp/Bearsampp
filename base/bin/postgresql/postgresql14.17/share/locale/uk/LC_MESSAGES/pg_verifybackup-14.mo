��    c      4  �   L      p     q  8   �  D   �  8   	  4   ?	  >   t	  <   �	  I   �	  9   :
  ?   t
  7   �
     �
  /     /   <  1   l     �  3   �  ,   �  !     $   5  $   Z       $   �  .   �  &   �  '         @  	   a  $   k  j   �  _   �     [  &   r  d   �  8   �  3   7  #   k  "   �  #   �     �  $   �  /        I     i  "   �     �     �  (   �  '   	  *   1  )   \  !   �     �  #   �     �            )   8     b  )     &   �  %   �  ,   �     #     +     E     `  4   h  6   �     �  $   �           5     V     q     �     �     �     �     �     �     �          $     3  /   N     ~     �     �     �     �     �          %     =  %   O     u  	   �  �  �  9   ?  [   y  n   �  7   D  L   |  n   �  U   8  h   �  O   �  l   G  \   �  3     J   E  J   �  c   �  )   ?  �   i  Y   �  O   O  D   �  D   �  9   )   K   c   Z   �   Q   
!  I   \!  >   �!     �!  B   �!  �   ="  �   �"  -   �#  Q   �#  �   	$     �$  �   H%  P   �%  X   &  <   v&  @   �&  9   �&  l   .'  U   �'  7   �'  :   )(  3   d(  ;   �(  g   �(  j   <)  ^   �)  a   *  <   h*  6   �*  c   �*  E   @+  I   �+  8   �+  S   	,  T   ],  h   �,  P   -  S   l-  e   �-     &.  6   7.  4   n.  
   �.  f   �.  f   /  7   |/  S   �/  O   0  M   X0  Q   �0  <   �0  @   51  !   v1     �1     �1  %   �1  (   �1  <   #2  $   `2  %   �2  7   �2  \   �2  6   @3  2   w3  4   �3  *   �3  6   
4  3   A4  -   u4  5   �4  %   �4  Q   �4  ?   Q5     �5     U   ;   E                            -                (   :              R      *   %               ]   B       Z   Q   I      !   7       '           #   \   c   G                               "       H   W   `   ^   &   C   )             D   1       /   Y   O                           V   ?   L   0   N   T       J          5       @   4          9   X                  $      M   .   [       a   2   S   6   <       	   8       3   +   _   K              
   b   A   F       ,       P            >          =    
Report bugs to <%s>.
   -?, --help                  show this help, then exit
   -V, --version               output version information, then exit
   -e, --exit-on-error         exit immediately on error
   -i, --ignore=RELATIVE_PATH  ignore indicated path
   -m, --manifest-path=PATH    use specified path for manifest
   -n, --no-parse-wal          do not try to parse WAL files
   -q, --quiet                 do not print any output, except for errors
   -s, --skip-checksums        skip checksum verification
   -w, --wal-directory=PATH    use specified path for WAL files
 "%s" has size %lld on disk but size %zu in the manifest "%s" is not a file or directory "%s" is present in the manifest but not on disk "%s" is present on disk but not in the manifest "\u" must be followed by four hexadecimal digits. %s home page: <%s>
 %s verifies a backup against the backup manifest.

 Character with value 0x%02x must be escaped. Escape sequence "\%s" is invalid. Expected "," or "]", but found "%s". Expected "," or "}", but found "%s". Expected ":", but found "%s". Expected JSON value, but found "%s". Expected array element or "]", but found "%s". Expected end of input, but found "%s". Expected string or "}", but found "%s". Expected string, but found "%s". Options:
 The input string ended unexpectedly. The program "%s" is needed by %s but was not found in the
same directory as "%s".
Check your installation. The program "%s" was found by "%s"
but was not the same version as %s.
Check your installation. Token "%s" is invalid. Try "%s --help" for more information.
 Unicode escape values cannot be used for code point values above 007F when the encoding is not UTF8. Unicode high surrogate must not follow a high surrogate. Unicode low surrogate must follow a high surrogate. Usage:
  %s [OPTION]... BACKUPDIR

 WAL parsing failed for timeline %u \u0000 cannot be converted to text. backup successfully verified
 both path name and encoded path name cannot duplicate null pointer (internal error)
 checksum mismatch for file "%s" checksum without algorithm could not close directory "%s": %m could not close file "%s": %m could not decode file name could not finalize checksum of file "%s" could not finalize checksum of manifest could not initialize checksum of file "%s" could not initialize checksum of manifest could not open directory "%s": %m could not open file "%s": %m could not parse backup manifest: %s could not parse end LSN could not parse start LSN could not read file "%s": %m could not read file "%s": read %d of %lld could not stat file "%s": %m could not stat file or directory "%s": %m could not update checksum of file "%s" could not update checksum of manifest duplicate path name in backup manifest: "%s" error:  expected at least 2 lines expected version indicator fatal:  file "%s" has checksum of length %d, but expected %d file "%s" should contain %zu bytes, but read %zu bytes file size is not an integer invalid checksum for file "%s": "%s" invalid manifest checksum: "%s" last line not newline-terminated manifest checksum mismatch manifest ended unexpectedly manifest has no checksum missing end LSN missing path name missing size missing start LSN missing timeline no backup directory specified out of memory out of memory
 timeline is not an integer too many command-line arguments (first is "%s") unexpected WAL range field unexpected array end unexpected array start unexpected file field unexpected manifest version unexpected object end unexpected object field unexpected object start unexpected scalar unrecognized checksum algorithm: "%s" unrecognized top-level field warning:  Project-Id-Version: postgresql
Report-Msgid-Bugs-To: pgsql-bugs@lists.postgresql.org
POT-Creation-Date: 2022-03-16 09:15+0000
PO-Revision-Date: 2022-06-19 10:10
Last-Translator: 
Language-Team: Ukrainian
Language: uk_UA
MIME-Version: 1.0
Content-Type: text/plain; charset=UTF-8
Content-Transfer-Encoding: 8bit
Plural-Forms: nplurals=4; plural=((n%10==1 && n%100!=11) ? 0 : ((n%10 >= 2 && n%10 <=4 && (n%100 < 12 || n%100 > 14)) ? 1 : ((n%10 == 0 || (n%10 >= 5 && n%10 <=9)) || (n%100 >= 11 && n%100 <= 14)) ? 2 : 3));
X-Crowdin-Project: postgresql
X-Crowdin-Project-ID: 324573
X-Crowdin-Language: uk
X-Crowdin-File: /REL_14_STABLE/pg_verifybackup.pot
X-Crowdin-File-ID: 756
 
Повідомляти про помилки на <%s>.
   -?, --help                   показати цю довідку, потім вийти
   -V, --version                вивести інформацію про версію, потім вийти
   -e, --exit-on-error вийти при помилці
   -i, --ignore=RELATIVE_PATH ігнорувати вказаний шлях
   -m, --manifest-path=PATH використовувати вказаний шлях для маніфесту
   -n, --no-parse-wal не намагатися аналізувати файли WAL
   -q, --quiet            не друкувати жодного виводу, окрім помилок
   -s, --skip-checksums не перевіряти контрольні суми
   -w, --wal-directory=PATH використовувати вказаний шлях для файлів WAL
 "%s" має розмір %lld на диску, але розмір %zu у маніфесті "%s" не є файлом або каталогом "%s" присутній у маніфесті, але не на диску "%s" присутній на диску, але не у маніфесті За "\u" повинні прямувати чотири шістнадцяткових числа. Домашня сторінка %s: <%s>
 %s перевіряє резервну копію відповідно до маніфесту резервного копіювання.

 Символ зі значенням 0x%02x повинен бути пропущений. Неприпустима спеціальна послідовність "\%s". Очікувалось "," або "]", але знайдено "%s". Очікувалось "," або "}", але знайдено "%s". Очікувалось ":", але знайдено "%s". Очікувалось значення JSON, але знайдено "%s". Очікувався елемент масиву або "]", але знайдено "%s". Очікувався кінець введення, але знайдено "%s". Очікувався рядок або "}", але знайдено "%s". Очікувався рядок, але знайдено "%s". Параметри:
 Несподіваний кінець вхідного рядка. Програма "%s" потрібна для %s, але не знайдена в тому ж каталозі, що й "%s".
Перевірте вашу установку. Програма "%s" була знайдена "%s", але не була тієї ж версії, що %s.
Перевірте вашу установку. Неприпустимий маркер "%s". Спробуйте "%s --help" для додаткової інформації.
 Значення виходу Unicode не можна використовувати для значень кодових точок більше 007F, якщо кодування не UTF8. Старший сурогат Unicode не повинен прямувати за іншим старшим сурогатом. Молодший сурогат Unicode не повинен прямувати за іншим молодшим сурогатом. Використання:
  %s [OPTION]... КАТАЛОГ_КОПІЮВАННЯ

 не вдалося проаналізувати WAL для часової шкали %u \u0000 не можна перетворити в текст. резервну копію успішно перевірено
 і ім'я шляху, і закодований шлях неможливо дублювати нульовий покажчик (внутрішня помилка)
 невідповідність контрольної суми для файлу "%s" контрольна сума без алгоритму не вдалося закрити каталог "%s": %m неможливо закрити файл "%s": %m не вдалося декодувати ім'я файлу не вдалося остаточно завершити контрольну суму файлу "%s" не вдалося остаточно завершити контрольну суму маніфесту не вдалося ініціалізувати контрольну суму файлу "%s" не вдалося ініціалізувати контрольну суму маніфесту не вдалося відкрити каталог "%s": %m не можливо відкрити файл "%s": %m не вдалося проаналізувати маніфест резервної копії: %s не вдалося проаналізувати кінцевий LSN не вдалося проаналізувати початковий LSN не вдалося прочитати файл "%s": %m не вдалося прочитати файл "%s": прочитано %d з %lld не вдалося отримати інформацію від файлу "%s": %m не вдалося отримати інформацію про файл або каталог "%s": %m не вдалося оновити контрольну суму файлу "%s" не вдалося оновити контрольну суму маніфесту дубльований шлях у маніфесті резервного копіювання: "%s" помилка:  очікувалося принаймні 2 рядки індикатор очікуваної версії збій:  файл "%s" має контрольну суму довжини %d, але очікувалось %d файл "%s" мусить містити %zu байтів, але прочитано %zu байтів розмір файлу не є цілим числом неприпустима контрольна сума для файлу "%s": "%s" неприпустима контрольна сума маніфесту: "%s" останній рядок не завершений новим рядком невідповідність контрольної суми маніфесту маніфест закінчився несподівано у маніфесті немає контрольної суми відсутній LSN кінця пропущено шлях відсутній розмір відсутній LSN початку відсутня часова шкала не вказано папку резервної копії недостатньо пам'яті недостатньо пам'яті
 часова лінія не є цілим числом забагато аргументів у командному рядку (перший "%s") неочікуване поле діапазону WAL неочікуваний кінець масиву неочікуваний початок масиву неочікуване поле файлу неочікувана версія маніфесту неочікуваний кінець об'єкта неочікуване поле об'єкта неочікуваний початок об'єкта неочікуваний скаляр нерозпізнаний алгоритм контрольної суми: "%s" нерозпізнане поле верхнього рівня попередження:  