��          �   %   �      `  �   a  
   ;  �   F     �  3   �  +     7   E  6   }  L   �  <        >  6   R  &   �     �  $   �  )   �  (     (   0     Y     x     �     �     �  !   �     �  	     �      �     �	  �   �	  C   x
  ;   �
  t   �
  n   m  �   �  �   ]  r   �  #   a  c   �  Y   �     C  P   P  p   �  n     n   �  Q   �     B     Q  F   b  U   �  d   �  c   d     �                                                                                
                   	                         
For use as archive_cleanup_command in postgresql.conf:
  archive_cleanup_command = 'pg_archivecleanup [OPTION]... ARCHIVELOCATION %%r'
e.g.
  archive_cleanup_command = 'pg_archivecleanup /mnt/server/archiverdir %%r'
 
Options:
 
Or for use as a standalone archive cleaner:
e.g.
  pg_archivecleanup /mnt/server/archiverdir 000000010000000000000010.00000020.backup
 
Report bugs to <%s>.
   %s [OPTION]... ARCHIVELOCATION OLDESTKEPTWALFILE
   -?, --help     show this help, then exit
   -V, --version  output version information, then exit
   -d             generate debug output (verbose mode)
   -n             dry run, show the names of the files that would be removed
   -x EXT         clean up files if they have this extension
 %s home page: <%s>
 %s removes older WAL files from PostgreSQL archives.

 Try "%s --help" for more information.
 Usage:
 archive location "%s" does not exist could not close archive location "%s": %m could not open archive location "%s": %m could not read archive location "%s": %m could not remove file "%s": %m error:  fatal:  invalid file name argument must specify archive location must specify oldest kept WAL file too many command-line arguments warning:  Project-Id-Version: pg_archivecleanup (PostgreSQL) 14
Report-Msgid-Bugs-To: pgsql-bugs@lists.postgresql.org
POT-Creation-Date: 2021-07-21 05:18+0000
PO-Revision-Date: 2021-07-21 10:02+0200
Last-Translator: Georgios Kokolatos <gkokolatos@pm.me>
Language-Team: 
Language: el
MIME-Version: 1.0
Content-Type: text/plain; charset=UTF-8
Content-Transfer-Encoding: 8bit
X-Generator: Poedit 3.0
 
Για χρήση ως archive_cleanup_command στο postgresql.conf:
  archive_cleanup_command = 'pg_archivecleanup [ΕΠΙΛΟΓΗ]... ARCHIVELOCATION %%r’
π.χ.
  archive_cleanup_command = ‘pg_archivecleanup /mnt/διακομιστής/αρχειοθήκη %%r’
 
Επιλογές:
 
Ή για χρήση ως αυτόνομο εκκαθαριστικό αρχειοθήκης:
π.χ.
  pg_archivecleanup /mnt/server/archiverdir 0000000100000000000000000010.00000020.backup
 
Υποβάλετε αναφορές σφάλματων σε <%s>.
   %s [ΕΠΙΛΟΓΗ]... ARCHIVELOCATION OLDESTKEPTWALFILE
   -?, --help     εμφάνισε αυτό το μήνυμα βοήθειας, στη συνέχεια έξοδος
   -V, --version  εμφάνισε πληροφορίες έκδοσης, στη συνέχεια έξοδος
   -d             δημιουργία εξόδου αποσφαλμάτωσης (περιφραστική λειτουργία)
   -n             ξηρή λειτουργία, εμφάνιση των ονομάτων των αρχείων που θα αφαιρεθούν
   -x EXT         εκκαθάριση αρχείων εάν περιέχουν αυτήν την επέκταση
 %s αρχική σελίδα: <%s>
 %s αφαιρεί παλαιότερα αρχεία WAL από αρχειοθήκες PostgreSQL.

 Δοκιμάστε «%s --help» για περισσότερες πληροφορίες.
 Χρήση:
 η τοποθεσία της αρχειοθήκης «%s» δεν υπάρχει δεν ήταν δυνατό το κλείσιμο της τοποθεσίας αρχειοθήκης «%s»: %m δεν ήταν δυνατό το άνοιγμα της τοποθεσίας αρχειοθήκης «%s»: %m δεν ήταν δυνατή η ανάγνωση της τοποθεσίας αρχειοθήκης «%s»: %m δεν ήταν δυνατή η αφαίρεση του αρχείου «%s»: %m σφάλμα:  κρίσιμο:  μη έγκυρη παράμετρος ονόματος αρχείου πρέπει να καθορίσετε τη τοποθεσία αρχειοθήκης πρέπει να καθορίσετε το παλαιότερο κρατημένο αρχείο WAL πάρα πολλές παράμετροι εισόδου από την γραμμή εντολών προειδοποίηση:  