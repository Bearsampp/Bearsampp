��    g      T  �   �      �  
   �     �  %   �  6   �  3   0	  P   d	  �   �	  P   ?
  ?   �
  I   �
  =     A   X  6   �  �   �  D   �  �   �  >   �  �   �  B   a  C   �  ~   �     g  D   j     �     �  9   �  4     2   M  ;   �  @   �  R   �     P  :   p  %   �     �  �   �  P   `  Q   �  �         �  #        %  #   C  -   g  )   �     �     �     �  6     !   N     p     �     �  '   �  *   �  5     T   L  I   �  @   �  =   ,     j  +   �     �  &   �  .   �           4  |   <     �  ;   �     �          2  5   O     �     �  >   �  A   �  <   6  <   s  $   �  '   �  *   �      (  \   I     �     �  ,   �  6     :   =  !   x  Q   �  "   �  .     #   >  $   b  0   �  $   �  /   �  A     $   O  	   t  �  ~  
   )      4   &   P   <   w   ?   �   v   �   �   k!  k   �!  A   f"  E   �"  A   �"  M   0#  /   ~#  �   �#  G   �$  �   �$  A   �%  �   �%  A   [&  C   �&  ~   �&     `'  C   c'     �'  #   �'  ?   �'  >   "(  ;   a(  ?   �(  E   �(  U   #)      y)  L   �)  .   �)     *  �   $*  K   �*  N   �*  �   I+      ,  %   @,     f,  !   �,  /   �,  +   �,  !   -     &-  "   F-  2   i-  "   �-     �-     �-     �-  *   .  /   D.  9   t.  \   �.  P   /  F   \/  E   �/     �/  .   0     70  )   @0  -   j0     �0     �0  y   �0     51  9   <1     v1     �1     �1  :   �1     2     2  >   52  D   t2  <   �2  <   �2  &   33  '   Z3  +   �3  &   �3  b   �3     84      N4  .   o4  6   �4  4   �4  $   
