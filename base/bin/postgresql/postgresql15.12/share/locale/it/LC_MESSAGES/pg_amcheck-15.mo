��    b      ,  �   <      H      I     j     �     �     �     �  S   �  H   (	  V   q	  =   �	  A   
  U   H
  Z   �
  K   �
  M   E  I   �  I   �  T   '  T   |     �  <   �  D   )  B   n  <   �  D   �  B   3  A   v  :   �  H   �  8   <  6   u  =   �  M   �  K   8  ;   �  U   �  7     =   N  ;   �  :   �  8     <   <  ,   y  0   �  7   �       <        O     c  +   ~     �     �     �     �  %   �     #     +  V   D  )   �  9   �     �       /   >     n     �     �     �  *   �     �  :   �  ,   .  !   [     }     �  3   �  2   �  ;        ?  :   W  :   �     �     �     �        '   3  /   [     �  %   �     �  .   �  #        0     A  0   P     �  /   �  	   �  �  �  *   ^     �     �     �  %   �     �  `     O   n  o   �  B   .  G   q  ]   �  f     P   ~  b   �  V   2  U   �  _   �  _   ?      �   >   �   K   �   K   F!  B   �!  L   �!  K   ""  L   n"  C   �"  M   �"  E   M#  =   �#  E   �#  E   $  G   ]$  E   �$  F   �$  @   2%  K   s%  E   �%  F   &  8   L&  ?   �&  -   �&  1   �&  8   %'     ^'  D   a'     �'  .   �'  -   �'  "    (     C(  2   V(     �(  ,   �(  
   �(     �(  f   �(  :   V)  P   �)  &   �)  '   	*  =   1*     o*     �*     �*  +   �*  7   �*     +  =   +  /   S+  $   �+     �+     �+  4   �+  5   ,  I   <,     �,  :   �,  ;   �,  %   -     <-     U-  '   p-  9   �-  A   �-     .  :   3.      n.  B   �.  5   �.     /     /  4   ,/     a/  9   �/     �/                           8   ^            $   @   1   b      Y       3           W           )          C      [   R               !   X   O   Q              0   D      "   7   .   ;   =   A      /   
   ?   P                 6   N          &   	       2   H             #      -       %   >          '      J       M   ]          T   +   (       G      S   9       `       B           4       U       ,       V   *      :   F   5   I         L      \   _      <          a      K   Z       E    
B-tree index checking options:
 
Connection options:
 
Other options:
 
Report bugs to <%s>.
 
Table checking options:
 
Target options:
       --endblock=BLOCK            check table(s) only up to the given block number
       --exclude-toast-pointers    do NOT follow relation TOAST pointers
       --heapallindexed            check that all heap tuples are found within indexes
       --install-missing           install missing extensions
       --maintenance-db=DBNAME     alternate maintenance database
       --no-dependent-indexes      do NOT expand list of relations to include indexes
       --no-dependent-toast        do NOT expand list of relations to include TOAST tables
       --no-strict-names           do NOT require patterns to match objects
       --on-error-stop             stop checking at end of first corrupt page
       --parent-check              check index parent/child relationships
       --rootdescend               search from root page to refind tuples
       --skip=OPTION               do NOT check "all-frozen" or "all-visible" blocks
       --startblock=BLOCK          begin checking table(s) at the given block number
   %s [OPTION]... [DBNAME]
   -?, --help                      show this help, then exit
   -D, --exclude-database=PATTERN  do NOT check matching database(s)
   -I, --exclude-index=PATTERN     do NOT check matching index(es)
   -P, --progress                  show progress information
   -R, --exclude-relation=PATTERN  do NOT check matching relation(s)
   -S, --exclude-schema=PATTERN    do NOT check matching schema(s)
   -T, --exclude-table=PATTERN     do NOT check matching table(s)
   -U, --username=USERNAME         user name to connect as
   -V, --version                   output version information, then exit
   -W, --password                  force password prompt
   -a, --all                       check all databases
   -d, --database=PATTERN          check matching database(s)
   -e, --echo                      show the commands being sent to the server
   -h, --host=HOSTNAME             database server host or socket directory
   -i, --index=PATTERN             check matching index(es)
   -j, --jobs=NUM                  use this many concurrent connections to the server
   -p, --port=PORT                 database server port
   -r, --relation=PATTERN          check matching relation(s)
   -s, --schema=PATTERN            check matching schema(s)
   -t, --table=PATTERN             check matching table(s)
   -v, --verbose                   write a lot of output
   -w, --no-password               never prompt for password
 %*s/%s relations (%d%%), %*s/%s pages (%d%%) %*s/%s relations (%d%%), %*s/%s pages (%d%%) %*s %*s/%s relations (%d%%), %*s/%s pages (%d%%) (%s%-*.*s) %s %s checks objects in a PostgreSQL database for corruption.

 %s home page: <%s>
 %s must be in range %d..%d Are %s's and amcheck's versions compatible? Cancel request sent
 Command was: %s Could not send cancel request:  Query was: %s Try "%s --help" for more information. Usage:
 btree index "%s.%s.%s":
 btree index "%s.%s.%s": btree checking function returned unexpected number of rows: %d cannot specify a database name with --all cannot specify both a database name and database patterns checking btree index "%s.%s.%s" checking heap table "%s.%s.%s" could not connect to database %s: out of memory database "%s": %s detail:  end block out of bounds end block precedes start block error sending command to database "%s": %s error:  heap table "%s.%s.%s", block %s, offset %s, attribute %s:
 heap table "%s.%s.%s", block %s, offset %s:
 heap table "%s.%s.%s", block %s:
 heap table "%s.%s.%s":
 hint:  improper qualified name (too many dotted names): %s improper relation name (too many dotted names): %s in database "%s": using amcheck version "%s" in schema "%s" including database "%s" internal error: received unexpected database pattern_id %d internal error: received unexpected relation pattern_id %d invalid argument for option %s invalid end block invalid start block invalid value "%s" for option %s no btree indexes to check matching "%s" no connectable databases to check matching "%s" no databases to check no heap tables to check matching "%s" no relations to check no relations to check in schemas matching "%s" no relations to check matching "%s" query failed: %s query was: %s
 skipping database "%s": amcheck is not installed start block out of bounds too many command-line arguments (first is "%s") warning:  Project-Id-Version: pg_amcheck (PostgreSQL) 15
Report-Msgid-Bugs-To: pgsql-bugs@lists.postgresql.org
POT-Creation-Date: 2022-09-26 08:20+0000
PO-Revision-Date: 2022-09-30 14:42+0200
Language: it
MIME-Version: 1.0
Content-Type: text/plain; charset=UTF-8
Content-Transfer-Encoding: 8bit
Last-Translator: Domenico Sgarbossa <sgarbossa.domenico@gmail.com>
Language-Team: 
X-Generator: Poedit 2.3
 
Opzioni di controllo dell'indice B-tree:
 
Opzioni di connessione:
 
Altre opzioni:
 
Segnala i bug a <%s>.
 
Opzioni di controllo della tabella:
 
Opzioni di destinazione:
       --endblock=BLOCCO          controlla le tabelle solo fino al numero di blocco specificato
       --exclude-toast-pointers      NON seguono i puntatori TOAST di relazione
       --heapallindexed              controlla che tutte le tuple dell'heap si trovino all'interno degli indici
      --install-missing            installa le estensioni mancanti
      --maintenance-db=DBNAME      database di manutenzione alternativo
       --no-dependent-indexes      Non espande l'elenco di relazioni per includere gli indici
      --no-dependent-toast         Non espande l'elenco delle relazioni per includere le tabelle TOAST
       --no-strict-names           Non richiede modelli per abbinare gli oggetti
      --on-error-stop             interrompe il controllo alla fine della prima pagina danneggiata
       --parent-check               controlla le relazioni genitore/figlio dell'indice
       --rootdescend               cerca dalla pagina principale per trovare le tuple
     --skip=OPZIONE               Non controlla i blocchi "tutto congelato" o "tutto visibile".
        --startblock=BLOCCO            inizia a controllare le tabelle al numero di blocco dato
   %s [OPZIONE]... [NOMEDB]
   -?, --help                      mostra questo aiuto ed esci
   -D, --exclude-database=PATTERN   Non controlla i database corrispondenti
   -I, --exclude-index=PATTERN      Non controlla gli indici corrispondenti
  -P, --progress mostra le informazioni sullo stato di avanzamento
   -R, --exclude-relation=PATTERN  Non controlla le relazioni corrispondenti
   -S, --exclude-schema=PATTERN     Non controlla gli schemi corrispondenti
    -T, --exclude-table=PATTERN      Non controlla le tabelle corrispondenti
   -U, --username=USERNAME          nome utente con cui connettersi
   -V, --version                   mostra informazioni sulla versione ed esci
   -W, --password                   forza la richiesta della password
    -a, --all                      controlla tutti i database
    -d, --database=PATTERN        controlla i database corrispondenti
   -e, --echo                      mostra i comandi inviati al server
   -h, --host=HOSTNAME            host del database  o directory socket
    -i, --index=PATTERN           controlla gli indici corrispondenti
  -j, --jobs=NUM usa questo numero di connessioni simultanee al server
   -p, --port=PORT                 porta del server del database
   -r, --relation=PATTERN          controlla le relazioni di corrispondenza
    -s, --schema=PATTERN          controlla gli schemi corrispondenti
    -t, --table=PATTERN            controlla le tabelle corrispondenti
   -v, --verbose                   mostra molti messaggi
   -w, --no-password               non richiede mai la password
 %*s/%s relazioni (%d%%), %*s/%s pagine (%d%%) %*s/%s relazioni (%d%%), %*s/%s pagine (%d%%) %*s %*s/%s relazioni (%d%%), %*s/%s pagine (%d%%) (%s%-*.*s) %s %s verifica la corruzione degli oggetti in un database PostgreSQL.

 Pagina iniziale di %s: <%s>
 %s deve essere compreso nell'intervallo %d..%d Le versioni di %s e amcheck sono compatibili? Richiesta di annullamento inviata
 Il comando era: %s Impossibile inviare la richiesta di annullamento:  La richiesta era: %s Prova "%s --help" per maggiori informazioni. Utilizzo:
 btree index "%s.%s.%s":
 btree index "%s.%s.%s": la funzione di controllo btree ha restituito un numero imprevisto di righe: %d non è possibile specificare un nome di database con --all non è possibile specificare sia il nome del database che i modelli del database controllo dell'indice btree "%s.%s.%s" controllo della tabella heap "%s.%s.%s" impossibile connettersi al database %s: memoria insufficiente database "%s": %s dettaglio:  blocco finale fuori limite il blocco finale precede il blocco iniziale errore durante l'invio del comando al database "%s": %s errore:  tabella heap "%s.%s.%s", blocco %s, offset %s, attributo %s:
 tabella heap "%s.%s.%s", blocco %s, offset %s:
 tabella heap "%s.%s.%s", blocco %s:
 tabella heap "%s.%s.%s":
 suggerimento:  nome qualificato improprio (troppi nomi puntati): %s nome di relazione improprio (troppi nomi puntati): %s nel database "%s": utilizzando la versione amcheck "%s" nello schema "%s" incluso il database "%s" errore interno: ricevuto database imprevisto pattern_id %d errore interno: ricevuta relazione imprevista pattern_id %d argomento non valido per l'opzione %s blocco finale non valido blocco di avvio non valido valore "%s" non valido per l'opzione %s nessun indice btree per verificare la corrispondenza "%s" nessun database collegabile per verificare la corrispondenza "%s" nessun database da controllare nessuna tabella heap per verificare la corrispondenza "%s" nessuna relazione da controllare nessuna relazione da archiviare negli schemi corrispondenti a "%s" nessuna relazione da verificare corrispondente a "%s" query fallita: %s la query era: %s
 saltando il database "%s": amcheck non è installato blocco di partenza fuori limite troppi argomenti della riga di comando (il primo è "%s") avvertimento:  