��    ,      |  ;   �      �  E   �  0        @  :   S  E   �  I   �  L     s   k  K   �  =   +  B   i  i   �  G     J   ^  M   �  M   �  ?   E  G   �  >   �  6   	  <   C	  >   �	  F   �	  P   
  I   W
  4   �
  2   �
     	       *   -     X  	   r     |  &   �     �  &   �      �  (   	     2  %   M     s     �     �  �  �  ^   ?  b   �       {     B   �  J   �  P     c   p  O   �  b   $  k   �  �   �  q   }  L   �  R   <  R   �  c   �  F   F  H   �  S   �  ^   *  m   �  k   �  �   c  v   �  3   h  G   �  )   �       U   &  2   |     �  8   �  Q   �     O  ?   j  >   �  J   �  D   4  U   y  /   �     �  $               #                )   	          '      &   ,       
   $   %                                  +      (                                                           *         "   !                      
%s provides information about the installed version of PostgreSQL.

 
With no arguments, all known items are shown.

   %s [OPTION]...

   --bindir              show location of user executables
   --cc                  show CC value used when PostgreSQL was built
   --cflags              show CFLAGS value used when PostgreSQL was built
   --cflags_sl           show CFLAGS_SL value used when PostgreSQL was built
   --configure           show options given to "configure" script when
                        PostgreSQL was built
   --cppflags            show CPPFLAGS value used when PostgreSQL was built
   --docdir              show location of documentation files
   --htmldir             show location of HTML documentation files
   --includedir          show location of C header files of the client
                        interfaces
   --includedir-server   show location of C header files for the server
   --ldflags             show LDFLAGS value used when PostgreSQL was built
   --ldflags_ex          show LDFLAGS_EX value used when PostgreSQL was built
   --ldflags_sl          show LDFLAGS_SL value used when PostgreSQL was built
   --libdir              show location of object code libraries
   --libs                show LIBS value used when PostgreSQL was built
   --localedir           show location of locale support files
   --mandir              show location of manual pages
   --pgxs                show location of extension makefile
   --pkgincludedir       show location of other C header files
   --pkglibdir           show location of dynamically loadable modules
   --sharedir            show location of architecture-independent support files
   --sysconfdir          show location of system-wide configuration files
   --version             show the PostgreSQL version
   -?, --help            show this help, then exit
 %s home page: <%s>
 %s() failed: %m %s: could not find own program executable
 %s: invalid argument: %s
 Options:
 Report bugs to <%s>.
 Try "%s --help" for more information.
 Usage:
 could not change directory to "%s": %m could not find a "%s" to execute could not identify current directory: %m could not read binary "%s" could not read symbolic link "%s": %m invalid binary "%s" not recorded out of memory Project-Id-Version: postgresql
Report-Msgid-Bugs-To: pgsql-bugs@lists.postgresql.org
POT-Creation-Date: 2022-05-17 15:31+0000
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
X-Crowdin-File: /REL_14_STABLE/pg_config.pot
X-Crowdin-File-ID: 766
 
%s надає інформацію про інстальовану версію PostgreSQL.

 
Без аргументів, всі відображаються відомі елементи.

   %s: [OPTION]...

   --bindir              показує розташування виконуваних файлів користувача
   --cc показує значення CC збірки PostgreSQL
   --cflags показує значення CFLAGS збірки PostgreSQL
   --cflags_sl показує значення CFLAGS_SL збірки PostgreSQL
   --configure показує параметри скрипта "configure" збірки PostgreSQL
   --cppflags показує значення CPPFLAGS збірки PostgreSQL 
   --docdir          показує розташування файлів документації
   --htmldir             показує розташування файлів документації HTML
   -includedir показує розташування заголовків файлів С клієнтських інтерфейсів
   -includedir-server показує розташування заголовків файлів С сервера
   --ldflags показує значення LDFLAGS збірки PostgreSQL
   --ldflags_ex показує значення LDFLAGS_EX збірки PostgreSQL
   --ldflags_sl показує значення LDFLAGS_SL збірки PostgreSQL
   --libdir показує розташування бібліотек об'єктного коду
   --libs показує значення LIBS збірки PostgreSQL
   --localedir показує місце файлів перекладу
   --mandir показує розташування сторінок мануала
   --pgxs показує місцерозташування makefile для розширень
   -pkgincludedir показує розташування інших файлів з заголовками C
   --pkglibdir показує місце динамічно завантажувальних модулів
   --sharedir показує місцерозташування архітектурно незалежних файлів підтримки
   --sysconfdir показує розташування всесистемних файлів конфігурації
   --version показує версію PostgreSQL
   -?, --help показує цю довідку, потім вихід
 Домашня сторінка %s: <%s>
 %s() помилка: %m %s: не вдалося знайти ехе файл власної програми
 %s: недопустимий аргумент: %s
 Параметри:
 Повідомляти про помилки на <%s>.
 Спробуйте "%s --help" для додаткової інформації.
 Використання:
 не вдалося змінити каталог на "%s": %m неможливо знайти "%s" для виконання не вдалося визначити поточний каталог: %m неможливо прочитати бінарний файл "%s" не можливо прочитати символічне послання "%s": %m невірний бінарний файл "%s" не записано недостатньо пам'яті 