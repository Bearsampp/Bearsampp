��    ]           �      �  X   �  
   B     M  3   f  ?   �  (   �  C   	     G	     [	     k	  ,   o	  ,   �	  )   �	  )   �	  )   
  )   G
  )   q
  )   �
  +   �
  )   �
  )     ,   E  )   r  ,   �  )   �  )   �  )     ,   G  )   t  )   �  ,   �  )   �  )     )   I  )   s  )   �  )   �  )   �  )     )   E  )   o  )   �  )   �  )   �  )     ,   A  )   n     �  )   �  >  �  )     %   A     g  )   o  �   �  "   `     �     �     �     �     �  (   �          2  (   O     x     �     �     �  )   �  )   �  )     )   H  )   r     �     �     �     �  )   �  )   �      	        &     <     J  /   V  )   �     �     �  )   �  )   
     4  �  8  j   4  
   �     �  5   �  D   �  ,   ?  K   l     �     �     �  2   �  2     /   L  /   |  /   �  /   �  /     /   <  8   l  /   �  /   �  2     /   8  2   h  /   �  /   �  /   �  2   +  /   ^  /   �  2   �  /   �  /   !  /   Q  /   �  /   �  /   �  /      /   A   /   q   /   �   /   �   /   !  /   1!  /   a!  2   �!  /   �!     �!  /   "  K  ;"  /   �#  ,   �#  
   �#  /   �#  �   $  3   �$     3%  
   C%  $   N%  "   s%     �%  -   �%  "   �%  !   &  .   )&  #   X&  "   |&     �&     �&  /   �&  /   �&  /   -'  2   ]'  /   �'     �'  %   �'     �'     �'  /   �'  /   .(  F  ^(     �)     �)     �)     �)  9   �)  /   $*     T*     p*  /   �*  /   �*     �*     5            -   :               G   [   4                     1           $   J       ]   @                         !   2                  =       '   
       C         E   \   >   ;       "   &          D   Q          U   #   Y   <   L       3   Z      /       ,   	   %   8          (   N   I            7   H           V   .   0   9                  X   A              K   F   B      +   S   P      R   O   T   *      6      W   ?                                  )   M    
If no data directory (DATADIR) is specified, the environment variable PGDATA
is used.

 
Options:
   %s [OPTION] [DATADIR]
   -?, --help             show this help, then exit
   -V, --version          output version information, then exit
  [-D, --pgdata=]DATADIR  data directory
 %s displays control information of a PostgreSQL database cluster.

 %s home page: <%s>
 64-bit integers ??? Backup end location:                  %X/%X
 Backup start location:                %X/%X
 Blocks per segment of large relation: %u
 Bytes per WAL segment:                %u
 Catalog version number:               %u
 Data page checksum version:           %u
 Database block size:                  %u
 Database cluster state:               %s
 Database system identifier:           %llu
 Date/time type storage:               %s
 End-of-backup record required:        %s
 Fake LSN counter for unlogged rels:   %X/%X
 Float8 argument passing:              %s
 Latest checkpoint location:           %X/%X
 Latest checkpoint's NextMultiOffset:  %u
 Latest checkpoint's NextMultiXactId:  %u
 Latest checkpoint's NextOID:          %u
 Latest checkpoint's NextXID:          %u:%u
 Latest checkpoint's PrevTimeLineID:   %u
 Latest checkpoint's REDO WAL file:    %s
 Latest checkpoint's REDO location:    %X/%X
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
 Min recovery ending loc's timeline:   %u
 Minimum recovery ending location:     %X/%X
 Mock authentication nonce:            %s
 Report bugs to <%s>.
 Size of a large-object chunk:         %u
 The WAL segment size stored in the file, %d byte, is not a power of two
between 1 MB and 1 GB.  The file is corrupt and the results below are
untrustworthy.

 The WAL segment size stored in the file, %d bytes, is not a power of two
between 1 MB and 1 GB.  The file is corrupt and the results below are
untrustworthy.

 Time of latest checkpoint:            %s
 Try "%s --help" for more information. Usage:
 WAL block size:                       %u
 WARNING: Calculated CRC checksum does not match value stored in file.
Either the file is corrupt, or it has a different layout than this program
is expecting.  The results below are untrustworthy.

 WARNING: invalid WAL segment size
 by reference by value byte ordering mismatch could not close file "%s": %m could not fsync file "%s": %m could not open file "%s" for reading: %m could not open file "%s": %m could not read file "%s": %m could not read file "%s": read %d of %zu could not write file "%s": %m in archive recovery in crash recovery in production max_connections setting:              %d
 max_locks_per_xact setting:           %d
 max_prepared_xacts setting:           %d
 max_wal_senders setting:              %d
 max_worker_processes setting:         %d
 no no data directory specified off on pg_control last modified:             %s
 pg_control version number:            %u
 possible byte ordering mismatch
The byte ordering used to store the pg_control file might not match the one
used by this program.  In that case the results below would be incorrect, and
the PostgreSQL installation would be incompatible with this data directory. shut down shut down in recovery shutting down starting up too many command-line arguments (first is "%s") track_commit_timestamp setting:       %s
 unrecognized status code unrecognized wal_level wal_level setting:                    %s
 wal_log_hints setting:                %s
 yes Project-Id-Version: pg_controldata (PostgreSQL) 11
