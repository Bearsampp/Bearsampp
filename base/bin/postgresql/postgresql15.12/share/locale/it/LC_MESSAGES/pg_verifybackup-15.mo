��    e      D  �   l      �     �  8   �  D   �  8   &	  4   _	  >   �	  <   �	  I   
  9   Z
  ?   �
  7   �
       /   ,  /   \  1   �     �  3   �  ,     !   3  $   U  $   z     �  $   �  .   �  &     '   8      `  	   �  $   �     �  %   �  d   �  8   R  3   �  #   �  "   �  #        *  $   H  /   m     �     �  "   �     �       (   4  '   ]  *   �  )   �  !   �     �  #        =     U     o  )   �     �  )   �  &   �  %   $     J  ,   S     �     �     �  4   �  6   �     )     E  $   L     q      �     �     �     �               $     1     C     T     r     �     �  L   �  A   �     -  /   H     x     �     �     �     �     �               7  %   I     o  	   �  ^  �     �  .     H   <  E   �  9   �  D     B   J  R   �  >   �  F     F   f  #   �  .   �  .      6   /     f  7   �  @   �  *   �  1   '  1   Y  &   �  1   �  B   �  5   '  9   ]  .   �  	   �  2   �       ,      �   M  =   �  :     (   Q  5   z  +   �     �  <   �  :   9   4   t      �   *   �   "   �   )   !  1   :!  :   l!  3   �!  1   �!  )   "  "   7"  0   Z"  #   �"  %   �"  !   �"  4   �"  >   ,#  J   k#  0   �#  .   �#     $  8   "$     [$     d$  "   |$  ;   �$  =   �$  .   %     H%  *   W%  "   �%  +   �%  -   �%  (   �%     (&     A&     U&     p&     �&     �&  '   �&     �&     �&     '  ]   '  Q   t'  -   �'  9   �'  $   .(     S(     n(     �(     �(     �(     �(     �(     )  ,   ')  '   T)     |)     T   9   C      b                     +                &   8          F   Q      (   #   _       =   W   A       \   P   H   -      5       %           !   ^   e   E                               J       G   Y   >   `   $   B   '             V   /           [   N                           X   )   K   .   M   S       I          3       ?   2          7   Z   U              "      L   ,   ]       c   0   R   4   :       	   6       1       a                  
   d   @   D       *       O            <          ;    
Report bugs to <%s>.
   -?, --help                  show this help, then exit
   -V, --version               output version information, then exit
   -e, --exit-on-error         exit immediately on error
   -i, --ignore=RELATIVE_PATH  ignore indicated path
   -m, --manifest-path=PATH    use specified path for manifest
   -n, --no-parse-wal          do not try to parse WAL files
   -q, --quiet                 do not print any output, except for errors
   -s, --skip-checksums        skip checksum verification
   -w, --wal-directory=PATH    use specified path for WAL files
 "%s" has size %lld on disk but size %zu in the manifest "%s" is not a file or directory "%s" is present in the manifest but not on disk "%s" is present on disk but not in the manifest "\u" must be followed by four hexadecimal digits. %s home page: <%s>
 %s verifies a backup against the backup manifest.

 Character with value 0x%02x must be escaped. Escape sequence "\%s" is invalid. Expected "," or "]", but found "%s". Expected "," or "}", but found "%s". Expected ":", but found "%s". Expected JSON value, but found "%s". Expected array element or "]", but found "%s". Expected end of input, but found "%s". Expected string or "}", but found "%s". Expected string, but found "%s". Options:
 The input string ended unexpectedly. Token "%s" is invalid. Try "%s --help" for more information. Unicode escape values cannot be used for code point values above 007F when the encoding is not UTF8. Unicode high surrogate must not follow a high surrogate. Unicode low surrogate must follow a high surrogate. Usage:
  %s [OPTION]... BACKUPDIR

 WAL parsing failed for timeline %u \u0000 cannot be converted to text. backup successfully verified
 both path name and encoded path name cannot duplicate null pointer (internal error)
 checksum mismatch for file "%s" checksum without algorithm could not close directory "%s": %m could not close file "%s": %m could not decode file name could not finalize checksum of file "%s" could not finalize checksum of manifest could not initialize checksum of file "%s" could not initialize checksum of manifest could not open directory "%s": %m could not open file "%s": %m could not parse backup manifest: %s could not parse end LSN could not parse start LSN could not read file "%s": %m could not read file "%s": read %d of %lld could not stat file "%s": %m could not stat file or directory "%s": %m could not update checksum of file "%s" could not update checksum of manifest detail:  duplicate path name in backup manifest: "%s" error:  expected at least 2 lines expected version indicator file "%s" has checksum of length %d, but expected %d file "%s" should contain %zu bytes, but read %zu bytes file size is not an integer hint:  invalid checksum for file "%s": "%s" invalid manifest checksum: "%s" last line not newline-terminated manifest checksum mismatch manifest ended unexpectedly manifest has no checksum missing end LSN missing path name missing size missing start LSN missing timeline no backup directory specified out of memory out of memory
 parsing failed program "%s" is needed by %s but was not found in the same directory as "%s" program "%s" was found by "%s" but was not the same version as %s timeline is not an integer too many command-line arguments (first is "%s") unexpected WAL range field unexpected array end unexpected array start unexpected file field unexpected manifest version unexpected object end unexpected object field unexpected object start unexpected scalar unrecognized checksum algorithm: "%s" unrecognized top-level field warning:  Project-Id-Version: pg_verifybackup (PostgreSQL) 15
