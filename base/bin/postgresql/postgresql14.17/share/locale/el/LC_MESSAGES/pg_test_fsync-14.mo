��          �   %   �      P  1   Q  2   �  /   �     �  8        :     T     o     �     �  (   �  U   �  [   D  K   �  c   �     P  .   k  E   �  &   �  +        3     N     [     _     d  �  q  �     �   �  [   @	  :   �	  u   �	  "   M
  $   p
  $   �
  $   �
     �
  Y   �
  �   X  �   %  �   �  �   P  $     \   A  u   �  Y     0   n  L   �     �                
                      
              	                                                                                         
Compare file sync methods using one %dkB write:
 
Compare file sync methods using two %dkB writes:
 
Compare open_sync with different write sizes:
 
Non-sync'ed %dkB writes:
 
Test if fsync on non-write file descriptor is honored:
  1 * 16kB open_sync write  2 *  8kB open_sync writes  4 *  4kB open_sync writes  8 *  2kB open_sync writes %13.3f ops/sec  %6.0f usecs/op
 %u second per test
 %u seconds per test
 (If the times are similar, fsync() can sync data written on a different
descriptor.)
 (This is designed to compare the cost of writing 16kB in different write
open_sync sizes.)
 (in wal_sync_method preference order, except fdatasync is Linux's default)
 * This file system and its mount options do not support direct
  I/O, e.g. ext4 in journaled mode.
 16 *  1kB open_sync writes Direct I/O is not supported on this platform.
 O_DIRECT supported on this platform for open_datasync and open_sync.
 Try "%s --help" for more information.
 Usage: %s [-f FILENAME] [-s SECS-PER-TEST]
 could not open output file fsync failed n/a n/a* write failed Project-Id-Version: pg_test_fsync (PostgreSQL) 14
Report-Msgid-Bugs-To: pgsql-bugs@lists.postgresql.org
POT-Creation-Date: 2021-07-20 08:49+0000
PO-Revision-Date: 2021-07-20 10:59+0200
Last-Translator: Georgios Kokolatos <gkokolatos@pm.me>
Language-Team: 
Language: el
MIME-Version: 1.0
Content-Type: text/plain; charset=UTF-8
Content-Transfer-Encoding: 8bit
Plural-Forms: nplurals=2; plural=n != 1;
X-Generator: Poedit 3.0
 
Συγκρίνετε τις μεθόδους συγχρονισμού αρχείων χρησιμοποιώντας μία εγγραφή %dkB:
 
Συγκρίνετε τις μεθόδους συγχρονισμού αρχείων χρησιμοποιώντας δύο εγγραφές %dkB:
 
Συγκρίνετε open_sync με διαφορετικά μεγέθη εγγραφής:
 
Μη-συγχρονισμένες %dkB εγγραφές:
 
Ελέγξτε εάν τηρείται το fsync σε μη-εγγράψιμο περιγραφέα αρχείων:
  1 * 16kB open_sync εγγραφή  2 *  8kB open_sync εγγραφές  4 *  4kB open_sync εγγραφές  8 *  2kB open_sync εγγραφές %13.3f ops/sec %6.0f usecs/op
 %u δευτερόλεπτο ανά τεστ
 %u δευτερόλεπτα ανά τεστ
 (Εάν οι χρόνοι είναι παρόμοιοι, το fsync() μπορεί να συγχρονίσει δεδομένα εγγεγραμμένα
σε διαφορετικό περιγραφέα.)
 (Αυτό έχει σχεδιαστεί για να συγκρίνει το κόστος της γραφής 16kB σε διαφορετικά
μεγέθη open_sync.)
 (με wal_sync_method σειρά προτίμησης, εκτός από fdatasync είναι η προεπιλογή σε Linux)
 * Αυτό το σύστημα αρχείων και οι επιλογές  προσάρτησής του δεν υποστηρίζουν
  άμεσο I/O, π.χ. ext4 σε λειτουργία journal.
 16 *  1kB open_sync εγγραφές Άμεσο I/O δεν υποστηρίζεται σε αυτήν την πλατφόρμα.
 O_DIRECT υποστηρίζεται σε αυτήν την πλατφόρμα για open_datasync και open_sync.
 Δοκιμάστε «%s --help» για περισσότερες πληροφορίες.
 Χρήση: %s [-f FILENAME] [-s SECS-PER-TEST]
 δεν ήταν δυνατό το άνοιγμα αρχείου εξόδου fsync απέτυχε n/a n/a* απέτυχε η εγγραφή 