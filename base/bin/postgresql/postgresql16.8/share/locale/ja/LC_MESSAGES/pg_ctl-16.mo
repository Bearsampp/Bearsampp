Þ          ô  Ó   Ì	                 :  &   L     s          ²     É     ß  /   ò     "  "   B  1   e       "   3  j   V  o   Á     1  D   O  !     3   ¶  ?   ê  H   *  D   s  C   ¸  E   ü  ?   B  ?     >   Â  9     L   ;  B     E   Ë       0     F   Ç  >     B   M  I     %   Ú  <      O   =  7        Å     Ì     Õ     ç  M   û     I  -   Y  !     C   ©  y   í  9   g  C   ¡  B   å  C   (  D   l  >   ±  @   ð  '   1  (   Y  ,     7   ¯  2   ç  6     >   Q  *     /   »  7   ë  4   #  %   X  %   ~  1   ¤  0   Ö  #        +  4   I  7   ~  2   ¶  5   é  0     /   P  +     -   ¬  3   Ú  7        F  +   f  1     6   Ä  6   û  1   2   *   d   "      7   ²   "   ê   $   !  J   2!     }!     !  2   °!  0   ã!     "  #   '"  !   K"     m"      "  $   ­"      Ò"  ,   ó"      #  4   @#  %   u#  $   #  "   À#  !   ã#  u   $  F   {$     Â$  7   Ö$  )   %  %   8%  &   ^%     %     %  /   ¬%  &   Ü%  0   &  .   4&  -   c&     &     ¨&      º&  ,   Û&     '  0   ''     X'     p'     ~'  M   '  B   Û'     (     /(     A(     W(  #   h(     (     (     ­(     ½(     Õ(      ô(  "   )     8)  Ý  W)  1   5+     g+  *   +  &   ®+  &   Õ+  -   ü+  3   *,  $   ^,  /   ,     ³,  "   Ó,  1   ö,     (-  "   Ä-  j   ç-  o   R.     Â.  D   à.  !   %/  >   G/  A   /  M   È/  b   0  Y   y0  S   Ó0  D   '1  8   l1  F   ¥1  P   ì1  e   =2  ]   £2  N   3  £   P3  )   ô3  k   4  R   4  M   Ý4  [   +5  5   5  D   ½5  b   6  ?   e6     ¥6     ­6  &   Á6     è6  j   7     p7  [   7  *   ë7  u   8  ¯   8  g   <9  j   ¤9  q   :  q   :  n   ó:  r   b;  t   Õ;  5   J<  F   <  G   Ç<  d   =  U   t=  V   Ê=  `   !>  J   >  ;   Í>  D   	?  P   N?  C   ?  H   ã?  \   ,@  Q   @  D   Û@  ?    A  K   `A  X   ¬A  C   B  U   IB  F   B  N   æB  A   5C  H   wC  `   ÀC  S   !D  6   uD  G   ¬D  K   ôD  Q   @E  d   E  U   ÷E  D   MF  2   F  c   ÅF  ?   )G  0   iG  S   G  2   îG  ,   !H  P   NH  O   H     ïH  ;   
I  ;   FI  ,   I  ,   ¯I  ,   ÜI  3   	J  D   =J  $   J  A   §J  '   éJ  0   K  !   BK  !   dK     K  K   !L  )   mL  ^   L  =   öL  =   4M  3   rM     ¦M  4   µM  B   êM  8   -N  A   fN  1   ¨N  ?   ÚN  0   O  $   KO  +   pO  G   O  5   äO  D   P     _P     }P     P  q   ªP  }   Q  "   Q     ½Q     ÝQ  1   ýQ  C   /R     sR  "   R  "   °R  1   ÓR  1   S  -   7S  9   eS  3   S                /       N   l   8          O           !         j                     m      E       =   *       C       Q      t          &             \              B      _      >          ?   z                  -           
      K   4   D       i   U   p   6         `   c                      1              @   P   7   {      Z          d   "                 :       9   q              g   )   T   A   W   ^       .      ;   $   o                                 f      y   b   2   <   s         	   e              #       J          3   ]      v               x      G         +   (      ,   n         Y   L      5   F       u       V   ~                    %   H         X   |      k   }      R       S   r          0   [       a       h   I       M   '      w                  
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
 %s: another server might be running; trying to start server anyway
 %s: cannot be run as root
Please log in (using, e.g., "su") as the (unprivileged) user that will
own the server process.
 %s: cannot promote server; server is not in standby mode
 %s: cannot promote server; single-user server is running (PID: %d)
 %s: cannot reload server; single-user server is running (PID: %d)
 %s: cannot restart server; single-user server is running (PID: %d)
 %s: cannot rotate log file; single-user server is running (PID: %d)
 %s: cannot set core file size limit; disallowed by hard limit
 %s: cannot stop server; single-user server is running (PID: %d)
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
 %s: could not send log rotation signal (PID: %d): %s
 %s: could not send promote signal (PID: %d): %s
 %s: could not send reload signal (PID: %d): %s
 %s: could not send signal %d (PID: %d): %s
 %s: could not send stop signal (PID: %d): %s
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
 %s: old server process (PID: %d) seems to be gone
 %s: option file "%s" must have exactly one line
 %s: out of memory
 %s: server did not promote in time
 %s: server did not start in time
 %s: server does not shut down
 %s: server is running (PID: %d)
 %s: service "%s" already registered
 %s: service "%s" not registered
 %s: single-user server is running (PID: %d)
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
 Timed out waiting for server startup
 Try "%s --help" for more information.
 Usage:
 Waiting for server startup...
 cannot duplicate null pointer (internal error)
 child process exited with exit code %d child process exited with unrecognized status %d child process was terminated by exception 0x%X child process was terminated by signal %d: %s command not executable command not found could not find a "%s" to execute could not get current working directory: %s
 could not read binary "%s": %m could not resolve path "%s" to absolute form: %m invalid binary "%s": %m out of memory out of memory
 program "%s" is needed by %s but was not found in the same directory as "%s"
 program "%s" was found by "%s" but was not the same version as %s
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
 waiting for server to promote... waiting for server to shut down... waiting for server to start... Project-Id-Version: pg_ctl (PostgreSQL 16)
Report-Msgid-Bugs-To: pgsql-bugs@lists.postgresql.org
POT-Creation-Date: 2023-03-24 09:21+0900
PO-Revision-Date: 2023-03-24 14:11+0900
Last-Translator: Kyotaro Horiguchi <horikyota.ntt@gmail.com>
Language-Team: Japan PostgreSQL Users Group <jpug-doc@ml.postgresql.jp>
Language: ja
MIME-Version: 1.0
Content-Type: text/plain; charset=UTF-8
Content-Transfer-Encoding: 8bit
Plural-Forms: nplurals=1; plural=0;
X-Generator: Poedit 1.8.13
 
killã¢ã¼ãã§å©ç¨ã§ããã·ã°ãã«å:
 
å±éã®ãªãã·ã§ã³:
 
ç»é²ãç»é²è§£é¤ã®ãªãã·ã§ã³:
 
èµ·åãåèµ·åã®ãªãã·ã§ã³
 
åæ­¢ãåèµ·åã®ãªãã·ã§ã³
 
ãã°ã¯<%s>ã«å ±åãã¦ãã ããã
 
ã·ã£ãããã¦ã³ã¢ã¼ãã¯ä»¥ä¸ã®éã:
 
èµ·åã¿ã¤ãã¯ä»¥ä¸ã®éã:
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
   -?, --help             ãã®ãã«ããè¡¨ç¤ºãã¦çµäº
   -D, --pgdata=DATADIR   ãã¼ã¿ãã¼ã¹æ ¼ç´é åã®å ´æ
   -N SERVICENAME  PostgreSQLãµã¼ãã¼ãç»é²ããéã®ãµã¼ãã¹å
   -P PASSWORD     PostgreSQLãµã¼ãã¼ãç»é²ããããã®ã¢ã«ã¦ã³ãã®ãã¹ã¯ã¼ã
   -S START-TYPE   PostgreSQLãµã¼ãã¼ãç»é²ããéã®ãµã¼ãã¹èµ·åã¿ã¤ã
   -U USERNAME     PostgreSQLãµã¼ãã¼ãç»é²ããããã®ã¢ã«ã¦ã³ãå
   -V, --version          ãã¼ã¸ã§ã³æå ±ãè¡¨ç¤ºãã¦çµäº
   -W, --no-wait          ä½æ¥­ã®å®äºãå¾ããªã
   -c, --core-files       postgresã®ã³ã¢ãã¡ã¤ã«çæãè¨±å¯
   -c, --core-files       ãã®ãã©ãããã©ã¼ã ã§ã¯é©ç¨ãããªã
   -e SOURCE              ãµã¼ãã¹ã¨ãã¦èµ·åãããã¨ãã®ã­ã°ã®ã¤ãã³ãã½ã¼ã¹
   -l, --log FILENAME     ãµã¼ãã¼ã­ã°ãFILENAMEã¸æ¸ãè¾¼ã(ã¾ãã¯è¿½å ãã)
   -m, --mode=MODE        MODEã¯"smart"ã"fast"ã"immediate"ã®ãããã
   -o, --options=OPTIONS  postgres(PostgreSQLãµã¼ãã¼å®è¡ãã¡ã¤ã«)ã¾ãã¯
                         initdb ã«æ¸¡ãã³ãã³ãã©ã¤ã³ãªãã·ã§ã³
   -p PATH-TO-POSTGRES    éå¸¸ã¯ä¸è¦
   -s, --silent           ã¨ã©ã¼ã¡ãã»ã¼ã¸ã®ã¿ãè¡¨ç¤ºãæå ±ã¡ãã»ã¼ã¸ã¯è¡¨ç¤ºããªã
   -t, --timeout=SECS     -wãªãã·ã§ã³ãä½¿ç¨ããæã«å¾æ©ããç§æ°
   -w, --wait             æä½ãå®äºããã¾ã§å¾æ© (ããã©ã«ã)
   auto       ã·ã¹ãã ã®èµ·åæã«ãµã¼ãã¹ãèªåçã«éå§(ããã©ã«ã)
   demand     è¦æ±ã«å¿ãã¦ãµã¼ãã¹ãéå§
   fast        é©åãªæç¶ãã§ç´ã¡ã«åæ­¢(ããã©ã«ã)
   immediate   é©åãªæç¶ãæãã§åæ­¢; åèµ·åæã«ã¯ãªã«ããªãå®è¡ããã
   smart       å¨ã¯ã©ã¤ã¢ã³ãã®æ¥ç¶åæ­å¾ã«åæ­¢
 å®äº
 å¤±æãã¾ãã
  å¾æ©å¦çãåæ­¢ããã¾ãã
 %s ãã¼ã ãã¼ã¸: <%s>
 %sã¯PostgreSQLãµã¼ãã¼ã®åæåãèµ·åãåæ­¢ãå¶å¾¡ãè¡ãã¦ã¼ãã£ãªãã£ã§ãã
 %s() ãå¤±æãã¾ãã: %m %s: -Sãªãã·ã§ã³ã¯ãã®ãã©ãããã©ã¼ã ã§ãµãã¼ãããã¦ãã¾ãã
 %s: PIDãã¡ã¤ã«"%s"ãããã¾ãã
 %s: ä»ã®ãµã¼ãã¼ãåä½ä¸­ã®å¯è½æ§ãããã¾ãããã¨ã«ããpostmasterã®èµ·åãè©¦ã¿ã¾ãã
 %s: rootã§ã¯å®è¡ã§ãã¾ãã
ãµã¼ãã¼ãã­ã»ã¹ã®ææèã¨ãªã(éç¹æ¨©)ã¦ã¼ã¶ã¼ã¨ãã¦("su"ãªã©ãä½¿ç¨ãã¦)
ã­ã°ã¤ã³ãã¦ãã ããã
 %s: ãµã¼ãã¼ãææ ¼ã§ãã¾ãã; ãµã¼ãã¼ã¯ã¹ã¿ã³ãã¤ã¢ã¼ãã§ã¯ããã¾ãã
 %s: ãµã¼ãã¼ãææ ¼ã§ãã¾ãã; ã·ã³ã°ã«ã¦ã¼ã¶ã¼ãµã¼ãã¼(PID: %d)ãåä½ä¸­ã§ã
 %s: ãµã¼ãã¼ããªã­ã¼ãã§ãã¾ãããã·ã³ã°ã«ã¦ã¼ã¶ã¼ãµã¼ãã¼(PID: %d)ãåä½ä¸­ã§ã
 %s: ãµã¼ãã¼ãåèµ·åã§ãã¾ãããã·ã³ã°ã«ã¦ã¼ã¶ã¼ãµã¼ãã¼(PID: %d)ãåä½ä¸­ã§ãã
 %s: ã­ã°ãã­ã¼ãã¼ãã§ãã¾ãã; ã·ã³ã°ã«ã¦ã¼ã¶ã¼ãµã¼ãã¼ãåä½ä¸­ã§ã (PID: %d)
 %s: ã³ã¢ãã¡ã¤ã«ã®ãµã¤ãºå¶éãè¨­å®ã§ãã¾ãã:åºå®ã®å¶éã«ããè¨±ããã¦ãã¾ãã
 %s: ãµã¼ãã¼ãåæ­¢ã§ãã¾ãããã·ã³ã°ã«ã¦ã¼ã¶ã¼ãµã¼ãã¼(PID: %d)ãåä½ãã¦ãã¾ãã
 %s: å¶å¾¡ãã¡ã¤ã«ãå£ãã¦ããããã§ã
 %s: ãã£ã¬ã¯ããª"%s"ã«ã¢ã¯ã»ã¹ã§ãã¾ããã§ãã: %s
 %s: SIDãå²ãå½ã¦ããã¾ããã§ãã: ã¨ã©ã¼ã³ã¼ã %lu
 %s: ã­ã°ã­ã¼ãã¼ãæç¤ºãã¡ã¤ã«"%s"ãä½æãããã¨ãã§ãã¾ããã§ãã: %s
 %s: ææ ¼æç¤ºãã¡ã¤ã«"%s"ãä½æãããã¨ãã§ãã¾ããã§ãã: %s
 %s: å¶éä»ããã¼ã¯ã³ãä½æã§ãã¾ããã§ãã: ã¨ã©ã¼ã³ã¼ã %lu
 %s: ã³ãã³ã"%s"ãä½¿ç¨ãããã¼ã¿ãã£ã¬ã¯ããªãæ±ºå®ã§ãã¾ããã§ãã
 %s: æ¬ãã­ã°ã©ã ã®å®è¡ãã¡ã¤ã«ã®æ¤ç´¢ã«å¤±æãã¾ãã
 %s: postgres ã®å®è¡ãã¡ã¤ã«ãè¦ã¤ããã¾ãã
 %s: æ¨©éã® LUID ãåå¾ã§ãã¾ãã: ã¨ã©ã¼ã³ã¼ã %lu
 %s: ãã¼ã¯ã³æå ±ãåå¾ã§ãã¾ããã§ãã: ã¨ã©ã¼ã³ã¼ã %lu
 %s: PIDãã¡ã¤ã«"%s"ããªã¼ãã³ã§ãã¾ããã§ãã: %s
 %s: ã­ã°ãã¡ã¤ã« "%s" ããªã¼ãã³ã§ãã¾ããã§ãã: %s
 %s: ãã­ã»ã¹ãã¼ã¯ã³ããªã¼ãã³ã§ãã¾ããã§ãã: ã¨ã©ã¼ã³ã¼ã %lu
 %s: ãµã¼ãã¹"%s"ã®ãªã¼ãã³ã«å¤±æãã¾ãã: ã¨ã©ã¼ã³ã¼ã %lu
 %s: ãµã¼ãã¹ããã¼ã¸ã£ã®ãªã¼ãã³ã«å¤±æãã¾ãã
 %s: ãã¡ã¤ã«"%s"ãèª­ã¿åããã¨ã«å¤±æãã¾ãã
 %s: ãµã¼ãã¹"%s"ã®ç»é²ã«å¤±æãã¾ãã: ã¨ã©ã¼ã³ã¼ã %lu
 %s: ã­ã°ã­ã¼ãã¼ã·ã§ã³æç¤ºãã¡ã¤ã«"%s"ã®åé¤ã«å¤±æãã¾ãã: %s
 %s: ææ ¼æç¤ºãã¡ã¤ã«"%s"ã®åé¤ã«å¤±æãã¾ãã: %s
 %s: ã­ã°ã­ã¼ãã¼ãã·ã°ãã«ãéä¿¡ã§ãã¾ããã§ãã (PID: %d): %s
 %s: ææ ¼ã·ã°ãã«ãéä¿¡ã§ãã¾ããã§ãã (PID: %d): %s
 %s: ãªã­ã¼ãã·ã°ãã«ãéä¿¡ã§ãã¾ããã§ããã(PID: %d): %s
 %s: ã·ã°ãã«%dãéä¿¡ã§ãã¾ããã§ãã(PID: %d): %s
 %s: åæ­¢ã·ã°ãã«ãéä¿¡ã§ãã¾ããã§ããã(PID: %d): %s
 %s: ãµã¼ãã¼ãèµ·åã§ãã¾ããã§ããã
ã­ã°åºåãç¢ºèªãã¦ãã ããã
 %s: setsid()ã«å¤±æãããããµã¼ãã¼ã«æ¥ç¶ã§ãã¾ããã§ãã: %s
 %s: ãµã¼ãã¼ã«æ¥ç¶ã§ãã¾ããã§ãã: %s
 %s: ãµã¼ãã¼ã®èµ·åã«å¤±æãã¾ãã: ã¨ã©ã¼ã³ã¼ã %lu
 %s: ãµã¼ãã¹"%s"ã®èµ·åã«å¤±æãã¾ãã: ã¨ã©ã¼ã³ã¼ã %lu
 %s: ãµã¼ãã¹"%s"ã®ç»é²åé¤ã«å¤±æãã¾ãã: ã¨ã©ã¼ã³ã¼ã %lu
 %s: ã­ã°ã­ã¼ãã¼ãæç¤ºãã¡ã¤ã«"%s"ã«æ¸ãåºããã¨ãã§ãã¾ããã§ãã: %s
 %s: ææ ¼æç¤ºãã¡ã¤ã«"%s"ã«æ¸ãåºããã¨ãã§ãã¾ããã§ãã: %s
 %s: ãã¼ã¿ãã¼ã¹ã·ã¹ãã ãåæåã«å¤±æãã¾ãã
 %s: ãã£ã¬ã¯ããª "%s" ã¯å­å¨ãã¾ãã
 %s: ãã£ã¬ã¯ããª"%s"ã¯ãã¼ã¿ãã¼ã¹ã¯ã©ã¹ã¿ãã£ã¬ã¯ããªã§ã¯ããã¾ãã
 %s: PIDãã¡ã¤ã«"%s"åã«ç¡å¹ãªãã¼ã¿ãããã¾ã
 %s: killã¢ã¼ãç¨ã®å¼æ°ãããã¾ãã
 %s: ãã¼ã¿ãã¼ã¹ã®æå®ããPGDATAç°å¢å¤æ°ã®è¨­å®ãããã¾ãã
 %s: æä½ã¢ã¼ããæå®ããã¦ãã¾ãã
 %s: ãµã¼ãã¼ãåä½ãã¦ãã¾ãã
 %s: å¤ããµã¼ãã¼ãã­ã»ã¹(PID: %d)ãåä½ãã¦ããªãããã§ã
 %s: ãªãã·ã§ã³ãã¡ã¤ã«"%s"ã¯1è¡ã®ã¿ã§ãªããã°ãªãã¾ãã
 %s: ã¡ã¢ãªä¸è¶³ã§ã
 %s: ãµã¼ãã¼ã¯æéåã«ææ ¼ãã¾ããã§ãã
 %s: ãµã¼ãã¼ã¯æéåã«èµ·åãã¾ããã§ãã
 %s: ãµã¼ãã¼ã¯åæ­¢ãã¦ãã¾ãã
 %s: ãµã¼ãã¼ãåä½ä¸­ã§ã(PID: %d)
 %s: ãµã¼ãã¹\"%s\"ã¯ç»é²æ¸ã¿ã§ã
 %s: ãµã¼ãã¹"%s"ã¯ç»é²ããã¦ãã¾ãã
 %s: ã·ã³ã°ã«ã¦ã¼ã¶ã¼ãµã¼ãã¼ãåä½ä¸­ã§ã(PID: %d)
 %s: PIDãã¡ã¤ã«"%s"ãç©ºã§ã
 %s: ã³ãã³ãã©ã¤ã³å¼æ°ãå¤ããã¾ã(åé ­ã¯"%s")
 %s: æä½ã¢ã¼ã"%s"ã¯ä¸æã§ã
 %s: ä¸æ­£ãªã·ã£ãããã¦ã³ã¢ã¼ã"%s"
 %s: ä¸æ­£ãªã·ã°ãã«å"%s"
 %s: ä¸æ­£ãªèµ·åã¿ã¤ã"%s"
 ãã³ã: "-m fast"ãªãã·ã§ã³ã¯ãã»ãã·ã§ã³åæ­ãå§ã¾ãã¾ã§å¾æ©ããã®ã§ã¯ãªã
å³åº§ã«ã»ãã·ã§ã³ãåæ­ãã¾ãã
 -Dãªãã·ã§ã³ã®çç¥æã¯PGDATAç°å¢å¤æ°ãä½¿ç¨ããã¾ãã
 ãµã¼ãã¼ãåä½ãã¦ãã¾ãã?
 ã·ã³ã°ã«ã¦ã¼ã¶ã¼ãµã¼ãã¼ãçµäºããã¦ãããååº¦å®è¡ãã¦ãã ãã
 ãµã¼ãã¼ã¯èµ·åããæ¥ç¶ãåãä»ãã¦ãã¾ã
 ãµã¼ãã¼ã®èµ·åå¾æ©ãã¿ã¤ã ã¢ã¦ããã¾ãã
 è©³ç´°ã¯"%s --help"ãå®è¡ãã¦ãã ããã
 ä½¿ç¨æ¹æ³:
 ãµã¼ãã¼ã®èµ·åå®äºãå¾ã£ã¦ãã¾ã...
 null ãã¤ã³ã¿ãè¤è£½ã§ãã¾ããï¼åé¨ã¨ã©ã¼ï¼ã
 å­ãã­ã»ã¹ãçµäºã³ã¼ã%dã§çµäºãã¾ãã å­ãã­ã»ã¹ãæªç¥ã®ã¹ãã¼ã¿ã¹%dã§çµäºãã¾ãã å­ãã­ã»ã¹ãä¾å¤0x%Xã§çµäºãã¾ãã å­ãã­ã»ã¹ã¯ã·ã°ãã«%dã«ããçµäºãã¾ãã: %s ã³ãã³ãã¯å®è¡å½¢å¼ã§ã¯ããã¾ãã ã³ãã³ããè¦ã¤ããã¾ãã å®è¡ãã"%s"ãããã¾ããã§ãã ç¾å¨ã®ä½æ¥­ãã£ã¬ã¯ããªãåå¾ã§ãã¾ããã§ãã: %s
 ãã¤ããª"%s"ãèª­ã¿åãã¾ããã§ãã: %m ãã¹"%s"ãçµ¶å¯¾ãã¹å½¢å¼ã«å¤æã§ãã¾ããã§ãã: %m ä¸æ­£ãªãã¤ããª"%s": %m ã¡ã¢ãªä¸è¶³ã§ã ã¡ã¢ãªä¸è¶³ã§ã
 %2$sã«ã¯ãã­ã°ã©ã "%1$s"ãå¿è¦ã§ããã"%3$s"ã¨åããã£ã¬ã¯ããªã«ããã¾ããã§ãã
 "%2$s"ããã­ã°ã©ã "%1$s"ãè¦ã¤ãã¾ããããããã¯%3$sã¨åããã¼ã¸ã§ã³ã§ã¯ããã¾ããã§ãã
 ãµã¼ãã¼ã¯ææ ¼ãã¾ãã
 ãµã¼ãã¼ãææ ¼ä¸­ã§ã
 ãµã¼ãã¼ã®åæ­¢ä¸­ã§ã
 ãµã¼ãã¼ã«ã·ã°ãã«ãéä¿¡ãã¾ãã
 ãµã¼ãã¼ãã­ã°ã­ã¼ãã¼ããã·ã°ãã«ããã¾ãã
 ãµã¼ãã¼èµ·åå®äº
 ãµã¼ãã¼ã¯èµ·åä¸­ã§ãã
 ãµã¼ãã¼ã¯åæ­¢ãã¾ãã
 ã¨ã«ãããµã¼ãã¼ãèµ·åãã¦ãã¾ã
 ã¨ã«ãããµã¼ãã¼ã®èµ·åãè©¦ã¿ã¾ã
 ãµã¼ãã¼ã®ææ ¼ãå¾ã£ã¦ãã¾ã... ãµã¼ãã¼åæ­¢å¦çã®å®äºãå¾ã£ã¦ãã¾ã... ãµã¼ãã¼ã®èµ·åå®äºãå¾ã£ã¦ãã¾ã... 