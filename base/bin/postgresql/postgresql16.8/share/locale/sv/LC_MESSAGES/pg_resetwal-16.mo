��    u      �  �   l      �	     �	  9   �	     5
  F   L
  =   �
  D   �
  I     �   `  A     ;   a  M   �  K   �  K   7  0   �  =   �  ;   �  2   .     a  +   u     �  )   �  )   �  )        /  )   L  )   v  +   �  )   �  R   �  )   I  )   s     �  U   �  A     )   R  )   |  )   �  ,   �  )   �  )   '  )   Q  )   {  )   �  )   �  )   �  )   #  )   M  )   w  )   �  )   �  )   �  )     )   I  )   s  )   �  )   �     �  )     )   2  )   \  )   �  	   �  )   �  �   �  %   �  !   �  )   �     �  ,     *   ;  C   f     �     �     �  '   �  &     "   ,  1   O     �  7   �  !   �  (   �     #  ,   @  :   m  !   �     �  0   �  8        Q  "   o     �     �     �     �     �     �  &   �  +     )   B     l     �  -   �  >   �  )   �     #  ;   &  =   b  �   �  )   =  /   g  B   �  7   �  (        ;  	   V  �  `     	   Y   %         F   �   D   �   H   '!  K   p!  �   �!  M   �"  :   �"  J   9#  Q   �#  T   �#  4   +$  A   `$  @   �$  /   �$     %  3   *%     ^%  /   o%  /   �%  /   �%  $   �%  0   $&  /   U&  1   �&  /   �&  \   �&  .   D'  0   s'  "   �'  q   �'  @   9(  /   z(  /   �(  /   �(  2   
)  /   =)  /   m)  /   �)  /   �)  /   �)  0   -*  /   ^*  0   �*  /   �*  /   �*  0   +  1   P+  0   �+  +   �+  +   �+  +   ,  -   7,  +   e,     �,  ,   �,  +   �,  ,   -  +   2-  	   ^-  0   h-  �   �-  .   Z.  +   �.  /   �.  #   �.  .   	/  1   8/  I   j/     �/     �/     �/  #   �/  %   
0  #   00  ;   T0     �0  9   �0  "   �0  .   1     ;1  +   Z1  D   �1  !   �1     �1  2   2  8   >2     w2     �2     �2     �2     �2     �2  !   �2  !   3  +   #3  /   O3  )   3     �3     �3  3   �3  F   �3  )   A4     k4  B   o4  I   �4  �   �4  0   �5  3   �5  N   6  >   P6  2   �6     �6  	   �6     <      i   X       Y   [   R   8   n   g       h   l           ,       1   U   0             +   A   M   -   k       t      '      N                 7   &          T          m                 /               >   G   (   4   f   D              #   6           Z   a   ]   p   r      %   9       L       3   q          .   	   )   H   @      c           5   *       o   !   $   e                W   
      ?           =   2                   s   J               O   j      d   V   \       b   C   F       B   _   P   I         :   ;   u   Q   S       `              E          K       ^       "    

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
 You must run %s as the PostgreSQL superuser. argument of --wal-segsize must be a number argument of --wal-segsize must be a power of two between 1 and 1024 by reference by value cannot be executed by "root" could not allocate SIDs: error code %lu could not change directory to "%s": %m could not close directory "%s": %m could not create restricted token: error code %lu could not delete file "%s": %m could not get exit code from subprocess: error code %lu could not open directory "%s": %m could not open file "%s" for reading: %m could not open file "%s": %m could not open process token: error code %lu could not re-execute with restricted token: error code %lu could not read directory "%s": %m could not read file "%s": %m could not read permissions of directory "%s": %m could not start process for command "%s": error code %lu could not write file "%s": %m data directory is of wrong version detail:  error:  fsync error: %m hint:  invalid argument for option %s lock file "%s" exists multitransaction ID (-m) must not be 0 multitransaction offset (-O) must not be -1 newestCommitTsXid:                    %u
 no data directory specified off oldest multitransaction ID (-m) must not be 0 oldest transaction ID (-u) must be greater than or equal to %u oldestCommitTsXid:                    %u
 on pg_control exists but has invalid CRC; proceed with caution pg_control exists but is broken or wrong version; ignoring it pg_control specifies invalid WAL segment size (%d byte); proceed with caution pg_control specifies invalid WAL segment size (%d bytes); proceed with caution pg_control version number:            %u
 too many command-line arguments (first is "%s") transaction ID (-c) must be either 0 or greater than or equal to 2 transaction ID (-x) must be greater than or equal to %u transaction ID epoch (-e) must not be -1 unexpected empty file "%s" warning:  Project-Id-Version: PostgreSQL 15
Report-Msgid-Bugs-To: pgsql-bugs@lists.postgresql.org
POT-Creation-Date: 2023-08-31 19:50+0000
PO-Revision-Date: 2023-08-31 22:00+0200
Last-Translator: Dennis Björklund <db@zigo.dhs.org>
Language-Team: Swedish <pgsql-translators@postgresql.org>
Language: sv
MIME-Version: 1.0
Content-Type: text/plain; charset=UTF-8
Content-Transfer-Encoding: 8bit
Plural-Forms: nplurals=2; plural=n != 1;
 

Värden att förändra:

 
Om dessa värden verkar godtagbara, använd då -f för att
framtvinga återställning.
 
Rapportera fel till <%s>.
       --wal-segsize=STORLEK        storlek på WAL-segment i megabyte
   -?, --help                       visa denna hjälp, avsluta sedan
   -O, --multixact-offset=OFFSET    sätt nästa multitransaktionsoffset
   -V, --version                    visa versionsinformation, avsluta sedan
   -c, --commit-timestamp-ids=XID,XID
                                   sätt äldsta och nyaste transaktionerna som
                                   kan ha commit-tidstämpel (noll betyder
                                   ingen ändring)
   -e, --epoch=XIDEPOCH             sätter epoch för nästa transaktions-ID
   -f, --force                      framtvinga uppdatering
   -l, --next-wal-file=WALFIL       sätt minsta startposition för ny WAL
   -m, --multixact-ids=MXID,MXID    sätt nästa och äldsta multitransaktions-ID
   -n, --dry-run                    ingen updatering; visa bara planerade åtgärder
   -o, --next-oid=OID               sätt nästa OID
   -u, --oldest-transaction-id=XID  sätt äldsta transaktions-ID
   -x, --next-transaction-id=XID    sätt nästa transaktions-ID
  [-D, --pgdata=]DATADIR            datakatalog
 hemsida för %s: <%s>
 %s återställer write-ahead-log för PostgreSQL.

 64-bitars heltal Block per segment i en stor relation:       %u
 Segmentstorlek i transaktionsloggen:        %u
 Katalogversion:                             %u
 Nuvarande värden för pg_control:

 Checksummaversion för datasidor:            %u
 Databasens blockstorlek:                    %u
 Databasens systemidentifierare:             %llu
 Representation av dag och tid:              %s
 Filen "%s" innehåller "%s", vilket inte är kompatibelt med detta programmets version "%s". Första loggsegment efter återställning: %s
 Åtkomst till float8-argument:               %s
 Gissade värden för pg_control:

 Om du är säker på att sökvägen till datakatalogen är riktig,
utför då "touch %s" och försök sedan igen. Kör servern redan? Om inte, radera låsfilen och försök igen. NextMultiOffset vid senaste kontrollpunkt:  %u
 NextMultiXactId vid senaste kontrollpunkt:  %u
 NextOID vid senaste kontrollpunkt:          %u
 NextXID vid senaste kontrollpunkt:          %u:%u
 TimeLineID vid senaste kontrollpunkt:       %u
 Senaste kontrollpunktens full_page_writes:  %s
 newestCommitTsXid vid senaste kontrollpunkt:%u
 oldestActiveXID vid senaste kontrollpunkt:  %u
 oldestCommitTsXid vid senaste kontrollpunkt:%u
 DB för oldestMulti vid senaste kontrollpkt: %u
 oldestMultiXid vid senaste kontrollpunkt:   %u
 DB för oldestXID vid senaste kontrollpunkt: %u
 oldestXID vid senaste kontrollpunkt:        %u
 Maximalt antal kolonner i ett index:        %u
 Maximal jämkning av data (alignment):       %u
 Maximal längd för identifierare:            %u
 Maximal storlek för en TOAST-enhet:         %u
 NextMultiOffset:                        %u
 NextMultiXactId:                        %u
 NextOID:                                %u
 Epoch för NextXID:                       %u
 NextXID:                                %u
 OID (-o) får inte vara 0. DB för OldestMulti:                     %u
 OldestMultiXid:                         %u
 DB för OldestXID:                       %u
 OldestXID:                              %u
 Flaggor:
 Storlek för large-object-enheter:           %u
 Databasservern stängdes inte av ordentligt. Att återställa
write-ahead-loggen kan medföra att data förloras. Om du ändå
vill fortsätta, använd -f för att framtvinga återställning.
 Försök med "%s --help" för mer information. Användning:
  %s [FLAGGA]... DATAKATALOG

 Blockstorlek i transaktionsloggen:          %u
 Återställning av write-ahead-log
 Du måste köra %s som PostgreSQL:s superuser. argumentet till --wal-segsize måste vara ett tal argumentet till --wal-segsize måste vara en tvåpotens mellan 1 och 1024 referens värdeåtkomst kan inte köras av "root" kunde inte allokera SID: felkod %lu kunde inte byta katalog till "%s": %m kunde inte stänga katalog "%s": %m kunde inte skapa token för begränsad åtkomst: felkod %lu kunde inte radera fil "%s": %m kunde inte hämta statuskod för underprocess: felkod %lu kunde inte öppna katalog "%s": %m kunde inte öppna filen "%s" för läsning: %m kunde inte öppna fil "%s": %m kunde inte öppna process-token: felkod %lu kunde inte köra igen med token för begränsad åtkomst: felkod %lu kunde inte läsa katalog "%s": %m kunde inte läsa fil "%s": %m kunde inte läsa rättigheter på katalog "%s": %m kunde inte starta process för kommando "%s": felkod %lu kunde inte skriva fil "%s": %m datakatalogen har fel version detalj:  fel:  misslyckad fsync: %m tips:  ogiltigt argument för flaggan %s låsfil med namn "%s" finns redan Multitransaktions-ID (-m) får inte vara 0. Multitransaktionsoffset (-O) får inte vara -1. newestCommitTsXid:                    %u
 ingen datakatalog angiven av Äldsta multitransaktions-ID (-m) får inte vara 0. äldsta transaktions-ID (-u) måste vara större än eller lika med %u oldestCommitTsXid:                    %u
 på pg_control existerar men har ogiltig CRC. Fortsätt med varsamhet. pg_control existerar men är trasig eller har fel version. Den ignoreras. pg_control anger ogiltig WAL-segmentstorlek (%d byte); fortsätt med varsamhet. pg_control anger ogiltig WAL-segmentstorlek (%d byte); fortsätt med varsamhet. Versionsnummer för pg_control:              %u
 för många kommandoradsargument (första är "%s") transaktions-ID (-c) måste antingen vara 0 eller större än eller lika med 2 transaktions-ID (-x) måste vara större än eller lika med %u Epoch (-e) för transaktions-ID får inte vara -1. oväntad tom fil "%s" varning:  