Report-Msgid-Bugs-To: pgsql-bugs@lists.postgresql.org
POT-Creation-Date: 2022-09-26 08:20+0000
PO-Revision-Date: 2022-09-26 15:19+0200
Last-Translator: Daniele Varrazzo <daniele.varrazzo@gmail.com>
Language-Team: https://github.com/dvarrazzo/postgresql-it
Language: it
MIME-Version: 1.0
Content-Type: text/plain; charset=UTF-8
Content-Transfer-Encoding: 8bit
Plural-Forms: nplurals=2; plural=n != 1;
X-Poedit-SourceCharset: utf-8
X-Generator: Poedit 3.1.1
 
Se non viene specificata un directory per i dati (DATADIR) verrà usata la
variabile d'ambiente PGDATA.

 
Opzioni:
   %s [OPZIONE] [DATADIR]
   -?, --help             mostra questo aiuto ed esci
   -V, --version          mostra informazioni sulla versione ed esci
  [-D, --pgdata=]DATADIR  directory dei dati
 %s mostra informazioni di controllo su un cluster di database PostgreSQL.

 %s pagina iniziale: <%s>
 interi a 64 bit ??? Posizione della fine del backup:            %X/%X
 Posizione dell'inizio del backup:           %X/%X
 Blocchi per ogni segmento grosse tabelle:   %u
 Byte per segmento WAL:                      %u
 Numero di versione del catalogo:            %u
 Versione somma di controllo dati pagine:    %u
 Dimensione blocco database:                 %u
 Stato del cluster di database:              %s
 Identificatore di sistema del database:           %llu

 Memorizzazione per tipi data/ora:           %s
 Record di fine backup richiesto:            %s
 Falso contatore LSN per rel. non loggate:   %X/%X
 Passaggio di argomenti Float8:              %s
 Ultima posizione del checkpoint:            %X/%X
 NextMultiOffset dell'ultimo checkpoint:     %u
 NextMultiXactId dell'ultimo checkpoint:     %u
 NextOID dell'ultimo checkpoint:             %u
 NextXID dell'ultimo checkpoint:             %u:%u
 PrevTimeLineID dell'ultimo checkpoint:      %u
 File WAL di REDO dell'ultimo checkpoint:    %s
 Locazione di REDO dell'ultimo checkpoint:   %X/%X
 TimeLineId dell'ultimo checkpoint:          %u
 Full_page_writes dell'ultimo checkpoint:    %s
 NewestCommitTsXid dell'ultimo checkpoint:   %u
 OldestActiveXID dell'ultimo checkpoint:     %u
 OldestCommitTsXid dell'ultimo checkpoint:   %u
 DB dell'oldestMulti dell'ultimo checkpoint: %u
 OldestMultiXID dell'ultimo checkpoint:      %u
 DB dell'oldestXID dell'ultimo checkpoint:   %u
 OldestXID dell'ultimo checkpoint:           %u
 Massimo numero di colonne in un indice:     %u
 Massimo allineamento dei dati:              %u
 Lunghezza massima degli identificatori:     %u
 Massima dimensione di un segmento TOAST:    %u
 Timeline posiz. minimum recovery ending:    %u
 Posizione del minimum recovery ending:      %X/%X
 Finto nonce di autenticazione:              %s
 Segnala i bug a <%s>.
 Dimensione di un blocco large-object:       %u
 La dimensione del segmento WAL memorizzata nel file, %d byte, non è una
potenza di 2 tra 1 MB e 1 GB. Il file è corrotto e i risultati
sottostanti non sono affidabili.

 The WAL segment size stored in the file, %d bytes, is not a power of two
between 1 MB and 1 GB.  The file is corrupt and the results below are
untrustworthy.

 Orario ultimo checkpoint:                   %s
 Prova "%s --help" per maggiori informazioni. Utilizzo:
 Dimensione blocco WAL:                      %u
 ATTENZIONE: Il codice di controllo CRC calcolato non combacia con quello
memorizzato nel file. O il file è corrotto o ha un formato diverso da quanto
questo programma si aspetta. I risultati seguenti non sono affidabili.

 ATTENZIONE: dimensione del segmento WAL non valida
 per riferimento per valore ordinamento dei byte non combaciante chiusura del file "%s" fallita: %m fsync del file "%s" fallito: %m apertura del file "%s" in lettura fallita: %m apertura del file "%s" fallita: %m lettura del file "%s" fallita: %m lettura del file "%s" fallita: letti %d di %zu scrittura nel file "%s" fallita: %m in fase di recupero di un archivio in fase di recupero da un crash in produzione impostazione di max_connections:            %d
 impostazione di max_locks_per_xact:         %d
 impostazione di max_prepared_xacts:         %d
 impostazione di max_wal_senders:              %d

 impostazione di max_worker_processes:       %d
 no nessuna directory di dati specificata disattivato attivato ultima modifica a pg_control:               %s
 numero di versione di pg_control:           %u
 possibile mancata corrispondenza dell'ordine dei byte
L'ordine dei byte utilizzato per memorizzare il file pg_control potrebbe non corrispondere a quello
utilizzato da questo programma. In tal caso i risultati seguenti non sarebbero corretti, e
l'installazione di PostgreSQL sarebbe incompatibile con questa directory di dati. spento arresto durante il ripristino arresto in corso avvio in corso troppi argomenti della riga di comando (il primo è "%s") impostazione di track_commit_timestamp:     %s
 codice di stato sconosciuto wal_level sconosciuto impostazione di wal_level:                  %s
 impostazione di wal_log_hints:              %s
 sì 