��    �      ,  �   <      �
  ~   �
     p  0   �  +   �  q   �     V  4   v  7   �  s   �  .   W  G   �  4   �  )     w   -  4   �  9   �          '  @   ;  7   |  ,   �  !   �       ,   !  1   N  *   �  -   �  1   �  '     &   3  +   Z  "   �  #   �     �  )   �  =   �  	   =     G  &   c  <   �  !   �  	   �  -   �  <   !  +   ^  "   �     �  ,   �     �  3     <   H  *   �  "   �  '   �     �       !   *     L  !   e     �      �  3   �  /   �  '   !  )   I  *   s  5   �  I   �  ,     /   K  *   {  =   �  '   �          '     C     U     p     �  -   �  ,   �  ,   �  5   )     _  )   {  ?   �  8   �  �        �     �  0   �  5        T  A   i  L   �  +   �     $  6   2  '   i  #   �     �  (   �  4   �  )         J  /   g      �     �      �  $   �       "   /  ,   R          �  !   �  '   �             D   ;  +   �  ?   �  0   �        8   <      u      �   &   �       �   �  �     �"  C   �#  ^   �#  t   @$  �   �$  #   l%  G   �%  n   �%  �   G&  M   '  �   e'  ^   �'  R   K(  �   �(  l   e)  �   �)  !   Z*  #   |*  �   �*  f   :+  w   �+  V   ,  @   p,  �   �,  S   6-  L   �-  O   �-  S   '.  I   {.  H   �.  M   /  8   \/  <   �/     �/  D   �/  �   &0     �0  1   �0  Y   1  z   ]1  6   �1     2  S   ,2  ]   �2  {   �2  [   Z3  .   �3  Z   �3  7   @4  b   x4  z   �4  \   V5  [   �5  V   6  4   f6  *   �6  3   �6  (   �6  1   #7  %   U7  E   {7  o   �7  e   18  N   �8  G   �8  I   .9  t   x9  �   �9  Y   �:  W   �:  M   8;  �   �;  V   <  G   g<  .   �<  1   �<  G   =  "   X=      {=  M   �=  L   �=  L   7>  N   �>  4   �>  T   ?  �   ]?  f   �?  a  O@  5   �A  #   �A  �   B  �   �B      C  �   1C  �   �C  W   `D     �D  {   �D  F   PE  F   �E  !   �E  H    F  Y   IF  D   �F  3   �F  h   G  M   �G  2   �G  M   H  Y   TH  9   �H  Q   �H  {   :I  &   �I  L   �I  =   *J  _   hJ  K   �J  K   K  �   `K  c   �K  �   YL  T   �L  =   6M  k   tM  D   �M  ;   %N  U   aN  S   �N         O   ,          G   b       A   z       "   S       s   Q   t         u                '            g          R   7   �       i   #           ?   d   [       \      >   H       )       v   2   9   c   V   0   L   k       Y              `   .              J   3   	   a   y   h   j   r   ;              q   $       *   5         X   (         f          4           @   6       &           N   �   %   U   p       W       F      l              |   
   8          _       }   e   =   D   P       w      E   o   K   +      -       M   Z   :      !              ]      m   n   I   1   /       B          x   <   ^       C          ~   �               {   T    
If no output file is specified, the name is formed by adding .c to the
input file name, after stripping off .pgc if present.
 
Report bugs to <%s>.
   --regression   run in regression testing mode
   -?, --help     show this help, then exit
   -C MODE        set compatibility mode; MODE can be one of
                 "INFORMIX", "INFORMIX_SE", "ORACLE"
   -D SYMBOL      define SYMBOL
   -I DIRECTORY   search DIRECTORY for include files
   -V, --version  output version information, then exit
   -c             automatically generate C code from embedded SQL code;
                 this affects EXEC SQL TYPE
   -d             generate parser debug output
   -h             parse a header file, this option includes option "-c"
   -i             parse system include files as well
   -o OUTFILE     write result to OUTFILE
   -r OPTION      specify run-time behavior; OPTION can be:
                 "no_indicator", "prepare", "questionmarks"
   -t             turn on autocommit of transactions
 "database" cannot be used as cursor name in INFORMIX mode %s at or near "%s" %s home page: <%s>
 %s is the PostgreSQL embedded SQL preprocessor for C programs.

 %s, the PostgreSQL embedded C preprocessor, version %s
 %s: could not locate my own executable path
 %s: could not open file "%s": %s
 %s: no input files specified
 %s: parser debug support (-d) not available
 AT option not allowed in CLOSE DATABASE statement AT option not allowed in CONNECT statement AT option not allowed in DISCONNECT statement AT option not allowed in SET CONNECTION statement AT option not allowed in TYPE statement AT option not allowed in VAR statement AT option not allowed in WHENEVER statement COPY FROM STDIN is not implemented CREATE TABLE AS cannot specify INTO ERROR:  EXEC SQL INCLUDE ... search starts here:
 Error: include path "%s/%s" is too long on line %d, skipping
 Options:
 SHOW ALL is not implemented Try "%s --help" for more information.
 Unix-domain sockets only work on "localhost" but not on "%s" Usage:
  %s [OPTION]... FILE...

 WARNING:  arrays of indicators are not allowed on input connection %s is overwritten with %s by DECLARE statement %s could not open include file "%s" on line %d could not remove output file "%s"
 cursor "%s" does not exist cursor "%s" has been declared but not opened cursor "%s" is already defined descriptor %s bound to connection %s does not exist descriptor %s bound to the default connection does not exist descriptor header item "%d" does not exist descriptor item "%s" cannot be set descriptor item "%s" is not implemented end of search list
 expected "://", found "%s" expected "@" or "://", found "%s" expected "@", found "%s" expected "postgresql", found "%s" incomplete statement incorrectly formed variable "%s" indicator for array/pointer has to be array/pointer indicator for simple data type has to be simple indicator for struct has to be a struct indicator struct "%s" has too few members indicator struct "%s" has too many members indicator variable "%s" is hidden by a local variable indicator variable "%s" is hidden by a local variable of a different type indicator variable must have an integer type initializer not allowed in EXEC SQL VAR command initializer not allowed in type definition internal error: unreachable state; please report this to <%s> interval specification not allowed here invalid bit string literal invalid connection type: %s invalid data type invalid hex string literal key_member is always 0 missing "EXEC SQL ENDIF;" missing identifier in EXEC SQL DEFINE command missing identifier in EXEC SQL IFDEF command missing identifier in EXEC SQL UNDEF command missing matching "EXEC SQL IFDEF" / "EXEC SQL IFNDEF" more than one EXEC SQL ELSE multidimensional arrays are not supported multidimensional arrays for simple data types are not supported multidimensional arrays for structures are not supported multilevel pointers (more than 2 levels) are not supported; found %d level multilevel pointers (more than 2 levels) are not supported; found %d levels name "%s" is already declared nested /* ... */ comments nested arrays are not supported (except strings) no longer supported LIMIT #,# syntax passed to server nullable is always 1 only data types numeric and decimal have precision/scale argument only protocols "tcp" and "unix" and database type "postgresql" are supported operator not allowed in variable definition out of memory pointer to pointer is not supported for this data type pointers to varchar are not implemented subquery in FROM must have an alias syntax error syntax error in EXEC SQL INCLUDE command too many levels in nested structure/union definition too many nested EXEC SQL IFDEF conditions type "%s" is already defined type name "string" is reserved in Informix mode unhandled previous state in xqs
 unmatched EXEC SQL ENDIF unrecognized data type name "%s" unrecognized descriptor item code %d unrecognized token "%s" unrecognized variable type code %d unsupported feature will be passed to server unterminated /* comment unterminated bit string literal unterminated dollar-quoted string unterminated hexadecimal string literal unterminated quoted identifier unterminated quoted string using variable "%s" in different declare statements is not supported variable "%s" is hidden by a local variable variable "%s" is hidden by a local variable of a different type variable "%s" is neither a structure nor a union variable "%s" is not a pointer variable "%s" is not a pointer to a structure or a union variable "%s" is not an array variable "%s" is not declared variable "%s" must have a numeric type zero-length delimited identifier Project-Id-Version: ecpg (PostgreSQL) 14
Report-Msgid-Bugs-To: pgsql-bugs@lists.postgresql.org
POT-Creation-Date: 2021-11-03 18:09+0000
PO-Revision-Date: 2021-11-05 11:17+0100
Last-Translator: Georgios Kokolatos <gkokolatos@pm.me>
Language-Team: 
Language: el
MIME-Version: 1.0
Content-Type: text/plain; charset=UTF-8
Content-Transfer-Encoding: 8bit
Plural-Forms: nplurals=2; plural=n != 1;
X-Generator: Poedit 3.0
 
Εάν δεν καθοριστεί αρχείο εξόδου, το όνομα σχηματίζεται προσθέτοντας .c στο
όνομα αρχείου εισόδου, μετά την αφαίρεση του .pgc, εάν αυτό υπάρχει.
 
Υποβάλετε αναφορές σφάλματων σε <%s>.
   --regression   εκτέλεσε σε λειτουργία ελέγχου αναδρομής
   -?, --help     εμφάνισε αυτό το μήνυμα βοήθειας, στη συνέχεια έξοδος
   -C MODE        όρισε λειτουργία συμβατότητας; MODE μπορεί να είναι ένα από
                 «INFORMIX», «INFORMIX_SE», «ORACLE»
   -D SYMBOL      όρισε SYMBOL
   -I DIRECTORY   ψάξε στο DIRECTORY για αρχεία include
   -V, --version  εμφάνισε πληροφορίες έκδοσης, στη συνέχεια έξοδος
   -c             δημιούργησε αυτόματα κώδικα σε C από ενσωματωμένο SQL κώδικα·
                 αυτό επηρεάζει την εντολή EXEC SQL TYPE
   -d             παράξε έξοδο αποσφαλμάτωσης parser
   -h             ανάλυσε αρχείο header, αυτή η επιλογή περιλαμβάνει την επιλογή «-c»
   -i             ανάλυσε επίσης αρχεία include του συστήματος
   -o OUTFILE     γράψε το αποτέλεσμα στο αρχείο OUTFILE
   -r OPTION      καθόρισε τη συμπεριφορά χρόνου εκτέλεσης. OPTION μπορεί να είναι:
                 «no_indicator», «prepare», «questionmarks»
   -t             ενεργοποίησε την αυτόματη ολοκλήρωση συναλλαγών
 το «database» δεν μπορεί να χρησιμοποιηθεί ως όνομα δρομέα σε λειτουργία INFORMIX %s σε ή κοντά σε «%s» %s αρχική σελίδα: <%s>
 %s είναι ο ενσωματωμένος SQL προεπεξεργαστής της PostgreSQL για προγράμματα γραμμένα σε C.

 %s, ο ενσωματωμένος προεπεξεργαστής C της PostgreSQL, έκδοση %s
 %s: δεν ήταν δυνατός ο εντοπισμός της ιδίας εκτελέσιμης διαδρομής
 %s: δεν ήταν δυνατό το άνοιγμα του αρχείου «%s»: %s
 %s: δεν καθορίστηκαν αρχεία εισόδου
 %s: η υποστήριξη εντοπισμού σφαλμάτων (-d) του αναλυτή δεν είναι διαθέσιμη
 η επιλογή AT δεν επιτρέπεται σε δήλωση CLOSE DATABASE η επιλογή AT δεν επιτρέπεται σε δήλωση CONNECT η επιλογή AT δεν επιτρέπεται σε δήλωση DISCONNECT η επιλογή AT δεν επιτρέπεται σε δήλωση SET CONNECTION η επιλογή AT δεν επιτρέπεται σε δήλωση TYPE η επιλογή AT δεν επιτρέπεται σε δήλωση VAR η επιλογή AT δεν επιτρέπεται σε δήλωση WHENEVER COPY FROM STDIN δεν είναι υλοποιημένο CREATE TABLE AS δεν δύναται να ορίσει INTO ΣΦΑΛΜΑ:  EXEC SQL INCLUDE ... η αναζήτηση ξεκινάει εδώ:
 Σφάλμα: η διαδρομή ενσωμάτωσης «%s/%s» είναι πολύ μακρυά στη γραμμή %d, παρακάμπτεται
 Επιλογές:
 SHOW ALL δεν είναι υλοποιημένο Δοκιμάστε «%s --help» για περισσότερες πληροφορίες.
 οι υποδοχές πεδίου-Unix λειτουργούν μόνο στο «localhost» αλλά όχι στο «%s» Χρήση:
  %s [ΕΠΙΛΟΓΗ]... ΑΡΧΕΙΟ...

 ΠΡΟΕΙΔΟΠΟΙΗΣΗ:  δεν επιτρέπονται δείκτες συστάδων για είσοδο η σύνδεση %s αντικαθίσταται με %s από τη δήλωση DECLARE %s δεν ήταν δυνατό το άνοιγμα του αρχείου ενσωμάτωσης «%s» στη γραμμή %d δεν ήταν δυνατή η αφαίρεση του αρχείου εξόδου «%s»
 ο δρομέας «%s» δεν υπάρχει ο δρομέας «%s» έχει δηλωθεί αλλά δεν έχει ανοιχτεί ο δρομέας «%s» έχει ήδη οριστεί ο περιγραφέας %s δεσμευμένος στη σύνδεση %s δεν υπάρχει ο περιγραφέας %s δεσμευμένος στη προεπιλεγμένη σύνδεση δεν υπάρχει ο περιγραφέας στοιχείου κεφαλίδας «%d» δεν υπάρχει το στοιχείο περιγραφής «%s» δεν δύναται να οριστεί το στοιχείο περιγραφής «%s» δεν έχει υλοποιηθεί τέλος της λίστας αναζήτησης
 ανέμενε «://», βρήκε «%s». ανέμενε «@» ή «://», βρήκε «%s». ανέμενε «@», βρήκε «%s». ανέμενε «postgresql», βρήκε «%s». ανολοκλήρωτη δήλωση εσφαλμένα σχηματισμένη μεταβλητή «%s» ο δείκτης για συστάδα/δείκτη πρέπει να είναι πίνακας/δείκτης ο δείκτης για απλό τύπο δεδομένων πρέπει να είναι απλός ο δείκτης για δομή πρέπει να είναι μια δομή ο δείκτης δομής «%s» έχει πολύ λίγα μέλη ο δείκτης δομής «%s» έχει πάρα πολλά μέλη ο δείκτης μεταβλητής «%s» αποκρύπτεται από μια τοπική μεταβλητή ο δείκτης μεταβλητής «%s» αποκρύπτεται από μια τοπική μεταβλητή διαφορετικού τύπου ο δείκτης μεταβλητής πρέπει να έχει ακέραιο τύπο δεν επιτρέπεται αρχικοποιητής σε εντολή EXEC SQL VAR δεν επιτρέπεται εκκινητής σε ορισμό τύπου εσωτερικό σφάλμα: μη δυνατή κατάσταση· Παρακαλούμε όπως το αναφέρετε σε <%s> προδιαγραφές διαστήματος δεν επιτρέπονται εδώ μη έγκυρη bit κυριολεκτική συμβολοσειρά άκυρη επιλογή σύνδεσης: %s μη έγκυρος τύπος δεδομένων μη έγκυρη hex κυριολεκτική συμβολοσειρά key_member είναι πάντα 0 λείπει «EXEC SQL ENDIF;» λείπει αναγνωριστικό στην εντολή EXEC SQL DEFINE λείπει αναγνωριστικό στην εντολή EXEC SQL IFDEF λείπει αναγνωριστικό στην εντολή EXEC SQL UNDEF λείπει αντιστοίχιση «EXEC SQL IFDEF» / «EXEC SQL IFNDEF» περισσότερες από μία EXEC SQL ELSE οι πολυδιάστατες συστάδες δεν υποστηρίζονται οι πολυδιάστατες συστυχίες για απλούς τύπους δεδομένων δεν υποστηρίζονται οι πολυδιάστατες συστάδες για δομές δεν υποστηρίζονται οι δείκτες πολλαπλών επιπέδων (περισσότερα από 2 επίπεδα) δεν υποστηρίζονται· βρέθηκε %d επίπεδο οι δείκτες πολλαπλών επιπέδων (περισσότερα από 2 επίπεδα) δεν υποστηρίζονται· βρέθηκαν %d επίπεδα το όνομα «%s» έχει ήδη δηλωθεί ένθετα /* ... */ σχόλια οι ένθετες συστάδες δεν υποστηρίζονται (εξαιρούνται οι συμβολοσειρές) μη υποστηριζόμενη πλέον σύνταξη LIMIT #,# που θα προωθηθεί στον διακομιστή nullable είναι πάντα 1 μόνο οι αριθμητικοί και δεκαδικοί τύποι δεδομένων έχουν όρισμα ακρίβειας/κλίμακας μόνο τα πρωτόκολλα "TCP" και "UNIX" και ο τύπος βάσης δεδομένων «postgresql» υποστηρίζονται δεν επιτρέπεται χειριστής σε ορισμό μεταβλητής έλλειψη μνήμης δεν υποστηρίζεται δείκτης προς δείκτη για αυτόν τον τύπο δεδομένων δείκτες σε varchar δεν είναι υλοποιημένοι υποερώτημα σε FROM πρέπει να έχει ένα alias συντακτικό σφάλμα συντακτικό σφάλμα στην εντολή EXEC SQL INCLUDE πάρα πολλά επίπεδα σε ένθετο ορισμό δομής/ένωσης πάρα πολλές ένθετες συνθήκες EXEC SQL IFDEF ο τύπος «%s» έχει ήδη οριστεί το όνομα τύπου «string» είναι δεσμευμένο σε λειτουργία Informix μη χειρισμένη προηγούμενη κατάσταση σε xqs
 μη αντιστοιχισμένο EXEC SQL ENDIF μη αναγνωρίσιμο όνομα τύπου δεδομένων «%s» μη αναγνωρίσιμος κωδικός στοιχείου περιγραφέα %d μη αναγνωρίσιμο διακριτικό «%s» μη αναγνωρίσιμος κωδικός τύπου μεταβλητής %d μη υποστηριζόμενο χαρακτηριστικό που θα προωθηθεί στον διακομιστή ατερμάτιστο /* σχόλιο ατερμάτιστη bit κυριολεκτική συμβολοσειρά ατερμάτιστη dollar-quoted συμβολοσειρά ατερμάτιστη δεκαεξαδική κυριολεκτική συμβολοσειρά ατερμάτιστο αναγνωριστικό σε εισαγωγικά ανολοκλήρωτη συμβολοσειρά με εισαγωγικά η χρήση της μεταβλητής «%s» σε διαφορετικές δηλώσεις προτάσεων δεν υποστηρίζεται η μεταβλητή «%s» αποκρύπτεται από μια τοπική μεταβλητή η μεταβλητή «%s» αποκρύπτεται από μια τοπική μεταβλητή διαφορετικού τύπου η μεταβλητή «%s» δεν είναι ούτε δομή ούτε ένωση η μεταβλητή «%s» δεν είναι δείκτης η μεταβλητή «%s» δεν είναι δείκτης προς μια δομή ή μια ένωση η μεταβλητή «%s» δεν είναι μία συστάδα η μεταβλητή «%s» δεν έχει δηλωθεί η μεταβλητή «%s» πρέπει να έχει αριθμητικό τύπο μηδενικού μήκους οριοθετημένο αναγνωριστικό 