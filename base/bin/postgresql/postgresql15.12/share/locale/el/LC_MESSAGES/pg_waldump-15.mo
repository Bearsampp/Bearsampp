��    `        �         (  
   )     4  %   K  3   q  P   �  �   �  P   �	  ?   �	  I   
  =   [
  A   �
  6   �
  �     D   �  �   ?  >   �  �     B   �  C   �  ~   )  D   �     �  9     4   ;  2   p  ;   �  @   �  R         s  :   �  %   �     �  �   �  P   �  Q   �  �   &        -   $  )   R     |     �     �  6   �  !        -     F     c  '   �  *   �  5   �  T   	  I   ^  @   �  =   �  +   '     S  .   \      �     �  |   �     1  ;   8     t     �     �  5   �     �  3     6   I  1   �     �  $   �  '   �  $     *   C      n     �  \   �            ,   ;  6   h  :   �  !   �  F   �  "   C  .   f  #   �  $   �  0   �  $     /   4  6   d  $   �  	   �  �  �     r  C   �  -   �  |   �  �   u  �      �   �   v   d!  �   �!  i   |"  �   �"  X   u#  g  �#  t   6%  B  �%  j   �&    Y'  v   [(  e   �(  �   8)  �   **  #   �*  }    +  u   ~+  j   �+  o   _,  w   �,  �   G-  8   �-  A   .  X   [.     �.  V  �.  �   0  �   �0  �  �1  (   �3  a   �3  [   4  X   h4  M   �4  W   5  s   g5  U   �5  M   16  Q   6  Q   �6  l   #7  d   �7     �7  �   u8  �   :9  }   �9  �   L:  `   �:     .;  j   G;  9   �;     �;  �   �;     �<  �   �<  )   �=  5   �=  *   �=  Q   >  .   a>  c   �>  y   �>  `   n?  B   �?  =   @  F   P@  H   �@  H   �@  B   )A  $   lA  �   �A  4   >B  D   sB  [   �B  p   C  �   �C  8   D  �   DD  =   �D  A   E  =   TE  C   �E  m   �E  :   DF  �   F  m   G  J   vG     �G                T   R   P       U              7   -            
   [          3   K   I      '   "      E           8   C         J   +                       W                          6      >                  $   S   Q           _       	   ;   O   ,   )   /      2   =       @   \   L   .   #          M       G   %   :           A      N          (      !   ?   5      9   Z   X                 `   V   H                    <       ^       B       D   0   4           &   Y   1   ]   *   F    
Options:
 
