��    c      4  �   L      p     q  8   �  D   �  8   	  4   ?	  >   t	  <   �	  I   �	  9   :
  ?   t
  7   �
     �
  /     /   <  1   l     �  3   �  ,   �  !     $   5  $   Z       $   �  .   �  &   �  '         @  	   a  $   k  j   �  _   �     [  &   r  d   �  8   �  3   7  #   k  "   �  #   �     �  $   �  /        I     i  "   �     �     �  (   �  '   	  *   1  )   \  !   �     �  #   �     �            )   8     b  )     &   �  %   �  ,   �     #     +     E     `  4   h  6   �     �  $   �           5     V     q     �     �     �     �     �     �     �          $     3  /   N     ~     �     �     �     �     �          %     =  %   O     u  	   �  �  �  C      �   d  {   �  O   b  b   �  �     c   �  y     s   }  �   �  l   r  ;   �  U     V   q  o   �  #   8  �   \  P   �  N   F  :   �  :   �  1      9   =   \   w   E   �   M   !  D   h!     �!  Y   �!  �   "  �   �"  @   �#  Y   �#  �   +$  t   %  o   |%  0   �%  L   &  W   j&  Z   �&  f   '  g   �'  `   �'  =   M(  W   �(  S   �(  e   7)  ~   �)  }   *  �   �*  �   +  U   �+  Q   �+  }   G,  H   �,  H   -  Q   W-  n   �-  T   .  h   m.  u   �.  {   L/  z   �/     C0  >   R0  ,   �0     �0  s   �0  w   C1  E   �1  Y   2  N   [2  O   �2  U   �2  7   P3  E   �3     �3  *   �3      4     84  &   V4  R   }4     �4     �4  <   	5  �   F5  ;   �5  ;   6  9   G6  5   �6  =   �6  ?   �6  ?   57  =   u7  "   �7  b   �7  J   98     �8     U   ;   E                            -                (   :              R      *   %               ]   B       Z   Q   I      !   7       '           #   \   c   G                               "       H   W   `   ^   &   C   )             D   1       /   Y   O                           V   ?   L   0   N   T       J          5       @   4          9   X                  $      M   .   [       a   2   S   6   <       	   8       3   +   _   K              
   b   A   F       ,       P            >          =    
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
 The input string ended unexpectedly. The program "%s" is needed by %s but was not found in the
same directory as "%s".
Check your installation. The program "%s" was found by "%s"
but was not the same version as %s.
Check your installation. Token "%s" is invalid. Try "%s --help" for more information.
 Unicode escape values cannot be used for code point values above 007F when the encoding is not UTF8. Unicode high surrogate must not follow a high surrogate. Unicode low surrogate must follow a high surrogate. Usage:
  %s [OPTION]... BACKUPDIR

 WAL parsing failed for timeline %u \u0000 cannot be converted to text. backup successfully verified
 both path name and encoded path name cannot duplicate null pointer (internal error)
 checksum mismatch for file "%s" checksum without algorithm could not close directory "%s": %m could not close file "%s": %m could not decode file name could not finalize checksum of file "%s" could not finalize checksum of manifest could not initialize checksum of file "%s" could not initialize checksum of manifest could not open directory "%s": %m could not open file "%s": %m could not parse backup manifest: %s could not parse end LSN could not parse start LSN could not read file "%s": %m could not read file "%s": read %d of %lld could not stat file "%s": %m could not stat file or directory "%s": %m could not update checksum of file "%s" could not update checksum of manifest duplicate path name in backup manifest: "%s" error:  expected at least 2 lines expected version indicator fatal:  file "%s" has checksum of length %d, but expected %d file "%s" should contain %zu bytes, but read %zu bytes file size is not an integer invalid checksum for file "%s": "%s" invalid manifest checksum: "%s" last line not newline-terminated manifest checksum mismatch manifest ended unexpectedly manifest has no checksum missing end LSN missing path name missing size missing start LSN missing timeline no backup directory specified out of memory out of memory
 timeline is not an integer too many command-line arguments (first is "%s") unexpected WAL range field unexpected array end unexpected array start unexpected file field unexpected manifest version unexpected object end unexpected object field unexpected object start unexpected scalar unrecognized checksum algorithm: "%s" unrecognized top-level field warning:  Project-Id-Version: pg_verifybackup (PostgreSQL) 14
