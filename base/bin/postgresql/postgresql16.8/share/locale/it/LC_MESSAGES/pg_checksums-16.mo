��    <      �  S   �      (  X   )  
   �     �  5   �  P   �  5   0  A   f  :   �  2   �  1     G   H  3   �  *   �     �  T        a     u     �     �     �     �     �     	     .	     I	     `	     v	  j   �	  %   �	     
  a   %
     �
     �
  ;   �
       !        >  (   [  3   �     �  )   �  5   �  .   5  -   d  )   �  "   �     �     �     �  +   �      #     D  2   `  !   �  )   �     �  /   �     &  	   <  [  F  j   �  
          7   5  a   m  M   �  F     G   d  :   �  7   �  S     7   s  .   �     �  \   �     U  .   r     �     �     �  "   �  "        0     P     l     �     �  �   �  ,   B  
   o  k   z      �  !     >   )     h  )   �  "   �  /   �  @     >   D  1   �  D   �  6   �  3   1  2   e  )   �     �     �     �  0   �  '     %   ?  ?   e  (   �  7   �  )     9   0  #   j     �     &          %   ,                   *   :            )   7                            8           0   .   1      5                                       3      /          <   #      ;      4         "      '          $   2       	   !          6   9       -          
   +                     (        
If no data directory (DATADIR) is specified, the environment variable PGDATA
is used.

 
Options:
   %s [OPTION]... [DATADIR]
   -?, --help               show this help, then exit
   -N, --no-sync            do not wait for changes to be written safely to disk
   -P, --progress           show progress information
   -V, --version            output version information, then exit
   -c, --check              check data checksums (default)
   -d, --disable            disable data checksums
   -e, --enable             enable data checksums
   -f, --filenode=FILENODE  check only relation with specified filenode
   -v, --verbose            output verbose messages
  [-D, --pgdata=]DATADIR    data directory
 %lld/%lld MB (%d%%) computed %s enables, disables, or verifies data checksums in a PostgreSQL database cluster.

 %s home page: <%s>
 %s must be in range %d..%d Bad checksums:  %lld
 Blocks scanned:  %lld
 Blocks written: %lld
 Checksum operation completed
 Checksums disabled in cluster
 Checksums enabled in cluster
 Data checksum version: %u
 Files scanned:   %lld
 Files written:  %lld
 Report bugs to <%s>.
 The database cluster was initialized with block size %u, but pg_checksums was compiled with block size %u. Try "%s --help" for more information. Usage:
 checksum verification failed in file "%s", block %u: calculated checksum %X but block contains %X checksums enabled in file "%s" checksums verified in file "%s" cluster is not compatible with this version of pg_checksums cluster must be shut down could not open directory "%s": %m could not open file "%s": %m could not read block %u in file "%s": %m could not read block %u in file "%s": read %d of %d could not stat file "%s": %m could not write block %u in file "%s": %m could not write block %u in file "%s": wrote %d of %d data checksums are already disabled in cluster data checksums are already enabled in cluster data checksums are not enabled in cluster database cluster is not compatible detail:  error:  hint:  invalid segment number %d in file name "%s" invalid value "%s" for option %s no data directory specified option -f/--filenode can only be used with --check pg_control CRC value is incorrect seek failed for block %u in file "%s": %m syncing data directory too many command-line arguments (first is "%s") updating control file warning:  Project-Id-Version: pg_checksums (PostgreSQL) 15
Report-Msgid-Bugs-To: pgsql-bugs@lists.postgresql.org
POT-Creation-Date: 2022-09-26 08:21+0000
PO-Revision-Date: 2023-09-05 08:10+0200
Last-Translator: 
Language-Team: 
Language: it
MIME-Version: 1.0
Content-Type: text/plain; charset=UTF-8
Content-Transfer-Encoding: 8bit
X-Generator: Poedit 3.1.1
 
Se non viene specificata un directory per i dati (DATADIR) verrà usata la
variabile d'ambiente PGDATA.

 
Opzioni:
   %s [OPZIONE]... [DATADIR]
   -?, --help               mostra questo aiuto ed esci
   -N, --no-sync            non attende che le modifiche vengano scritte in modo sicuro sul disco
   -P, --progress           mostra le informazioni sullo stato di avanzamento
   -V, --version            mostra informazioni sulla versione ed esci
   -c, --check              controlla i checksum dei dati (predefinito)
   -d, --disable            disabilita i checksum dei dati
   -e, --enable             abilita i checksum dei dati
   -f, --filenode=FILENODE  controlla solo la relazione con il filenode specificato
   -v, --verbose            genera messaggi dettagliati
  [-D, --pgdata=]DATADIR    directory dei dati
 %lld/%lld MB (%d%%) calcolati %s abilita, disabilita o verifica i checksum dei dati in un cluster di database PostgreSQL.
 Pagina iniziale di %s: <%s>
 %s deve essere compreso nell'intervallo %d..%d Checksum errati: %lld
 Blocchi scansionati: %lld
 Blocchi scritti: %lld
 Operazione di checksum completata
 Checksum disabilitati nel cluster
 Checksum abilitati nel cluster
 Versione checksum dati: %u
 File scansionati: %lld
 File scritti: %lld
 Segnala i bug a <%s>.
 Il cluster di database è stato inizializzato con la dimensione del blocco %u, ma pg_checksums è stato compilato con la dimensione del blocco %u. Prova "%s --help" per maggiori informazioni. Utilizzo:
 verifica del checksum non riuscita nel file "%s", blocco %u: checksum calcolato %X ma il blocco contiene %X checksum abilitati nel file "%s" checksum verificati nel file "%s" cluster non è compatibile con questa versione di pg_checksums il cluster deve essere spento apertura della directory "%s" fallita: %m apertura del file "%s" fallita: %m lettura del blocco %u nel file "%s" fallita: %m impossibile leggere il blocco %u nel file "%s": leggere %d di %d non è stato possibile ottenere informazioni sul file "%s": %m scrittura del blocco %u nel file "%s" fallita: %m impossibile scrivere il blocco %u nel file "%s": ha scritto %d di %d i checksum dei dati sono già disabilitati nel cluster i checksum dei dati sono già abilitati nel cluster i checksum dei dati non sono abilitati nel cluster il cluster di database non è compatibile dettaglio:  errore:  suggerimento:  numero segmento non valido %d nel nome file "%s" valore "%s" non valido per l'opzione %s nessuna directory di dati specificata l'opzione -f/--filenode può essere utilizzata solo con --check pg_control Il valore CRC non è corretto ricerca non riuscita per il blocco %u nel file "%s": %m sincronizzazione della directory dei dati troppi argomenti della riga di comando (il primo è "%s") aggiornamento del file di controllo avvertimento:  