Report bugs to <%s>.
   %s [OPTION]... [STARTSEG [ENDSEG]]
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
   -p, --path=PATH        directory in which to find log segment files or a
                         directory with a ./pg_wal that contains such files
                         (default: current directory, ./pg_wal, $PGDATA/pg_wal)
   -q, --quiet            do not print any output, except for errors
   -r, --rmgr=RMGR        only show records generated by resource manager RMGR;
                         use --rmgr=list to list valid resource manager names
   -s, --start=RECPTR     start reading at WAL location RECPTR
   -t, --timeline=TLI     timeline from which to read log records
                         (default: 1 or the value used in STARTSEG)
   -w, --fullpage         only show records with a full page write
   -x, --xid=XID          only show records with transaction ID XID
   -z, --stats[=record]   show statistics instead of records
                         (optionally, show per-record statistics)
 %s decodes and displays PostgreSQL write-ahead logs for debugging.

 %s home page: <%s>
 BKPBLOCK_HAS_DATA not set, but data length is %u at %X/%X BKPBLOCK_HAS_DATA set, but no data included at %X/%X BKPBLOCK_SAME_REL set but no previous rel at %X/%X BKPIMAGE_COMPRESSED set, but block image length %u at %X/%X BKPIMAGE_HAS_HOLE not set, but hole offset %u length %u at %X/%X BKPIMAGE_HAS_HOLE set, but hole offset %u length %u block image length %u at %X/%X ENDSEG %s is before STARTSEG %s Expecting "tablespace OID/database OID/relation filenode". Try "%s --help" for more information. Usage:
 WAL file is from different database system: WAL file database system identifier is %llu, pg_control database system identifier is %llu WAL file is from different database system: incorrect XLOG_BLCKSZ in page header WAL file is from different database system: incorrect segment size in page header WAL segment size must be a power of two between 1 MB and 1 GB, but the WAL file "%s" header specifies %d byte WAL segment size must be a power of two between 1 MB and 1 GB, but the WAL file "%s" header specifies %d bytes contrecord is requested by %X/%X could not decompress image at %X/%X, block %d could not find a valid record after %X/%X could not find any WAL file could not find file "%s": %m could not locate WAL file "%s" could not locate backup block with ID %d in WAL record could not open directory "%s": %m could not open file "%s" could not open file "%s": %m could not read file "%s": %m could not read file "%s": read %d of %d could not read from file %s, offset %d: %m could not read from file %s, offset %d: read %d of %d could not restore image at %X/%X compressed with %s not supported by build, block %d could not restore image at %X/%X compressed with unknown method, block %d could not restore image at %X/%X with invalid block %d specified could not restore image at %X/%X with invalid state, block %d custom resource manager "%s" does not exist detail:  end WAL location %X/%X is not inside file "%s" error in WAL record at %X/%X: %s error:  first record is after %X/%X, at %X/%X, skipping over %u byte
 first record is after %X/%X, at %X/%X, skipping over %u bytes
 hint:  incorrect resource manager data checksum in record at %X/%X invalid WAL location: "%s" invalid block number: "%s" invalid block_id %u at %X/%X invalid contrecord length %u (expected %lld) at %X/%X invalid fork name: "%s" invalid info bits %04X in log segment %s, offset %u invalid magic number %04X in log segment %s, offset %u invalid record length at %X/%X: wanted %u, got %u invalid record offset at %X/%X invalid relation specification: "%s" invalid resource manager ID %u at %X/%X invalid timeline specification: "%s" invalid transaction ID specification: "%s" invalid value "%s" for option %s missing contrecord at %X/%X neither BKPIMAGE_HAS_HOLE nor BKPIMAGE_COMPRESSED set, but block image length is %u at %X/%X no arguments specified no start WAL location given option %s requires option %s to be specified out of memory while allocating a WAL reading processor out of memory while trying to decode a record of length %u out-of-order block_id %u at %X/%X out-of-sequence timeline ID %u (after %u) in log segment %s, offset %u record length %u at %X/%X too long record with incorrect prev-link %X/%X at %X/%X record with invalid length at %X/%X resource manager "%s" does not exist start WAL location %X/%X is not inside file "%s" there is no contrecord flag at %X/%X too many command-line arguments (first is "%s") unexpected pageaddr %X/%X in log segment %s, offset %u unrecognized value for option %s: %s warning:  Project-Id-Version: pg_waldump (PostgreSQL) 15
Report-Msgid-Bugs-To: pgsql-bugs@lists.postgresql.org
POT-Creation-Date: 2023-04-16 09:47+0000
PO-Revision-Date: 2023-04-17 11:17+0200
Last-Translator: Georgios Kokolatos <gkokolatos@pm.me>
Language-Team: 
Language: el
MIME-Version: 1.0
Content-Type: text/plain; charset=UTF-8
Content-Transfer-Encoding: 8bit
Plural-Forms: nplurals=2; plural=n != 1;
X-Generator: Poedit 3.2.2
 
Επιλογές:
 