Report-Msgid-Bugs-To: pgsql-bugs@lists.postgresql.org
POT-Creation-Date: 2021-07-14 08:14+0000
PO-Revision-Date: 2021-06-04 09:16+0200
Last-Translator: Georgios Kokolatos <gkokolatos@pm.me>
Language-Team: 
Language: el
MIME-Version: 1.0
Content-Type: text/plain; charset=UTF-8
Content-Transfer-Encoding: 8bit
X-Generator: Poedit 2.4.3
 
Υποβάλετε αναφορές σφάλματων σε <%s>.
   -?, --help                  εμφάνισε αυτό το μήνυμα βοήθειας, στη συνέχεια έξοδος
   -V, --version               εμφάνισε πληροφορίες έκδοσης, στη συνέχεια έξοδος
   -e, --exit-on-error         να εξέλθει άμεσα σε σφάλμα
   -i, --ignore=RELATIVE_PATH  αγνόησε την υποδεικνυόμενη διαδρομή
   -m, --manifest-path=PATH    χρησιμοποίησε την καθορισμένη διαδρομή για την διακήρυξη
   -n, --no-parse-wal          μην δοκιμάσεις να αναλύσεις αρχεία WAL
   -q, --quiet                 να μην εκτυπώσεις καμία έξοδο, εκτός από σφάλματα
   -s, --skip-checksums        παράκαμψε την επαλήθευση αθροισμάτων ελέγχου
   -w, --wal-directory=PATH    χρησιμοποίησε την καθορισμένη διαδρομή για αρχεία WAL
 «%s» έχει μέγεθος %lld στο δίσκο, αλλά μέγεθος %zu στη διακήρυξη «%s» δεν είναι αρχείο ή κατάλογος «%s» βρίσκεται στη διακήρυξη αλλά όχι στο δίσκο «%s» βρίσκεται στο δίσκο, αλλά όχι στη διακήρυξη Το «\u» πρέπει να ακολουθείται από τέσσερα δεκαεξαδικά ψηφία. %s αρχική σελίδα: <%s>
 %s επαληθεύει ένα αντίγραφο ασφαλείας έναντι της διακήρυξης αντιγράφων ασφαλείας.

 Ο χαρακτήρας με τιμή 0x%02x πρέπει να διαφύγει. Η ακολουθία διαφυγής «\%s» δεν είναι έγκυρη. Ανέμενε «,» ή «]», αλλά βρήκε «%s». Ανέμενε «,» ή «}», αλλά βρήκε «%s». Ανέμενε «:», αλλά βρήκε «%s». Ανέμενε τιμή JSON, αλλά βρήκε «%s». Ανέμενε στοιχείο συστυχίας ή «]», αλλά βρέθηκε «%s». Ανέμενε τέλος εισόδου, αλλά βρήκε «%s». Ανέμενε συμβολοσειρά ή «}», αλλά βρήκε «%s». Ανέμενε συμβολοσειρά, αλλά βρήκε «%s». Επιλογές:
 Η συμβολοσειρά εισόδου τερματίστηκε αναπάντεχα. Το πρόγραμμα «%s» απαιτείται από %s αλλά δεν βρέθηκε στον
