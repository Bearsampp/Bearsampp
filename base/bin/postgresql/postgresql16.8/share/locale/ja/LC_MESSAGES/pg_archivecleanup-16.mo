Þ          ü   %   Ü      p  Ù   q  
   K     V     Þ  3   õ  +   )  7   U  6     L   Ä  <        N  6   b  %        ¿  $   Ç  )   ì  (     (   ?     h                         º  !   Ø     ú  	     Ä  $  $  é     
  ¼   !
  -   Þ
  `     B   m  H   °  >   ù  K   8  E        Ê  S   ç  2   ;     n  1   z  G   ¬  G   ô  A   <  5   ~     ´     ½     É  $   Õ  3   ú  ?   .  -   n                                                                                   
                    	                         
For use as archive_cleanup_command in postgresql.conf:
  archive_cleanup_command = 'pg_archivecleanup [OPTION]... ARCHIVELOCATION %%r'
e.g.
  archive_cleanup_command = 'pg_archivecleanup /mnt/server/archiverdir %%r'
 
Options:
 
Or for use as a standalone archive cleaner:
e.g.
  pg_archivecleanup /mnt/server/archiverdir 000000010000000000000010.00000020.backup
 
Report bugs to <%s>.
   %s [OPTION]... ARCHIVELOCATION OLDESTKEPTWALFILE
   -?, --help     show this help, then exit
   -V, --version  output version information, then exit
   -d             generate debug output (verbose mode)
   -n             dry run, show the names of the files that would be removed
   -x EXT         clean up files if they have this extension
 %s home page: <%s>
 %s removes older WAL files from PostgreSQL archives.

 Try "%s --help" for more information. Usage:
 archive location "%s" does not exist could not close archive location "%s": %m could not open archive location "%s": %m could not read archive location "%s": %m could not remove file "%s": %m detail:  error:  hint:  invalid file name argument must specify archive location must specify oldest kept WAL file too many command-line arguments warning:  Project-Id-Version: pg_archivecleanup (PostgreSQL 16)
Report-Msgid-Bugs-To: pgsql-bugs@lists.postgresql.org
POT-Creation-Date: 2023-07-04 09:43+0900
PO-Revision-Date: 2023-07-04 09:48+0900
Last-Translator: Kyotaro Horiguchi <horikyota.ntt@gmail.com>
Language-Team: Japan PostgreSQL Users Group <jpug-doc@ml.postgresql.jp>
Language: ja
MIME-Version: 1.0
Content-Type: text/plain; charset=UTF-8
Content-Transfer-Encoding: 8bit
X-Generator: Poedit 1.8.13
 
postgresql.confã§archive_cleanup_commandã¨ãã¦ä½¿ç¨ããå ´åã¯ä»¥ä¸ã®ããã«ãã¾ã:
  archive_cleanup_command = 'pg_archivecleanup [ãªãã·ã§ã³]... ã¢ã¼ã«ã¤ãã®å ´æ %%r'
ä¾ã¨ãã¦ã¯:
  archive_cleanup_command = 'pg_archivecleanup /mnt/server/archiverdir %%r'
 
ãªãã·ã§ã³:
 
ãããã¯ã¹ã¿ã³ãã¢ã­ã³ã®ã¢ã¼ã«ã¤ãã¯ãªã¼ãã¼ã¨ãã¦ä½¿ãå ´åã¯:
ä½¿ç¨ä¾
  pg_archivecleanup /mnt/server/archiverdir 000000010000000000000010.00000020.backup
 
ãã°ã¯<%s>ã«å ±åãã¦ãã ããã
 %s [ãªãã·ã§ã³] ... {ã¢ã¼ã«ã¤ãã®å ´æ} {ä¿å­ããæå¤ã® WAL ãã¡ã¤ã«å}
   -?, --help     ãã®ãã«ããè¡¨ç¤ºãã¦ãçµäºãã¾ã
   -V, --version  ãã¼ã¸ã§ã³æå ±ãè¡¨ç¤ºãã¦ãçµäºãã¾ã
   -d             ãããã°æå ±ãåºå(åé·ã¢ã¼ã)
   -n             ãªãã¼ãµã«ãåé¤å¯¾è±¡ã®ãã¡ã¤ã«åãè¡¨ç¤º
   -x EXT         ãã®æ¡å¼µå­ãæã¤ãã¡ã¤ã«ãåé¤ãã
 %s ãã¼ã ãã¼ã¸: <%s>
 %sã¯PostgreSQLã®ã¢ã¼ã«ã¤ãããå¤ãWALãã¡ã¤ã«ãåé¤ãã¾ãã

 è©³ç´°ã¯"%s --help"ãå®è¡ãã¦ãã ããã ä½¿ç¨æ³:
 ã¢ã¼ã«ã¤ãã®å ´æ"%s"ãå­å¨ãã¾ãã ã¢ã¼ã«ã¤ãã®å ´æ"%s"ãã¯ã­ã¼ãºã§ãã¾ããã§ãã: %m ã¢ã¼ã«ã¤ãã®å ´æ"%s"ããªã¼ãã³ã§ãã¾ããã§ãã: %m ã¢ã¼ã«ã¤ãã®å ´æ"%s"ãèª­ã¿è¾¼ãã¾ããã§ãã: %m ãã¡ã¤ã«"%s"ãåé¤ã§ãã¾ããã§ãã: %m è©³ç´°:  ã¨ã©ã¼:  ãã³ã:  ãã¡ã¤ã«åå¼æ°ãç¡å¹ã§ã ã¢ã¼ã«ã¤ãã®å ´æãæå®ãã¦ãã ãã ä¿å­ããæå¤ã®WALãã¡ã¤ã«ãæå®ãã¦ãã ãã ã³ãã³ãã©ã¤ã³å¼æ°ãå¤ããã¾ã è­¦å:  