Υποβάλετε αναφορές σφάλματων σε <%s>.
   %s [ΕΠΙΛΟΓΗ]... [STARTSEG [ENDSEG]]
   -?, --help             εμφάνισε αυτό το μήνυμα βοήθειας, στη συνέχεια έξοδος
   -B, --block=N          μαζί με --relation, εμφάνισε μόνο εγγραφές που τροποποιούν το μπλοκ N
   -F, --fork=FORK        εμφάνισε μόνο εγγραφές που τροποποιούν μπλοκ στο fork FORK,
                         έγκυρες ονομασίες είναι main, fsm, vm, init
   -R, --relation=T/D/R   εμφάνισε μόνο εγγραφές που τροποποιούν μπλοκ στη σχέση T/D/R
   -V, --version          εμφάνισε πληροφορίες έκδοσης, στη συνέχεια έξοδος
   -b, --bkp-details      πάραγε λεπτομερείς πληροφορίες σχετικά με τα μπλοκ αντιγράφων ασφαλείας
   -e, --end=RECPTR       σταμάτησε την ανάγνωση στη τοποθεσία WAL RECPTR
   -f, --follow           εξακολούθησε την προσπάθεια μετά την επίτευξη του τέλους του WAL
   -n, --limit=N          αριθμός των εγγραφών για εμφάνιση
   -p, --path=PATH        κατάλογος στον οποίο βρίσκονται αρχεία τμήματος καταγραφής ή
                         ένα κατάλογο με ./pg_wal που περιέχει τέτοια αρχεία
                         (προεπιλογή: τρέχων κατάλογος, ./pg_wal, $PGDATA/pg_wal)
   -q, --quiet            να μην εκτυπωθεί καμία έξοδος, εκτός από σφάλματα
   -r, --rmgr=RMGR        εμφάνισε μόνο εγγραφές που δημιουργούνται από τον διαχειριστή πόρων RMGR·
                         χρησιμοποίησε --rmgr=list για την παράθεση έγκυρων ονομάτων διαχειριστών πόρων
   -s, --start=RECPTR     άρχισε την ανάγνωση WAL από την τοποθεσία RECPTR
   -t, --timeline=TLI     χρονογραμή από την οποία να αναγνωστούν εγγραφές καταγραφής
                         (προεπιλογή: 1 ή η τιμή που χρησιμοποιήθηκε στο STARTSEG)
   -w, --fullpage         εμφάνισε μόνο εγγραφές με εγγραφή πλήρους σελίδας
   -x, --xid=XID          εμφάνισε μόνο εγγραφές με ID συναλλαγής XID
   -z, --stats[=record]   εμφάνισε στατιστικά στοιχεία αντί για εγγραφές
                         (προαιρετικά, εμφάνισε στατιστικά στοιχεία ανά εγγραφή)
 %s αποκωδικοποιεί και εμφανίζει αρχεία καταγραφής εμπρόσθιας-εγγραφής PostgreSQL για αποσφαλμάτωση.

 %s αρχική σελίδα: <%s>
 BKPBLOCK_HAS_DATA δεν έχει οριστεί, αλλά το μήκος των δεδομένων είναι %u σε %X/%X BKPBLOCK_HAS_DATA έχει οριστεί, αλλά δεν περιλαμβάνονται δεδομένα σε %X/%X BKPBLOCK_SAME_REL είναι ορισμένο, αλλά καμία προηγούμενη rel στο %X/%X BKPIMAGE_IS_COMPRESSED έχει οριστεί, αλλά μέγεθος μπλοκ εικόνας %u σε %X/%X BKPIMAGE_HAS_HOLE δεν έχει οριστεί, αλλά οπή με μετατόπιση %u μήκος %u σε %X/%X BKPIMAGE_HAS_HOLE έχει οριστεί, αλλά οπή με μετατόπιση %u μήκος %u μήκος μπλοκ εικόνας %u σε %X/%X ENDSEG %s βρίσκεται πριν από STARTSEG %s Αναμένει "tablespace OID/database OID/relation filenode". Δοκιμάστε «%s --help» για περισσότερες πληροφορίες. Χρήση:
 WAL αρχείο προέρχεται από διαφορετικό σύστημα βάσης δεδομένων: το WAL αναγνωριστικό συστήματος βάσης δεδομένων αρχείων είναι %llu, το pg_control αναγνωριστικό συστήματος βάσης δεδομένων είναι %llu WAL αρχείο προέρχεται από διαφορετικό σύστημα βάσης δεδομένων: εσφαλμένο XLOG_BLCKSZ στην κεφαλίδα σελίδας WAL αρχείο προέρχεται από διαφορετικό σύστημα βάσης δεδομένων: εσφαλμένο μέγεθος τμήματος στην κεφαλίδα σελίδας η τιμή του μεγέθους τμήματος WAL πρέπει να ανήκει σε δύναμη του δύο μεταξύ 1 MB και 1 GB, αλλά η κεφαλίδα «%s» του αρχείου WAL καθορίζει %d byte η τιμή του μεγέθους τμήματος WAL πρέπει να ανήκει σε δύναμη του δύο μεταξύ 1 MB και 1 GB, αλλά η κεφαλίδα «%s» του αρχείου WAL καθορίζει %d bytes contrecord ζητείται από %X/%X δεν ήταν δυνατή η αποσυμπιέση εικόνας στο %X/%X, μπλοκ %d δεν ήταν δυνατή η εύρεση έγκυρης εγγραφής μετά %X/%X δεν ήταν δυνατή η εύρεση οποιουδήποτε αρχείου WAL δεν ήταν δυνατή η εύρεση του αρχείου «%s»: %m δεν ήταν δυνατός ο εντοπισμός του αρχείου WAL «%s» δεν ήταν δυνατή η εύρεση μπλοκ αντιγράφου με ID %d στην εγγραφή WAL δεν ήταν δυνατό το άνοιγμα του καταλόγου «%s»: %m δεν ήταν δυνατό το άνοιγμα του αρχείου «%s» δεν ήταν δυνατό το άνοιγμα του αρχείου «%s»: %m δεν ήταν δυνατή η ανάγνωση του αρχείου «%s»: %m δεν ήταν δυνατή η ανάγνωση του αρχείου «%s»: ανέγνωσε %d από %d δεν ήταν δυνατή η ανάγνωση από αρχείο %s, μετατόπιση %d: %m δεν ήταν δυνατή η ανάγνωση από αρχείο %s, μετατόπιση %d: ανέγνωσε %d από %d δεν ήταν δυνατή η επαναφορά εικόνας σε %X/%X συμπιεσμένη με %s που δεν υποστηρίζεται από την υλοποίηση, μπλοκ %d δεν ήταν δυνατή η επαναφορά εικόνας σε %X/%X συμπιεσμένη με άγνωστη μέθοδο, μπλοκ %d δεν ήταν δυνατή η επαναφορά εικόνας στο %X/%X με ορισμένο άκυρο μπλοκ %d δεν ήταν δυνατή η επαναφορά εικόνας στο %X/%X με άκυρη κατάσταση, μπλοκ %d ο προσαρμοσμένος διαχειριστής πόρων «%s» δεν υπάρχει λεπτομέρεια:  η τελική τοποθεσία WAL %X/%X δεν βρίσκεται μέσα στο αρχείο «%s» σφάλμα στην εγγραφή WAL στο %X/%X: %s σφάλμα:  πρώτη εγγραφή βρίσκεται μετά από %X/%X, σε %X/%X, παρακάμπτοντας %u byte
 πρώτη εγγραφή βρίσκεται μετά από %X/%X, σε %X/%X, παρακάμπτοντας %u bytes
 υπόδειξη:  εσφαλμένο άθροισμα ελέγχου δεδομένων διαχειριστή πόρων σε εγγραφή στο %X/%X άκυρη τοποθεσία WAL: «%s» μη έγκυρος αριθμός μπλοκ: «%s» μη έγκυρο block_id %u στο %X/%X μη έγκυρο μήκος contrecord %u (αναμένεται %lld) σε %X/%X μη έγκυρη ονομασία fork «%s» μη έγκυρα info bits %04X στο τμήμα καταγραφής %s, μετατόπιση %u μη έγκυρος μαγικός αριθμός %04X στο τμήμα καταγραφής %s, μετατόπιση %u μη έγκυρο μήκος εγγραφής σε %X/%X: χρειαζόταν %u, έλαβε %u μη έγκυρη μετατόπιση εγγραφών σε %X/%X μη έγκυρη προδιαγραφή σχέσης: «%s» μη έγκυρο ID %u διαχειριστή πόρων στο %X/%X άκυρη προδιαγραφή χρονοδιαγραμμής: «%s» μη έγκυρη προδιαγραφή ID συναλλαγής: «%s» μη έγκυρη τιμή  «%s» για την επιλογή %s λείπει contrecord στο %X/%X ούτε BKPIMAGE_HAS_HOLE ούτε BKPIMAGE_IS_COMPRESSED είναι ορισμένα, αλλά το μήκος της εικόνας μπλοκ είναι %u στο %X/%X δεν καθορίστηκαν παράμετροι δεν δόθηκε καμία τοποθεσία έναρξης WAL η επιλογή %s απαιτεί να έχει καθοριστεί η επιλογή %s η μνήμη δεν επαρκεί για την εκχώρηση επεξεργαστή ανάγνωσης WAL έλλειψη μνήμης κατά την προσπάθεια αποκωδικοποίησης εγγραφής με μήκος %u εκτός ακολουθίας block_id %u στο %X/%X εκτός ακολουθίας ID χρονογραμμής %u (μετά %u) στο τμήμα καταγραφής %s, μετατόπιση %u μήκος εγγραφής %u σε %X/%X πολύ μακρύ εγγραφή με εσφαλμένο prev-link %X/%X σε %X/%X εγγραφή με μη έγκυρο μήκος στο %X/%X ο διαχειριστής πόρων «%s» δεν υπάρχει τοποθεσία εκκίνησης WAL %X/%X δεν βρίσκεται μέσα στο αρχείο «%s» δεν υπάρχει σημαία contrecord στο %X/%X πάρα πολλές παράμετροι εισόδου από την γραμμή εντολών (η πρώτη είναι η «%s») μη αναμενόμενο pageaddr %X/%X στο τμήμα καταγραφής %s, μετατόπιση %u μη αναγνωρίσιμη τιμή για την επιλογή %s: %s προειδοποίηση:  