ίδιο κατάλογο με το «%s».
Ελέγξτε την εγκατάστασή σας. Το πρόγραμμα «%s» βρέθηκε από το "%s"
αλλά δεν ήταν η ίδια εκδοχή με %s.
Ελέγξτε την εγκατάστασή σας. Το διακριτικό «%s» δεν είναι έγκυρο. Δοκιμάστε «%s --help» για περισσότερες πληροφορίες.
 Δεν μπορούν να χρησιμοποιηθούν τιμές διαφυγής Unicode για τιμές σημείου κώδικα άνω του 007F όταν η κωδικοποίηση δεν είναι UTF8. Υψηλό διακριτικό Unicode δεν πρέπει να ακολουθεί υψηλό διακριτικό. Χαμηλό διακριτικό Unicode πρέπει να ακολουθεί υψηλό διακριτικό. Χρήση:
  %s [ΕΠΙΛΟΓΗ]... BACKUPDIR

 απέτυχε η ανάλυση WAL για την χρονογραμμή %u Δεν είναι δυνατή η μετατροπή του \u0000 σε κείμενο. το αντίγραφο ασφαλείας επαληθεύτηκε με επιτυχία
 και όνομα διαδρομής και κωδικοποιημένο όνομα διαδρομής δεν ήταν δυνατή η αντιγραφή δείκτη null (εσωτερικό σφάλμα)
 αναντιστοιχία αθροίσματος ελέγχου για το αρχείο «%s» άθροισμα ελέγχου χωρίς αλγόριθμο δεν ήταν δυνατό το κλείσιμο του καταλόγου «%s»: %m δεν ήταν δυνατό το κλείσιμο του αρχείου «%s»: %m δεν ήταν δυνατή η αποκωδικοποίηση του ονόματος αρχείου δεν ήταν δυνατή η ολοκλήρωση του αθροίσματος ελέγχου του αρχείου «%s» δεν ήταν δυνατή η ολοκλήρωση του αθροίσματος ελέγχου της διακήρυξης δεν ήταν δυνατή η αρχικοποίηση του αθροίσματος ελέγχου του αρχείου «%s» δεν ήταν δυνατή η αρχικοποίηση του αθροίσματος ελέγχου της διακήρυξης δεν ήταν δυνατό το άνοιγμα του καταλόγου «%s»: %m δεν ήταν δυνατό το άνοιγμα του αρχείου «%s»: %m δεν ήταν δυνατή η ανάλυση του αντιγράφου ασφαλείας της διακήρυξης: %s δεν ήταν δυνατή η ανάλυση του τελικού LSN δεν ήταν δυνατή η ανάλυση του αρχικού LSN δεν ήταν δυνατή η ανάγνωση του αρχείου «%s»: %m δεν ήταν δυνατή η ανάγνωση του αρχείου «%s»: ανέγνωσε %d από %lld δεν ήταν δυνατή η εκτέλεση stat στο αρχείο «%s»: %m δεν ήταν δυνατή η εκτέλεση stat στο αρχείο ή κατάλογο «%s»: %m δεν ήταν δυνατή η ενημέρωση αθροίσματος ελέγχου του αρχείου «%s» δεν ήταν δυνατή η ενημέρωση του αθροίσματος ελέγχου της διακήρυξης διπλότυπο όνομα διαδρομής στη διακήρυξη αντιγράφου ασφαλείας: «%s» σφάλμα:  αναμένονταν τουλάχιστον 2 γραμμές ανέμενε ένδειξη έκδοσης κρίσιμο:  το αρχείο «%s» έχει άθροισμα ελέγχου μήκους %d, αλλά αναμένεται %d το αρχείο «%s» έπρεπε να περιέχει %zu bytes, αλλά να αναγνώστηκαν %zu bytes το μέγεθος αρχείου δεν είναι ακέραιος μη έγκυρο άθροισμα ελέγχου για το αρχείο «%s»: «%s» μη έγκυρο άθροισμα ελέγχου διακήρυξης: «%s» η τελευταία γραμμή δεν τερματίστηκε με newline αναντιστοιχία ελέγχου αθροίσματος διακήρυξης η διακήρυξη έληξε απροσδόκητα η διακήρυξη δεν έχει άθροισμα ελέγχου λείπει τελικό LSN λείπει όνομα διαδρομής λείπει το μέγεθος λείπει αρχικό LSN λείπει η χρονογραμμή δεν ορίστηκε κατάλογος αντιγράφου ασφαλείας έλλειψη μνήμης έλλειψη μνήμης
 η χρονογραμμή δεν είναι ακέραιος πάρα πολλές παράμετροι εισόδου από την γραμμή εντολών (η πρώτη είναι η «%s») μη αναμενόμενο πεδίο περιοχής WAL μη αναμενόμενο τέλος συστοιχίας μη αναμενόμενη αρχή συστοιχίας μη αναμενόμενο πεδίο αρχείου μη αναμενόμενη έκδοση διακήρυξης μη αναμενόμενο τέλος αντικειμένου μη αναμενόμενο πεδίο αντικειμένου μη αναμενόμενη αρχή αντικειμένου μη αναμενόμενο scalar μη αναγνωρίσιμος αλγόριθμος αθροίσματος ελέγχου: «%s» μη αναγνωρίσιμο πεδίο ανώτατου επιπέδου προειδοποίηση:  