��    ]           �      �  X   �  
   B     M  3   f  ?   �  (   �  C   	     G	     [	     k	  ,   o	  ,   �	  )   �	  )   �	  )   
  )   G
  )   q
  )   �
  +   �
  )   �
  )     ,   E  )   r  ,   �  )   �  )   �  )     ,   G  )   t  )   �  ,   �  )   �  )     )   I  )   s  )   �  )   �  )   �  )     )   E  )   o  )   �  )   �  )   �  )     ,   A  )   n     �  )   �  >  �  )     &   A     h  )   p  �   �  "   a     �     �     �     �     �  (   �          3  (   P     y     �     �     �  )   �  )   �  )     )   I  )   s     �     �     �     �  )   �  )   �      	        '     =     K  /   W  )   �     �     �  )   �  )        5  �  9  �   �     �      �  m   �  v   @  ?   �  �   �  #   {     �     �  M   �  K   	  P   U  >   �  M   �  ^   3  P   �  U   �  ]   9  U   �  G   �  S   5  E   �  [   �  R   +   R   ~   R   �   U   $!  R   z!  X   �!  ^   &"  R   �"  R   �"  R   +#  R   ~#  R   �#  T   $$  R   y$  T   �$  R   !%  R   t%  N   �%  P   &  P   g&  c   �&  ^   '  H   {'  B   �'  X   (  �  `(  V   �*  Y   P+     �+  B   �+  �  �+  R   �-     ..     B.  0   P.  S   �.  k   �.  b   A/  Q   �/  Q   �/  m   H0  H   �0  4   �0  9   41     n1  =   �1  =   �1  =    2  =   >2  =   |2     �2  =   �2     �2     3  J   3  D   h3  N  �3     �5  4   6     H6     ]6  �   n6  =   �6  C   57  '   y7  =   �7  =   �7     8                  -   :               G   [   4                     1           $   J       ]   @                         !   2                  =       '   
       C         E   \   >   ;       "   &          D   Q          U   #   Y   <   L       3   7      /       ,   	   %   8          (   N   I            5   H           V   .   0   9                  X   A              K   F   B      +   S   P      R   O   T   *      6      W   ?                      Z           )   M    
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
 Try "%s --help" for more information.
 Usage:
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
 yes Project-Id-Version: pg_controldata (PostgreSQL) 14
Report-Msgid-Bugs-To: pgsql-bugs@lists.postgresql.org
POT-Creation-Date: 2021-07-14 09:48+0000
PO-Revision-Date: 2021-05-31 11:02+0200
Last-Translator: Georgios Kokolatos <gkokolatos@pm.me>
Language-Team: 
Language: el
MIME-Version: 1.0
Content-Type: text/plain; charset=UTF-8
Content-Transfer-Encoding: 8bit
Plural-Forms: nplurals=2; plural=n != 1;
X-Generator: Poedit 2.4.3
 
Εάν δεν έχει καθοριστεί κατάλογος δεδομένων (DATADIR), χρησιμοποιείται η
μεταβλητή περιβάλλοντος PGDATA.

 
Επιλογές:
   %s [ΕΠΙΛΟΓΗ] [DATADIR]
   -?, --help             εμφάνισε αυτό το μήνυμα βοήθειας, μετά έξοδος
   -V, --version          εμφάνισε πληροφορίες έκδοσης, στη συνέχεια έξοδος
  [-D, --pgdata=]DATADIR  κατάλογος δεδομένων
 %s εμφανίζει πληροφορίες ελέγχου μίας συστάδας βάσεων δεδομένων PostgreSQL.

 %s αρχική σελίδα: <%s>
 Ακέραιοι 64-bit ??? Τοποθεσία τερματισμου Backup:                      %X/%X
 Τοποθεσία εκκίνησης Backup:                        %X/%X
 Μπλοκ ανά τμήμα μεγάλης σχέσης:                    %u
 Bytes ανά τμήμα WAL:                               %u
 Αριθμός έκδοσης καταλόγου:                         %u
 Έκδοση αθροίσματος ελέγχου σελίδας δεδομένων:      %u
 Μέγεθος μπλοκ βάσης δεδομένων:                     %u
 Κατάσταση συστάδας βάσης δεδομένων:                %s
 Αναγνωριστικό συστήματος βάσης δεδομένων:          %llu
 Τύπος αποθήκευσης ημερομηνίας/ώρας:                %s
 Απαιτείται εγγραφή end-of-backup:                  %s
 Ψεύτικος μετρητής LSN για μη κενές rels:           %X/%X
 Μεταβλητή Float8 τέθηκε:                           %s
 Πιο πρόσφατη τοποθεσία σημείου ελέγχου:            %X/%X
 Πιο πρόσφατο NextMultiOffset του σημείου ελέγχου:  %u
 Πιο πρόσφατο NextMultiXactId του σημείου ελέγχου:  %u
 Πιο πρόσφατο NextOID του σημείου ελέγχου:          %u
 Πιο πρόσφατο NextXID του σημείου ελέγχου:          %u:%u
 Πιο πρόσφατο PrevTimeLineID του σημείου ελέγχου:   %u
 Πιο πρόσφατο αρχείο REDO WAL του σημείου ελέγχου:  %s
 Πιο πρόσφατη τοποθεσία REDO του σημείου ελέγχου:   %X/%X
 Πιο πρόσφατο TimeLineID του σημείου ελέγχου:       %u
 Πιο πρόσφατο full_page_writes του σημείου ελέγχου: %s
 Πιο πρόσφατο newestCommitTsXid του σημείου ελέγχου:%u
 Πιο πρόσφατο oldestActiveXID του σημείου ελέγχου:  %u
 Πιο πρόσφατο oldestCommitTsXid του σημείου ελέγχου:%u
 Πιο πρόσφατο oldestMulti’s DB του σημείου ελέγχου: %u
 Πιο πρόσφατο oldestMultiXid του σημείου ελέγχου:   %u
 Πιο πρόσφατο oldestXID’s DB του σημείου ελέγχου:   %u
 Πιο πρόσφατο oldestXID του σημείου ελέγχου:        %u
 Μέγιστες στήλες σε ένα ευρετήριο:                  %u
 Μέγιστη στοίχιση δεδομένων:                        %u
 Μέγιστο μήκος αναγνωριστικών:                      %u
 Μέγιστο μέγεθος ενός τμήματος TOAST:               %u
 Χρονογραμμή ελάχιστης θέσης τερματισμού ανάκαμψης: %u
 Ελάχιστη τοποθεσία τερματισμού ανάκαμψης:          %X/%X
 Μακέτα (mock) nonce ταυτοποίησης:                  %s
 Υποβάλετε αναφορές σφάλματων σε <%s>.
 Μέγεθος τμήματος μεγάλου αντικειμένου:             %u
 Το μέγεθος τμήματος WAL που είναι αποθηκευμένο στο αρχείο, %d byte, δεν είναι δύναμη
