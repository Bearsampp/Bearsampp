��    w      �  �   �      
     
  9   3
     m
  F   �
  =   �
  D   	  I   N  �   �  A   W  ;   �  M   �  K   #  K   o  0   �  =   �  ;   *  2   f     �  +   �     �  )   �  )     )   =     g  )   �  )   �  +   �  )     R   .  )   �  )   �     �  U   �  A   H  )   �  )   �  )   �  ,     )   5  )   _  )   �  )   �  )   �  )     )   1  )   [  )   �  )   �  )   �  )     )   -  )   W  )   �  )   �  )   �  )   �     )  )   @  )   j  )   �  )   �  	   �  )   �  �     %   �  !   �  )        /  ,   F  *   s  A   �     �     �     �  @     '   T  &   |  "   �  1   �     �  7     +   O  !   {  (   �     �  ,   �  :     !   K     m  0   �  8   �     �  "        5     >     F     V     ]     |  &   �  +   �  )   �          +  -   /  >   ]  )   �     �  ;   �  =     �   C  )   �  /   
  B   :  7   }  (   �     �  	   �  �       �   Q   �      5!  D   M!  C   �!  M   �!  I   $"  �   n"  E   W#  0   �#  S   �#  W   "$  J   z$  -   �$  H   �$  G   <%  +   �%     �%  5   �%     &  -   &  &   A&  (   h&      �&  ,   �&  &   �&  7   '  )   >'  \   h'  7   �'  (   �'     &(  a   F(  W   �(  +    )  +   ,)  +   X)  2   �)  ,   �)  ,   �)  5   *  4   G*  6   |*  .   �*  4   �*  4   +  ,   L+  2   y+  "   �+  3   �+  .   ,  "   2,  "   U,  *   x,  #   �,  *   �,     �,  *   -  $   8-  *   ]-  &   �-     �-  D   �-  �   �-  -   �.  %   �.     /  $   3/  0   X/  2   �/  D   �/     0  
   0  "   0  T   @0  0   �0  -   �0  *   �0  7   1  &   W1  V   ~1  ;   �1  (   2  2   :2  #   m2  =   �2  ?   �2  )   3  $   93  7   ^3  I   �3  %   �3  +   4     24  	   >4     H4     Y4  %   h4  "   �4  ,   �4  1   �4     5  %   /5     U5  9   \5  E   �5  "   �5     �5  >   6  J   E6  �   �6  '   L7  9   t7  ;   �7  8   �7  .   #8     R8     m8         >   9   E   #   o      T                  1          6           !               \            d       C   `       S   
   p   n   ^   4       F   c   h       U   N   r       (       '       V      t   ,       *      W   B   3   Z       a   u   Y      q   P   v           %          	   e   J      @       A       H          _   G   l           0   m   ?   =   .   $             Q   s                                      [   O       2      5   +       j   M   f       w   8                     X   k       ;   /      7           K       R   ]   D   I   -   &       )   <   "          :       L   i   g       b        

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
 You must run %s as the PostgreSQL superuser. argument of --wal-segsize must be a number argument of --wal-segsize must be a power of 2 between 1 and 1024 by reference by value cannot be executed by "root" cannot create restricted tokens on this platform: error code %lu could not allocate SIDs: error code %lu could not change directory to "%s": %m could not close directory "%s": %m could not create restricted token: error code %lu could not delete file "%s": %m could not get exit code from subprocess: error code %lu could not load library "%s": error code %lu could not open directory "%s": %m could not open file "%s" for reading: %m could not open file "%s": %m could not open process token: error code %lu could not re-execute with restricted token: error code %lu could not read directory "%s": %m could not read file "%s": %m could not read permissions of directory "%s": %m could not start process for command "%s": error code %lu could not write file "%s": %m data directory is of wrong version detail:  error:  fsync error: %m hint:  invalid argument for option %s lock file "%s" exists multitransaction ID (-m) must not be 0 multitransaction offset (-O) must not be -1 newestCommitTsXid:                    %u
 no data directory specified off oldest multitransaction ID (-m) must not be 0 oldest transaction ID (-u) must be greater than or equal to %u oldestCommitTsXid:                    %u
 on pg_control exists but has invalid CRC; proceed with caution pg_control exists but is broken or wrong version; ignoring it pg_control specifies invalid WAL segment size (%d byte); proceed with caution pg_control specifies invalid WAL segment size (%d bytes); proceed with caution pg_control version number:            %u
 too many command-line arguments (first is "%s") transaction ID (-c) must be either 0 or greater than or equal to 2 transaction ID (-x) must be greater than or equal to %u transaction ID epoch (-e) must not be -1 unexpected empty file "%s" warning:  Project-Id-Version: PostgreSQL 15
Report-Msgid-Bugs-To: pgsql-bugs@lists.postgresql.org
POT-Creation-Date: 2022-09-26 08:18+0000
PO-Revision-Date: 2022-10-02 19:06+0200
Last-Translator: Peter Eisentraut <peter@eisentraut.org>
Language-Team: German <pgsql-translators@postgresql.org>
Language: it
MIME-Version: 1.0
Content-Type: text/plain; charset=UTF-8
Content-Transfer-Encoding: 8bit
Plural-Forms: nplurals=2; plural=n != 1;
X-Generator: Poedit 3.1.1
 

Valori da modificare:

 
Se questi valori sembrano accettabili, utilizzare -f per forzare il ripristino.
 
Segnala i bug a <%s>.
        --wal-segsize=SIZE dimensione dei segmenti WAL, in megabyte 
  -?, --help                       mostra questo aiuto, quindi esci
   -O, --multixact-offset=OFFSET imposta l'offset multitransazione successivo
    -V, --version restituisce le informazioni sulla versione, quindi esci
   -c, --commit-timestamp-ids=XID,XID
                                   impostare il rilevamento delle transazioni più vecchie e più recenti
                                   timestamp di commit (zero significa nessuna modifica)
  -e, --epoch=XIDEPOCH imposta l'epoca dell'ID transazione successiva
   -f, --force forza l'aggiornamento da eseguire
  -l, --next-wal-file=WALFILE imposta la posizione iniziale minima per il nuovo WAL
  -m, --multixact-ids=MXID,MXID imposta l'ID multitransazione successivo e meno recente
  -n, --dry-run nessun aggiornamento, mostra solo cosa sarebbe stato fatto
  -o, --next-oid=OID imposta l'OID successivo
   -u, --oldest-transaction-id=XID imposta l'ID transazione più vecchio
      -x, --next-transaction-id=XID imposta l'ID transazione successiva
  [-D, --pgdata=]DATADIR directory dei dati
 Pagina iniziale di %s: <%s>
 %s reimposta il registro write-ahead di PostgreSQL.

 Interi a 64 bit Blocchi per segmento di relazione grande: %u
 Byte per segmento WAL:             %u
 Numero di versione del catalogo:     %u
 Valori correnti di pg_control:

 Versione checksum pagina dati:         %u
 
 Dimensione blocco database:        %u
 Identificatore di sistema del database:           %llu
 Tipo di archiviazione data/ora:       %s
 Il file "%s" contiene "%s", che non è compatibile con la versione "%s" di questo programma. Primo segmento di registro dopo il ripristino:      %s
 Passaggio argomento float8:          %s
 Valori pg_control ipotizzati:

 Se sei sicuro che il percorso della directory dei dati sia corretto, esegui
  tocca %s
e riprova. Un server è in esecuzione? In caso contrario, eliminare il file di blocco e riprovare. NextMultiOffset dell'ultimo checkpoint: %u
 NextMultiXactId dell'ultimo checkpoint: %u
 NextOID dell'ultimo checkpoint:         %u
 NextXID dell'ultimo checkpoint:             %u:%u
 TimeLineID dell'ultimo checkpoint:       %u
 Full_page_writes dell'ultimo checkpoint: %s
 Il più recenteCommitTsXid dell'ultimo checkpoint:%u
 L'ActiveXID più vecchio dell'ultimo checkpoint: %u
 Il più vecchio CommitTsXid dell'ultimo checkpoint:%u
 Il più vecchio DB dell'ultimo checkpoint: %u
 Il più vecchio MultiXid dell'ultimo checkpoint: %u
 DB dell'XID più vecchio dell'ultimo checkpoint: %u
 XID più vecchio dell'ultimo checkpoint: %u
 Numero massimo di colonne in un indice:        %u
 Allineamento massimo dei dati: %u
 Lunghezza massima degli identificatori:         %u
 Dimensione massima di un blocco TOAST:     %u
 NextMultiOffset:               %u
 NextMultiXactId:               %u
 NextOID:                               %u
 Epoca NextXID:                  %u
 NextXID:                               %u
 OID (-o) non deve essere 0 DB di OldestMulti:                     %u
 OldestMultiXid:                  %u
 DB di OldestXID:                       %u
 XID più vecchio:                  %u
 Opzioni
 Dimensione di un blocco di oggetti di grandi dimensioni:         %u
 Il server del database non è stato arrestato correttamente.
La reimpostazione del registro write-ahead potrebbe causare la perdita di dati.
Se vuoi procedere comunque, usa -f per forzare il reset.
 Prova "%s --help" per ulteriori informazioni. Utilizzo:
  %s [OPZIONE]... DATADIR

 Dimensione blocco WAL: %u
 Ripristino del registro write-ahead
 Devi eseguire %s come superutente di PostgreSQL. l'argomento di --wal-segsize deve essere un numero argomento di --wal-segsize deve essere una potenza di 2 tra 1 e 1024 come riferimento per valore non può essere eseguito da "root" impossibile creare token con restrizioni su questa piattaforma: codice di errore %lu impossibile allocare i SID: codice di errore %lu impossibile cambiare la directory in "%s": %m impossibile chiudere la directory "%s": %m impossibile creare token limitato: codice di errore %lu impossibile eliminare il file "%s": %m impossibile ottenere il codice di uscita dal processo secondario: codice di errore %lu impossibile caricare la libreria "%s": codice di errore %lu impossibile aprire la directory "%s": %m impossibile aprire il file "%s" per la lettura: %m impossibile aprire il file "%s": %m impossibile aprire il token di processo: codice di errore %lu impossibile rieseguire con token limitato: codice di errore %lu impossibile leggere la directory "%s": %m impossibile leggere il file "%s": %m impossibile leggere i permessi della directory "%s": %m impossibile avviare il processo per il comando "%s": codice di errore %lu impossibile scrivere il file "%s": %m la directory dei dati è di versione errata dettaglio:  errore:   errore fsync: %m suggerimento:  argomento non valido per l'opzione %s il file di blocco "%s" esiste già l'ID multitransazione (-m) non deve essere 0 l'offset multitransazione (-O) non deve essere -1 newestCommitTsXid:         %u
 nessuna directory di dati specificata spento l'ID multitransazione più vecchio (-m) non deve essere 0 l'ID transazione più vecchio (-u) deve essere maggiore o uguale a %u oldCommitTsXid:                %u
 acceso pg_control esiste ma ha un CRC non valido; procedi con cautela pg_control esiste ma è una versione non funzionante o errata; ignorandolo pg_control specifica la dimensione del segmento WAL non valida (%d byte); procedi con cautela pg_control specifica la dimensione dei segmenti WAL non valida (%d byte); procedi con cautela pg_control numero di versione:      %u
 troppi argomenti della riga di comando (il primo è "%s") l'ID transazione (-c) deve essere 0 o maggiore o uguale a 2 l'ID transazione (-x) deve essere maggiore o uguale a %u l'ID transazione epoch (-e) non deve essere -1 file vuoto imprevisto "%s" avviso:  