Report-Msgid-Bugs-To: pgsql-bugs@lists.postgresql.org
POT-Creation-Date: 2022-09-26 08:16+0000
PO-Revision-Date: 2022-10-04 19:41+0200
Last-Translator: 
Language-Team: 
Language: it
MIME-Version: 1.0
Content-Type: text/plain; charset=UTF-8
Content-Transfer-Encoding: 8bit
X-Generator: Poedit 3.1.1
 
Segnala i bug a <%s>.
   -?, --help mostra questo aiuto, quindi esci
   -V, --version restituisce le informazioni sulla versione, quindi esci
   -e, --exit-on-error          esce immediatamente in caso di errore
   -i, --ignore=RELATIVE_PATH ignora il percorso indicato
   -m, --manifest-path=PATH usa il percorso specificato per manifest
   -n, --no-parse-wal           non tenta di analizzare i file WAL
   -q, --quiet                  non stampa alcun output, ad eccezione degli errori
   -s, --skip-checksums         salta la verifica del checksum
   -w, --wal-directory=PATH usa il percorso specificato per i file WAL
 "%s" ha la dimensione %lld sul disco ma la dimensione %zu nel manifest "%s" non è un file o una directory "%s" è presente nel manifest ma non sul disco "%s" è presente sul disco ma non nel manifest "\u" deve essere seguito da quattro cifre esadecimali. Pagina iniziale di %s: <%s>
 %s verifica un backup rispetto al manifest di backup.

 Il carattere con valore 0x%02x deve essere sottoposto ad escape. La sequenza di escape "\%s" non è valida. Era previsto "," oppure "]", trovato "%s" invece. Era previsto "," oppure "}", trovato "%s" invece. Era previsto ":", trovato "%s" invece. Era previsto un valore JSON, trovato "%s" invece. Era previsto un elemento di array oppure "]", trovato "%s" invece. Era prevista la fine dell'input, trovato "%s" invece. Era prevista una stringa oppure "}", trovato "%s" invece. Era prevista una stringa, trovato "%s" invece. Opzioni:
 La stringa di input è terminata inaspettatamente. Il token "%s" non è valido. Prova "%s --help" per maggiori informazioni. I valori di escape Unicode non possono essere utilizzati per i valori del punto di codice superiori a 007F quando la codifica non è UTF8. Il surrogato alto Unicode non deve seguire un surrogato alto. Il surrogato basso Unicode deve seguire un surrogato alto. Utilizzo:
  %s [OPZIONE]... DIR.BACKUP

 Analisi WAL non riuscita per la sequenza temporale %u \u0000 non può essere convertito in testo. backup verificato con successo
 sia il nome del percorso che il nome del percorso codificato impossibile duplicare il puntatore nullo (errore interno)
 mancata corrispondenza del checksum per il file "%s" checksum senza algoritmo impossibile chiudere la directory "%s": %m chiusura del file "%s" fallita: %m impossibile decodificare il nome del file impossibile finalizzare il checksum del file "%s" non è stato possibile finalizzare il checksum di manifest impossibile inizializzare il checksum del file "%s" impossibile inizializzare il checksum di manifest apertura della directory "%s" fallita: %m apertura del file "%s" fallita: %m impossibile analizzare il manifest di backup: %s impossibile analizzare l'LSN finale impossibile analizzare l'LSN di avvio lettura del file "%s" fallita: %m impossibile leggere il file "%s": leggere %d di %lld non è stato possibile ottenere informazioni sul file "%s": %m non è stato possibile ottenere informazioni sul file o directory "%s": %m impossibile aggiornare il checksum del file "%s" impossibile aggiornare il checksum di manifest dettaglio:  nome del percorso duplicato nel manifest di backup: "%s" errore:  previsto almeno 2 righe indicatore della versione prevista il file "%s" ha un checksum di lunghezza %d, ma previsto %d il file "%s" dovrebbe contenere %zu byte, ma leggere %zu byte la dimensione del file non è un numero intero suggerimento:  checksum non valido per il file "%s": "%s" checksum manifest non valido: "%s" ultima riga non terminata da una nuova riga mancata corrispondenza del checksum manifesto manifest è terminato in modo imprevisto manifest non ha checksum LSN finale mancante nome del percorso mancante dimensione mancante LSN iniziale mancante sequenza temporale mancante nessuna directory di backup specificata memoria esaurita memoria esaurita
 analisi non riuscita il programma "%s" è necessario per %s ma non è stato trovato nella stessa directory di "%s" il programma "%s" è stato trovato da "%s" ma non era della stessa versione di %s la sequenza temporale non è un numero intero troppi argomenti della riga di comando (il primo è "%s") campo dell'intervallo WAL imprevisto fine imprevista dell'array avvio imprevisto dell'array campo file imprevisto versione manifest imprevista fine dell'oggetto inaspettato campo oggetto imprevisto inizio dell'oggetto imprevisto scalare inaspettato algoritmo di checksum non riconosciuto: "%s" campo di primo livello non riconosciuto avvertimento:  