του δύο μεταξύ 1 MB και 1 GB.  Το αρχείο είναι αλλοιωμένο και τα παρακάτω αποτελέσματα
είναι αναξιόπιστα.

 Το μέγεθος τμήματος WAL που είναι αποθηκευμένο στο αρχείο, %d bytes, δεν είναι δύναμη
του δύο μεταξύ 1 MB και 1 GB.  Το αρχείο είναι αλλοιωμένο και τα παρακάτω αποτελέσματα
είναι αναξιόπιστα.

 Ώρα του πιο πρόσφατου σημείου ελέγχου:             %s
 Δοκιμάστε «%s --help» για περισσότερες πληροφορίες.
 Χρήση:
 Μέγεθος μπλοκ WAL:                                 %u
 ΠΡΟΕΙΔΟΠΟΙΗΣΗ: Το υπολογιζόμενο άθροισμα ελέγχου CRC δεν συμφωνεί με την τιμή που είναι αποθηκευμένη στο αρχείο.
Είτε το αρχείο είναι αλλοιωμένο είτε έχει διαφορετική διάταξη από αυτή που περιμένει
αυτό το πρόγραμμα.  Τα παρακάτω αποτελέσματα είναι αναξιόπιστα.

 ΠΡΟΕΙΔΟΠΟΙΗΣΗ: μη έγκυρο μέγεθος τμήματος WAL
 με αναφορά με τιμή αναντιστοιχία διάταξης byte δεν ήταν δυνατό το κλείσιμο του αρχείου «%s»: %m δεν ήταν δυνατή η εκτέλεση της εντολής fsync στο αρχείο «%s»: %m δεν ήταν δυνατό το άνοιγμα αρχείου «%s» για ανάγνωση: %m δεν ήταν δυνατό το άνοιγμα του αρχείου «%s»: %m δεν ήταν δυνατή η ανάγνωση του αρχείου «%s»: %m δεν ήταν δυνατή η ανάγνωση του αρχείου «%s»: ανέγνωσε %d από %zu δεν ήταν δυνατή η εγγραφή αρχείου «%s»: %m σε αποκατάσταση αρχειοθήκης σε αποκατάσταση από κρασάρισμα σε παραγωγή ρύθμιση max_connections:                           %d
 ρύθμιση max_locks_per_xact:                        %d
 ρύθμιση max_prepared_xacts:                        %d
 ρύθμιση max_wal_senders:                           %d
 ρύθμιση max_worker_processes:                      %d
 όχι δεν ορίστηκε κατάλογος δεδομένων κλειστό ανοικτό πιο πρόσφατη μετατροπή pg_control:                 %s
 pg_control αριθμός έκδοσης:                        %u
 πιθανή αναντιστοιχία διάταξης byte
Η διάταξη byte που χρησιμοποιείται για την αποθήκευση του αρχείου pg_control ενδέχεται να μην ταιριάζει με αυτήν
που χρησιμοποιείται από αυτό το πρόγραμμα.  Στην περίπτωση αυτή, τα παρακάτω αποτελέσματα θα ήταν εσφαλμένα, και
η εγκατάσταση PostgreSQL θα ήταν ασύμβατη με αυτόν τον κατάλογο δεδομένων. τερματισμός τερματισμός σε αποκατάσταση τερματίζει εκκίνηση πάρα πολλές παράμετροι εισόδου από την γραμμή εντολών (η πρώτη είναι η «%s») ρύθμιση track_commit_timestamp:                    %s
 μη αναγνωρίσιμος κωδικός κατάστασης μη αναγνωρίσιμο wal_level ρύθμιση wal_level:                                 %s
 ρύθμιση wal_log_hints:                             %s
 ναι 