5  X   /5  &   �5  ,   �5  !   �5     �5  .   6  +   M6  3   y6  ?   �6  !   �6  	   7           /   ,   "   '   I       Z           <       @   7       6          F          f   ;   S       e      ]   `   g   C       +   $   -   E   R   \   a           2   *   U       Q           Y   b         A      K   1   8   	   d                    B         V   O                     4   )   >   %   (       .      :   _   
   P   3            H       D   &       L                      [      c       N      5      J   0   M      T       =                    9          X   G           ?       #       !   W   ^    
Options:
 
Report bugs to <%s>.
   %s [OPTION]... [STARTSEG [ENDSEG]]
   --save-fullpage=DIR    save full page images to DIR
   -?, --help             show this help, then exit
   -B, --block=N          with --relation, only show records that modify block N
   -F, --fork=FORK        only show records that modify blocks in fork FORK;
                         valid names are main, fsm, vm, init
   -R, --relation=T/D/R   only show records that modify blocks in relation T/D/R
   -V, --version          output version information, then exit
   -b, --bkp-details      output detailed information about backup blocks
   -e, --end=RECPTR       stop reading at WAL location RECPTR
   -f, --follow           keep retrying after reaching end of WAL
   -n, --limit=N          number of records to display
   -p, --path=PATH        directory in which to find WAL segment files or a
                         directory with a ./pg_wal that contains such files
                         (default: current directory, ./pg_wal, $PGDATA/pg_wal)
   -q, --quiet            do not print any output, except for errors
   -r, --rmgr=RMGR        only show records generated by resource manager RMGR;
                         use --rmgr=list to list valid resource manager names
   -s, --start=RECPTR     start reading at WAL location RECPTR
   -t, --timeline=TLI     timeline from which to read WAL records
                         (default: 1 or the value used in STARTSEG)
   -w, --fullpage         only show records with a full page write
   -x, --xid=XID          only show records with transaction ID XID
   -z, --stats[=record]   show statistics instead of records
                         (optionally, show per-record statistics)
 %s %s decodes and displays PostgreSQL write-ahead logs for debugging.

 %s home page: <%s>
 %s must be in range %u..%u BKPBLOCK_HAS_DATA not set, but data length is %u at %X/%X BKPBLOCK_HAS_DATA set, but no data included at %X/%X BKPBLOCK_SAME_REL set but no previous rel at %X/%X BKPIMAGE_COMPRESSED set, but block image length %u at %X/%X BKPIMAGE_HAS_HOLE not set, but hole offset %u length %u at %X/%X BKPIMAGE_HAS_HOLE set, but hole offset %u length %u block image length %u at %X/%X ENDSEG %s is before STARTSEG %s Expecting "tablespace OID/database OID/relation filenode". Try "%s --help" for more information. Usage:
 WAL file is from different database system: WAL file database system identifier is %llu, pg_control database system identifier is %llu WAL file is from different database system: incorrect XLOG_BLCKSZ in page header WAL file is from different database system: incorrect segment size in page header WAL segment size must be a power of two between 1 MB and 1 GB, but the WAL file "%s" header specifies %d byte WAL segment size must be a power of two between 1 MB and 1 GB, but the WAL file "%s" header specifies %d bytes contrecord is requested by %X/%X could not access directory "%s": %m could not close file "%s": %m could not create directory "%s": %m could not decompress image at %X/%X, block %d could not find a valid record after %X/%X could not find any WAL file could not find file "%s": %m could not locate WAL file "%s" could not locate backup block with ID %d in WAL record could not open directory "%s": %m could not open file "%s" could not open file "%s": %m could not read file "%s": %m could not read file "%s": read %d of %d could not read from file %s, offset %d: %m could not read from file %s, offset %d: read %d of %d could not restore image at %X/%X compressed with %s not supported by build, block %d could not restore image at %X/%X compressed with unknown method, block %d could not restore image at %X/%X with invalid block %d specified could not restore image at %X/%X with invalid state, block %d could not write file "%s": %m custom resource manager "%s" does not exist detail:  directory "%s" exists but is not empty end WAL location %X/%X is not inside file "%s" error in WAL record at %X/%X: %s error:  first record is after %X/%X, at %X/%X, skipping over %u byte
 first record is after %X/%X, at %X/%X, skipping over %u bytes
 hint:  incorrect resource manager data checksum in record at %X/%X invalid WAL location: "%s" invalid block number: "%s" invalid block_id %u at %X/%X invalid contrecord length %u (expected %lld) at %X/%X invalid fork name: "%s" invalid fork number: %u invalid info bits %04X in WAL segment %s, LSN %X/%X, offset %u invalid magic number %04X in WAL segment %s, LSN %X/%X, offset %u invalid record length at %X/%X: expected at least %u, got %u invalid record offset at %X/%X: expected at least %u, got %u invalid relation specification: "%s" invalid resource manager ID %u at %X/%X invalid transaction ID specification: "%s" invalid value "%s" for option %s neither BKPIMAGE_HAS_HOLE nor BKPIMAGE_COMPRESSED set, but block image length is %u at %X/%X no arguments specified no start WAL location given option %s requires option %s to be specified out of memory while allocating a WAL reading processor out of memory while trying to decode a record of length %u out-of-order block_id %u at %X/%X out-of-sequence timeline ID %u (after %u) in WAL segment %s, LSN %X/%X, offset %u record length %u at %X/%X too long record with incorrect prev-link %X/%X at %X/%X record with invalid length at %X/%X resource manager "%s" does not exist start WAL location %X/%X is not inside file "%s" there is no contrecord flag at %X/%X too many command-line arguments (first is "%s") unexpected pageaddr %X/%X in WAL segment %s, LSN %X/%X, offset %u unrecognized value for option %s: %s warning:  Project-Id-Version: PostgreSQL 16
Report-Msgid-Bugs-To: pgsql-bugs@lists.postgresql.org
POT-Creation-Date: 2023-08-02 03:17+0000
PO-Revision-Date: 2023-08-30 08:59+0200
Last-Translator: Dennis Björklund <db@zigo.dhs.org>
Language-Team: Swedish <pgsql-translators@postgresql.org>
Language: sv
MIME-Version: 1.0
Content-Type: text/plain; charset=UTF-8
Content-Transfer-Encoding: 8bit
Plural-Forms: nplurals=2; plural=(n != 1);
 
Flaggor:
 
Rapportera fel till <%s>.
   %s [FLAGGA]... [STARTSEG [SLUTSEG]]
   --save-fullpage=KAT    spara kopia av hela sidor till KAT
   -?, --help             visa den här hjälpen, avsluta sedan
   -B, --block=N          tillsammans med --relation, visa bara poster som
                         modifierar block N
   -F, --fork=GREN        visa bara poster som modifierar block i grenen GREN
                         gilriga namn är main, fsm, vm och init
   -R, --relation=T/D/R   visa bara poster som modifierar block i
                         relationen T/D/R
   -V, --version          visa versionsinformation, avsluta sedan
   -b, --bkp-details      skriv detaljerad information om backupblock
   -e, --end=RECPTR       stoppa läsning vid WAL-position RECPTR
   -f, --follow           fortsätt försök efter att ha nått slutet av WAL
   -n, --limit=N          antal poster att visa
   -p, --path=SÖKVÄG      katalog där man hittar WAL-segmentfiler eller en
                         katalog med en ./pg_wal som innehåller sådana filer
                         (standard: aktuell katalog, ./pg_wal, $PGDATA/pg_wal)
   -q, --quiet            skriv inte ut några meddelanden förutom fel
   -r, --rmgr=RMGR        visa bara poster skapade av resurshanteraren RMGR;
                         använd --rmgr=list för att lista giltiga resurshanterarnamn
   -s, --start=RECPTR     börja läsning vid WAL-position RECPTR
   -t, --timeline=TLI     tidslinje från vilken vi läser WAL-poster
                         (standard: 1 eller värdet som används i STARTSEG)
   -w, --fullpage         visa bara poster som skrivit hela sidor
   -x, --xid=XID          visa baras poster med transaktions-ID XID
   -z, --stats[=post]     visa statistik istället för poster
                         (alternativt, visa statistik per post)
 %s %s avkodar och visar PostgreSQLs write-ahead-logg för debuggning.
 hemsida för %s: <%s>
 %s måste vara i intervallet %u..%u BKPBLOCK_HAS_DATA är ej satt men datalängden är %u vid %X/%X BKPBLOCK_HAS_DATA är satt men ingen data inkluderad vid %X/%X BKPBLOCK_SAME_REL är satt men ingen tidigare rel vid %X/%X BKPIMAGE_COMPRESSED är satt men blockavbildlängd %u vid %X/%X BKPIMAGE_HAS_HOLE är inte satt men håloffset %u längd %u vid %X/%X BKPIMAGE_HAS_HOLE är satt men håloffset %u längd %u blockavbildlängd %u vid %X/%X SLUTSEG %s är före STARTSEG %s Skall vara en av "OID för tabellutrymme/OID för databas/relations filnod". Försök med "%s --help" för mer information. Användning:
 WAL-fil är från ett annat databassystem: WAL-filens databassystemidentifierare är %llu, pg_control databassystemidentifierare är %llu WAL-fil är från ett annat databassystem: inkorrekt XLOG_BLCKSZ i sidhuvud WAL-fil är från ett annat databassystem: inkorrekt segmentstorlek i sidhuvud WAL-segmentstorlek måste vara en tvåpotens mellan 1MB och 1GB men headern i WAL-filen "%s" anger %d byte WAL-segmentstorlek måste vara en tvåpotens mellan 1MB och 1GB men headern i WAL-filen "%s" anger %d byte contrecord är begärd vid %X/%X kunde inte komma åt katalog "%s": %m kunde inte stänga fil "%s": %m kunde inte skapa katalog "%s": %m kunde inte packa upp avbild vid %X/%X, block %d kunde inte hitta en giltig post efter %X/%X kunde inte hitta några WAL-filer kunde inte hitta filen "%s": %m kunde inte lokalisera WAL-fil "%s" kunde inte hitta backup-block med ID %d i WAL-post kunde inte öppna katalog "%s": %m kunde inte öppna filen "%s" kunde inte öppna fil "%s": %m kunde inte läsa fil "%s": %m kunde inte läsa fil "%s": läste %d av %d Kunde inte läsa från fil %s på offset %d: %m kunde inte läsa från fil %s, offset %d, läste %d av %d kunde inte återställa avbild vid %X/%X, komprimerad med %s stöds inte av bygget, block %d kunde inte återställa avbild vid %X/%X, komprimerad med okänd metod, block %d kunde inte återställa avbild vid %X/%X med ogiltigt block %d angivet kunde inte återställa avbild vid %X/%X med ogiltigt state, block %d kunde inte skriva fil "%s": %m egendefinierad resurshanterare "%s" finns inte detalj:  katalogen "%s" existerar men är inte tom slut-WAL-position %X/%X är inte i filen "%s" fel i WAL-post vid %X/%X: %s fel:  första posten efter %X/%X, vid %X/%X, hoppar över %u byte
 första posten efter %X/%X, vid %X/%X, hoppar över %u byte
 tips:  felaktig resurshanterardatakontrollsumma i post vid %X/%X ogiltig WAL-position: "%s" ogiltigt portnummer "%s" ogiltig block_id %u vid %X/%X ogiltig contrecord-längd %u (förväntade %lld) vid %X/%X ogiltigt fork-namn: "%s" ogiltigt fork-nummer: %u ogiltiga infobitar %04X i WAL-segment %s, LSN %X/%X, offset %u felaktigt magiskt nummer %04X i WAL-segment %s, LSN %X/%X, offset %u ogiltig postlängd vid %X/%X: förväntade minst %u, fick %u ogiltig postoffset vid %X/%X: förväntade minst %u, fick %u ogiltig inställning av relation: "%s" ogiltigt resurshanterar-ID %u vid %X/%X ogiltig inställning av transaktions-ID: %s ogiltigt värde "%s" för flaggan "%s" varken BKPIMAGE_HAS_HOLE eller BKPIMAGE_COMPRESSED är satt men blockavbildlängd är %u vid %X/%X inga argument angivna ingen start-WAL-position angiven flaggan %s kräver att flaggan %s också anges slut på minne vid allokering av en WAL-läs-processor slut på minne vid avkodning av post med längden %u "ej i sekvens"-block_id %u vid %X/%X "ej i sekvens"-fel på tidslinje-ID %u (efter %u) i WAL-segment %s, LSN %X/%X, offset %u postlängd %u vid %X/%X är för lång post med inkorrekt prev-link %X/%X vid %X/%X post med ogiltig längd vid %X/%X resurshanterare "%s" finns inte start-WAL-position %X/%X är inte i filen "%s" det finns ingen contrecord-flagga vid %X/%X för många kommandoradsargument (första är "%s") oväntad sidadress %X/%X i WAL-segment %s, LSN %X/%X, offset %u okänt värde för flaggan %s: %s varning:  