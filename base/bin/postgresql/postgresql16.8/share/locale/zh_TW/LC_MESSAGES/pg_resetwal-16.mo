Þ    j      l     ¼      	     	  9   +	     e	  F   |	  =   Ã	  D   
  I   F
  ¾   
  A   O  ;     M   Í  K     K   g  0   ³  =   ä  ;   "  2   ^       +   ¥     Ñ  )   á  )     )   5     _  )   |  )   ¦  +   Ð  )   ü  R   &  )   y  )   £     Í  U   ê  A   @  )     )   ¬  )   Ö  ,      )   -  )   W  )     )   «  )   Õ  )   ÿ  )   )  )   S  )   }  )   §  )   Ñ  )   û  )   %     O  	   f  )   p        %   ;  !   a  )        ­  ,   Ä  *   ñ  C        `     m     v  '     &   »  "   â  1        7  7   V  !     (   °     Ù  ,   ö  :   #  !   ^       0     8   Î       "   %     H     Q     Y     i     p       &   ¥  +   Ì     ø       -     >   F       ;     =   Ä       )     /   É  B   ù  7   <  (   t       	   ¸     Â     c  B   {     ¾  F   Ø  ?     K   _  ?   «  ¶   ë  F   ¢  6   é  G       S   h   T   ¼   7   !  <   I!  <   !  0   Ã!     ô!  0   "     6"  *   E"  -   p"  (   "     Ç"  ,   ã"  +   #  .   <#  -   k#  8   #  /   Ò#  *   $     -$  Q   I$  N   $  )   ê$  )   %  )   >%  ,   h%  )   %  )   ¿%  )   é%  )   &  )   =&  -   g&  )   &  -   ¿&  )   í&  +   '  *   C'  ,   n'  '   '     Ã'     Ø'  )   á'     (  %   (  "   º(  (   Ý(     )  9    )  &   Z)  F   )     È)     Ï)     Ö)     î)     *     -*  -   I*     w*  0   *     Ä*  &   à*     +  '   #+  7   K+     +     +  #   »+  /   ß+     ,     +,     D,     S,     \,     m,     v,     ,  #   ©,  (   Í,     ö,     -  -   -  /   >-     n-  4   q-  ;   ¦-  K   â-  (   ..  (   W.  +   .  &   ¬.  !   Ó.     õ.     /        S   <   M   N   8       '   R   *   7      -      #       J                            +   4          P   A   3   e          h       g          6   C   D   )   0   >              "       F   G               5      2                  d           X       ]   L           B   ;   j   Q       @   _   ?            $       (          9   ^       `   
   %          H      K      1               W       U   a   &   Z   T                     :   \   E   V   f   !      O          Y   c           b   /           	   [   .             ,   =                    i   I    

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
 OID (-o) must not be 0 Options:
 Size of a large-object chunk:         %u
 The database server was not shut down cleanly.
Resetting the write-ahead log might cause data to be lost.
If you want to proceed anyway, use -f to force reset.
 Try "%s --help" for more information. Usage:
  %s [OPTION]... DATADIR

 WAL block size:                       %u
 Write-ahead log reset
 You must run %s as the PostgreSQL superuser. argument of --wal-segsize must be a number argument of --wal-segsize must be a power of two between 1 and 1024 by reference by value cannot be executed by "root" could not allocate SIDs: error code %lu could not change directory to "%s": %m could not close directory "%s": %m could not create restricted token: error code %lu could not delete file "%s": %m could not get exit code from subprocess: error code %lu could not open directory "%s": %m could not open file "%s" for reading: %m could not open file "%s": %m could not open process token: error code %lu could not re-execute with restricted token: error code %lu could not read directory "%s": %m could not read file "%s": %m could not read permissions of directory "%s": %m could not start process for command "%s": error code %lu could not write file "%s": %m data directory is of wrong version detail:  error:  fsync error: %m hint:  invalid argument for option %s lock file "%s" exists multitransaction ID (-m) must not be 0 multitransaction offset (-O) must not be -1 no data directory specified off oldest multitransaction ID (-m) must not be 0 oldest transaction ID (-u) must be greater than or equal to %u on pg_control exists but has invalid CRC; proceed with caution pg_control exists but is broken or wrong version; ignoring it pg_control specifies invalid WAL segment size (%d byte); proceed with caution pg_control specifies invalid WAL segment size (%d bytes); proceed with caution pg_control version number:            %u
 too many command-line arguments (first is "%s") transaction ID (-c) must be either 0 or greater than or equal to 2 transaction ID (-x) must be greater than or equal to %u transaction ID epoch (-e) must not be -1 unexpected empty file "%s" warning:  Project-Id-Version: pg_resetwal (PostgreSQL) 16
Report-Msgid-Bugs-To: pgsql-bugs@lists.postgresql.org
POT-Creation-Date: 2023-09-08 21:50+0000
PO-Revision-Date: 2023-11-06 08:49+0800
Last-Translator: Zhenbang Wei <znbang@gmail.com>
Language-Team: 
Language: zh_TW
MIME-Version: 1.0
Content-Type: text/plain; charset=UTF-8
Content-Transfer-Encoding: 8bit
Plural-Forms: nplurals=1; plural=0;
X-Generator: Poedit 3.4.1
 

å°è¦è®æ´çå¼:

 
å¦æéäºå¼çèµ·ä¾å¯ä»¥æ¥åï¼è«ç¨ -f å¼·å¶éè¨­ã
 
åå ±é¯èª¤è³ <%s>ã
       --wal-segsize=SIZE           WAL çæ®µçå¤§å°ï¼å®ä½æ¯ MB
   -?, --help                       é¡¯ç¤ºèªªæï¼ç¶å¾çµæ
   -O, --multixact-offset=OFFSET    è¨­å®ä¸ä¸å multitransaction offset
   -V, --version                    é¡¯ç¤ºçæ¬ï¼ç¶å¾çµæ
   -c, --commit-timestamp-ids=XID,XID
                                   è¨­å®å·ææäº¤æéæ³è¨çæèåææ°äº¤æ
                                   (0 è¡¨ç¤ºä¸ä¿®æ¹)
   -e, --epoch=XIDEPOCH             è¨­å®ä¸ä¸åäº¤æ ID ç epoch
   -f, --force                      å¼·å¶å·è¡æ´æ°
   -l, --next-wal-file=WALFILE      è¨­å®æ° WAL çæå°èµ·å§ä½ç½®
   -m, --multixact-ids=MXID,MXID    è¨­å®ä¸ä¸ååæèç multitransaction ID
   -n, --dry-run                    ä¸å·è¡æ´æ°ï¼åªé¡¯ç¤ºé è¨å·è¡çæä½
   -o, --next-oid=OID               è¨­å®ä¸ä¸å OID
   -u, --oldest-transaction-id=XID  è¨­å®æèçäº¤æ ID
   -x, --next-transaction-id=XID    è¨­å®ä¸ä¸åäº¤æ ID
  [-D, --pgdata=]DATADIR            è³æç®é
 %s ç¶²é : <%s>
 %s éè¨­ PostgreSQL ç write-ahead æ¥èªã

 64ä½åæ´æ¸ å¤§åéè¯æ¯åçæ®µçåå¡æ¸:  %u
 æ¯å WAL çæ®µçä½åçµæ¸:         %u
 Catalog çæ¬èç¢¼:                %u
 ç®åç pg_control å¼:

 è³æé æª¢æ¥ç¢¼çæ¬:                %u
 è³æåº«åå¡å¤§å°:                  %u
 è³æåº«ç³»çµ±è­å¥ç¢¼:                %llu
 æ¥æ/æéçå²å­æ¹å¼:             %s
 æªæ¡ %s åå« %sï¼èæ¬ç¨å¼ççæ¬ %s ä¸ç¸å®¹ éè¨­å¾çç¬¬ä¸åæ¥èªçæ®µ:          %s
 Float8 åæ¸å³éæ¹å¼:             %s
 çæ¸¬ç pg_control å¼:

 å¦æä½ ç¢ºå®è³æç®éçè·¯å¾æ­£ç¢ºï¼è«å·è¡
  touch %s
ç¶å¾éè©¦ã ä¼ºæå¨æ¯å¦æ­£å¨å·è¡ï¼å¦ææ²æï¼è«åªé¤éå®æªç¶å¾éè©¦ã ææ°æª¢æ¥é» NextMultiOffset:      %u
 ææ°æª¢æ¥é» NextMultiXactId:      %u
 ææ°æª¢æ¥é» NextOID:              %u
 ææ°æª¢æ¥é» NextXID:              %u:%u
 ææ°æª¢æ¥é» TimeLineID:           %u
 ææ°æª¢æ¥é» full_page_writes:     %s
 ææ°æª¢æ¥é» newestCommitTsXid:    %u
 ææ°æª¢æ¥é» oldestActiveXID:      %u
 ææ°æª¢æ¥é» oldestCommitTsXid:    %u
 ææ°æª¢æ¥é» oldestMulti çè³æåº«: %u
 ææ°æª¢æ¥é» oldestMultiXid:       %u
 ææ°æª¢æ¥é» oldestXID çè³æåº«:   %u
 ææ°æª¢æ¥é» oldestXID:            %u
 æå¤§ç´¢å¼æ¬ä½æ¸:                  %u
 è³æå°é½ä¸é:                    %u
 æå¤§è­å¥åç¨±é·åº¦:                %u
 TOAST å¡å¤§å°ä¸é:              %u
 OID (-o) ä¸å¯çº 0 é¸é :
 å¤§ç©ä»¶å¡çå¤§å°:                %u
 è³æåº«ä¼ºæå¨æªæ­£å¸¸ééã
éè¨­äº¤ææ¥èªå¯è½æå°è´è³æéºå¤±ã
å¦æä½ ä»è¦ç¹¼çºï¼è«ç¨ -f å¼·å¶éè¨­ã
 ç¨ "%s --help" åå¾æ´å¤è³è¨ã ç¨æ³:
  %s [OPTION]... DATADIR

 WAL åå¡å¤§å°:                    %u
 Write-ahead æ¥èªéè¨­
 æ¨å¿é ä»¥ PostgreSQL è¶ç´ä½¿ç¨èèº«åå·è¡ %sã --wal-segsize çåæ¸å¿é æ¯æ¸å­ --wal-segsize çåæ¸å¿é æ¯ä»æ¼1å°1024ä¹éçäºçæ¬¡æ¹æ¸ å³å å³å¼ ç¡æ³ç¨ "root" å·è¡ ç¡æ³éç½® SID: é¯èª¤ç¢¼ %lu ç¡æ³è®æ´ç®éå° "%s": %m ç¡æ³ééç®é "%s": %m ç¡æ³å»ºç«åéå¶ç token: é¯èª¤ç¢¼ %lu ç¡æ³åªé¤æªæ¡ "%s": %m ç¡æ³åå¾å­è¡ç¨ççµæç¢¼: é¯èª¤ç¢¼ %lu ç¡æ³éåç®é "%s": %m ç¡æ³éåæªæ¡"%s"é²è¡è®å: %m ç¡æ³éåæªæ¡ "%s": %m ç¡æ³éåè¡ç¨ token: é¯èª¤ç¢¼ %lu ç¡æ³ç¨åéå¶ç token éæ°å·è¡: é¯èª¤ç¢¼ %lu ç¡æ³è®åç®é "%s": %m ç¡æ³è®åæªæ¡ "%s": %m ç¡æ³è®åç®é"%s"çæ¬é: %m ç¡æ³ååå½ä»¤çè¡ç¨ "%s": é¯èª¤ç¢¼ %lu ç¡æ³å¯«å¥æªæ¡ "%s": %m è³æç®éçæ¬é¯èª¤ è©³ç´°å§å®¹:  é¯èª¤:  fsync é¯èª¤: %m æç¤º:  é¸é  %s çåæ¸ç¡æ éå®æª "%s" å·²å­å¨ multitransaction ID(-m) ä¸å¯çº 0 multitransaction offset(-O) ä¸å¯çº -1 æªæå®è³æç®é off æèç multitransaction ID(-m) ä¸å¯çº 0 æèçäº¤æ ID(-u) å¿é å¤§æ¼æç­æ¼ %u on pg_control å­å¨ä½ CRC ç¡æï¼è«è¬¹ææä½ã pg_control å­å¨ä½å·²æå£æçæ¬ä¸æ­£ç¢ºï¼å¿½ç¥å® pg_control æå®ç¡æç WAL çæ®µå¤§å°(%d ä½åçµ)ï¼è«è¬¹ææä½ pg_control çæ¬èç¢¼:             %u
 å½ä»¤ååæ¸éå¤(ç¬¬ä¸åæ¯ "%s") äº¤æ ID(-c) å¿é çº 0 æå¤§æ¼ç­æ¼ 2 äº¤æ ID(-x) å¿é å¤§æ¼æç­æ¼ %u äº¤æ ID epoch (-e) ä¸å¯çº -1 éé æçç©ºæª "%s" è­¦å:  